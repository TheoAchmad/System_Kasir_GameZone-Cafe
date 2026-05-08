<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\PsUnit;
use App\Models\Menu;
use App\Models\Transaksi;

class DashboardController extends Controller
{
    public function index()
    {
        $user     = auth()->user();
        $cabangId = $user->cabang_id;

        $psUnits = $this->getPsData($cabangId);

        // Menu hanya dari cabang kasir ini
        $menus = Menu::where('is_aktif', true)
            ->where('cabang_id', $cabangId)
            ->get()
            ->groupBy('kategori');

        $menusArr = [];
        foreach ($menus as $kat => $items) {
            $menusArr[$kat] = $items->values()->toArray();
        }

        return view('kasir.dashboard', [
            'psUnits' => $psUnits,
            'menus'   => $menusArr,
        ]);
    }

    public function apiPsUnits()
    {
        $cabangId = auth()->user()->cabang_id;
        return response()->json($this->getPsData($cabangId));
    }

    private function getPsData(?int $cabangId): array
    {
        $query = PsUnit::with(['activeRental.orders.menu']);

        // Filter berdasarkan cabang kasir
        if ($cabangId) {
            $query->where('cabang_id', $cabangId);
        }

        return $query->orderBy('nomor_ps')->get()
            ->map(function ($ps) {
                $arr = $ps->toArray();
                $arr['rental'] = null;

                if ($ps->activeRental) {
                    $r = $ps->activeRental;
                    $arr['rental'] = [
                        'id'              => $r->id,
                        'nama_pelanggan'  => $r->nama_pelanggan,
                        'mode_billing'    => $r->mode_billing,
                        'jam_mulai_ts'    => $r->jam_mulai->timestamp * 1000,
                        'jam_selesai_ts'  => $r->jam_selesai?->timestamp * 1000,
                        'jam_mulai_fmt'   => $r->jam_mulai->format('H:i'),
                        'jam_selesai_fmt' => $r->jam_selesai?->format('H:i'),
                        'subtotal_menu'   => (float) $r->orders->sum('subtotal'),
                        'subtotal_sewa'   => 0.0,
                        'subtotal_sementara' => 0.0,
                        'is_expired'      => false,
                        'harga_terkunci'  => false,
                        'timer_display'   => '--:--:--',
                        'orders'          => $r->orders->map(fn($o) => [
                            'id'       => $o->id,
                            'qty'      => $o->qty,
                            'harga'    => (float) $o->harga,
                            'subtotal' => (float) $o->subtotal,
                            'menu'     => ['nama_menu' => $o->menu?->nama_menu ?? ''],
                        ])->values()->toArray(),
                    ];
                }

                return $arr;
            })
            ->values()
            ->toArray();
    }

    public function riwayat()
    {
        $cabangId  = auth()->user()->cabang_id;

        $transaksi = Transaksi::with(['rental.psUnit'])
            ->whereDate('tanggal', today())
            ->where(function ($q) use ($cabangId) {
                $q->whereHas('rental.psUnit', fn($q2) => $q2->where('cabang_id', $cabangId))
                  ->orWhere(function ($q3) use ($cabangId) {
                      $q3->whereNull('rental_id')
                         ->where('kasir_id', auth()->id());
                  });
            })
            ->orderByDesc('tanggal')
            ->get();

        return view('kasir.riwayat', compact('transaksi'));
    }
}