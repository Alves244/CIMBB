<?php

namespace App\Services;

use App\Models\LogAcesso;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogger
{
    /**
     * Regista um evento na tabela log_acessos.
     */
    public static function log(string $acao, ?string $descricao = null, ?int $userId = null): void
    {
        $utilizadorId = $userId ?? optional(Auth::user())->id;

        if (! $utilizadorId) {
            return; // não conseguimos associar o log a um utilizador
        }

        LogAcesso::create([
            'utilizador_id' => $utilizadorId,
            'acao' => $acao,
            'descricao' => $descricao,
            'ip' => Request::ip(),
            'data_hora' => now(),
        ]);
    }
}
