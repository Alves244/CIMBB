<?php

namespace App\Http\Middleware;

use App\Models\User; // <-- ADICIONA ou certifica-te que este import existe
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckFreguesia
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \App\Models\User|null $user */ // <-- ADICIONA ESTA LINHA PHPDoc
        $user = auth()->user();

        // Modifica o 'if' para usar a variável $user e verificar se não é null
        // (Nota: Ajustei a lógica aqui ligeiramente para ser mais clara)

        // Se estiver autenticado E for freguesia, permite passar
        if (auth()->check() && $user && $user->isFreguesia()) {
            return $next($request);
        }

        // Se estiver autenticado mas NÃO for freguesia, redireciona com erro
        if (auth()->check()){
             return redirect('/dashboard')->with('error', 'Acesso não autorizado.');
        }

        // Se não estiver autenticado (fallback, embora o middleware 'auth' deva tratar disto)
        return redirect()->route('login');
    }
}