<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UnitController extends Controller
{
    private function getAllowedUnitIds()
    {
        $user = auth()->user();
        if ($user->role == 5) {
            return Unit::where('user_id', $user->id)->pluck('id');
        } elseif ($user->role == 4) {
            return Unit::where('user_id', $user->id)
                ->orWhereHas('user', function ($q) use ($user) {
                    $q->where('parent_id', $user->id);
                })
                ->pluck('id');
        }
        return Unit::pluck('id');
    }

    public function index()
    {
        $user = auth()->user();
        $allowedUnitIds = $this->getAllowedUnitIds();

        $units = Unit::with('user:id,name,email,role,address,phone_number')
            ->whereIn('id', $allowedUnitIds)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar unit',
            'data' => $units
        ], 200);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if ($user->role != 4 && $user->role != 0) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya Distributor / Admin yang dapat menambahkan unit.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'alamat' => 'nullable|string',
            'address' => 'nullable|string',
            'nomor_hp' => 'nullable|string|max:20',
            'phone_number' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $ownerId = $user->getOwnerId();

        try {
            DB::beginTransaction();

            $password = !empty($request->password) ? $request->password : 'password123';
            $unitUser = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($password),
                'role' => 5,
                'parent_id' => $ownerId,
                'address' => $request->alamat ?? $request->address ?? null,
                'phone_number' => $request->nomor_hp ?? $request->phone_number ?? null,
                'saldo' => 0,
                'limit' => 0,
                'status' => 'active',
                'trial' => 0,
                'tipe' => 1,
                'is_login' => 0,
                'transaction_count' => 0,
            ]);

            $unit = Unit::create([
                'name' => $request->name,
                'user_id' => $unitUser->id,
            ]);

            DB::commit();

            $unit->load('user:id,name,email,role,address,phone_number');

            return response()->json([
                'success' => true,
                'message' => 'Unit dan akun pengguna berhasil dibuat.',
                'data' => $unit
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat unit: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $allowedUnitIds = $this->getAllowedUnitIds();
        $unit = Unit::with('user:id,name,email,role,address,phone_number')
            ->whereIn('id', $allowedUnitIds)
            ->find($id);

        if (!$unit) {
            return response()->json([
                'success' => false,
                'message' => 'Unit tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail unit',
            'data' => $unit
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        if ($user->role != 4 && $user->role != 0) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya Distributor / Admin yang dapat mengupdate unit.'
            ], 403);
        }

        $allowedUnitIds = $this->getAllowedUnitIds();
        $unit = Unit::with('user')->whereIn('id', $allowedUnitIds)->find($id);

        if (!$unit) {
            return response()->json([
                'success' => false,
                'message' => 'Unit tidak ditemukan'
            ], 404);
        }

        $unitUser = $unit->user;
        $userId = $unitUser ? $unitUser->id : null;

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => [
                'nullable',
                'email',
                $userId ? Rule::unique('users', 'email')->ignore($userId) : 'unique:users,email',
            ],
            'alamat' => 'nullable|string',
            'address' => 'nullable|string',
            'nomor_hp' => 'nullable|string|max:20',
            'phone_number' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $unit->update([
                'name' => $request->name,
            ]);

            if ($unitUser) {
                $userUpdates = [
                    'name' => $request->name,
                ];
                if ($request->filled('email')) {
                    $userUpdates['email'] = $request->email;
                }
                if ($request->has('alamat') || $request->has('address')) {
                    $userUpdates['address'] = $request->alamat ?? $request->address;
                }
                if ($request->has('nomor_hp') || $request->has('phone_number')) {
                    $userUpdates['phone_number'] = $request->nomor_hp ?? $request->phone_number;
                }
                if (!empty($request->password)) {
                    $userUpdates['password'] = Hash::make($request->password);
                }

                $unitUser->update($userUpdates);
            }

            DB::commit();

            $unit->load('user:id,name,email,role,address,phone_number');

            return response()->json([
                'success' => true,
                'message' => 'Unit berhasil diupdate',
                'data' => $unit
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate unit: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $user = auth()->user();
        if ($user->role != 4 && $user->role != 0) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya Distributor / Admin yang dapat menghapus unit.'
            ], 403);
        }

        $ownerId = $user->getOwnerId();
        $allowedUnitIds = $this->getAllowedUnitIds();
        $unit = Unit::with('user')->whereIn('id', $allowedUnitIds)->find($id);

        if (!$unit) {
            return response()->json([
                'success' => false,
                'message' => 'Unit tidak ditemukan'
            ], 404);
        }

        try {
            DB::beginTransaction();

            $unitUser = $unit->user;
            $unit->delete();

            if ($unitUser && $unitUser->role == 5 && $unitUser->parent_id == $ownerId) {
                $unitUser->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Unit dan akun terkait berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus unit: ' . $e->getMessage()
            ], 500);
        }
    }
}

