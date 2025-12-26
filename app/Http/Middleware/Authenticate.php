<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

// Middleware responsável por validar a identidade do utilizador antes do acesso ao sistema [cite: 14, 23]
class Authenticate extends Middleware
{
    /**
     * Define o destino do utilizador caso tente aceder a dados protegidos sem estar logado.
     * Contribui para a salvaguarda da segurança dos dados do sistema.
     */
    protected function redirectTo($request)
    {
        // Se o pedido não for uma API (JSON), redireciona para a página de login oficial [cite: 19]
        if (! $request->expectsJson()) {
            return route('login');
        }
    }
}