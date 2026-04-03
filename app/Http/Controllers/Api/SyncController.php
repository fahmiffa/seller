<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncController extends Controller
{
    /**
     * Terima batch transaksi offline dari Flutter.
     * Endpoint: POST /api/sync-offline
     *
     * Body JSON:
     * {
     *   "items": [
     *     {
     *       "customer_id": 1,
     *       "tanggal_transaksi": "2024-01-01",
     *       "metode_pembayaran": "cash",
     *       "subtotal": 50000,
     *       "diskon": 0,
     *       "items": [
     *         {"item_id": 1, "qty": 2, "harga_satuan": 25000}
     *       ]
     *     }
     *   ]
     * }
     */
    public function uploadOffline(Request $request)
    {
        $rawItems = $request->input('items', []);

        if (empty($rawItems)) {
            return response()->json([
                'message' => 'Tidak ada item untuk di-sync.',
                'results' => [],
            ], 200);
        }

        $transaksiController = new TransaksiController();
        $results = [];

        foreach ($rawItems as $index => $item) {
            DB::beginTransaction();
            try {
                // Buat fake Request untuk diteruskan ke TransaksiController
                $fakeRequest = new Request($item);
                $fakeRequest->setUserResolver($request->getUserResolver());

                $response = $transaksiController->store($fakeRequest);
                $responseData = json_decode($response->getContent(), true);

                DB::commit();
                $results[] = [
                    'index'   => $index,
                    'success' => true,
                    'data'    => $responseData['data'] ?? null,
                ];
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Sync offline error [index=$index]: " . $e->getMessage());
                $results[] = [
                    'index'   => $index,
                    'success' => false,
                    'error'   => $e->getMessage(),
                ];
            }
        }

        $successCount = count(array_filter($results, fn($r) => $r['success']));

        return response()->json([
            'message'       => "$successCount dari " . count($rawItems) . " transaksi berhasil di-sync.",
            'results'       => $results,
            'total'         => count($rawItems),
            'success_count' => $successCount,
            'failed_count'  => count($rawItems) - $successCount,
        ]);
    }
}
