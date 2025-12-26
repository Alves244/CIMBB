<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SendCredentials extends Notification
{
    private $email;
    private $password;

    public function __construct($email, $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Credenciais de acesso - CIMBB')
            ->greeting('Olá!')
            ->line('A sua conta foi criada com sucesso no sistema CIMBB.')
            ->line('Credenciais de acesso:')
            ->line('E-mail: ' . $this->email)
            ->line('Password: ' . $this->password)
            ->line('Recomendamos que altere a sua password após o primeiro acesso.')
            ->salutation('Cumprimentos, Comunidade Intermunicipal da Beira Baixa');
    }
}
