<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAgrupamento
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        /** @var User|null $user */
        $user = auth()->user();

        if ($user && $user->isAgrupamento()) {
            return $next($request);
        }

        return redirect('/dashboard')->with('error', 'Acesso não autorizado.');
    }
}
