<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ps_units', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_ps');
            $table->enum('tipe_ps', ['PS4', 'PS5']);
            $table->enum('status', ['kosong', 'dipakai', 'booking', 'maintenance'])
                  ->default('kosong');
            $table->decimal('harga_per_jam', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ps_units');
    }
};