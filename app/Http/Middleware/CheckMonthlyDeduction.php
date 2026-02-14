<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMonthlyDeduction
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Jalankan scheduler bayangan jika belum jalan hari ini
        // Kita simpan di cache sampai akhir hari (end of day)
        \Illuminate\Support\Facades\Cache::remember('lazy_scheduler_run', now()->endOfDay(), function () {
            // Kita gunakan queue agar tidak menghambat request user
            // Ini akan memicu perintah artisan di background (Worker/Supervisor)
            \Illuminate\Support\Facades\Artisan::queue('app:monthly-deduction');
            return true;
        });

        return $next($request);
    }
}
