<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UnitController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('units.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'alamat' => 'nullable|string',
            'nomor_hp' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6',
        ]);

        $currentUser = auth()->user();
        $ownerId = $currentUser->getOwnerId();

        try {
            DB::beginTransaction();

            // 1. Buat User baru role 5 (Unit) dengan parent_id dari pembuat (role 4)
            $password = !empty($validated['password']) ? $validated['password'] : 'password123';
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($password),
                'role' => 5,
                'parent_id' => $ownerId,
                'address' => $validated['alamat'] ?? null,
                'phone_number' => $validated['nomor_hp'] ?? null,
                'saldo' => 0,
                'limit' => 0,
                'status' => 'active',
                'trial' => 0,
                'tipe' => 1,
                'is_login' => 0,
                'transaction_count' => 0,
            ]);

            // 2. Buat Unit dengan user_id merujuk ke user role 5 yang baru dibuat
            $unit = Unit::create([
                'name' => $validated['name'],
                'user_id' => $user->id,
            ]);

            DB::commit();

            return redirect()->route('units.index')->with('success', 'Unit dan akun pengguna berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal membuat unit: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Unit $unit)
    {
        $ownerId = auth()->user()->getOwnerId();
        $unitUser = $unit->user;

        // Pastikan unit ini milik role 4 (baik direct user_id atau parent_id user adalah role 4)
        if ($unit->user_id != $ownerId && (!$unitUser || $unitUser->parent_id != $ownerId)) {
            abort(403, 'Anda tidak memiliki akses ke unit ini.');
        }

        return view('units.show', compact('unit'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Unit $unit)
    {
        $ownerId = auth()->user()->getOwnerId();
        $unitUser = $unit->user;

        if ($unit->user_id != $ownerId && (!$unitUser || $unitUser->parent_id != $ownerId)) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit unit ini.');
        }

        return view('units.edit', compact('unit'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Unit $unit)
    {
        $ownerId = auth()->user()->getOwnerId();
        $unitUser = $unit->user;

        if ($unit->user_id != $ownerId && (!$unitUser || $unitUser->parent_id != $ownerId)) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit unit ini.');
        }

        $userId = $unitUser ? $unitUser->id : null;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                $userId ? Rule::unique('users', 'email')->ignore($userId) : 'unique:users,email',
            ],
            'alamat' => 'nullable|string',
            'nomor_hp' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6',
        ]);

        try {
            DB::beginTransaction();

            // 1. Update Unit
            $unit->update([
                'name' => $validated['name'],
            ]);

            // 2. Update User terkait
            if ($unitUser) {
                $userUpdates = [
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'address' => $validated['alamat'] ?? null,
                    'phone_number' => $validated['nomor_hp'] ?? null,
                ];

                if (!empty($validated['password'])) {
                    $userUpdates['password'] = Hash::make($validated['password']);
                }

                $unitUser->update($userUpdates);
            }

            DB::commit();

            return redirect()->route('units.index')->with('success', 'Unit dan akun pengguna berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui unit: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Unit $unit)
    {
        $ownerId = auth()->user()->getOwnerId();
        $unitUser = $unit->user;

        if ($unit->user_id != $ownerId && (!$unitUser || $unitUser->parent_id != $ownerId)) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus unit ini.');
        }

        try {
            DB::beginTransaction();

            // Hapus unit
            $unit->delete();

            // Hapus akun user role 5 terkait jika ada
            if ($unitUser && $unitUser->role == 5 && $unitUser->parent_id == $ownerId) {
                $unitUser->delete();
            }

            DB::commit();

            return redirect()->route('units.index')->with('success', 'Unit dan akun terkait berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('units.index')->with('error', 'Gagal menghapus unit: ' . $e->getMessage());
        }
    }
}
