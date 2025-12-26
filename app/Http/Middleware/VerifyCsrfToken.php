<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

// Middleware de segurança para prevenir ataques de falsificação de pedidos entre sites (Objetivo 4)
class VerifyCsrfToken extends Middleware
{
    /**
     * Lista de URLs que não exigem a verificação do token de segurança.
     * Útil para integrações externas específicas ou rotas de API controladas.
     * No contexto da Beira Baixa, a rota 'session' está excluída para gerir fluxos de login.
     */
    protected $except = [
        'session',
    ];

    /**
     * Valida se o token enviado pelo navegador coincide com o token guardado no servidor.
     * Implementa monitorização extra para diagnosticar falhas de segurança em ambiente de produção.
     */
    protected function tokensMatch($request)
    {
        // Verifica se os tokens coincidem através da lógica padrão da framework
        $matches = parent::tokensMatch($request);

        // Se houver uma falha na validação na rota de sessão, regista os detalhes técnicos no log
        if (! $matches && $request->is('session')) {
            logger()->warning('Falha de token CSRF em /session', [
                'tem_sessao' => $request->hasSession(),
                'tamanho_token_sessao' => strlen(optional($request->session())->token() ?? ''),
                'tamanho_token_pedido' => strlen((string) $this->getTokenFromRequest($request)),
                'url' => $request->fullUrl(),
                'ip' => $request->ip(),
                'navegador' => substr((string) $request->userAgent(), 0, 120),
            ]);
        }

        return $matches;
    }
}