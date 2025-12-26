<?php

namespace App\Http\Middleware;

use App\Models\User; 
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// Middleware para restringir o acesso a utilizadores com o perfil de Junta de Freguesia
class CheckFreguesia
{
    /**
     * Valida o nível de autorização do utilizador antes de permitir a gestão de dados familiares.
     * Crucial para o Objetivo 4: Salvaguardar dados em termos de segurança.
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();

        // Se o utilizador estiver autenticado e possuir o perfil de Freguesia, autoriza o pedido
        if (auth()->check() && $user && $user->isFreguesia()) {
            return $next($request);
        }

        // Se o utilizador estiver logado mas tentar aceder a uma área de freguesia sem permissão
        if (auth()->check()){
             return redirect('/dashboard')->with('error', 'Acesso não autorizado.');
        }

        // Caso o utilizador não tenha sessão iniciada, redireciona para o login (Salvaguarda de Acesso)
        return redirect()->route('login');
    }
}