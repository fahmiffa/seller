<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AppConfigController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'android' => [
                    'version' => '1.0.0',
                    'build_number' => env('BUILD_NUMBER'),
                    'force_update' => true,
                    'maintenance' => false,
                    'message' => 'Versi aplikasi terbaru sudah tersedia. Silahkan perbarui untuk melanjutkan.',
                    'update_url' => 'https://play.google.com/store/apps/details?id=com.qlabcode.beres' // Ganti dengan package name anda
                ],
                'ios' => [
                    'version' => '1.0.0',
                    'build_number' => 1,
                    'force_update' => false,
                    'maintenance' => false,
                    'message' => 'Update terbaru tersedia.',
                    'update_url' => ''
                ],
                'global_maintenance' => false
            ]
        ]);
    }
}
