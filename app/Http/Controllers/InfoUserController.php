<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Services\AuditLogger;

class InfoUserController extends Controller
{
    /**
     * Mostra o formulário de edição de perfil.
     */
    public function create()
    {
        if (view()->exists('profile')) {
            return view('profile');
        }
        // Fallback para o nome de template antigo
        return view('laravel-examples.user-profile');
    }

    /**
     * Atualiza a informação do utilizador.
     * (Apenas para o telemóvel)
     */
    public function store(Request $request)
    {
        // 1. Validar apenas o telemóvel
        $validatedData = $request->validate([
            'telemovel' => 'nullable|digits:9', 
        ]);

        /** @var \App\Models\User $user */ 
        $user = Auth::user();

        // 2. Atualizar apenas o telemóvel
        $user->update([
            'telemovel' => $validatedData['telemovel'],
        ]);

        AuditLogger::log('profile_update', 'Atualizou o telemóvel no perfil.');

        return back()->with('success', 'Telemóvel atualizado com sucesso!');
    }


    /**
     * Atualiza a palavra-passe do utilizador.
     */
    public function updatePassword(Request $request)
    {
        // 1. Validar os dados
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        // 2. Verificar se a password atual está correta
        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return back()->withErrors(['current_password' => 'A palavra-passe atual está incorreta.']);
        }

        /** @var \App\Models\User $user */ 
        $user = auth()->user();

        // 3. Atualizar a password
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        AuditLogger::log('profile_password', 'Alterou a palavra-passe através do perfil.');

        return back()->with('success_password', 'Palavra-passe alterada com sucesso!');
    }
}