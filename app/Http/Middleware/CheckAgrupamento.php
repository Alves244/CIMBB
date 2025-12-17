<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAgrupamento
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (auth()->check() && $user && $user->isAgrupamento()) {
            return $next($request);
        }

        if (auth()->check()) {
            return redirect('/dashboard')->with('error', 'Acesso não autorizado.');
        }

        return redirect()->route('login');
    }
}
