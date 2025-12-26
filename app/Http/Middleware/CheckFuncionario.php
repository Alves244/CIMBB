<?php

namespace App\Http\Middleware;

use App\Models\User; 
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// Middleware para autorizar o acesso de técnicos e administradores da CIMBB
class CheckFuncionario
{
    /**
     * Valida se o utilizador possui privilégios de consulta regional.
     * Essencial para o Objetivo 2: Ferramenta dinâmica de acesso a dados atualizados.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Obtém o utilizador autenticado para verificação de perfil
        /** @var \App\Models\User|null $user */ 
        $user = auth()->user();

        // Permite a passagem se o utilizador for um funcionário técnico ou administrador
        if (auth()->check() && $user && ($user->isFuncionario() || $user->isAdmin())) {
            return $next($request);
        }

        // Bloqueia utilizadores de Freguesias ou Agrupamentos de acederem a dados consolidados
        return redirect('/dashboard')->with('error', 'Acesso não autorizado.');
    }
}