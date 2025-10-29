<?php

namespace App\Http\Middleware;

use App\Models\User; // <-- ADICIONA ou certifica-te que este import existe
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \App\Models\User|null $user */ // <-- ADICIONA ESTA LINHA PHPDoc
        $user = auth()->user();

        // Modifica o 'if' para usar a variável $user e verificar se não é null
        if (auth()->check() && $user && $user->isAdmin()) {
            return $next($request);
        }

        return redirect('/dashboard')->with('error', 'Acesso não autorizado.');
    }
}