<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Responsável pelo registo inicial de utilizadores no sistema de monitorização
class RegisterController extends Controller
{
    // Apresenta o formulário de criação de conta para novos técnicos ou parceiros
    public function create()
    {
        return view('session.register');
    }

    // Processa a gravação do novo utilizador, assegurando a integridade dos dados de acesso
    public function store()
    {
        // Valida as credenciais conforme os requisitos de segurança do projeto [cite: 23]
        $attributes = request()->validate([
            'name' => ['required', 'max:50'],
            'email' => ['required', 'email', 'max:50', Rule::unique('users', 'email')],
            'password' => ['required', 'min:5', 'max:20'],
            'agreement' => ['accepted'] // Garante a aceitação dos termos de uso e privacidade
        ]);
        
        // Encripta a password antes do armazenamento em servidores institucionais [cite: 25, 26]
        $attributes['password'] = bcrypt($attributes['password'] );

        // Cria o registo na base de dados e informa o utilizador sobre o sucesso da operação
        session()->flash('success', 'Your account has been created.');
        $user = User::create($attributes);
        
        // Efetua o login automático para agilizar o acesso à ferramenta dinâmica [cite: 10]
        Auth::login($user); 
        
        // Redireciona para o painel principal de análise e recolha de dados [cite: 14]
        return redirect('/dashboard');
    }
}