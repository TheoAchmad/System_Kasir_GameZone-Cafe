<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Rental;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Tambah pesanan F&B ke rental yang sedang berjalan.
     * Orders disimpan dengan transaksi_id = NULL dulu.
     * transaksi_id baru diisi saat sesi diselesaikan (di RentalController::selesaikan).
     */
    public function tambahKeRental(Request $request, Rental $rental)
    {
        $request->validate([
            'items'           => 'required|array|min:1',
            'items.*.menu_id' => 'required|exists:menu,id',
            'items.*.qty'     => 'required|integer|min:1',
        ]);

        if ($rental->status !== 'berjalan') {
            return response()->json(['error' => 'Sesi tidak aktif'], 422);
        }

        $created = [];

        foreach ($request->items as $item) {
            $menu = Menu::findOrFail($item['menu_id']);

            if ($menu->stok < $item['qty']) {
                return response()->json([
                    'error' => "Stok {$menu->nama_menu} tidak cukup ({$menu->stok} tersisa)"
                ], 422);
            }

            // ✅ transaksi_id = NULL — akan diisi saat checkout
            $order = Order::create([
                'transaksi_id' => null,
                'rental_id'    => $rental->id,
                'menu_id'      => $menu->id,
                'qty'          => $item['qty'],
                'harga'        => $menu->harga,
                'subtotal'     => $menu->harga * $item['qty'],
            ]);

            $menu->decrement('stok', $item['qty']);
            $created[] = $order->load('menu');
        }

        return response()->json([
            'success' => true,
            'orders'  => $created,
        ]);
    }

    /**
     * Transaksi Cafe Only — langsung lunas tanpa rental.
     */
    public function cafeOnly(Request $request)
    {
        $request->validate([
            'items'           => 'required|array|min:1',
            'items.*.menu_id' => 'required|exists:menu,id',
            'items.*.qty'     => 'required|integer|min:1',
            'metode_bayar'    => 'required|in:tunai,transfer,qris',
            'uang_bayar'      => 'required|numeric|min:0',
        ]);

        $subtotal  = 0;
        $orderData = [];

        foreach ($request->items as $item) {
            $menu = Menu::findOrFail($item['menu_id']);

            if ($menu->stok < $item['qty']) {
                return response()->json([
                    'error' => "Stok {$menu->nama_menu} tidak cukup"
                ], 422);
            }

            $sub        = $menu->harga * $item['qty'];
            $subtotal  += $sub;
            $orderData[] = ['menu' => $menu, 'qty' => $item['qty'], 'sub' => $sub];
        }

        if ((float)$request->uang_bayar < $subtotal) {
            return response()->json(['error' => 'Uang bayar kurang'], 422);
        }

        $transaksi = Transaksi::create([
            'rental_id'      => null,
            'kasir_id'       => auth()->id(),
            'tipe_transaksi' => 'cafe_only',
            'subtotal_sewa'  => 0,
            'subtotal_menu'  => $subtotal,
            'diskon'         => 0,
            'total_bayar'    => $subtotal,
            'uang_bayar'     => (float) $request->uang_bayar,
            'kembalian'      => (float) $request->uang_bayar - $subtotal,
            'metode_bayar'   => $request->metode_bayar,
            'tanggal'        => now(),
            'status_bayar'   => 'lunas',
        ]);

        foreach ($orderData as $od) {
            Order::create([
                'transaksi_id' => $transaksi->id,
                'rental_id'    => null,
                'menu_id'      => $od['menu']->id,
                'qty'          => $od['qty'],
                'harga'        => $od['menu']->harga,
                'subtotal'     => $od['sub'],
            ]);

            $od['menu']->decrement('stok', $od['qty']);
        }

        return response()->json([
            'success'   => true,
            'transaksi' => $transaksi->load('orders.menu'),
        ]);
    }
}