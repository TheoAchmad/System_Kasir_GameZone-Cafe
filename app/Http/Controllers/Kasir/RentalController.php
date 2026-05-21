<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\PsUnit;
use App\Models\Rental;
use App\Models\Transaksi;
use App\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RentalController extends Controller
{
    public function mulai(Request $request)
    {
        $request->validate([
            'ps_id'          => 'required|exists:ps_units,id',
            'nama_pelanggan' => 'required|string|max:100',
            'mode_billing'   => 'required|in:up,down',
            'durasi_awal'    => 'required_if:mode_billing,down|nullable|numeric|min:0.5',
        ]);

        $ps = PsUnit::findOrFail($request->ps_id);

        if ($ps->status !== 'kosong') {
            return response()->json(['error' => 'PS sedang tidak tersedia'], 422);
        }

        $jamMulai    = Carbon::now();
        $jamSelesai  = null;
        $durasiMenit = null;

        if ($request->mode_billing === 'down') {
            $durasiMenit = (float) $request->durasi_awal * 60;
            $jamSelesai  = $jamMulai->copy()->addMinutes($durasiMenit);
        }

        $rental = Rental::create([
            'ps_id'           => $ps->id,
            'kasir_id'        => auth()->id(),
            'nama_pelanggan'  => $request->nama_pelanggan,
            'mode_billing'    => $request->mode_billing,
            'jam_mulai'       => $jamMulai,
            'jam_selesai'     => $jamSelesai,
            'durasi_awal'     => $durasiMenit,
            'durasi_tambahan' => 0,
            'status'          => 'berjalan',
        ]);

        $ps->update(['status' => 'dipakai']);

        return response()->json([
            'success' => true,
            'rental'  => $rental->load('psUnit'),
        ]);
    }

    public function tambahWaktu(Request $request, Rental $rental)
    {
        $request->validate(['tambahan_jam' => 'required|numeric|min:0.5']);

        if ($rental->status !== 'berjalan') {
            return response()->json(['error' => 'Sesi tidak aktif'], 422);
        }

        $menit = (float) $request->tambahan_jam * 60;

        $rental->update([
            'jam_selesai'     => Carbon::parse($rental->jam_selesai)->addMinutes($menit),
            'durasi_tambahan' => $rental->durasi_tambahan + $menit,
        ]);

        return response()->json([
            'success'     => true,
            'jam_selesai' => $rental->fresh()->jam_selesai->toIso8601String(),
        ]);
    }

    /**
     * Selesaikan sesi dan buat transaksi.
     *
     * PENTING: Backend adalah sumber kebenaran untuk harga sewa.
     * Frontend TIDAK mengirim harga_final lagi.
     * Backend menghitung sendiri dari data database.
     */
    public function kalkulasi(Rental $rental)
{
    if ($rental->status !== 'berjalan') {
        return response()->json(['error' => 'Sesi tidak aktif'], 422);
    }
 
    $now         = \Carbon\Carbon::now();
    $hargaPerJam = (float) $rental->psUnit->harga_per_jam;
 
    // Hitung sewa
    if ($rental->mode_billing === 'down') {
        // Mode countdown: dari jam_mulai → jam_selesai yang disepakati
        $menit     = $rental->jam_mulai->diffInMinutes($rental->jam_selesai);
        $hargaSewa = round(($menit / 60) * $hargaPerJam);
    } else {
        // Mode open: dari jam_mulai → sekarang
        $menit     = $rental->jam_mulai->diffInMinutes($now);
        $hargaSewa = round(($menit / 60) * $hargaPerJam);
    }
 
    // Subtotal menu (hanya yang belum terikat transaksi)
    $subtotalMenu = (int) $rental->orders()
        ->whereNull('transaksi_id')
        ->sum('subtotal');
 
    $total = $hargaSewa + $subtotalMenu;
 
    return response()->json([
        'subtotal_sewa' => $hargaSewa,
        'subtotal_menu' => $subtotalMenu,
        'total'         => $total,
        'mode_billing'  => $rental->mode_billing,
        'jam_mulai'     => $rental->jam_mulai->format('H:i'),
        'jam_selesai'   => $rental->jam_selesai?->format('H:i') ?? 'Open',
    ]);
}


    public function selesaikan(Request $request, Rental $rental)
    {
        $request->validate([
            'metode_bayar' => 'required|in:tunai,transfer,qris',
            'uang_bayar'   => 'required|numeric|min:0',
        ]);

        if ($rental->status !== 'berjalan') {
            return response()->json(['error' => 'Sesi sudah selesai'], 422);
        }

        $now         = Carbon::now();
        $hargaPerJam = (float) $rental->psUnit->harga_per_jam;

        // ── Kalkulasi harga sewa — SEMUA di backend ──────────────────
        if ($rental->mode_billing === 'down') {
            // Mode countdown: harga dihitung dari jam_mulai → jam_selesai yang disepakati
            // Bukan dari now(), sehingga telat checkout tidak menambah harga
            $jamSelesaiHitung = $rental->jam_selesai; // waktu yang disepakati di awal

            // Menit total = durasi awal yang disepakati (sudah include tambah waktu)
            $menitTotal = $rental->jam_mulai->diffInMinutes($jamSelesaiHitung);
            $hargaSewa  = round(($menitTotal / 60) * $hargaPerJam);
        } else {
            // Mode open billing: hitung dari now()
            $menitTotal = $rental->jam_mulai->diffInMinutes($now);
            $hargaSewa  = round(($menitTotal / 60) * $hargaPerJam);
        }

        // ── Ambil pending orders ──────────────────────────────────────
        $pendingOrders = $rental->orders()
            ->whereNull('transaksi_id')
            ->get();

        $subtotalMenu = (int) $pendingOrders->sum('subtotal');

        // ── Total ─────────────────────────────────────────────────────
        $total    = $hargaSewa + $subtotalMenu;
        $uangBayar = (float) $request->uang_bayar;

        if ($uangBayar < $total) {
            return response()->json([
                'error' => "Uang bayar kurang. Total: Rp " . number_format($total, 0, ',', '.')
            ], 422);
        }

        // ── Buat transaksi ────────────────────────────────────────────
        $transaksi = Transaksi::create([
            'rental_id'      => $rental->id,
            'kasir_id'       => auth()->id(),
            'tipe_transaksi' => 'rental',
            'subtotal_sewa'  => $hargaSewa,
            'subtotal_menu'  => $subtotalMenu,
            'diskon'         => 0,
            'total_bayar'    => $total,
            'uang_bayar'     => $uangBayar,
            'kembalian'      => $uangBayar - $total,
            'metode_bayar'   => $request->metode_bayar,
            'tanggal'        => $now,
            'status_bayar'   => 'lunas',
        ]);

        // ── Update rental ─────────────────────────────────────────────
        $rental->update([
            'jam_selesai' => $now,
            'harga_sewa'  => $hargaSewa,
            'status'      => 'selesai',
        ]);

        // ── Ikat orders ke transaksi ──────────────────────────────────
        foreach ($pendingOrders as $order) {
            $order->update(['transaksi_id' => $transaksi->id]);
        }

        // ── Bebaskan PS ───────────────────────────────────────────────
        $rental->psUnit->update(['status' => 'kosong']);

        return response()->json([
            'success'   => true,
            'transaksi' => $transaksi->load('orders.menu', 'rental.psUnit'),
        ]);
    }
}