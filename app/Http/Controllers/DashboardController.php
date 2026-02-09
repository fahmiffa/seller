<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Pembelian;
use App\Models\Transaksi;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Summary cards
        $totalPenjualanBulanIni = Transaksi::where('user_id', auth()->user()->getOwnerId())->whereMonth('tanggal_transaksi', Carbon::now()->month)
            ->whereYear('tanggal_transaksi', Carbon::now()->year)
            ->sum('total_harga');

        $totalPembelianBulanIni = Pembelian::where('user_id', auth()->user()->getOwnerId())->whereMonth('tanggal_pembelian', Carbon::now()->month)
            ->whereYear('tanggal_pembelian', Carbon::now()->year)
            ->sum('total_pembelian');

        $totalTransaksiBulanIni = Transaksi::where('user_id', auth()->user()->getOwnerId())->whereMonth('tanggal_transaksi', Carbon::now()->month)
            ->whereYear('tanggal_transaksi', Carbon::now()->year)
            ->count();

        $stokMenipis = Item::where('user_id', auth()->user()->getOwnerId())->where('tipe_item', 'barang')
            ->where('stok', '<=', 10)
            ->count();

        return view('dashboard', compact(
            'totalPenjualanBulanIni',
            'totalPembelianBulanIni',
            'totalTransaksiBulanIni',
            'stokMenipis'
        ));
    }
}
