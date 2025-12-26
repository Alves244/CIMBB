<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

// Notificação responsável pela entrega segura das credenciais de acesso (Objetivo 17)
class SendCredentials extends Notification
{
    private $email;
    private $password;

    /**
     * Recebe os dados de acesso gerados pelo administrador no momento da criação da conta.
     */
    public function __construct($email, $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    /**
     * Define o canal de envio (neste caso, correio eletrónico).
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Constrói o corpo do e-mail com as diretrizes de segurança (Objetivo 4).
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            // Identifica claramente o portal institucional da CIMBB
            ->subject('Credenciais de acesso - CIMBB')
            ->greeting('Olá!')
            ->line('A sua conta foi criada com sucesso no sistema CIMBB.')
            ->line('Credenciais de acesso:')
            // Fornece os dados necessários para o login inicial
            ->line('E-mail: ' . $this->email)
            ->line('Password: ' . $this->password)
            /**
             * Recomendação de Segurança: Vital para a conformidade com boas práticas 
             * e proteção de dados de residentes estrangeiros.
             */
            ->line('Recomendamos que altere a sua password após o primeiro acesso.')
            ->salutation('Cumprimentos, Comunidade Intermunicipal da Beira Baixa');
    }
}