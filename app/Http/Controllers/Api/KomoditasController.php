<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Komoditas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KomoditasController extends Controller
{
    private function getDistributorId()
    {
        $user = auth()->user();
        if ($user->role == 5) {
            return $user->parent_id ?: $user->id;
        }
        return $user->id;
    }

    public function index()
    {
        $distributorId = $this->getDistributorId();
        $komoditas = Komoditas::where('user_id', $distributorId)->latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar komoditas',
            'data' => $komoditas
        ], 200);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if ($user->role != 4 && $user->role != 0) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya Distributor yang dapat menambahkan komoditas.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $komoditas = Komoditas::create([
            'user_id' => $user->id,
            'name' => $request->name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Komoditas berhasil ditambahkan',
            'data' => $komoditas
        ], 201);
    }

    public function show($id)
    {
        $distributorId = $this->getDistributorId();
        $komoditas = Komoditas::where('user_id', $distributorId)->find($id);

        if (!$komoditas) {
            return response()->json([
                'success' => false,
                'message' => 'Komoditas tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail komoditas',
            'data' => $komoditas
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        if ($user->role != 4 && $user->role != 0) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya Distributor yang dapat mengupdate komoditas.'
            ], 403);
        }

        $komoditas = Komoditas::where('user_id', $user->id)->find($id);

        if (!$komoditas) {
            return response()->json([
                'success' => false,
                'message' => 'Komoditas tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $komoditas->update([
            'name' => $request->name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Komoditas berhasil diupdate',
            'data' => $komoditas
        ], 200);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        if ($user->role != 4 && $user->role != 0) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya Distributor yang dapat menghapus komoditas.'
            ], 403);
        }

        $komoditas = Komoditas::where('user_id', $user->id)->find($id);

        if (!$komoditas) {
            return response()->json([
                'success' => false,
                'message' => 'Komoditas tidak ditemukan'
            ], 404);
        }

        $komoditas->delete();

        return response()->json([
            'success' => true,
            'message' => 'Komoditas berhasil dihapus'
        ], 200);
    }
}
