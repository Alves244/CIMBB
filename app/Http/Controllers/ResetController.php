<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\View;

/**
 * Controlador para o reset de password.
 */
class ResetController extends Controller
{
    // Mostra o formulário para solicitar o email de reset de password
    public function create()
    {
        return view('session/reset-password/sendEmail');
    }

    // Envia o email de reset de password
    public function sendEmail(Request $request)
    {
        // Verifica se está em modo demo
        if(env('IS_DEMO'))
        {
            return redirect()->back()->withErrors(['msg2' => 'You are in a demo version, you can\'t recover your password.']);
        }
        else{
            $request->validate(['email' => 'required|email']);

            $status = Password::sendResetLink(
                $request->only('email')
            );
            // Retorna a resposta adequada com base no status do envio do link
            return $status === Password::RESET_LINK_SENT
                        ? back()->with(['success' => __($status)])
                        : back()->withErrors(['email' => __($status)]);
        }
    }
    // Mostra o formulário para resetar a password
    public function resetPass($token)
    {
        return view('session/reset-password/resetPassword', ['token' => $token]);
    }
}
