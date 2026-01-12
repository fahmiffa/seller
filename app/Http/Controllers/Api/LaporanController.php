<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Transaksi;
use App\Models\Pembelian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    /**
     * Laporan Stok Barang
     */
    public function stok()
    {
        $stok = Item::select('item_id', 'nama_item', 'kategori_id', 'stok', 'harga_beli', 'harga_jual')
            ->where('user_id', auth()->id())
            ->where('tipe_item', 'barang') // Hanya barang yang punya stok
            ->with('kategori:kategori_id,nama_kategori')
            ->orderBy('stok', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Laporan Stok Barang',
            'data' => $stok
        ]);
    }

    /**
     * Laporan Penjualan per Periode
     */
    public function penjualan(Request $request)
    {
        $startDate = $request->input('start_date', date('Y-m-01')); // Default awal bulan ini
        $endDate = $request->input('end_date', date('Y-m-d')); // Default hari ini

        $penjualan = Transaksi::where('user_id', auth()->id())
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->with(['customer', 'user', 'details.item'])
            ->orderBy('tanggal_transaksi', 'desc')
            ->get();

        $totalPenjualan = $penjualan->sum('total_harga');

        return response()->json([
            'success' => true,
            'message' => 'Laporan Penjualan',
            'periode' => "$startDate - $endDate",
            'total_penjualan' => $totalPenjualan,
            'data' => $penjualan
        ]);
    }

    /**
     * Laporan Pembelian per Periode
     */
    public function pembelian(Request $request)
    {
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-d'));

        $pembelian = Pembelian::where('user_id', auth()->id())
            ->whereBetween('tanggal_pembelian', [$startDate, $endDate])
            ->with(['supplier', 'user', 'details.item'])
            ->orderBy('tanggal_pembelian', 'desc')
            ->get();

        $totalPembelian = $pembelian->sum('total_pembelian');

        return response()->json([
            'success' => true,
            'message' => 'Laporan Pembelian',
            'periode' => "$startDate - $endDate",
            'total_pembelian' => $totalPembelian,
            'data' => $pembelian
        ]);
    }

    /**
     * Ringkasan / Dashboard Analytics
     */
    public function ringkasan(Request $request)
    {
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-d'));

        // Total Penjualan
        $totalPenjualan = Transaksi::where('user_id', auth()->id())->whereBetween('tanggal_transaksi', [$startDate, $endDate])->sum('total_harga');

        // Total Pembelian
        $totalPembelian = Pembelian::where('user_id', auth()->id())->whereBetween('tanggal_pembelian', [$startDate, $endDate])->sum('total_pembelian');
        
        $penjualanDetails = DB::table('detail_transaksis')
            ->join('transaksis', 'detail_transaksis.transaksi_id', '=', 'transaksis.transaksi_id')
            ->join('items', 'detail_transaksis.item_id', '=', 'items.item_id')
            ->where('transaksis.user_id', auth()->id())
            ->whereBetween('transaksis.tanggal_transaksi', [$startDate, $endDate])
            ->selectRaw('SUM((detail_transaksis.qty * detail_transaksis.harga_satuan) - (detail_transaksis.qty * items.harga_beli)) as profit')
            ->first();
            
        $labaKotor = $penjualanDetails->profit ?? 0;

        // Stok Menipis (misal stok < 10)
        $stokMenipis = Item::where('user_id', auth()->id())->where('stok', '<', 10)->where('tipe_item', 'barang')->count();

        // Data Chart 7 Hari Terakhir
        $chartHistory = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $label = date('d M', strtotime($date));
            
            $jual = Transaksi::where('user_id', auth()->id())->whereDate('tanggal_transaksi', $date)->sum('total_harga');
            $beli = Pembelian::where('user_id', auth()->id())->whereDate('tanggal_pembelian', $date)->sum('total_pembelian');
            
            $chartHistory[] = [
                'tanggal' => $date,
                'label' => $label,
                'penjualan' => (float) $jual,
                'pembelian' => (float) $beli
            ];
        }

        // Top 10 Stok Barang
        $topStock = Item::where('user_id', auth()->id())->where('tipe_item', 'barang')
            ->orderBy('stok', 'desc')
            ->limit(10)
            ->get(['nama_item', 'stok']);

        return response()->json([
            'success' => true,
            'message' => 'Ringkasan Laporan',
            'periode' => "$startDate - $endDate",
            'data' => [
                'total_penjualan' => (float) $totalPenjualan,
                'total_pembelian' => (float) $totalPembelian,
                'laba_kotor_estimasi' => (float) $labaKotor,
                'jumlah_transaksi' => Transaksi::where('user_id', auth()->id())->whereBetween('tanggal_transaksi', [$startDate, $endDate])->count(),
                'stok_menipis' => $stokMenipis,
                'chart_history' => $chartHistory,
                'top_stock' => $topStock
            ]
        ]);
    }
    /**
     * Laporan Laba Rugi per Periode
     */
    public function labaRugi(Request $request)
    {
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-d'));

        // Total Penjualan
        $penjualan = Transaksi::where('user_id', auth()->id())
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->sum('total_harga');

        // Total Pembelian
        $pembelian = Pembelian::where('user_id', auth()->id())
            ->whereBetween('tanggal_pembelian', [$startDate, $endDate])
            ->sum('total_pembelian');

        // HPP (Harga Pokok Penjualan)
        $detailHpp = DB::table('detail_transaksis')
            ->join('transaksis', 'detail_transaksis.transaksi_id', '=', 'transaksis.transaksi_id')
            ->join('items', 'detail_transaksis.item_id', '=', 'items.item_id')
            ->where('transaksis.user_id', auth()->id())
            ->whereBetween('transaksis.tanggal_transaksi', [$startDate, $endDate])
            ->selectRaw('SUM(detail_transaksis.qty * COALESCE(items.harga_beli, 0)) as total_hpp')
            ->first();

        $hpp = $detailHpp->total_hpp ?? 0;
        $labaKotor = $penjualan - $hpp;
        
        // Di sini bisa ditambahkan biaya operasional jika ada di database
        $labaBersih = $labaKotor;

        return response()->json([
            'success' => true,
            'message' => 'Laporan Laba Rugi',
            'periode' => "$startDate - $endDate",
            'data' => [
                'total_penjualan' => (float) $penjualan,
                'total_pembelian' => (float) $pembelian,
                'total_hpp' => (float) $hpp,
                'laba_kotor' => (float) $labaKotor,
                'laba_bersih' => (float) $labaBersih
            ]
        ]);
    }
}
