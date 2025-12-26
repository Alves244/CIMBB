<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\View;

// Controlador responsável por gerir a recuperação de palavras-passe esquecidas
class ResetController extends Controller
{
    // Apresenta a vista para o utilizador introduzir o email de recuperação
    public function create()
    {
        return view('session/reset-password/sendEmail');
    }

    // Processa o pedido de envio do link de recuperação para o email do utilizador
    public function sendEmail(Request $request)
    {
        // Verifica se o sistema está em modo de demonstração para restringir alterações
        if(env('IS_DEMO'))
        {
            return redirect()->back()->withErrors(['msg2' => 'You are in a demo version, you can\'t recover your password.']);
        }
        else{
            // Valida se o formato do email introduzido é válido antes de prosseguir
            $request->validate(['email' => 'required|email']);

            // Solicita ao sistema o envio do token de recuperação (Salvaguarda de segurança)
            $status = Password::sendResetLink(
                $request->only('email')
            );

            // Retorna feedback ao utilizador sobre o sucesso ou erro no envio do link
            return $status === Password::RESET_LINK_SENT
                        ? back()->with(['success' => __($status)])
                        : back()->withErrors(['email' => __($status)]);
        }
    }

    // Apresenta o formulário final para a definição da nova palavra-passe
    public function resetPass($token)
    {
        // Recebe o token único gerado para garantir a legitimidade da alteração
        return view('session/reset-password/resetPassword', ['token' => $token]);
    }
}