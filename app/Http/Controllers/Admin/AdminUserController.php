<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\WelcomeNewUserMail;
use App\Models\Agrupamento;
use App\Models\Freguesia;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    /**
     * Lista os utilizadores com os dados necessários para o ecrã da administração.
     */
    public function index(): View
    {
        $users = User::with(['freguesia.concelho', 'agrupamento.concelho'])
            ->orderByDesc('created_at')
            ->paginate(10);

        $freguesias = Freguesia::with('concelho')
            ->orderBy('nome')
            ->get();

        $agrupamentos = Agrupamento::with('concelho')
            ->orderBy('nome')
            ->get();

        return view('laravel-examples.user-management', [
            'users' => $users,
            'freguesias' => $freguesias,
            'agrupamentos' => $agrupamentos,
        ]);
    }

    /**
     * Cria um novo utilizador.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validateWithBag('createUser', [
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'telemovel' => ['nullable', 'string', 'max:20'],
            'perfil' => ['required', Rule::in(['admin', 'cimbb', 'freguesia', 'agrupamento'])],
            'freguesia_id' => ['nullable', 'exists:freguesias,id'],
            'agrupamento_id' => ['nullable', 'exists:agrupamentos,id'],
        ]);

        if ($data['perfil'] === 'freguesia' && empty($data['freguesia_id'])) {
            return back()->withErrors([
                'freguesia_id' => 'Selecione uma freguesia para utilizadores do perfil "freguesia".',
            ], 'createUser')->withInput();
        }

        if ($data['perfil'] === 'agrupamento' && empty($data['agrupamento_id'])) {
            return back()->withErrors([
                'agrupamento_id' => 'Selecione um agrupamento para utilizadores deste perfil.',
            ], 'createUser')->withInput();
        }

        $generatedPassword = Str::random(12);

        $novoUtilizador = User::create([
            'nome' => $data['nome'],
            'email' => $data['email'],
            'telemovel' => $data['telemovel'],
            'perfil' => $data['perfil'],
            'freguesia_id' => $data['perfil'] === 'freguesia' ? $data['freguesia_id'] : null,
            'agrupamento_id' => $data['perfil'] === 'agrupamento' ? $data['agrupamento_id'] : null,
            'password' => Hash::make($generatedPassword),
        ]);

        AuditLogger::log('admin_user_create', 'Criou o utilizador '.$novoUtilizador->nome.' (#'.$novoUtilizador->id.').');

        try {
            Mail::to($novoUtilizador->email)->send(new WelcomeNewUserMail($novoUtilizador, $generatedPassword));
        } catch (\Throwable $exception) {
            Log::error('Falha ao enviar email de boas-vindas para o utilizador.', [
                'user_id' => $novoUtilizador->id,
                'exception' => $exception->getMessage(),
            ]);

            return back()->with('warning', 'Utilizador criado, mas ocorreu um erro ao enviar o email de boas-vindas. Atualize a password manualmente e partilhe o acesso.');
        }

        return back()->with('success', 'Utilizador criado e email de boas-vindas enviado com sucesso.');
    }

    /**
     * Atualiza os dados principais de um utilizador.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'telemovel' => ['nullable', 'string', 'max:20'],
            'perfil' => ['required', Rule::in(['admin', 'cimbb', 'freguesia', 'agrupamento'])],
            'freguesia_id' => ['nullable', 'exists:freguesias,id'],
            'agrupamento_id' => ['nullable', 'exists:agrupamentos,id'],
        ]);

        if ($data['perfil'] === 'freguesia' && empty($data['freguesia_id'])) {
            return back()->withErrors([
                'freguesia_id' => 'Selecione uma freguesia para utilizadores do perfil "freguesia".',
            ]);
        }

        if ($data['perfil'] === 'agrupamento' && empty($data['agrupamento_id'])) {
            return back()->withErrors([
                'agrupamento_id' => 'Selecione um agrupamento para utilizadores deste perfil.',
            ]);
        }

        $user->update([
            'nome' => $data['nome'],
            'email' => $data['email'],
            'telemovel' => $data['telemovel'],
            'perfil' => $data['perfil'],
            'freguesia_id' => $data['perfil'] === 'freguesia' ? $data['freguesia_id'] : null,
            'agrupamento_id' => $data['perfil'] === 'agrupamento' ? $data['agrupamento_id'] : null,
        ]);

        AuditLogger::log('admin_user_update', 'Atualizou o utilizador '.$user->nome.' (#'.$user->id.').');

        return back()->with('success', 'Utilizador atualizado com sucesso.');
    }

    /**
     * Permite ao administrador atualizar a password de um utilizador.
     */
    public function updatePassword(Request $request, User $user): RedirectResponse
    {
        $data = $request->validateWithBag('passwordUser', [
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user->update([
            'password' => Hash::make($data['password']),
        ]);

        AuditLogger::log('admin_user_password', 'Alterou a password do utilizador '.$user->nome.' (#'.$user->id.').');

        return back()->with('success', 'Password atualizada com sucesso.');
    }

    /**
     * Remove um utilizador (não permite eliminar o próprio utilizador autenticado).
     */
    public function destroy(User $user): RedirectResponse
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Não pode remover o seu próprio utilizador.');
        }

        $user->delete();

        AuditLogger::log('admin_user_delete', 'Removeu o utilizador '.$user->nome.' (#'.$user->id.').');

        return back()->with('success', 'Utilizador removido com sucesso.');
    }
}
