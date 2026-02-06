<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pembelian;
use App\Models\DetailPembelian;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PembelianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pembelians = Pembelian::where('user_id', auth()->user()->getOwnerId())
            ->with(['supplier', 'user', 'details.item'])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar pembelian',
            'data' => $pembelians
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required|exists:suppliers,supplier_id',
            'tanggal_pembelian' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,item_id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.harga_beli' => 'required|numeric|min:0',
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

            $user = auth()->user();
            if ($user->saldo < 50000) {
                return response()->json([
                    'success' => false,
                    'message' => 'Saldo anda di bawah Rp 50.000, tidak dapat melakukan transaksi pembelian.'
                ], 400);
            }

            // Calculate total
            $total_pembelian = 0;
            foreach ($request->items as $item) {
                $total_pembelian += $item['qty'] * $item['harga_beli'];
            }

            // Create Header
            $pembelian = Pembelian::create([
                'supplier_id' => $request->supplier_id,
                'user_id' => auth()->user()->getOwnerId(), // Assuming JWT auth
                'tanggal_pembelian' => $request->tanggal_pembelian,
                'total_pembelian' => $total_pembelian,
            ]);

            // Create Details and Update Stock
            foreach ($request->items as $itemData) {
                DetailPembelian::create([
                    'pembelian_id' => $pembelian->pembelian_id,
                    'item_id' => $itemData['item_id'],
                    'qty' => $itemData['qty'],
                    'harga_beli' => $itemData['harga_beli'],
                    'subtotal' => $itemData['qty'] * $itemData['harga_beli'],
                ]);

                // Update Item Stock & Buy Price (Optional: update buy price to latest?)
                $item = Item::find($itemData['item_id']);
                $item->stok += $itemData['qty'];
                $item->harga_beli = $itemData['harga_beli']; // Update latest buy price
                $item->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pembelian berhasil disimpan',
                'data' => $pembelian->load('details.item')
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
        $pembelian = Pembelian::where('user_id', auth()->user()->getOwnerId())
            ->with(['supplier', 'user', 'details.item'])
            ->find($id);

        if (!$pembelian) {
            return response()->json([
                'success' => false,
                'message' => 'Pembelian tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail pembelian',
            'data' => $pembelian
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     * Note: Full replacement logic usually easier for transactions.
     */
    // Skipping Update for Transactional Data usually recommended unless complex reversal logic is implemented
    // But if requested, I'll implement a simple delete-then-create logic or similar.
    // Given the complexity of stock management, I will skip 'update' for now and assume users should delete and re-enter, 
    // OR I can implement it by rolling back stock first. Let's do delete-then-create logic for simplicity if update is triggered.
    /* 
    public function update(Request $request, string $id)
    {
        // Implementation complexity: High (Stock reversal required)
        // ...
    }
    */

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $pembelian = Pembelian::where('user_id', auth()->user()->getOwnerId())->with('details')->find($id);

        if (!$pembelian) {
            return response()->json([
                'success' => false,
                'message' => 'Pembelian tidak ditemukan'
            ], 404);
        }

        try {
            DB::beginTransaction();

            // Revert Stock
            foreach ($pembelian->details as $detail) {
                $item = Item::find($detail->item_id);
                if ($item) {
                    $item->stok -= $detail->qty;
                    $item->save();
                }
            }

            // Delete details (handled by cascade if set in DB, but manually here to be safe or if not set)
            $pembelian->details()->delete();
            $pembelian->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pembelian berhasil dihapus dan stok dikembalikan'
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
