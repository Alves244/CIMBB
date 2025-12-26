<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

// Middleware responsável por garantir a confidencialidade dos dados armazenados no cliente
class EncryptCookies extends Middleware
{
    /**
     * Lista de cookies que não devem ser encriptados.
     * Útil para cookies de terceiros ou integrações que exijam leitura direta no lado do cliente.
     * No contexto da CIMBB, mantém-se vazio para garantir segurança máxima por omissão.
     */
    protected $except = [
        //
    ];
}