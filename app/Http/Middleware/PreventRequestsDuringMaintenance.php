<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance as Middleware;

// Middleware que interrompe o acesso ao portal quando o sistema está em manutenção técnica
class PreventRequestsDuringMaintenance extends Middleware
{
    /**
     * Lista de URIs (rotas) que devem continuar acessíveis mesmo em modo de manutenção.
     * Permite que administradores ou serviços de monitorização continuem a operar.
     * Contribui para a eficácia e manutenção do portal (Objetivo 2).
     */
    protected $except = [
        //
    ];
}