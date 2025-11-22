<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User; // Importar o Model User
use App\Services\AuditLogger;

class ChangePasswordController extends Controller
{
    public function changePassword(Request $request)
    {
        // 1. Validação dos campos
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed', // O campo de confirmação deve chamar-se 'password_confirmation' no HTML
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 2. Verificar se a password atual está correta
        if (!Hash::check($request->get('current_password'), $user->password)) {
            return back()->withErrors(['current_password' => 'A password atual não está correta.']);
        }

        // 3. Atualizar para a nova password
        // NOTA: Se na tua base de dados a coluna se chamar 'password_hash' (como no diagrama), 
        // muda 'password' para 'password_hash' na linha abaixo. 
        // Se usaste o padrão do Laravel, mantém 'password'.
        $user->update([
            'password' => Hash::make($request->get('password'))
        ]);

        AuditLogger::log('profile_password', 'Alterou a password através da página de perfil.');

        return back()->with('success', 'Password alterada com sucesso!');
    }
}