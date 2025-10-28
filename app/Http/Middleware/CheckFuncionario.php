<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckFuncionario
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && (auth()->user()->isFuncionario() || auth()->user()->isAdmin())) {
            return $next($request);
        }

        return redirect('/dashboard')->with('error', 'Acesso não autorizado.');
    }
}