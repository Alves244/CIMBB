<?php

namespace App\Services;

use App\Models\LogAcesso;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogger
{
    /**
     * Regista um evento na tabela log_acessos de forma estática.
     * * @param string $acao Nome da operação realizada (ex: 'LOGIN', 'ELIMINAR_FAMILIA').
     * @param string|null $descricao Detalhes adicionais contextuais sobre o evento.
     * @param int|null $userId ID do utilizador (opcional, caso não seja o autenticado).
     */
    public static function log(string $acao, ?string $descricao = null, ?int $userId = null): void
    {
        // Determina o autor da ação: usa o ID fornecido ou tenta obter o utilizador autenticado na sessão.
        $utilizadorId = $userId ?? optional(Auth::user())->id;

        // Validação de segurança: se não houver um utilizador rastreável, o registo é abortado.
        if (! $utilizadorId) {
            return;
        }

        // Persistência dos dados de auditoria através do modelo LogAcesso.
        LogAcesso::create([
            'utilizador_id' => $utilizadorId,
            'acao' => $acao,
            'descricao' => $descricao,
            // Captura o endereço IP de origem do pedido HTTP.
            'ip' => Request::ip(),
            // Regista o carimbo temporal exato da operação.
            'data_hora' => now(),
        ]);
    }
}