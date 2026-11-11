<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Tolak jika belum login
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // 2. Cek apakah role sesuai (ADMIN / OWNER)
        $userRole = strtoupper(Auth::user()->role);
        $allowedRoles = array_map('strtoupper', $roles);

        if (!in_array($userRole, $allowedRoles)) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki wewenang ke halaman ini.');
        }

        return $next($request);
    }
}
