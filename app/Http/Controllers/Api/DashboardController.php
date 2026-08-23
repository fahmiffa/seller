<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\Komoditas;
use App\Models\Order;
use App\Models\Pembelian;
use App\Models\Satuan;
use App\Models\Supplier;
use App\Models\Transaksi;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // -------------------------------------------------------
        // Role 4: Distributor
        // -------------------------------------------------------
        if ($user->role == 4) {
            $allowedUnitIds = Unit::where('user_id', $user->id)
                ->orWhereHas('user', fn($q) => $q->where('parent_id', $user->id))
                ->pluck('id');

            $supplierCount  = Supplier::where('user_id', $user->id)->count();
            $komoditasCount = Komoditas::where('user_id', $user->id)->count();
            $unitCount      = $allowedUnitIds->count();
            $satuanCount    = Satuan::where('user_id', $user->id)->count();

            $orderTotal      = Order::whereIn('unit_id', $allowedUnitIds)->count();
            $orderPending    = Order::whereIn('unit_id', $allowedUnitIds)->where('status', 'pending')->count();
            $orderDiproses   = Order::whereIn('unit_id', $allowedUnitIds)->where('status', 'diproses')->count();
            $orderSelesai    = Order::whereIn('unit_id', $allowedUnitIds)->where('status', 'selesai')->count();
            $orderDibatalkan = Order::whereIn('unit_id', $allowedUnitIds)->where('status', 'dibatalkan')->count();

            $invoiceQuery       = Invoice::whereHas('order', fn($q) => $q->whereIn('unit_id', $allowedUnitIds));
            $invoiceTotal       = (clone $invoiceQuery)->count();
            $invoicePaid        = (clone $invoiceQuery)->where('payment_status', 'paid')->count();
            $invoiceUnpaid      = (clone $invoiceQuery)->where('payment_status', 'unpaid')->count();
            $invoiceTotalAmount = (clone $invoiceQuery)->sum('total');

            return response()->json([
                'success' => true,
                'message' => 'Dashboard ringkasan data distributor',
                'data' => [
                    'role' => 4,
                    'supplier_count' => $supplierCount,
                    'komoditas_count' => $komoditasCount,
                    'unit_count' => $unitCount,
                    'satuan_count' => $satuanCount,
                    'orders' => [
                        'total' => $orderTotal,
                        'pending' => $orderPending,
                        'diproses' => $orderDiproses,
                        'selesai' => $orderSelesai,
                        'dibatalkan' => $orderDibatalkan,
                    ],
                    'invoices' => [
                        'total' => $invoiceTotal,
                        'paid' => $invoicePaid,
                        'unpaid' => $invoiceUnpaid,
                        'total_amount' => (float) $invoiceTotalAmount,
                    ]
                ]
            ], 200);
        }

        // -------------------------------------------------------
        // Role 5: Unit
        // -------------------------------------------------------
        if ($user->role == 5) {
            $unitIds = Unit::where('user_id', $user->id)->pluck('id');

            $orderTotal      = Order::whereIn('unit_id', $unitIds)->count();
            $orderPending    = Order::whereIn('unit_id', $unitIds)->where('status', 'pending')->count();
            $orderDiproses   = Order::whereIn('unit_id', $unitIds)->where('status', 'diproses')->count();
            $orderSelesai    = Order::whereIn('unit_id', $unitIds)->where('status', 'selesai')->count();
            $orderDibatalkan = Order::whereIn('unit_id', $unitIds)->where('status', 'dibatalkan')->count();

            return response()->json([
                'success' => true,
                'message' => 'Dashboard ringkasan data unit',
                'data' => [
                    'role' => 5,
                    'orders' => [
                        'total' => $orderTotal,
                        'pending' => $orderPending,
                        'diproses' => $orderDiproses,
                        'selesai' => $orderSelesai,
                        'dibatalkan' => $orderDibatalkan,
                    ]
                ]
            ], 200);
        }

        // -------------------------------------------------------
        // Role 0, 1, 2, 3: Dashboard Standar
        // -------------------------------------------------------
        $totalPenjualanBulanIni = Transaksi::where('user_id', $user->getOwnerId())
            ->whereMonth('tanggal_transaksi', Carbon::now()->month)
            ->whereYear('tanggal_transaksi', Carbon::now()->year)
            ->sum('total_harga');

        $totalPembelianBulanIni = Pembelian::where('user_id', $user->getOwnerId())
            ->whereMonth('tanggal_pembelian', Carbon::now()->month)
            ->whereYear('tanggal_pembelian', Carbon::now()->year)
            ->sum('total_pembelian');

        $totalTransaksiBulanIni = Transaksi::where('user_id', $user->getOwnerId())
            ->whereMonth('tanggal_transaksi', Carbon::now()->month)
            ->whereYear('tanggal_transaksi', Carbon::now()->year)
            ->count();

        $stokMenipis = Item::where('user_id', $user->getOwnerId())
            ->where('tipe_item', 'barang')
            ->where('stok', '<=', 10)
            ->count();

        return response()->json([
            'success' => true,
            'message' => 'Dashboard ringkasan data toko',
            'data' => [
                'role' => $user->role,
                'total_penjualan_bulan_ini' => (float) $totalPenjualanBulanIni,
                'total_pembelian_bulan_ini' => (float) $totalPembelianBulanIni,
                'total_transaksi_bulan_ini' => $totalTransaksiBulanIni,
                'stok_menipis' => $stokMenipis,
                'saldo' => (float) ($user->saldo ?? 0),
            ]
        ], 200);
    }
}
