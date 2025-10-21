<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (! $request->user() || $request->user()->role !== $role) {
            // Boleh redirect ke login jika belum login, atau 403 jika login tapi bukan rolenya
            return $request->user()
                ? abort(403, 'Unauthorized.')
                : redirect()->route('login');
        }
        return $next($request);
    }
}
