<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

// Classe que gere a transmissão de eventos em tempo real (Objetivo 2)
class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Inicializa os serviços de transmissão (Broadcasting).
     */
    public function boot()
    {
        // Regista as rotas necessárias para autenticar pedidos de canais privados
        // Essencial para garantir que uma Freguesia só ouve os seus próprios alertas (Objetivo 4)
        Broadcast::routes();

        // Carrega as definições de autorização de canais
        // É aqui que se define, por exemplo, que apenas o Admin pode ouvir o canal 'suporte-tecnico'
        require base_path('routes/channels.php');
    }
}