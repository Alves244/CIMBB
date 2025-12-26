<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Mapeamento de ouvintes (listeners).
     * Esta propriedade associa eventos específicos a uma ou mais classes que devem 
     * ser executadas quando o evento é disparado.
     * * @var array
     */
    protected $listen = [
        // O evento 'Registered' é disparado pelo Laravel após a criação de um utilizador.
        // O listener 'SendEmailVerificationNotification' captura este evento para 
        // enviar o e-mail de verificação.
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Inicialização de serviços de eventos.
     * Método utilizado para registar listeners manualmente, configurar "Observers" de modelos 
     * (Eloquent Observers) ou definir closures que reagem a eventos do sistema.
     * * @return void
     */
    public function boot()
    {
        //
    }
}