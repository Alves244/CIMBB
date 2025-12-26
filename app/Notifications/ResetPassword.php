<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordBase;
use Illuminate\Notifications\Messages\MailMessage;

// Notificação personalizada para recuperação de acesso ao portal (Objetivo 4)
class ResetPassword extends ResetPasswordBase
{
    /**
     * Constrói a mensagem de e-mail personalizada em português.
     * Garante que a comunicação institucional da CIMBB é clara e segura.
     */
    protected function buildMailMessage($url)
    {
        return (new MailMessage)
            // Assunto focado na identidade institucional (CIMBB)
            ->subject('Redefinição de Password - CIMBB')
            ->greeting('Olá!')
            // Mensagens claras para evitar erros de interpretação por parte dos técnicos
            ->line('Recebemos um pedido para redefinir a password da sua conta.')
            // Botão de ação que utiliza o token de segurança gerado pelo Laravel
            ->action('Redefinir Password', $url)
            // Definição de limite temporal para aumentar a segurança do acesso
            ->line('Este link irá expirar em 60 minutos.')
            ->line('Se não solicitou a redefinição, ignore este e-mail. Nenhuma ação adicional é necessária.')
            // Identificação oficial da entidade regional
            ->salutation('Cumprimentos, Comunidade Intermunicipal da Beira Baixa');
    }
}