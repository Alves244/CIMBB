<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeNewUserMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $plainPassword;
    public string $loginUrl;

    public function __construct(User $user, string $plainPassword)
    {
        $this->user = $user;
        $this->plainPassword = $plainPassword;
        $this->loginUrl = route('login');
    }

    public function build(): self
    {
        return $this->subject('Bem-vindo à plataforma CIMBB')
            ->view('emails.users.welcome')
            ->with([
                'user' => $this->user,
                'password' => $this->plainPassword,
                'loginUrl' => $this->loginUrl,
            ]);
    }
}
