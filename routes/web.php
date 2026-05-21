<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Kasir\DashboardController as KasirDash;
use App\Http\Controllers\Kasir\RentalController;
use App\Http\Controllers\Kasir\OrderController;
use App\Http\Controllers\Kasir\StrukController;
use App\Http\Controllers\Admin\DashboardController as AdminDash;
use App\Http\Controllers\Admin\PsController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\KasirController;

Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->role === 'admin'
            ? redirect()->route('admin.dashboard')
            : redirect()->route('kasir.dashboard');
    }
    return redirect()->route('login');
});

require __DIR__ . '/auth.php';

// ─── KASIR ─────────────────────────────────────────────────────
Route::middleware(['auth', 'role:kasir'])
    ->prefix('kasir')->name('kasir.')
    ->group(function () {

    Route::get('/dashboard',    [KasirDash::class, 'index'])->name('dashboard');
    Route::get('/api/ps-units', [KasirDash::class, 'apiPsUnits'])->name('api.psUnits');
    Route::get('/riwayat',      [KasirDash::class, 'riwayat'])->name('riwayat');

    // Struk print
    Route::get('/struk/{transaksi}', [StrukController::class, 'show'])->name('struk.show');

    // Rental
    Route::post('/rental/mulai',                 [RentalController::class, 'mulai'])->name('rental.mulai');
    Route::post('/rental/{rental}/tambah-waktu', [RentalController::class, 'tambahWaktu'])->name('rental.tambahWaktu');
    Route::get('/rental/{rental}/kalkulasi',     [RentalController::class, 'kalkulasi'])->name('rental.kalkulasi');
    Route::post('/rental/{rental}/selesaikan',   [RentalController::class, 'selesaikan'])->name('rental.selesaikan');

    // Order
    Route::post('/order/rental/{rental}', [OrderController::class, 'tambahKeRental'])->name('order.rental');
    Route::post('/order/cafe-only',       [OrderController::class, 'cafeOnly'])->name('order.cafeOnly');
});

// ─── ADMIN ─────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')->name('admin.')
    ->group(function () {

    Route::get('/dashboard',    [AdminDash::class, 'index'])->name('dashboard');
    Route::get('/api/overview', [AdminDash::class, 'overview'])->name('api.overview');
    Route::get('/api/laporan',  [AdminDash::class, 'laporan'])->name('api.laporan');
    Route::get('/api/ps',       [PsController::class, 'index'])->name('api.ps');
    Route::get('/api/menu',     [MenuController::class, 'index'])->name('api.menu');
    Route::get('/api/kasir',    [KasirController::class, 'index'])->name('api.kasir');

    Route::post('/ps',           [PsController::class, 'store'])->name('ps.store');
    Route::patch('/ps/{ps}',     [PsController::class, 'update'])->name('ps.update');
    Route::delete('/ps/{ps}',    [PsController::class, 'destroy'])->name('ps.destroy');

    Route::post('/menu',         [MenuController::class, 'store'])->name('menu.store');
    Route::patch('/menu/{menu}', [MenuController::class, 'update'])->name('menu.update');
    Route::delete('/menu/{menu}',[MenuController::class, 'destroy'])->name('menu.destroy');

    Route::post('/kasir',         [KasirController::class, 'store'])->name('kasir.store');
    Route::delete('/kasir/{user}',[KasirController::class, 'destroy'])->name('kasir.destroy');
});