<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * Stack de middleware global.
     * Estes componentes são executados em todos os pedidos ao portal (Objetivo 4).
     */
    protected $middleware = [
        // Garante que o portal responde apenas a domínios seguros
        \App\Http\Middleware\TrustProxies::class,
        // Gere permissões de acesso entre domínios (CORS)
        \Illuminate\Http\Middleware\HandleCors::class,
        // Permite isolar o sistema para manutenção técnica sem perda de dados
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        // Impede o upload de ficheiros/dados excessivamente grandes
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        // Limpa espaços em branco para manter a consistência estatística (Objetivo 2)
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * Grupos de middleware por tipo de rota.
     */
    protected $middlewareGroups = [
        'web' => [
            // Encriptação de cookies para proteger a sessão dos técnicos (Objetivo 4)
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            // Inicia a sessão para rastrear as ações no portal
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            // Proteção contra ataques CSRF em formulários de inquérito
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            'throttle:api', // Limita o número de pedidos para evitar sobrecarga do servidor
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * Middlewares de rota individuais.
     * Estes são os "rótulos" que usas nas tuas rotas para controlar quem entra onde.
     */
    protected $routeMiddleware = [
        //'auth' => \App\Http\Middleware\Authenticate::class,  !!!algum erro!!!
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

        // --- MIDDLEWARES DO PROJETO CIMBB (Objetivo 23 - Perfis de Utilizador) ---
        
        // Restringe o acesso apenas a técnicos das Juntas de Freguesia
        'check_freguesia' => \App\Http\Middleware\CheckFreguesia::class,
        
        // Restringe o acesso à gestão total do sistema (CIMBB Admin)
        'check_admin' => \App\Http\Middleware\CheckAdmin::class,
        
        // Permite o acesso a técnicos regionais para análise de relatórios
        'check_funcionario' => \App\Http\Middleware\CheckFuncionario::class,
        
        // Restringe o acesso apenas aos responsáveis pelos Agrupamentos Escolares
        'check_agrupamento' => \App\Http\Middleware\CheckAgrupamento::class,
    ];
}