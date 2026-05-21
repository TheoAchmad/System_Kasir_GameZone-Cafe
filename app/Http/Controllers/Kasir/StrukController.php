<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class StrukController extends Controller
{
    /**
     * GET /kasir/struk/{transaksi}
     * Tampilkan halaman struk yang bisa di-print.
     */
    public function show(Transaksi $transaksi)
    {
        // Pastikan kasir hanya bisa lihat struk dari cabangnya sendiri
        $kasirCabangId = auth()->user()->cabang_id;

        $allowed = false;

        if ($transaksi->tipe_transaksi === 'rental') {
            // Struk rental: cek cabang PS-nya
            $allowed = $transaksi->rental?->psUnit?->cabang_id === $kasirCabangId;
        } else {
            // Struk cafe only: cek kasir_id
            $allowed = $transaksi->kasir_id === auth()->id();
        }

        if (!$allowed) {
            abort(403, 'Tidak berhak mengakses struk ini.');
        }

        $transaksi->load([
            'rental.psUnit',
            'orders.menu',
            'kasir.cabang',
        ]);

        return view('struk.cetak', compact('transaksi'));
    }
}