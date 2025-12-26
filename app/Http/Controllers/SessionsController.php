<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Services\AuditLogger;

class SessionsController extends Controller
{
    /**
     * Atualiza a senha do usuário após o reset.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = \Illuminate\Support\Facades\Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = bcrypt($password);
                $user->save();
            }
        );

        return $status === \Illuminate\Support\Facades\Password::PASSWORD_RESET
            ? redirect('/login')->with('success', 'Senha redefinida com sucesso!')
            : back()->withErrors(['email' => [__($status)]]);
    }
    /**
     * Mostra o formulário de login.
     */
    public function create()
    {
        return view('session.login-session');
    }

    /**
     * Processa o login.
     */
    public function store()
    {
        $attributes = request()->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($attributes)) {
            session()->regenerate();
            AuditLogger::log('login', 'Sessão iniciada.');
            
            return redirect('dashboard')->with(['success' => 'Sessão iniciada com sucesso.']);
        }
        
        return back()->withErrors(['email' => 'Email ou password incorretos.']);
    }

    /**
     * Faz o logout (Terminar Sessão).
     */
    public function destroy()
    {
        AuditLogger::log('logout', 'Sessão terminada.');
        Auth::logout();

        return redirect('/login')->with(['success' => 'Sessão terminada.']);
    }
}