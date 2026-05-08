<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\PsUnit;
use App\Models\Rental;
use App\Models\Transaksi;
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
            'ps_id'          => $ps->id,
            'kasir_id'       => auth()->id(),
            'nama_pelanggan' => $request->nama_pelanggan,
            'mode_billing'   => $request->mode_billing,
            'jam_mulai'      => $jamMulai,
            'jam_selesai'    => $jamSelesai,
            'durasi_awal'    => $durasiMenit,
            'durasi_tambahan'=> 0,
            'status'         => 'berjalan',
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

    public function selesaikan(Request $request, Rental $rental)
    {
        $request->validate([
            'metode_bayar' => 'required|in:tunai,transfer,qris',
            'uang_bayar'   => 'required|numeric|min:0',
            // harga_final dikirim dari frontend (sudah dikunci saat waktu habis)
            'harga_final'  => 'nullable|numeric|min:0',
        ]);

        if ($rental->status !== 'berjalan') {
            return response()->json(['error' => 'Sesi sudah selesai'], 422);
        }

        $jamSelesaiActual = Carbon::now();
        $hargaPerJam      = $rental->psUnit->harga_per_jam;

        // ── Kalkulasi harga sewa ──────────────────────────────
        if ($rental->mode_billing === 'down') {
            // MODE COUNTDOWN:
            // Gunakan harga yang sudah dikunci di frontend (saat waktu habis)
            // Atau hitung berdasarkan jam_selesai yang sudah ditentukan (bukan now())
            if ($request->harga_final && $request->harga_final > 0) {
                // Gunakan harga yang dikirim frontend (sudah terkunci)
                $hargaSewa = (float) $request->harga_final;
            } else {
                // Fallback: hitung dari durasi yang disepakati (jam_mulai → jam_selesai awal)
                $menitDisepakati = $rental->jam_mulai->diffInMinutes($rental->jam_selesai)
                    + $rental->durasi_tambahan;
                $hargaSewa = ($menitDisepakati / 60) * $hargaPerJam;
            }
            // Update jam_selesai ke waktu selesai aktual
            $rental->update(['jam_selesai' => $jamSelesaiActual]);
        } else {
            // MODE OPEN BILLING: hitung dari now()
            $menitActual = $rental->jam_mulai->diffInMinutes($jamSelesaiActual);
            $hargaSewa   = ($menitActual / 60) * $hargaPerJam;
        }

        // Subtotal menu (orders yang belum terikat transaksi)
        $subtotalMenu = (float) $rental->orders()->whereNull('transaksi_id')->sum('subtotal');
        $total        = $hargaSewa + $subtotalMenu;

        $transaksi = Transaksi::create([
            'rental_id'      => $rental->id,
            'kasir_id'       => auth()->id(),
            'tipe_transaksi' => 'rental',
            'subtotal_sewa'  => $hargaSewa,
            'subtotal_menu'  => $subtotalMenu,
            'diskon'         => 0,
            'total_bayar'    => $total,
            'uang_bayar'     => $request->uang_bayar,
            'kembalian'      => $request->uang_bayar - $total,
            'metode_bayar'   => $request->metode_bayar,
            'tanggal'        => $jamSelesaiActual,
            'status_bayar'   => 'lunas',
        ]);

        $rental->update([
            'harga_sewa' => $hargaSewa,
            'status'     => 'selesai',
        ]);

        // Ikat semua orders ke transaksi ini
        $rental->orders()->whereNull('transaksi_id')
            ->update(['transaksi_id' => $transaksi->id]);

        // Bebaskan PS
        $rental->psUnit->update(['status' => 'kosong']);

        return response()->json([
            'success'   => true,
            'transaksi' => $transaksi->load('orders.menu', 'rental.psUnit'),
        ]);
    }
}