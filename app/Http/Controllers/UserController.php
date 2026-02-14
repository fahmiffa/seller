<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Check if user has admin role (role = 0)
     */
    private function checkAdminAccess()
    {
        if (auth()->user()->role != 0) {
            abort(403, 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.');
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->checkAdminAccess();
        $users = User::latest()->paginate(10);
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->checkAdminAccess();
        $parents = User::where('role', 0)->orWhere('role', 1)->get();
        return view('users.create', compact('parents'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->checkAdminAccess();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|integer|in:0,1,3',
            'saldo' => 'required|numeric|min:0',
            'limit' => 'required|numeric|min:0',
            'status' => 'required|string|in:active,inactive',
            'parent_id' => 'nullable|exists:users,id',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'trial' => 'required|boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        if ($request->hasFile('img')) {
            $file = $request->file('img');
            $filename = $file->hashName();
            $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
            $image = $manager->read($file);
            $image->scale(width: 500);
            $encoded = $image->toJpeg(quality: 70);
            Storage::disk('public')->put('users/' . $filename, $encoded);
            $validated['img'] = 'users/' . $filename;
        }

        User::create($validated);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $this->checkAdminAccess();

        $items = $user->items()->with(['satuan', 'supplier'])->latest()->paginate(10, ['*'], 'items_page');
        $transaksis = $user->transaksis()->with('customer')->latest()->paginate(10, ['*'], 'transaksis_page');

        return view('users.show', compact('user', 'items', 'transaksis'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $this->checkAdminAccess();
        $parents = User::where('id', '!=', $user->id)->get();
        return view('users.edit', compact('user', 'parents'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $this->checkAdminAccess();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|confirmed',
            'role' => 'required|integer|in:0,1,3',
            'saldo' => 'required|numeric|min:0',
            'limit' => 'required|numeric|min:0',
            'status' => 'required|string|in:active,inactive',
            'parent_id' => 'nullable|exists:users,id',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'trial' => 'required|boolean',
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }

        if ($request->hasFile('img')) {
            // Delete old image
            if ($user->img) {
                Storage::disk('public')->delete($user->img);
            }
            $file = $request->file('img');
            $filename = $file->hashName();
            $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
            $image = $manager->read($file);
            $image->scale(width: 500);
            $encoded = $image->toJpeg(quality: 70);
            Storage::disk('public')->put('users/' . $filename, $encoded);
            $validated['img'] = 'users/' . $filename;
        }

        $user->update($validated);

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $this->checkAdminAccess();

        try {
            // Menggunakan transaksi database agar atomik (semua berhasil atau tidak sama sekali)
            \Illuminate\Support\Facades\DB::beginTransaction();

            // 1. Ambil semua item milik user
            $items = \App\Models\Item::where('user_id', $user->id)->get();

            foreach ($items as $item) {
                // 2. Hapus detail pembelian yang terkait dengan item ini
                $item->detailPembelian()->delete();

                // 3. Hapus detail transaksi yang terkait dengan item ini
                $item->detailTransaksi()->delete();

                // 4. Hapus item itu sendiri
                $item->delete();
            }

            // Hapus Transaksi dan Pembelian jika foreign key user_id tidak cascade (opsional, tapi aman)
            // Asumsi: Transaksi dan Pembelian mungkin sudah cascade or tidak, kita handle user delete

            // 5. Hapus user
            $user->delete();

            \Illuminate\Support\Facades\DB::commit();

            return redirect()->route('users.index')->with('success', 'User dan data relasinya (Item, Transaksi terkait) berhasil dihapus.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus user: ' . $e->getMessage());
        }
    }
}
