<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordBase;
use Illuminate\Notifications\Messages\MailMessage;

// Notificação padronizada para a recuperação de credenciais no ecossistema CIMBB
class ResetPasswordNotification extends ResetPasswordBase
{
    /**
     * Constrói o corpo do e-mail de recuperação.
     * Fundamental para o Objetivo 4: Garantir o acesso seguro dos utilizadores autorizados.
     */
    protected function buildMailMessage($url)
    {
        return (new MailMessage)
            // Assunto formal que identifica a origem do pedido
            ->subject('Redefinição de Password - CIMBB')
            ->greeting('Olá!')
            // Explicação clara do motivo do contacto
            ->line('Recebemos um pedido para redefinir a password da sua conta.')
            // Botão dinâmico que contém o token de segurança único
            ->action('Redefinir Password', $url)
            // Medida de segurança: expiração do link para evitar utilização maliciosa posterior
            ->line('Este link irá expirar em 60 minutos.')
            ->line('Se não solicitou a redefinição, ignore este e-mail. Nenhuma ação adicional é necessária.')
            // Assinatura institucional que reforça a confiança no portal
            ->salutation('Cumprimentos, Comunidade Intermunicipal da Beira Baixa');
    }
}