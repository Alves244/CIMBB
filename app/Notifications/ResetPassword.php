<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordBase;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPassword extends ResetPasswordBase
{
    /**
     * Get the reset password notification mail message for the given URL.
     */
    protected function buildMailMessage($url)
    {
        return (new MailMessage)
            ->subject('Redefinição de Password - CIMBB')
            ->greeting('Olá!')
            ->line('Recebemos um pedido para redefinir a password da sua conta.')
            ->action('Redefinir Password', $url)
            ->line('Este link irá expirar em 60 minutos.')
            ->line('Se não solicitou a redefinição, ignore este e-mail. Nenhuma ação adicional é necessária.')
            ->salutation('Cumprimentos, Comunidade Intermunicipal da Beira Baixa');
    }
}
