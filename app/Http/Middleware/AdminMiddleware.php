<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && in_array(Auth::user()->role, ['Super Admin', 'Admin'])) {
            return $next($request);
        }

        // Tampilkan halaman 403 jika bukan admin
        abort(403, 'Halaman yang anda minta tidak ditemukan atau tidak ada akses');
    }
}
