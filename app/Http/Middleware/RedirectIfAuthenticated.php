<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Middleware que redireciona utilizadores já autenticados para evitar acessos redundantes
class RedirectIfAuthenticated
{
    /**
     * Processa o pedido e verifica se existe uma sessão ativa antes de permitir o acesso.
     * Contribui para a eficácia do portal web (Objetivo 2).
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        // Define os guardas de autenticação (padrão ou personalizados)
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            // Se o utilizador já estiver autenticado, redireciona-o para a página principal (Dashboard)
            if (Auth::guard($guard)->check()) {
                return redirect(RouteServiceProvider::HOME);
            }
        }

        // Se não estiver autenticado, permite que o utilizador prossiga para a página solicitada (ex: Login)
        return $next($request);
    }
}