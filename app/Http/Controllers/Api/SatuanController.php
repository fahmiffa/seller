<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Satuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SatuanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $satuans = Satuan::where('user_id', auth()->id())->latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar satuan',
            'data' => $satuans
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_satuan' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->all();
        $data['user_id'] = auth()->id();
        $satuan = Satuan::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Satuan berhasil ditambahkan',
            'data' => $satuan
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $satuan = Satuan::where('user_id', auth()->id())->find($id);

        if (!$satuan) {
            return response()->json([
                'success' => false,
                'message' => 'Satuan tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail satuan',
            'data' => $satuan
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $satuan = Satuan::where('user_id', auth()->id())->find($id);

        if (!$satuan) {
            return response()->json([
                'success' => false,
                'message' => 'Satuan tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama_satuan' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $satuan->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Satuan berhasil diupdate',
            'data' => $satuan
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $satuan = Satuan::where('user_id', auth()->id())->find($id);

        if (!$satuan) {
            return response()->json([
                'success' => false,
                'message' => 'Satuan tidak ditemukan'
            ], 404);
        }

        $satuan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Satuan berhasil dihapus'
        ], 200);
    }
}
