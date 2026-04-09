<?php

use Illuminate\Support\Facades\Route;

Route::view('privacy-policy', 'privacy-policy')->name('privacy-policy');


Route::get('dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('users', \App\Http\Controllers\UserController::class);
    Route::get('customers', \App\Livewire\CustomerTable::class)->name('customers.index');
    Route::resource('customers', \App\Http\Controllers\CustomerController::class)->except('index');

    Route::get('suppliers', \App\Livewire\SupplierTable::class)->name('suppliers.index');
    Route::resource('suppliers', \App\Http\Controllers\SupplierController::class)->except('index');

    Route::get('satuans', \App\Livewire\SatuanTable::class)->name('satuans.index');
    Route::resource('satuans', \App\Http\Controllers\SatuanController::class)->except('index');

    Route::get('items/print-qrcode', [\App\Http\Controllers\ItemController::class, 'printQrCode'])->name('items.print-qrcode');
    Route::get('items', \App\Livewire\ItemTable::class)->name('items.index');
    Route::resource('items', \App\Http\Controllers\ItemController::class)->except('index');

    Route::get('pembelians', \App\Livewire\PembelianTable::class)->name('pembelians.index');
    Route::resource('pembelians', \App\Http\Controllers\PembelianController::class)->except('index');

    Route::get('transaksis', \App\Livewire\TransaksiTable::class)->name('transaksis.index');
    Route::resource('transaksis', \App\Http\Controllers\TransaksiController::class)->except('index');

    // Laporan Routes
    Route::get('/laporans', [\App\Http\Controllers\LaporanController::class, 'index'])->name('laporans.index');
    Route::get('/laporans/penjualan', [\App\Http\Controllers\LaporanController::class, 'penjualan'])->name('laporans.penjualan');
    Route::get('/laporans/pembelian', [\App\Http\Controllers\LaporanController::class, 'pembelian'])->name('laporans.pembelian');
    Route::get('/laporans/stok', [\App\Http\Controllers\LaporanController::class, 'stok'])->name('laporans.stok');
    Route::get('/laporans/laba-rugi', [\App\Http\Controllers\LaporanController::class, 'labaRugi'])->name('laporans.laba-rugi');

    // Saldo Management Routes
    Route::get('/saldos', [\App\Http\Controllers\SaldoController::class, 'index'])->name('saldos.index');
    Route::get('/saldos/history', [\App\Http\Controllers\SaldoController::class, 'history'])->name('saldos.history');
    Route::post('/saldos/topup/{user}', [\App\Http\Controllers\SaldoController::class, 'topup'])->name('saldos.topup');
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__ . '/auth.php';
