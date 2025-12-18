<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckUserActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Se o usuário está autenticado e não é super admin
        // Se estiver em modo de impersonação, permite acesso mesmo se inativo
        if (Auth::check() && !Auth::user()->isSuperAdmin() && !session()->has('impersonator_id')) {
            // Verifica se o usuário está ativo
            if (!Auth::user()->is_active || Auth::user()->refunded_at) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return redirect()->route('login')
                    ->with('error', Auth::user()->refunded_at
                        ? 'Acesso bloqueado: sua assinatura foi reembolsada.'
                        : 'Sua conta foi desativada. Entre em contato com o suporte.');
            }
        }

        return $next($request);
    }
}

