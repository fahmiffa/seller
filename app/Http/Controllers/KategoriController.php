<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kategoris = Kategori::where('user_id', auth()->id())->latest()->paginate(10);
        return view('kategoris.index', compact('kategoris'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('kategoris.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'tipe' => 'required|in:barang,jasa',
        ]);

        $validated['user_id'] = auth()->id();
        Kategori::create($validated);

        return redirect()->route('kategoris.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Kategori $kategori)
    {
        // Check if kategori belongs to current user
        if ($kategori->user_id != auth()->id()) {
            abort(403, 'Anda tidak memiliki akses untuk melihat kategori ini.');
        }

        return view('kategoris.show', compact('kategori'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kategori $kategori)
    {
        return view('kategoris.edit', compact('kategori'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kategori $kategori)
    {
        // Check if kategori belongs to current user
        if ($kategori->user_id != auth()->id()) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit kategori ini.');
        }

        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'tipe' => 'required|in:barang,jasa',
        ]);

        $kategori->update($validated);

        return redirect()->route('kategoris.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kategori $kategori)
    {
        // Check if kategori belongs to current user
        if ($kategori->user_id != auth()->id()) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus kategori ini.');
        }

        $kategori->delete();

        return redirect()->route('kategoris.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
