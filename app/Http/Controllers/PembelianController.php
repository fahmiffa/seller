<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PembelianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pembelians = \App\Models\Pembelian::with(['supplier', 'user'])->latest()->paginate(10);
        return view('pembelians.index', compact('pembelians'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pembelians.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Store is handled by Livewire component in create view
    }

    /**
     * Display the specified resource.
     */
    public function show(\App\Models\Pembelian $pembelian)
    {
        $pembelian->load(['supplier', 'user', 'details.item']);
        return view('pembelians.show', compact('pembelian'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Edit could also be a Livewire component
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(\App\Models\Pembelian $pembelian)
    {
        $pembelian->delete();
        return redirect()->route('pembelians.index')->with('success', 'Pembelian berhasil dihapus.');
    }
}
