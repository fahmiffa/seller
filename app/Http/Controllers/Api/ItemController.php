<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Log;


class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Item::where('user_id', auth()->user()->getOwnerId())->with(['satuan', 'supplier']);

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
            'image' => 'nullable|image|max:3072', // Max 3MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->except('image');
        $data['user_id'] = auth()->user()->getOwnerId();

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = $file->hashName();
            $manager = new ImageManager(new Driver());
            $image = $manager->read($file);
            $image->scale(width: 800);
            $encoded = $image->toJpeg(quality: 75);
            Storage::disk('public')->put('items/' . $filename, $encoded);
            $data['image'] = 'items/' . $filename;
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

        $item = Item::where('user_id', auth()->user()->getOwnerId())->with(['satuan', 'supplier'])->find($id);

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
        Log::info('Update item request masuk', [
            'item_id' => $id,
            'user_id' => auth()->id(),
            'payload' => $request->except('image'),
            'has_image' => $request->hasFile('image'),
        ]);

        $item = Item::where('user_id', auth()->user()->getOwnerId())->find($id);

        if (!$item) {
            Log::warning('Item tidak ditemukan', [
                'item_id' => $id,
                'user_id' => auth()->id(),
            ]);

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
            'image' => 'nullable|image|max:3072',
        ]);

        if ($validator->fails()) {
            Log::error('Validasi gagal saat update item', [
                'errors' => $validator->errors()->toArray(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->except('image');

        try {
            if ($request->hasFile('image')) {
                Log::info('Upload image dimulai', [
                    'original_name' => $request->file('image')->getClientOriginalName(),
                    'size' => $request->file('image')->getSize(),
                    'mime' => $request->file('image')->getMimeType(),
                ]);

                if ($item->image) {
                    Storage::disk('public')->delete($item->image);
                }

                $file = $request->file('image');
                $filename = $file->hashName();

                $manager = new ImageManager(new Driver());
                $image = $manager->read($file);
                $image->scale(width: 800);

                $encoded = $image->toJpeg(quality: 75);
                Storage::disk('public')->put('items/' . $filename, $encoded);

                $data['image'] = 'items/' . $filename;

                Log::info('Upload image berhasil', [
                    'path' => $data['image'],
                ]);
            }

            $item->update($data);

            Log::info('Item berhasil diupdate', [
                'item_id' => $item->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Item berhasil diupdate',
                'data' => $item
            ], 200);

        } catch (\Throwable $e) {
            Log::critical('Gagal update item', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server'
            ], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $item = Item::where('user_id', auth()->user()->getOwnerId())->find($id);

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
