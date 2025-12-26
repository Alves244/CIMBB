<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Services\AuditLogger;

// Controlador responsável pela gestão de autenticação e integridade das sessões dos utilizadores
class SessionsController extends Controller
{
    // Finaliza o processo de recuperação de acesso, atualizando a credencial no servidor [cite: 25, 26]
    public function reset(Request $request)
    {
        // Valida os requisitos mínimos para a nova password e o token de segurança 
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        // Executa a redefinição utilizando o broker de passwords do Laravel [cite: 26]
        $status = \Illuminate\Support\Facades\Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = bcrypt($password);
                $user->save();
            }
        );

        // Redireciona conforme o sucesso da operação, garantindo feedback ao utilizador 
        return $status === \Illuminate\Support\Facades\Password::PASSWORD_RESET
            ? redirect('/login')->with('success', 'Senha redefinida com sucesso!')
            : back()->withErrors(['email' => [__($status)]]);
    }

    // Apresenta a interface de entrada no portal para os stakeholders autorizados 
    public function create()
    {
        return view('session.login-session');
    }

    // Processa a tentativa de autenticação e inicia a monitorização da sessão [cite: 15]
    public function store()
    {
        // Valida as credenciais enviadas através do portal web 
        $attributes = request()->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Tenta autenticar o utilizador contra os registos institucionais [cite: 25]
        if (Auth::attempt($attributes)) {
            // Regenera a sessão para prevenir ataques de fixação (Segurança) 
            session()->regenerate();
            
            // Regista o evento de entrada para fins de auditoria e segurança 
            AuditLogger::log('login', 'Sessão iniciada.');
            
            return redirect('dashboard')->with(['success' => 'Sessão iniciada com sucesso.']);
        }
        
        // Retorna erro caso as credenciais não permitam o acesso aos dados [cite: 15]
        return back()->withErrors(['email' => 'Email ou password incorretos.']);
    }

    // Termina a sessão do utilizador, garantindo a proteção dos dados após o uso 
    public function destroy()
    {
        // Regista a saída do utilizador no log de auditoria do sistema 
        AuditLogger::log('logout', 'Sessão terminada.');
        
        // Encerra a autenticação e limpa o estado da sessão no servidor [cite: 25, 26]
        Auth::logout();

        return redirect('/login')->with(['success' => 'Sessão terminada.']);
    }
}