<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cabang;
use App\Models\PsUnit;
use App\Models\Transaksi;
use App\Models\Rental;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $cabangList = Cabang::where('is_aktif', true)->get();
        return view('admin.dashboard', compact('cabangList'));
    }

    // GET /admin/api/overview?cabang=1
    public function overview(Request $request)
    {
        $cabangId = $request->cabang;

        $psUnits = PsUnit::with(['activeRental.orders'])
            ->where('cabang_id', $cabangId)
            ->orderBy('nomor_ps')
            ->get();

        $totalPs = $psUnits->count();
        $psAktif = $psUnits->where('status', 'dipakai')->count();

        $pendapatan = Transaksi::where('status_bayar', 'lunas')
            ->whereDate('tanggal', today())
            ->whereHas('rental.psUnit', fn($q) => $q->where('cabang_id', $cabangId))
            ->sum('total_bayar');

        $totalTrx = Transaksi::where('status_bayar', 'lunas')
            ->whereDate('tanggal', today())
            ->whereHas('rental.psUnit', fn($q) => $q->where('cabang_id', $cabangId))
            ->count();

        // ── ps_realtime: kirim timestamp ke frontend agar JS bisa hitung timer sendiri ──
        $psRealtime = $psUnits->map(function ($ps) {
            $rental = $ps->activeRental;
            $data = [
                'id'            => $ps->id,
                'nomor_ps'      => $ps->nomor_ps,
                'tipe_ps'       => $ps->tipe_ps,
                'status'        => $ps->status,
                'harga_per_jam' => (float) $ps->harga_per_jam,
                'pelanggan'     => null,
                'mode_billing'  => null,
                'jam_mulai_ts'  => null,
                'jam_selesai_ts'=> null,
                'subtotal_menu' => 0,
            ];

            if ($rental) {
                $data['pelanggan']    = $rental->nama_pelanggan;
                $data['mode_billing'] = $rental->mode_billing;
                // Timestamp dalam milliseconds untuk JS Date.now()
                $data['jam_mulai_ts']   = $rental->jam_mulai->timestamp * 1000;
                $data['jam_selesai_ts'] = $rental->jam_selesai?->timestamp * 1000;
                $data['subtotal_menu']  = (float) $rental->orders->sum('subtotal');
            }

            return $data;
        })->values()->toArray();

        return response()->json([
            'total_ps'        => $totalPs,
            'ps_aktif'        => $psAktif,
            'pendapatan_hari' => (float) $pendapatan,
            'total_trx'       => $totalTrx,
            'ps_realtime'     => $psRealtime,  // ← data lengkap dengan timestamp
        ]);
    }

    // GET /admin/api/laporan?cabang=1&dari=...&sampai=...
    public function laporan(Request $request)
    {
        $cabangId = $request->cabang;
        $dari     = Carbon::parse($request->dari)->startOfDay();
        $sampai   = Carbon::parse($request->sampai)->endOfDay();

        $transaksi = Transaksi::with(['rental.psUnit'])
            ->where('status_bayar', 'lunas')
            ->whereBetween('tanggal', [$dari, $sampai])
            ->whereHas('rental.psUnit', fn($q) => $q->where('cabang_id', $cabangId))
            ->orderByDesc('tanggal')
            ->get();

        $total  = $transaksi->sum('total_bayar');
        $rental = $transaksi->where('tipe_transaksi', 'rental')->sum('total_bayar');
        $cafe   = $transaksi->where('tipe_transaksi', 'cafe_only')->sum('total_bayar');

        $rows = $transaksi->map(fn($t) => [
            'tanggal' => Carbon::parse($t->tanggal)->format('d/m H:i'),
            'tipe'    => $t->tipe_transaksi,
            'detail'  => $t->rental
                ? ($t->rental->psUnit->nomor_ps ?? '?') . ' — ' . $t->rental->nama_pelanggan
                : 'Cafe Only',
            'metode'  => ucfirst($t->metode_bayar),
            'total'   => (float) $t->total_bayar,
        ]);

        return response()->json([
            'total'     => $total,
            'rental'    => $rental,
            'cafe'      => $cafe,
            'count'     => $transaksi->count(),
            'transaksi' => $rows,
        ]);
    }
}