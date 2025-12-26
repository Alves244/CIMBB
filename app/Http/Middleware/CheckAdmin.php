<?php

namespace App\Http\Middleware;

use App\Models\User; 
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// Middleware de restrição de acesso para garantir que apenas administradores gerem o sistema 
class CheckAdmin
{
    // Processa a validação do nível de autorização do utilizador antes de permitir o acesso [cite: 14]
    public function handle(Request $request, Closure $next): Response
    {
        // Obtém a instância do utilizador autenticado para verificação de permissões 
        /** @var \App\Models\User|null $user */ 
        $user = auth()->user();

        // Valida se o utilizador possui privilégios de administrador para prosseguir [cite: 14, 23]
        if (auth()->check() && $user && $user->isAdmin()) {
            return $next($request);
        }

        // Bloqueia o acesso não autorizado, redirecionando para a área segura do dashboard 
        return redirect('/dashboard')->with('error', 'Acesso não autorizado.');
    }
}