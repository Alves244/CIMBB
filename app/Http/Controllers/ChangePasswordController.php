<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User; // Importar o Model User
use App\Services\AuditLogger;

/**
 * Controlador para alteração de password do utilizador autenticado.
 */
class ChangePasswordController extends Controller
{
    
    public function changePassword(Request $request)
    {
        // 1. Validação dos campos
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 2. Verificação da password atual
        if (!Hash::check($request->get('current_password'), $user->password)) {
            return back()->withErrors(['current_password' => 'A password atual não está correta.']);
        }

        // 3. Atualização da password
        $user->update([
            'password' => Hash::make($request->get('password'))
        ]);

        AuditLogger::log('profile_password', 'Alterou a password através da página de perfil.');

        return back()->with('success', 'Password alterada com sucesso!');
    }
}