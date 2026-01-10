<?php

namespace App\Http\Controllers;

use App\Models\App;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AppController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('apps.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'nama_aplikasi' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'saldo' => 'required|numeric|min:0',
            'status' => 'required|string|in:active,inactive',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        App::create($validated);

        return redirect()->route('users.index')->with('success', 'Data aplikasi berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(App $app)
    {
        return view('apps.edit', compact('app'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, App $app)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'nama_aplikasi' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'saldo' => 'required|numeric|min:0',
            'status' => 'required|string|in:active,inactive',
        ]);

        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($app->logo) {
                Storage::disk('public')->delete($app->logo);
            }
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        } else {
            unset($validated['logo']);
        }

        $app->update($validated);

        return redirect()->route('users.index')->with('success', 'Data aplikasi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(App $app)
    {
        // Delete logo if exists
        if ($app->logo) {
            Storage::disk('public')->delete($app->logo);
        }

        $app->delete();

        return redirect()->route('users.index')->with('success', 'Data aplikasi berhasil dihapus.');
    }
}

