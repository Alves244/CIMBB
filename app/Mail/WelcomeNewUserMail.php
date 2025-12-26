<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

// Classe responsável pelo envio do email de boas-vindas e credenciais de acesso
class WelcomeNewUserMail extends Mailable
{
    // Traits que permitem o envio em fila (Background) para não bloquear a interface
    use Queueable, SerializesModels;

    public User $user;
    public string $plainPassword;
    public string $loginUrl;

    // Construtor que recebe o utilizador criado e a password temporária gerada
    public function __construct(User $user, string $plainPassword)
    {
        $this->user = $user;
        $this->plainPassword = $plainPassword;
        // Gera automaticamente o link para o portal web institucional
        $this->loginUrl = route('login');
    }

    // Constrói a mensagem de email utilizando um template visual
    public function build(): self
    {
        return $this->subject('Bem-vindo à plataforma CIMBB')
            ->view('emails.users.welcome') // Aponta para a vista HTML do email
            ->with([
                'user' => $this->user,
                'password' => $this->plainPassword,
                'loginUrl' => $this->loginUrl,
            ]);
    }
}