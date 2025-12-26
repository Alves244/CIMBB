<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TrimStrings as Middleware;

// Middleware responsável por limpar espaços em branco desnecessários nos dados submetidos
class TrimStrings extends Middleware
{
    /**
     * Lista de atributos que não devem ser alterados pelo processo de limpeza (trim).
     * Mantém a integridade das palavras-passe, onde espaços podem ser caracteres válidos.
     * Contribui para a salvaguarda da segurança dos dados (Objetivo 4).
     */
    protected $except = [
        'current_password',
        'password',
        'password_confirmation',
    ];
}