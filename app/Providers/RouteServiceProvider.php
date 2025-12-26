<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Rota de Redirecionamento Padrão.
     * Define o destino automático para onde o sistema envia o utilizador logo 
     * após uma autenticação bem-sucedida.
     */
    public const HOME = '/dashboard';

    /**
     * Inicialização do Sistema de Rotas.
     * Este método configura os limitadores de tráfego e carrega os ficheiros 
     * de definição de rotas, aplicando-lhes os middlewares correspondentes.
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            // Configuração do contexto de API:
            // Define o prefixo '/api' no URL e aplica o grupo de segurança 'api' (stateless).
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            // Configuração do contexto WEB:
            // Aplica o grupo 'web' (sessões, cookies, CSRF) para navegação via browser.
            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configuração do Limitador de Pedidos (Rate Limiter).
     * Estabelece regras de proteção contra abuso, limitando o número de pedidos 
     * por minuto que um utilizador ou endereço IP pode realizar.
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            // Limita a 60 pedidos por minuto, identificando o utilizador pelo ID 
            // ou pelo endereço IP caso não esteja autenticado.
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}