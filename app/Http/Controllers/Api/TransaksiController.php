<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transaksis = Transaksi::where('user_id', auth()->id())
            ->with(['customer', 'user', 'details.item'])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar transaksi penjualan',
            'data' => $transaksis
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal_transaksi' => 'required|date',
            'metode_pembayaran' => 'required|string', // cash, transfer, qris, etc.
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,item_id',
            'items.*.qty' => 'required|integer|min:1',
            // Harga satuan can be fetched from DB or sent from FE. 
            // Usually FE sends it to lock price, but server should ideally validate or fetch.
            // For this POS, let's allow FE to send it (e.g. for discounts) but default to DB if missing? 
            // Let's require it to be safe, or fetch if not present. I'll require it for now.
            'items.*.harga_satuan' => 'required|numeric|min:0', 
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Calculate total
            $total_harga = 0;
            foreach ($request->items as $itemData) {
                $total_harga += $itemData['qty'] * $itemData['harga_satuan'];
            }

            // Create Header
            $transaksi = Transaksi::create([
                'customer_id' => $request->customer_id,
                'user_id' => auth()->id(),
                'tanggal_transaksi' => $request->tanggal_transaksi,
                'total_harga' => $total_harga,
                'metode_pembayaran' => $request->metode_pembayaran,
            ]);

            // Create Details and Reduce Stock
            foreach ($request->items as $itemData) {
                // Check stock first
                $item = Item::lockForUpdate()->find($itemData['item_id']);
                
                // If item type is 'barang', check stock. 'jasa' unlimited.
                if ($item->tipe_item === 'barang') {
                     if ($item->stok < $itemData['qty']) {
                         throw new \Exception("Stok barang {$item->nama_item} tidak mencukupi. Sisa: {$item->stok}");
                     }
                     $item->stok -= $itemData['qty'];
                     $item->save();
                }

                DetailTransaksi::create([
                    'transaksi_id' => $transaksi->transaksi_id,
                    'item_id' => $itemData['item_id'],
                    'qty' => $itemData['qty'],
                    'harga_satuan' => $itemData['harga_satuan'],
                    'subtotal' => $itemData['qty'] * $itemData['harga_satuan'],
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil disimpan',
                'data' => $transaksi->load('details.item')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $transaksi = Transaksi::where('user_id', auth()->id())
            ->with(['customer', 'user', 'details.item'])
            ->find($id);

        if (!$transaksi) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail transaksi',
            'data' => $transaksi
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $transaksi = Transaksi::where('user_id', auth()->id())->with('details.item')->find($id);

        if (!$transaksi) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        }

        try {
            DB::beginTransaction();

            // Revert Stock (Add back)
            foreach ($transaksi->details as $detail) {
                if ($detail->item && $detail->item->tipe_item === 'barang') {
                    $detail->item->stok += $detail->qty;
                    $detail->item->save();
                }
            }

            // Delete details
            $transaksi->details()->delete();
            $transaksi->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dibatalkan dan stok dikembalikan'
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
