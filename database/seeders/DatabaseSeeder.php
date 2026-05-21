<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\PsUnit;
use App\Models\Menu;
use App\Models\Cabang;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Nonaktifkan foreign key check sementara ──
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Hapus semua data lama (urutan penting: child dulu baru parent)
        DB::table('orders')->truncate();
        DB::table('transaksi')->truncate();
        DB::table('rentals')->truncate();
        DB::table('menu')->truncate();
        DB::table('ps_units')->truncate();
        DB::table('users')->truncate();
        DB::table('cabang')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // ── 1. Cabang ──────────────────────────────────
        $cabang1 = Cabang::create([
            'nama_cabang' => 'Cabang 1',
            'alamat'      => 'Jl. Utama No. 1',
            'is_aktif'    => true,
        ]);

        $cabang2 = Cabang::create([
            'nama_cabang' => 'Cabang 2',
            'alamat'      => 'Jl. Kedua No. 2',
            'is_aktif'    => true,
        ]);

        // ── 2. Users ───────────────────────────────────
        User::create([
            'name'       => 'Admin',
            'email'      => 'admin@gamezone.com',
            'password'   => Hash::make('password'),
            'role'       => 'admin',
            'cabang_id'  => null,
        ]);

        User::create([
            'name'       => 'Kasir Cabang 1',
            'email'      => 'kasir1@gamezone.com',
            'password'   => Hash::make('password'),
            'role'       => 'kasir',
            'cabang_id'  => $cabang1->id,
        ]);

        User::create([
            'name'       => 'Kasir Cabang 2',
            'email'      => 'kasir2@gamezone.com',
            'password'   => Hash::make('password'),
            'role'       => 'kasir',
            'cabang_id'  => $cabang2->id,
        ]);

        // ── 3. PS Units Cabang 1 ───────────────────────
        // PS4 = Rp 7.000/jam | PS5 = Rp 10.000/jam
        $psCabang1 = [
            ['nomor_ps' => 'PS 01', 'tipe_ps' => 'PS5', 'harga_per_jam' => 10000],
            ['nomor_ps' => 'PS 02', 'tipe_ps' => 'PS5', 'harga_per_jam' => 10000],
            ['nomor_ps' => 'PS 03', 'tipe_ps' => 'PS4', 'harga_per_jam' => 7000],
            ['nomor_ps' => 'PS 04', 'tipe_ps' => 'PS4', 'harga_per_jam' => 7000],
            ['nomor_ps' => 'PS 05', 'tipe_ps' => 'PS4', 'harga_per_jam' => 7000],
        ];

        foreach ($psCabang1 as $ps) {
            PsUnit::create(array_merge($ps, [
                'status'    => 'kosong',
                'cabang_id' => $cabang1->id,
            ]));
        }

        // ── 4. PS Units Cabang 2 ───────────────────────
        $psCabang2 = [
            ['nomor_ps' => 'PS 01', 'tipe_ps' => 'PS5', 'harga_per_jam' => 10000],
            ['nomor_ps' => 'PS 02', 'tipe_ps' => 'PS5', 'harga_per_jam' => 10000],
            ['nomor_ps' => 'PS 03', 'tipe_ps' => 'PS4', 'harga_per_jam' => 7000],
            ['nomor_ps' => 'PS 04', 'tipe_ps' => 'PS4', 'harga_per_jam' => 7000],
        ];

        foreach ($psCabang2 as $ps) {
            PsUnit::create(array_merge($ps, [
                'status'    => 'kosong',
                'cabang_id' => $cabang2->id,
            ]));
        }

        // ── 5. Menu Cabang 1 ───────────────────────────
        $menuCabang1 = [
            ['nama_menu' => 'Nasi Goreng',    'kategori' => 'makanan', 'harga' => 20000, 'stok' => 50],
            ['nama_menu' => 'Mie Goreng',     'kategori' => 'makanan', 'harga' => 18000, 'stok' => 50],
            ['nama_menu' => 'Indomie Rebus',  'kategori' => 'makanan', 'harga' => 15000, 'stok' => 50],
            ['nama_menu' => 'Es Teh Manis',   'kategori' => 'minuman', 'harga' => 5000,  'stok' => 100],
            ['nama_menu' => 'Kopi Hitam',     'kategori' => 'minuman', 'harga' => 8000,  'stok' => 100],
            ['nama_menu' => 'Es Jeruk',       'kategori' => 'minuman', 'harga' => 7000,  'stok' => 100],
            ['nama_menu' => 'Pocky Coklat',   'kategori' => 'snack',   'harga' => 10000, 'stok' => 30],
            ['nama_menu' => 'Chitato',        'kategori' => 'snack',   'harga' => 8000,  'stok' => 30],
        ];

        foreach ($menuCabang1 as $m) {
            Menu::create(array_merge($m, [
                'is_aktif'  => true,
                'cabang_id' => $cabang1->id,
            ]));
        }

        // ── 6. Menu Cabang 2 ───────────────────────────
        $menuCabang2 = [
            ['nama_menu' => 'Nasi Goreng',  'kategori' => 'makanan', 'harga' => 20000, 'stok' => 30],
            ['nama_menu' => 'Mie Goreng',   'kategori' => 'makanan', 'harga' => 18000, 'stok' => 30],
            ['nama_menu' => 'Es Teh Manis', 'kategori' => 'minuman', 'harga' => 5000,  'stok' => 80],
            ['nama_menu' => 'Kopi Hitam',   'kategori' => 'minuman', 'harga' => 8000,  'stok' => 80],
            ['nama_menu' => 'Chitato',      'kategori' => 'snack',   'harga' => 8000,  'stok' => 20],
        ];

        foreach ($menuCabang2 as $m) {
            Menu::create(array_merge($m, [
                'is_aktif'  => true,
                'cabang_id' => $cabang2->id,
            ]));
        }

        $this->command->info('✅ Database berhasil direset!');
        $this->command->info('');
        $this->command->info('Akun login:');
        $this->command->info('  Admin  : admin@gamezone.com   / password');
        $this->command->info('  Kasir 1: kasir1@gamezone.com  / password  (Cabang 1)');
        $this->command->info('  Kasir 2: kasir2@gamezone.com  / password  (Cabang 2)');
    }
}