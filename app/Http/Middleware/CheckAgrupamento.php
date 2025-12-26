<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// Middleware para restringir o acesso a utilizadores com o perfil de Agrupamento
class CheckAgrupamento
{
    // Valida o nível de autorização antes de permitir o carregamento de dados (Objetivo 14)
    public function handle(Request $request, Closure $next): Response
    {
        // Redireciona para o login se não houver uma sessão ativa no portal (Objetivo 23)
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        /** @var User|null $user */
        $user = auth()->user();

        // Verifica se o utilizador pertence a um agrupamento escolar autorizado (Objetivo 19)
        if ($user && $user->isAgrupamento()) {
            return $next($request);
        }

        // Impede o acesso indevido a dados sensíveis da região (Objetivo 4)
        return redirect('/dashboard')->with('error', 'Acesso não autorizado.');
    }
}