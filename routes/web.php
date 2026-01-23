<?php

use Illuminate\Support\Facades\Route;

Route::view('privacy-policy', 'privacy-policy')->name('privacy-policy');


Route::get('dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('users', \App\Http\Controllers\UserController::class);
    Route::resource('customers', \App\Http\Controllers\CustomerController::class);
    Route::resource('suppliers', \App\Http\Controllers\SupplierController::class);
    Route::resource('satuans', \App\Http\Controllers\SatuanController::class);
    Route::resource('items', \App\Http\Controllers\ItemController::class);
    Route::resource('pembelians', \App\Http\Controllers\PembelianController::class);
    Route::resource('transaksis', \App\Http\Controllers\TransaksiController::class);

    // Laporan Routes
    Route::get('/laporans', [\App\Http\Controllers\LaporanController::class, 'index'])->name('laporans.index');
    Route::get('/laporans/penjualan', [\App\Http\Controllers\LaporanController::class, 'penjualan'])->name('laporans.penjualan');
    Route::get('/laporans/pembelian', [\App\Http\Controllers\LaporanController::class, 'pembelian'])->name('laporans.pembelian');
    Route::get('/laporans/stok', [\App\Http\Controllers\LaporanController::class, 'stok'])->name('laporans.stok');
    Route::get('/laporans/laba-rugi', [\App\Http\Controllers\LaporanController::class, 'labaRugi'])->name('laporans.laba-rugi');
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__ . '/auth.php';
