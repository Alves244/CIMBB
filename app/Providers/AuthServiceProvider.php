<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

// Classe que centraliza as políticas de segurança e permissões (Objetivo 4 e 23)
class AuthServiceProvider extends ServiceProvider
{
    /**
     * Mapeamento de Políticas.
     * Liga cada Modelo (ex: Familia) a uma Classe de Política (ex: FamiliaPolicy)
     * que define regras granulares (quem pode editar, quem pode ver).
     */
    protected $policies = [
        // 'App\Models\Familia' => 'App\Policies\FamiliaPolicy',
    ];

    /**
     * Inicialização dos serviços de autorização.
     */
    public function boot()
    {
        $this->registerPolicies();

        /**
         * Definição de Gates (Portões de Acesso).
         * Os Gates são verificações rápidas para permissões globais.
         */

        // Gate para garantir que apenas administradores da CIMBB gerem utilizadores
        Gate::define('gerir-utilizadores', function ($user) {
            return $user->perfil === 'admin';
        });

        // Gate para permitir a inserção de inquéritos apenas durante o período aberto
        Gate::define('submeter-inquerito', function ($user) {
            return in_array($user->perfil, ['freguesia', 'agrupamento']);
        });

        // Gate para visualização de relatórios macro (CIMBB e Admin)
        Gate::define('ver-estatisticas-regionais', function ($user) {
            return in_array($user->perfil, ['admin', 'cimbb']);
        });
    }
}