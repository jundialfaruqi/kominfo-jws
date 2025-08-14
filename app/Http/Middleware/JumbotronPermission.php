<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class JumbotronPermission
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Check legacy role-based access OR Spatie permission (salah satu saja)
        $hasRoleAccess = in_array(Auth::user()->role, ['Super Admin', 'Admin']);
        $hasPermission = Auth::user()->can('view-jumbotron');

        // User hanya perlu memiliki SALAH SATU akses (role ATAU permission)
        if (!$hasRoleAccess && !$hasPermission) {
            abort(403, 'Unauthorized - Anda tidak memiliki akses ke halaman jumbotron');
        }

        return $next($request);
    }
}
