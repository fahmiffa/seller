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
    LaporanController,
    AppConfigController
};

/*
|--------------------------------------------------------------------------
| AUTH (Public)
|--------------------------------------------------------------------------
*/

Route::prefix('auth')->group(function () {
    Route::post('login',    [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('refresh',  [AuthController::class, 'refresh']);
});

Route::get('app-config', [AppConfigController::class, 'index']);

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
            Route::get('me',       [AuthController::class, 'me']);
            Route::post('update-profile', [AuthController::class, 'updateProfile']);
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
        Route::get('items/{id}/qrcode', [ItemController::class, 'qrcode'])->name('items.qrcode');

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
