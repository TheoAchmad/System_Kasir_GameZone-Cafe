<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\PsUnit;
use App\Models\Menu;
use App\Models\Cabang;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Cabang ──────────────────────────
        $cabang1 = Cabang::firstOrCreate(['nama_cabang' => 'Cabang 1'], ['alamat' => 'Jl. Utama No. 1']);
        $cabang2 = Cabang::firstOrCreate(['nama_cabang' => 'Cabang 2'], ['alamat' => 'Jl. Kedua No. 2']);

        // ── Users ───────────────────────────
        User::firstOrCreate(['email' => 'admin@gamezone.com'], [
            'name'       => 'Admin',
            'password'   => Hash::make('password'),
            'role'       => 'admin',
            'cabang_id'  => null, // admin tidak terikat cabang
        ]);

        User::firstOrCreate(['email' => 'kasir1@gamezone.com'], [
            'name'       => 'Kasir Cabang 1',
            'password'   => Hash::make('password'),
            'role'       => 'kasir',
            'cabang_id'  => $cabang1->id,
        ]);

        User::firstOrCreate(['email' => 'kasir2@gamezone.com'], [
            'name'       => 'Kasir Cabang 2',
            'password'   => Hash::make('password'),
            'role'       => 'kasir',
            'cabang_id'  => $cabang2->id,
        ]);

        // ── PS Units Cabang 1 ────────────────
        // PS4 = Rp 7.000/jam, PS5 = Rp 10.000/jam
        if (PsUnit::where('cabang_id', $cabang1->id)->count() === 0) {
            $psCabang1 = [
                ['nomor_ps' => 'PS 01', 'tipe_ps' => 'PS5', 'harga_per_jam' => 10000],
                ['nomor_ps' => 'PS 02', 'tipe_ps' => 'PS5', 'harga_per_jam' => 10000],
                ['nomor_ps' => 'PS 03', 'tipe_ps' => 'PS4', 'harga_per_jam' => 7000],
                ['nomor_ps' => 'PS 04', 'tipe_ps' => 'PS4', 'harga_per_jam' => 7000],
                ['nomor_ps' => 'PS 05', 'tipe_ps' => 'PS4', 'harga_per_jam' => 7000],
            ];
            foreach ($psCabang1 as $ps) {
                PsUnit::create(array_merge($ps, ['status' => 'kosong', 'cabang_id' => $cabang1->id]));
            }
        }

        // ── PS Units Cabang 2 ────────────────
        if (PsUnit::where('cabang_id', $cabang2->id)->count() === 0) {
            $psCabang2 = [
                ['nomor_ps' => 'PS 01', 'tipe_ps' => 'PS5', 'harga_per_jam' => 10000],
                ['nomor_ps' => 'PS 02', 'tipe_ps' => 'PS5', 'harga_per_jam' => 10000],
                ['nomor_ps' => 'PS 03', 'tipe_ps' => 'PS4', 'harga_per_jam' => 7000],
                ['nomor_ps' => 'PS 04', 'tipe_ps' => 'PS4', 'harga_per_jam' => 7000],
            ];
            foreach ($psCabang2 as $ps) {
                PsUnit::create(array_merge($ps, ['status' => 'kosong', 'cabang_id' => $cabang2->id]));
            }
        }

        // ── Menu Cabang 1 ────────────────────
        if (Menu::where('cabang_id', $cabang1->id)->count() === 0) {
            $menu1 = [
                ['nama_menu' => 'Nasi Goreng',   'kategori' => 'makanan', 'harga' => 20000, 'stok' => 50],
                ['nama_menu' => 'Mie Goreng',    'kategori' => 'makanan', 'harga' => 18000, 'stok' => 50],
                ['nama_menu' => 'Indomie Rebus', 'kategori' => 'makanan', 'harga' => 15000, 'stok' => 50],
                ['nama_menu' => 'Es Teh Manis',  'kategori' => 'minuman', 'harga' => 5000,  'stok' => 100],
                ['nama_menu' => 'Kopi Hitam',    'kategori' => 'minuman', 'harga' => 8000,  'stok' => 100],
                ['nama_menu' => 'Es Jeruk',      'kategori' => 'minuman', 'harga' => 7000,  'stok' => 100],
                ['nama_menu' => 'Pocky Coklat',  'kategori' => 'snack',   'harga' => 10000, 'stok' => 30],
            ];
            foreach ($menu1 as $m) {
                Menu::create(array_merge($m, ['cabang_id' => $cabang1->id]));
            }
        }

        // ── Menu Cabang 2 ────────────────────
        if (Menu::where('cabang_id', $cabang2->id)->count() === 0) {
            $menu2 = [
                ['nama_menu' => 'Nasi Goreng',   'kategori' => 'makanan', 'harga' => 20000, 'stok' => 30],
                ['nama_menu' => 'Mie Goreng',    'kategori' => 'makanan', 'harga' => 18000, 'stok' => 30],
                ['nama_menu' => 'Es Teh Manis',  'kategori' => 'minuman', 'harga' => 5000,  'stok' => 80],
                ['nama_menu' => 'Kopi Hitam',    'kategori' => 'minuman', 'harga' => 8000,  'stok' => 80],
                ['nama_menu' => 'Chitato',       'kategori' => 'snack',   'harga' => 8000,  'stok' => 20],
            ];
            foreach ($menu2 as $m) {
                Menu::create(array_merge($m, ['cabang_id' => $cabang2->id]));
            }
        }
    }
}