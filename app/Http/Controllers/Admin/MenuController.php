<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $menu = Menu::where('cabang_id', $request->cabang)
            ->orderBy('kategori')
            ->orderBy('nama_menu')
            ->get();

        return response()->json($menu);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_menu' => 'required|string|max:100',
            'kategori'  => 'required|in:makanan,minuman,snack',
            'harga'     => 'required|numeric|min:0',
            'stok'      => 'required|integer|min:0',
            'cabang_id' => 'required|exists:cabang,id',
        ]);

        $menu = Menu::create([
            'nama_menu' => $request->nama_menu,
            'kategori'  => $request->kategori,
            'harga'     => $request->harga,
            'stok'      => $request->stok,
            'is_aktif'  => $request->boolean('is_aktif', true),
            'cabang_id' => $request->cabang_id,
        ]);

        return response()->json(['success' => true, 'menu' => $menu]);
    }

    // ✅ PATCH /admin/menu/{id}
    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'nama_menu' => 'required|string|max:100',
            'kategori'  => 'required|in:makanan,minuman,snack',
            'harga'     => 'required|numeric|min:0',
            'stok'      => 'required|integer|min:0',
        ]);

        $menu->update([
            'nama_menu' => $request->nama_menu,
            'kategori'  => $request->kategori,
            'harga'     => $request->harga,
            'stok'      => $request->stok,
            'is_aktif'  => $request->boolean('is_aktif', true),
        ]);

        return response()->json(['success' => true, 'menu' => $menu]);
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();
        return response()->json(['success' => true]);
    }
}