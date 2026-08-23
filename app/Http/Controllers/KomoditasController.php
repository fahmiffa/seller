<?php

namespace App\Http\Controllers;

use App\Models\Komoditas;
use Illuminate\Http\Request;

class KomoditasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $komoditas = Komoditas::where('user_id', auth()->user()->getOwnerId())->latest()->paginate(10);
        return view('komoditas.index', compact('komoditas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('komoditas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $validated['user_id'] = auth()->user()->getOwnerId();
        Komoditas::create($validated);

        return redirect()->route('komoditas.index')->with('success', 'Komoditas berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Komoditas $komoditas)
    {
        if ($komoditas->user_id != auth()->user()->getOwnerId()) {
            abort(403, 'Anda tidak memiliki akses untuk melihat komoditas ini.');
        }

        return view('komoditas.show', compact('komoditas'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Komoditas $komoditas)
    {
        if ($komoditas->user_id != auth()->user()->getOwnerId()) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit komoditas ini.');
        }

        return view('komoditas.edit', compact('komoditas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Komoditas $komoditas)
    {
        if ($komoditas->user_id != auth()->user()->getOwnerId()) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit komoditas ini.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $komoditas->update($validated);

        return redirect()->route('komoditas.index')->with('success', 'Komoditas berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Komoditas $komoditas)
    {
        if ($komoditas->user_id != auth()->user()->getOwnerId()) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus komoditas ini.');
        }

        $komoditas->delete();

        return redirect()->route('komoditas.index')->with('success', 'Komoditas berhasil dihapus.');
    }
}
