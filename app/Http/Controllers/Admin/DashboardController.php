<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cabang;
use App\Models\PsUnit;
use App\Models\Transaksi;
use App\Models\User;
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
        $cabangId = (int) $request->cabang;

        $psUnits = PsUnit::with(['activeRental.orders'])
            ->where('cabang_id', $cabangId)
            ->orderBy('nomor_ps')
            ->get();

        $totalPs = $psUnits->count();
        $psAktif = $psUnits->where('status', 'dipakai')->count();

        // Ambil semua kasir di cabang ini
        $kasirIds = User::where('cabang_id', $cabangId)->pluck('id');

        // Pendapatan hari ini — rental + cafe only dari kasir cabang ini
        $pendapatan = Transaksi::where('status_bayar', 'lunas')
            ->whereDate('tanggal', today())
            ->where(function ($q) use ($cabangId, $kasirIds) {
                $q->whereHas('rental.psUnit', fn($q2) => $q2->where('cabang_id', $cabangId))
                  ->orWhere(function ($q3) use ($kasirIds) {
                      $q3->whereNull('rental_id')
                         ->whereIn('kasir_id', $kasirIds);
                  });
            })
            ->sum('total_bayar');

        $totalTrx = Transaksi::where('status_bayar', 'lunas')
            ->whereDate('tanggal', today())
            ->where(function ($q) use ($cabangId, $kasirIds) {
                $q->whereHas('rental.psUnit', fn($q2) => $q2->where('cabang_id', $cabangId))
                  ->orWhere(function ($q3) use ($kasirIds) {
                      $q3->whereNull('rental_id')
                         ->whereIn('kasir_id', $kasirIds);
                  });
            })
            ->count();

        // ps_realtime — kirim timestamp agar JS bisa hitung timer sendiri
        $psRealtime = $psUnits->map(function ($ps) {
            $rental = $ps->activeRental;
            return [
                'id'             => $ps->id,
                'nomor_ps'       => $ps->nomor_ps,
                'tipe_ps'        => $ps->tipe_ps,
                'status'         => $ps->status,
                'harga_per_jam'  => (float) $ps->harga_per_jam,
                'pelanggan'      => $rental?->nama_pelanggan,
                'mode_billing'   => $rental?->mode_billing,
                'jam_mulai_ts'   => $rental ? $rental->jam_mulai->timestamp * 1000 : null,
                'jam_selesai_ts' => $rental?->jam_selesai
                    ? $rental->jam_selesai->timestamp * 1000
                    : null,
                'subtotal_menu'  => $rental ? (float) $rental->orders->sum('subtotal') : 0,
            ];
        })->values()->toArray();

        return response()->json([
            'total_ps'        => $totalPs,
            'ps_aktif'        => $psAktif,
            'pendapatan_hari' => (float) $pendapatan,
            'total_trx'       => (int) $totalTrx,
            'ps_realtime'     => $psRealtime,
        ]);
    }

    // GET /admin/api/laporan?cabang=1&dari=...&sampai=...
    public function laporan(Request $request)
    {
        $cabangId = (int) $request->cabang;
        $dari     = Carbon::parse($request->dari)->startOfDay();
        $sampai   = Carbon::parse($request->sampai)->endOfDay();

        // Ambil kasir_id milik cabang ini
        $kasirIds = User::where('cabang_id', $cabangId)->pluck('id');

        // Ambil semua transaksi: rental PS cabang ini + cafe only dari kasir cabang ini
        $transaksi = Transaksi::with(['rental.psUnit', 'kasir'])
            ->where('status_bayar', 'lunas')
            ->whereBetween('tanggal', [$dari, $sampai])
            ->where(function ($q) use ($cabangId, $kasirIds) {
                $q->whereHas('rental.psUnit', fn($q2) => $q2->where('cabang_id', $cabangId))
                  ->orWhere(function ($q3) use ($kasirIds) {
                      $q3->whereNull('rental_id')
                         ->whereIn('kasir_id', $kasirIds);
                  });
            })
            ->orderByDesc('tanggal')
            ->get();

        $total  = (float) $transaksi->sum('total_bayar');
        $rental = (float) $transaksi->where('tipe_transaksi', 'rental')->sum('total_bayar');
        $cafe   = (float) $transaksi->where('tipe_transaksi', 'cafe_only')->sum('total_bayar');

        $rows = $transaksi->map(fn($t) => [
            'tanggal' => Carbon::parse($t->tanggal)->format('d/m H:i'),
            'tipe'    => $t->tipe_transaksi,
            'detail'  => $t->rental
                ? (($t->rental->psUnit->nomor_ps ?? '?') . ' — ' . $t->rental->nama_pelanggan)
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