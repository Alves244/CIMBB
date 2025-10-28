<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckFreguesia
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->isFreguesia()) {
            return $next($request);
        }

        return redirect('/dashboard')->with('error', 'Acesso não autorizado.');
    }
}