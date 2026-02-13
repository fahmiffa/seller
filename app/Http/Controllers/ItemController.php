<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ItemController extends Controller
{
    /**
     * Display QR Code for printing.
     */
    public function printQrCode(Request $request)
    {
        $search = $request->input('search');
        $items = Item::where('user_id', auth()->user()->getOwnerId())
            ->when($search, function ($query, $search) {
                return $query->where('nama_item', 'like', "%{$search}%");
            })
            ->get();
        return view('items.print-qrcode', compact('items'));
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $items = Item::where('user_id', auth()->user()->getOwnerId())
            ->when($search, function ($query, $search) {
                return $query->where('nama_item', 'like', "%{$search}%");
            })
            ->with(['satuan', 'supplier'])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('items.index', compact('items', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $satuans = \App\Models\Satuan::where('user_id', auth()->user()->getOwnerId())->get();
        $suppliers = \App\Models\Supplier::where('user_id', auth()->user()->getOwnerId())->get();
        return view('items.create', compact('satuans', 'suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'satuan_id' => 'required|exists:satuans,satuan_id',
            'supplier_id' => 'nullable|exists:suppliers,supplier_id',
            'nama_item' => 'required|string|max:255',
            'tipe_item' => 'required|in:barang,jasa',
            'harga_beli' => 'nullable|numeric|min:0',
            'harga_jual' => [
                'required',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->tipe_item === 'barang' && $request->filled('harga_beli') && $value <= $request->harga_beli) {
                        $fail('Harga jual harus lebih besar dari harga beli.');
                    }
                },
            ],
            'stok' => 'nullable|integer|min:0',
            'expired_at' => 'nullable|date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:3072',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = $file->hashName();
            $manager = new ImageManager(new Driver());
            $image = $manager->read($file);
            $image->scale(width: 800);
            $encoded = $image->toJpeg(quality: 75);
            Storage::disk('public')->put('items/' . $filename, $encoded);
            $validated['image'] = 'items/' . $filename;
        }

        if ($validated['tipe_item'] === 'jasa') {
            $validated['harga_beli'] = null;
            $validated['stok'] = null;
            $validated['supplier_id'] = null;
        }

        $validated['user_id'] = auth()->user()->getOwnerId();
        Item::create($validated);

        return redirect()->route('items.index')->with('success', 'Item berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item)
    {
        // Check if item belongs to current user
        if ($item->user_id != auth()->user()->getOwnerId()) {
            abort(403, 'Anda tidak memiliki akses untuk melihat item ini.');
        }

        return view('items.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Item $item)
    {
        // Check if item belongs to current user
        if ($item->user_id != auth()->user()->getOwnerId()) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit item ini.');
        }

        $satuans = \App\Models\Satuan::where('user_id', auth()->user()->getOwnerId())->get();
        $suppliers = \App\Models\Supplier::where('user_id', auth()->user()->getOwnerId())->get();
        return view('items.edit', compact('item', 'satuans', 'suppliers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Item $item)
    {
        // Check if item belongs to current user
        if ($item->user_id != auth()->user()->getOwnerId()) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit item ini.');
        }

        $validated = $request->validate([
            'satuan_id' => 'required|exists:satuans,satuan_id',
            'supplier_id' => 'nullable|exists:suppliers,supplier_id',
            'nama_item' => 'required|string|max:255',
            'tipe_item' => 'required|in:barang,jasa',
            'harga_beli' => 'nullable|numeric|min:0',
            'harga_jual' => [
                'required',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->tipe_item === 'barang' && $request->filled('harga_beli') && $value <= $request->harga_beli) {
                        $fail('Harga jual harus lebih besar dari harga beli.');
                    }
                },
            ],
            'stok' => 'nullable|integer|min:0',
            'expired_at' => 'nullable|date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:3072',
        ]);

        if ($request->hasFile('image')) {
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
            $validated['image'] = 'items/' . $filename;
        }

        if ($validated['tipe_item'] === 'jasa') {
            $validated['harga_beli'] = null;
            $validated['stok'] = null;
            $validated['supplier_id'] = null;
        }

        $item->update($validated);

        return redirect()->route('items.index')->with('success', 'Item berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item)
    {
        // Check if item belongs to current user
        if ($item->user_id != auth()->user()->getOwnerId()) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus item ini.');
        }

        if ($item->image) {
            Storage::disk('public')->delete($item->image);
        }
        $item->delete();

        return redirect()->route('items.index')->with('success', 'Item berhasil dihapus.');
    }
}
