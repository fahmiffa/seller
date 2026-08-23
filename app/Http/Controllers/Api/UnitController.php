<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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

        $units = Unit::with('user:id,name,email,role')
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
            'user_id' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $targetUserId = $request->user_id ?? $user->id;

        $unit = Unit::create([
            'name' => $request->name,
            'user_id' => $targetUserId,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Unit berhasil ditambahkan',
            'data' => $unit
        ], 201);
    }

    public function show($id)
    {
        $allowedUnitIds = $this->getAllowedUnitIds();
        $unit = Unit::with('user:id,name,email,role')
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
        $unit = Unit::whereIn('id', $allowedUnitIds)->find($id);

        if (!$unit) {
            return response()->json([
                'success' => false,
                'message' => 'Unit tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'user_id' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $updateData = ['name' => $request->name];
        if ($request->has('user_id')) {
            $updateData['user_id'] = $request->user_id;
        }

        $unit->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Unit berhasil diupdate',
            'data' => $unit
        ], 200);
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

        $allowedUnitIds = $this->getAllowedUnitIds();
        $unit = Unit::whereIn('id', $allowedUnitIds)->find($id);

        if (!$unit) {
            return response()->json([
                'success' => false,
                'message' => 'Unit tidak ditemukan'
            ], 404);
        }

        $unit->delete();

        return response()->json([
            'success' => true,
            'message' => 'Unit berhasil dihapus'
        ], 200);
    }
}
