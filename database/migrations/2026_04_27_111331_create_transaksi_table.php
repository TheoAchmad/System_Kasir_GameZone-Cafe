<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rental_id')->nullable()->constrained('rentals');
            $table->foreignId('kasir_id')->constrained('users');
            $table->enum('tipe_transaksi', ['rental', 'cafe_only']);
            $table->decimal('subtotal_sewa', 10, 2)->default(0);
            $table->decimal('subtotal_menu', 10, 2)->default(0);
            $table->decimal('diskon', 10, 2)->default(0);
            $table->decimal('total_bayar', 10, 2);
            $table->decimal('uang_bayar', 10, 2)->default(0);
            $table->decimal('kembalian', 10, 2)->default(0);
            $table->enum('metode_bayar', ['tunai', 'transfer', 'qris']);
            $table->dateTime('tanggal');
            $table->enum('status_bayar', ['lunas', 'belum'])->default('belum');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};