<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Services\AuditLogger;

/**
 * Controlador para a gestão de sessões (login, logout e reset de password).
 */
class SessionsController extends Controller
{
    // Mostra o formulário para resetar a password
    public function reset(Request $request)
    {
        // Validação dos campos
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        // Tenta resetar a password
        $status = \Illuminate\Support\Facades\Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = bcrypt($password);
                $user->save();
            }
        );
        // Retorna a resposta adequada com base no status do reset
        return $status === \Illuminate\Support\Facades\Password::PASSWORD_RESET
            ? redirect('/login')->with('success', 'Senha redefinida com sucesso!')
            : back()->withErrors(['email' => [__($status)]]);
    }

    // Mostra o formulário de login
    public function create()
    {
        return view('session.login-session');
    }

    // Processa o login (Iniciar Sessão)
    public function store()
    {
        // Validação dos campos
        $attributes = request()->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        // Tenta autenticar o utilizador
        if (Auth::attempt($attributes)) {
            session()->regenerate();
            AuditLogger::log('login', 'Sessão iniciada.');
            
            return redirect('dashboard')->with(['success' => 'Sessão iniciada com sucesso.']);
        }
        
        return back()->withErrors(['email' => 'Email ou password incorretos.']);
    }

    // Processa o logout (Terminar Sessão)
    public function destroy()
    {
        AuditLogger::log('logout', 'Sessão terminada.');
        Auth::logout();

        return redirect('/login')->with(['success' => 'Sessão terminada.']);
    }
}