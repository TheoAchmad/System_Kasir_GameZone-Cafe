<?php
// Simpan sebagai: database/migrations/2026_04_28_000001_create_cabang_and_add_columns.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Buat tabel cabang
        Schema::create('cabang', function (Blueprint $table) {
            $table->id();
            $table->string('nama_cabang');
            $table->string('alamat')->nullable();
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });

        // 2. Tambah cabang_id ke users
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('cabang_id')
                ->nullable()
                ->after('role')
                ->constrained('cabang')
                ->nullOnDelete();
        });

        // 3. Tambah cabang_id ke ps_units
        Schema::table('ps_units', function (Blueprint $table) {
            $table->foreignId('cabang_id')
                ->nullable()
                ->after('harga_per_jam')
                ->constrained('cabang')
                ->nullOnDelete();
        });

        // 4. Tambah cabang_id ke menu
        Schema::table('menu', function (Blueprint $table) {
            $table->foreignId('cabang_id')
                ->nullable()
                ->after('is_aktif')
                ->constrained('cabang')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('menu', function (Blueprint $table) {
            $table->dropForeign(['cabang_id']);
            $table->dropColumn('cabang_id');
        });
        Schema::table('ps_units', function (Blueprint $table) {
            $table->dropForeign(['cabang_id']);
            $table->dropColumn('cabang_id');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['cabang_id']);
            $table->dropColumn('cabang_id');
        });
        Schema::dropIfExists('cabang');
    }
};