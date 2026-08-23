<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class CheckDistributorAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Jika user yang login adalah Distributor (role 4) atau Unit (role 5)
        if ($user && ($user->role == 4 || $user->role == 5)) {
            $allowedPatterns = [
                'profile',
                'profile.*',
                'logout',
                'livewire.*',
                'orders.*',
            ];

            if ($user->role == 4) {
                $allowedPatterns[] = 'suppliers.*';
                $allowedPatterns[] = 'komoditas.*';
                $allowedPatterns[] = 'units.*';
                $allowedPatterns[] = 'satuans.*';
            }

            $currentRouteName = $request->route() ? $request->route()->getName() : null;

            $isAllowed = false;
            if ($currentRouteName) {
                foreach ($allowedPatterns as $pattern) {
                    if (Str::is($pattern, $currentRouteName)) {
                        $isAllowed = true;
                        break;
                    }
                }
            }

            // Jika route yang diakses tidak diizinkan
            if (! $isAllowed) {
                $roleName = $user->role == 4 ? 'Distributor' : 'Unit';
                $allowedMenus = $user->role == 4 ? 'Supplier, Komoditas, Unit, Satuan, dan Order.' : 'Order.';

                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => "Akses ditolak. {$roleName} hanya memiliki hak akses ke menu {$allowedMenus}"
                    ], 403);
                }

                $redirectRoute = $user->role == 4 ? 'suppliers.index' : 'orders.index';
                return redirect()->route($redirectRoute)->with('error', "Akses dibatasi. Akun {$roleName} hanya dapat mengakses menu {$allowedMenus}");
            }
        }

        return $next($request);
    }
}
