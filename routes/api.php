<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
    AuthController,
    CustomerController,
    SupplierController,
    SatuanController,
    ItemController,
    PembelianController,
    TransaksiController,
    LaporanController
};

/*
|--------------------------------------------------------------------------
| AUTH (Public)
|--------------------------------------------------------------------------
*/

Route::prefix('auth')->group(function () {
    Route::post('login',    [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
});

/*
|--------------------------------------------------------------------------
| AUTHENTICATED API
|--------------------------------------------------------------------------
*/
Route::middleware('auth:api')
    ->as('api.')
    ->group(function () {

        /*
        |--------------------------
        | AUTH
        |--------------------------
        */
        Route::prefix('auth')->group(function () {
            Route::post('logout',  [AuthController::class, 'logout']);
            Route::post('refresh', [AuthController::class, 'refresh']);
            Route::get('me',       [AuthController::class, 'me']);
        });

        /*
        |--------------------------
        | MASTER DATA
        |--------------------------
        */
        Route::apiResources([
            'customers'  => CustomerController::class,
            'suppliers'  => SupplierController::class,
            'satuans'    => SatuanController::class,
            'items'      => ItemController::class,
        ]);

        /*
        |--------------------------
        | TRANSAKSI
        |--------------------------
        */
        Route::apiResources([
            'pembelians' => PembelianController::class,
            'transaksis' => TransaksiController::class,
        ]);

        /*
        |--------------------------
        | LAPORAN
        |--------------------------
        */
        Route::prefix('laporan')->as('laporan.')->group(function () {
            Route::get('stok',      [LaporanController::class, 'stok'])->name('stok');
            Route::get('penjualan', [LaporanController::class, 'penjualan'])->name('penjualan');
            Route::get('pembelian', [LaporanController::class, 'pembelian'])->name('pembelian');
            Route::get('laba-rugi', [LaporanController::class, 'labaRugi'])->name('laba-rugi');
            Route::get('ringkasan', [LaporanController::class, 'ringkasan'])->name('ringkasan');
        });

        /*
        |--------------------------
        | HISTORY
        |--------------------------
        */
        Route::get('histories', [\App\Http\Controllers\Api\HistoryController::class, 'index']);
    });
