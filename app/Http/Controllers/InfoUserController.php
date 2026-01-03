<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Services\AuditLogger;

/**
 * Controlador para gestão da informação do utilizador.
 */
class InfoUserController extends Controller
{
    // Mostra o formulário de edição do perfil do utilizador
    public function create()
    {
        // Verifica se a vista 'profile' existe
        if (view()->exists('profile')) {
            return view('profile');
        }
        // Caso contrário, mostra a vista padrão
        return view('laravel-examples.user-profile');
    }

    // Atualiza a informação do perfil do utilizador
    public function store(Request $request)
    {
        // Validar os dados recebidos
        $validatedData = $request->validate([
            'telemovel' => 'nullable|digits:9', 
        ]);

        /** @var \App\Models\User $user */ 
        $user = Auth::user();

        // Atualiza o telemóvel do utilizador
        $user->update([
            'telemovel' => $validatedData['telemovel'],
        ]);
        // Regista a ação no log de auditoria
        AuditLogger::log('profile_update', 'Atualizou o telemóvel no perfil.');
        // Redireciona de volta com uma mensagem de sucesso
        return back()->with('success', 'Telemóvel atualizado com sucesso!');
    }


    // Atualiza a password do utilizador
    public function updatePassword(Request $request)
    {
        // Validar os dados recebidos
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        // Verifica se a password atual está correta
        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return back()->withErrors(['current_password' => 'A palavra-passe atual está incorreta.']);
        }

        /** @var \App\Models\User $user */ 
        $user = auth()->user();

        // Atualiza a nova password
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);
        // Regista a ação no log de auditoria
        AuditLogger::log('profile_password', 'Alterou a palavra-passe através do perfil.');
        // Redireciona de volta com uma mensagem de sucesso
        return back()->with('success_password', 'Palavra-passe alterada com sucesso!');
    }
}