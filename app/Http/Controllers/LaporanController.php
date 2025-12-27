<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Pembelian;
use App\Models\Item;
use App\Models\DetailTransaksi;
use App\Models\DetailPembelian;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index()
    {
        return view('laporans.index');
    }

    public function penjualan(Request $request)
    {
        $request->validate([
            'tanggal_dari' => 'required|date',
            'tanggal_sampai' => 'required|date|after_or_equal:tanggal_dari',
        ]);

        $transaksis = Transaksi::with(['customer', 'user', 'details.item'])
            ->whereBetween('tanggal_transaksi', [$request->tanggal_dari, $request->tanggal_sampai])
            ->orderBy('tanggal_transaksi', 'desc')
            ->get();

        $total = $transaksis->sum('total_harga');

        return view('laporans.penjualan', compact('transaksis', 'total', 'request'));
    }

    public function pembelian(Request $request)
    {
        $request->validate([
            'tanggal_dari' => 'required|date',
            'tanggal_sampai' => 'required|date|after_or_equal:tanggal_dari',
        ]);

        $pembelians = Pembelian::with(['supplier', 'user', 'details.item'])
            ->whereBetween('tanggal_pembelian', [$request->tanggal_dari, $request->tanggal_sampai])
            ->orderBy('tanggal_pembelian', 'desc')
            ->get();

        $total = $pembelians->sum('total_pembelian');

        return view('laporans.pembelian', compact('pembelians', 'total', 'request'));
    }

    public function stok(Request $request)
    {
        $items = Item::with(['kategori', 'satuan'])
            ->where('tipe_item', 'barang')
            ->orderBy('nama_item')
            ->get();

        return view('laporans.stok', compact('items'));
    }

    public function labaRugi(Request $request)
    {
        $request->validate([
            'tanggal_dari' => 'required|date',
            'tanggal_sampai' => 'required|date|after_or_equal:tanggal_dari',
        ]);

        // Total Penjualan
        $penjualan = Transaksi::whereBetween('tanggal_transaksi', [$request->tanggal_dari, $request->tanggal_sampai])
            ->sum('total_harga');

        // Total Pembelian
        $pembelian = Pembelian::whereBetween('tanggal_pembelian', [$request->tanggal_dari, $request->tanggal_sampai])
            ->sum('total_pembelian');

        // HPP (Harga Pokok Penjualan) - dari detail transaksi
        $hpp = DetailTransaksi::whereHas('transaksi', function($query) use ($request) {
                $query->whereBetween('tanggal_transaksi', [$request->tanggal_dari, $request->tanggal_sampai]);
            })
            ->join('items', 'detail_transaksis.item_id', '=', 'items.item_id')
            ->selectRaw('SUM(detail_transaksis.qty * COALESCE(items.harga_beli, 0)) as total_hpp')
            ->value('total_hpp') ?? 0;

        $laba_kotor = $penjualan - $hpp;
        $laba_bersih = $laba_kotor; // Bisa dikurangi biaya operasional jika ada

        return view('laporans.laba-rugi', compact('penjualan', 'pembelian', 'hpp', 'laba_kotor', 'laba_bersih', 'request'));
    }
}
