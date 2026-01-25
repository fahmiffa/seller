<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\History;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function index(Request $request)
    {
        $histories = History::where('user_id', auth()->id())
            ->latest()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'message' => 'Riwayat saldo berhasil diambil',
            'data' => $histories
        ]);
    }
}
