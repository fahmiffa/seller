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
    AppConfigController,
    KomoditasController,
    UnitController,
    OrderController,
    InvoiceController,
    DashboardController
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
    Route::post('forget',   [AuthController::class, 'forget']);
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
            Route::post('pay-service-fee', [AuthController::class, 'payServiceFee']);
        });

        /*
        |--------------------------
        | DASHBOARD SUMMARY
        |--------------------------
        */
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

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
            'komoditas'  => KomoditasController::class,
            'units'      => UnitController::class,
        ]);
        Route::get('items/{id}/qrcode', [ItemController::class, 'qrcode'])->name('items.qrcode');

        /*
        |--------------------------
        | ORDER & INVOICE (Role 4 & 5)
        |--------------------------
        */
        Route::apiResource('orders', OrderController::class);
        
        Route::apiResource('invoices', InvoiceController::class)->only(['index', 'show']);
        Route::patch('invoices/{id}/mark-paid', [InvoiceController::class, 'markPaid'])->name('invoices.mark-paid');
        Route::get('invoices/{id}/stream', [InvoiceController::class, 'stream'])->name('invoices.stream');

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

