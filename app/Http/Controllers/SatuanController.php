<?php

namespace App\Http\Controllers;

use App\Models\Satuan;
use Illuminate\Http\Request;

class SatuanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $satuans = Satuan::where('user_id', auth()->user()->getOwnerId())->latest()->paginate(10);
        return view('satuans.index', compact('satuans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('satuans.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_satuan' => 'required|string|max:255',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $validated['user_id'] = auth()->user()->getOwnerId();
        Satuan::create($validated);

        return redirect()->route('satuans.index')->with('success', 'Satuan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Satuan $satuan)
    {
        // Check if satuan belongs to current user
        if ($satuan->user_id != auth()->user()->getOwnerId()) {
            abort(403, 'Anda tidak memiliki akses untuk melihat satuan ini.');
        }

        return view('satuans.show', compact('satuan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Satuan $satuan)
    {
        return view('satuans.edit', compact('satuan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Satuan $satuan)
    {
        // Check if satuan belongs to current user
        if ($satuan->user_id != auth()->user()->getOwnerId()) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit satuan ini.');
        }

        $validated = $request->validate([
            'nama_satuan' => 'required|string|max:255',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $satuan->update($validated);

        return redirect()->route('satuans.index')->with('success', 'Satuan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Satuan $satuan)
    {
        // Check if satuan belongs to current user
        if ($satuan->user_id != auth()->user()->getOwnerId()) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus satuan ini.');
        }

        $satuan->delete();

        return redirect()->route('satuans.index')->with('success', 'Satuan berhasil dihapus.');
    }
}
