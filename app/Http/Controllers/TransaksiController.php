<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = \App\Models\Transaksi::where('user_id', auth()->id())->with(['customer', 'user']);

        if ($request->has('metode_pembayaran') && $request->metode_pembayaran != '') {
            $query->where('metode_pembayaran', $request->metode_pembayaran);
        }

        $transaksis = $query->latest()->paginate(10);
        return view('transaksis.index', compact('transaksis'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('transaksis.create');
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
    public function show(\App\Models\Transaksi $transaksi)
    {
        // Check if transaksi belongs to current user
        if ($transaksi->user_id != auth()->id()) {
            abort(403, 'Anda tidak memiliki akses untuk melihat transaksi ini.');
        }

        $transaksi->load(['customer', 'user', 'details.item']);
        return view('transaksis.show', compact('transaksi'));
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
    public function destroy(\App\Models\Transaksi $transaksi)
    {
        // Check if transaksi belongs to current user
        if ($transaksi->user_id != auth()->id()) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus transaksi ini.');
        }

        $transaksi->delete();
        return redirect()->route('transaksis.index')->with('success', 'Transaksi berhasil dihapus.');
    }
}
