<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->status !== 'Active') {
            // Optionally, log the user out (remove this if you want the user to stay logged in)
            // Auth::logout();

            // Redirect to the inactive page with an error message
            return redirect()->route('inactive.index')->with('error', 'Akun kamu tidak aktif atau ditangguhkan. Silahkan hubungi Admin untuk mengaktifkan akun.');
        }
        return $next($request);
    }
}
