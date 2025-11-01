<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class InfoUserController extends Controller
{
    /**
     * Mostra o formulário de edição de perfil.
     */
    public function create()
    {
        return view('laravel-examples.user-profile');
    }

    /**
     * Atualiza a informação do utilizador.
     */
    public function store(Request $request)
    {
        // Validação dos dados recebidos do formulário
        $validatedData = $request->validate([
            
            // ALTERAÇÃO AQUI: max:255 mudou para max:15
            'nome' => 'required|string|max:25',
            
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore(Auth::id()),
            ],

            // ALTERAÇÃO AQUI: max:20 mudou para digits:9
            // 'digits:9' força a que tenha exatamente 9 caracteres E que sejam todos numéricos.
            'telemovel' => 'nullable|digits:9', 
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Atualizar o utilizador autenticado com os novos dados
        $user->update([
            'nome' => $validatedData['nome'],
            'email' => $validatedData['email'],
            'telemovel' => $validatedData['telemovel'],
        ]);

        // Redirecionar para trás com uma mensagem de sucesso
        return back()->with('success', 'Perfil atualizado com sucesso!');
    }
}