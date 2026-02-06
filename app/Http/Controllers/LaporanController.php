<?php

namespace App\Http\Controllers;

use App\Models\DetailTransaksi;
use App\Models\Item;
use App\Models\Pembelian;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index()
    {
        return view('laporans.index');
    }

    public function penjualan(Request $request)
    {
        $request->validate([
            'tanggal_dari'   => 'required|date',
            'tanggal_sampai' => 'required|date|after_or_equal:tanggal_dari',
        ]);

        $transaksis = Transaksi::with(['customer', 'user', 'details.item'])
            ->whereBetween('tanggal_transaksi', [$request->tanggal_dari, $request->tanggal_sampai])
            ->where('user_id', auth()->user()->getOwnerId())
            ->orderBy('tanggal_transaksi', 'desc')
            ->get();

        $totalSubtotal = $transaksis->sum(fn($t) => $t->subtotal ?? $t->total_harga);
        $totalDiskon = $transaksis->sum('diskon');
        $total = $transaksis->sum('total_harga');

        return view('laporans.penjualan', compact('transaksis', 'total', 'totalSubtotal', 'totalDiskon', 'request'));
    }

    public function pembelian(Request $request)
    {
        $request->validate([
            'tanggal_dari'   => 'required|date',
            'tanggal_sampai' => 'required|date|after_or_equal:tanggal_dari',
        ]);

        $pembelians = Pembelian::with(['supplier', 'user', 'details.item'])
            ->whereBetween('tanggal_pembelian', [$request->tanggal_dari, $request->tanggal_sampai])
            ->where('user_id', auth()->user()->getOwnerId())
            ->orderBy('tanggal_pembelian', 'desc')
            ->get();

        $total = $pembelians->sum('total_pembelian');

        return view('laporans.pembelian', compact('pembelians', 'total', 'request'));
    }

    public function stok(Request $request)
    {
        $items = Item::with(['satuan'])
            ->where('user_id', auth()->user()->getOwnerId())
            ->where('tipe_item', 'barang')
            ->orderBy('nama_item')
            ->get();

        return view('laporans.stok', compact('items'));
    }

    public function labaRugi(Request $request)
    {
        $request->validate([
            'tanggal_dari'   => 'required|date',
            'tanggal_sampai' => 'required|date|after_or_equal:tanggal_dari',
        ]);

        $query = Transaksi::whereBetween('tanggal_transaksi', [$request->tanggal_dari, $request->tanggal_sampai])
            ->where('user_id', auth()->user()->getOwnerId());

        // Total Penjualan (Net)
        $penjualan = (clone $query)->sum('total_harga');

        // Total Diskon
        $totalDiskon = (clone $query)->sum('diskon');

        // Total Pembelian
        $pembelian = Pembelian::whereBetween('tanggal_pembelian', [$request->tanggal_dari, $request->tanggal_sampai])
            ->where('user_id', auth()->user()->getOwnerId())
            ->sum('total_pembelian');

        // HPP (Harga Pokok Penjualan) - dari detail transaksi
        $hpp = DetailTransaksi::whereHas('transaksi', function ($query) use ($request) {
            $query->whereBetween('tanggal_transaksi', [$request->tanggal_dari, $request->tanggal_sampai])->where('user_id', auth()->user()->getOwnerId());
        })
            ->join('items', 'detail_transaksis.item_id', '=', 'items.item_id')
            ->selectRaw('SUM(detail_transaksis.qty * COALESCE(items.harga_beli, 0)) as total_hpp')
            ->value('total_hpp') ?? 0;

        $laba_kotor  = $penjualan - $hpp;
        $laba_bersih = $laba_kotor; // Bisa dikurangi biaya operasional jika ada

        return view('laporans.laba-rugi', compact('penjualan', 'totalDiskon', 'pembelian', 'hpp', 'laba_kotor', 'laba_bersih', 'request'));
    }
}
