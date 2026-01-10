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
        // Get data for last 7 days
        $dates         = [];
        $penjualanData = [];
        $pembelianData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date    = Carbon::now()->subDays($i)->format('Y-m-d');
            $dates[] = Carbon::now()->subDays($i)->format('d M');

            // Penjualan per hari
            $penjualanData[] = Transaksi::where('user_id', auth()->user()->id)->whereDate('tanggal_transaksi', $date)->sum('total_harga');

            // Pembelian per hari
            $pembelianData[] = Pembelian::where('user_id', auth()->user()->id)->whereDate('tanggal_pembelian', $date)->sum('total_pembelian');
        }

        // Stok barang (top 10 items)
        $stokItems = Item::where('tipe_item', 'barang')
            ->where('user_id', auth()->user()->id)
            ->orderBy('stok', 'desc')
            ->limit(10)
            ->get();

        $itemNames = $stokItems->pluck('nama_item')->toArray();
        $itemStoks = $stokItems->pluck('stok')->toArray();

        // Summary cards
        $totalPenjualanBulanIni = Transaksi::where('user_id', auth()->user()->id)->whereMonth('tanggal_transaksi', Carbon::now()->month)
            ->whereYear('tanggal_transaksi', Carbon::now()->year)
            ->sum('total_harga');

        $totalPembelianBulanIni = Pembelian::where('user_id', auth()->user()->id)->whereMonth('tanggal_pembelian', Carbon::now()->month)
            ->whereYear('tanggal_pembelian', Carbon::now()->year)
            ->sum('total_pembelian');

        $totalTransaksiBulanIni = Transaksi::where('user_id', auth()->user()->id)->whereMonth('tanggal_transaksi', Carbon::now()->month)
            ->whereYear('tanggal_transaksi', Carbon::now()->year)
            ->count();

        $stokMenipis = Item::where('user_id', auth()->user()->id)->where('tipe_item', 'barang')
            ->where('stok', '<=', 10)
            ->count();

        return view('dashboard', compact(
            'dates',
            'penjualanData',
            'pembelianData',
            'itemNames',
            'itemStoks',
            'totalPenjualanBulanIni',
            'totalPembelianBulanIni',
            'totalTransaksiBulanIni',
            'stokMenipis'
        ));
    }
}
