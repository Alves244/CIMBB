<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controlador para o registo de novos utilizadores.
 */
class RegisterController extends Controller
{
    // Mostra o formulário de registo
    public function create()
    {
        return view('session.register');
    }

    // Processa o registo do utilizador
    public function store()
    {
        // Validar os dados do formulário de registo
        $attributes = request()->validate([
            'name' => ['required', 'max:50'],
            'email' => ['required', 'email', 'max:50', Rule::unique('users', 'email')],
            'password' => ['required', 'min:5', 'max:20'],
            'agreement' => ['accepted']
        ]);
        $attributes['password'] = bcrypt($attributes['password'] );

        // Criar o utilizador, iniciar sessão e redirecionar para o dashboard
        session()->flash('success', 'Your account has been created.');
        $user = User::create($attributes);
        Auth::login($user); 
        return redirect('/dashboard');
    }
}
