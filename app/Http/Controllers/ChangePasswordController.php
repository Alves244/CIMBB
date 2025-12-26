<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User; 
use App\Services\AuditLogger;

// Controlador para gestão de segurança de conta pelo próprio utilizador
class ChangePasswordController extends Controller
{
    // Processa a alteração de credenciais para garantir a proteção do acesso ao portal
    public function changePassword(Request $request)
    {
        // Validação rigorosa para garantir a força da nova password e evitar erros de escrita
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed', 
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Camada de segurança: validação da identidade através da password atual antes da mudança
        if (!Hash::check($request->get('current_password'), $user->password)) {
            return back()->withErrors(['current_password' => 'A password atual não está correta.']);
        }

        // Atualização da credencial na base de dados usando hashing seguro
        $user->update([
            'password' => Hash::make($request->get('password'))
        ]);

        // Registo de auditoria para monitorização de alterações de segurança (Objetivo 4)
        AuditLogger::log('profile_password', 'Alterou a password através da página de perfil.');

        return back()->with('success', 'Password alterada com sucesso!');
    }
}