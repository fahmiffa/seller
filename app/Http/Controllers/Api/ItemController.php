<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Item::where('user_id', auth()->id())->with(['satuan', 'supplier']);

        // Search by name
        if ($request->has('nama_item')) {
            $query->where('nama_item', 'like', '%' . $request->nama_item . '%');
        }


        $items = $query->latest()->get();

        // Transform image URL
        $items->transform(function ($item) {
            if ($item->image) {
                $item->image_url = url(Storage::url($item->image));
            }
            return $item;
        });

        return response()->json([
            'success' => true,
            'message' => 'Daftar item',
            'data' => $items
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'satuan_id' => 'required|exists:satuans,satuan_id',
            'supplier_id' => 'nullable|exists:suppliers,supplier_id',
            'nama_item' => 'required|string|max:255',
            'tipe_item' => 'required|in:barang,jasa',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'stok' => 'nullable|integer|min:0',
            'expired_at' => 'nullable|date',
            'image' => 'nullable|image|max:2048', // Max 2MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->except('image');
        $data['user_id'] = auth()->id();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('items', 'public');
            $data['image'] = $path;
        }

        $item = Item::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Item berhasil ditambahkan',
            'data' => $item
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Check if ID is likely a barcode (numeric and longer than typical ID) or just search by ID first then barcode
        // Assuming item_id is integer. If barcode is stored in a column, use that. 
        // Since no barcode column in fillable, assuming standard ID lookup.

        $item = Item::where('user_id', auth()->id())->with(['satuan', 'supplier'])->find($id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Item tidak ditemukan'
            ], 404);
        }

        if ($item->image) {
            $item->image_url = url(Storage::url($item->image));
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail item',
            'data' => $item
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $item = Item::where('user_id', auth()->id())->find($id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Item tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'satuan_id' => 'required|exists:satuans,satuan_id',
            'supplier_id' => 'nullable|exists:suppliers,supplier_id',
            'nama_item' => 'required|string|max:255',
            'tipe_item' => 'required|in:barang,jasa',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'stok' => 'nullable|integer|min:0',
            'expired_at' => 'nullable|date',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            // Delete old image
            if ($item->image) {
                Storage::disk('public')->delete($item->image);
            }
            $path = $request->file('image')->store('items', 'public');
            $data['image'] = $path;
        }

        $item->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Item berhasil diupdate',
            'data' => $item
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $item = Item::where('user_id', auth()->id())->find($id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Item tidak ditemukan'
            ], 404);
        }

        if ($item->image) {
            Storage::disk('public')->delete($item->image);
        }

        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item berhasil dihapus'
        ], 200);
    }
}
