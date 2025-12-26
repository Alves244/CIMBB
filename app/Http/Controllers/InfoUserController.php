<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Services\AuditLogger;

// Gere as informações de perfil e segurança dos utilizadores autorizados (Objetivo 14)
class InfoUserController extends Controller
{
    // Apresenta a interface de gestão de perfil para os utilizadores do sistema
    public function create()
    {
        // Verifica a existência da view para garantir a consistência do portal web
        if (view()->exists('profile')) {
            return view('profile');
        }
        // Fallback para manter a compatibilidade com estruturas de template anteriores
        return view('laravel-examples.user-profile');
    }

    // Atualiza os dados de contacto, permitindo manter a base de informação atualizada (Objetivo 10)
    public function store(Request $request)
    {
        // Validação do formato de telemóvel para assegurar a consistência dos dados (Objetivo 15)
        $validatedData = $request->validate([
            'telemovel' => 'nullable|digits:9', 
        ]);

        /** @var \App\Models\User $user */ 
        $user = Auth::user();

        // Persistência da atualização dos dados de contacto no perfil do utilizador
        $user->update([
            'telemovel' => $validatedData['telemovel'],
        ]);

        // Registo de auditoria para monitorização de alterações no sistema (Objetivo 23)
        AuditLogger::log('profile_update', 'Atualizou o telemóvel no perfil.');

        return back()->with('success', 'Telemóvel atualizado com sucesso!');
    }


    // Implementa a salvaguarda de segurança através da gestão de palavras-passe (Objetivo 23)
    public function updatePassword(Request $request)
    {
        // Define requisitos mínimos de segurança para a nova palavra-passe
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        // Camada de segurança: validação da identidade antes de permitir a alteração
        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return back()->withErrors(['current_password' => 'A palavra-passe atual está incorreta.']);
        }

        /** @var \App\Models\User $user */ 
        $user = auth()->user();

        // Encriptação da nova credencial para armazenamento seguro em servidor institucional (Objetivo 25)
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        // Log de segurança para rastreabilidade de acessos e modificações (Objetivo 23)
        AuditLogger::log('profile_password', 'Alterou a palavra-passe através do perfil.');

        return back()->with('success_password', 'Palavra-passe alterada com sucesso!');
    }
}