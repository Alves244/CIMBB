<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register: Usado para ligar interfaces a implementações.
     * Aqui registas "serviços" que o sistema vai usar, como o motor de estatísticas.
     */
    public function register()
    {
        // Exemplo: Registar um serviço de anonimização de dados (Objetivo 5)
    }

    /**
     * Bootstrap: Executado após todos os serviços estarem registados.
     * Ideal para configurações de base de dados e UI (Objetivo 2).
     */
    public function boot()
    {
        // Garante compatibilidade com versões de bases de dados mais antigas (comum em servidores institucionais)
        Schema::defaultStringLength(191);

    }
}