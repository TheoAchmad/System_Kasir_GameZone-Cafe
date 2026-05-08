<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rentals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ps_id')->constrained('ps_units');
            $table->foreignId('kasir_id')->constrained('users');
            $table->string('nama_pelanggan');
            $table->enum('mode_billing', ['up', 'down']);
            $table->dateTime('jam_mulai');
            $table->dateTime('jam_selesai')->nullable();
            $table->integer('durasi_awal')->nullable();
            $table->integer('durasi_tambahan')->default(0);
            $table->decimal('harga_sewa', 10, 2)->default(0);
            $table->enum('status', ['berjalan', 'selesai', 'batal'])->default('berjalan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rentals');
    }
};