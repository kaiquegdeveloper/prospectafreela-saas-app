<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfSuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->isSuperAdmin()) {
            // Don't redirect if already in super-admin area
            if (!$request->is('super-admin*')) {
                return redirect()->route('super-admin.dashboard');
            }
        }

        return $next($request);
    }
}

