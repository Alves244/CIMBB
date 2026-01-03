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

/**
 * Controlador para gestão administrativa de utilizadores.
 */
class AdminUserController extends Controller
{
    // Listagem paginada de utilizadores com dados geográficos associados
    public function index(): View
    {
        // Consulta dos utilizadores com relacionamentos necessários
        $users = User::with(['freguesia.concelho', 'agrupamento.concelho'])
            ->orderByDesc('created_at')
            ->paginate(10);

        // Consulta de freguesias e agrupamentos para filtros e seleções
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

    // Criação de novo utilizador com validação e envio de credenciais
    public function store(Request $request): RedirectResponse
    {
        // Validação dos dados de entrada
        $data = $request->validateWithBag('createUser', [
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'telemovel' => ['nullable', 'string', 'max:20'],
            'perfil' => ['required', Rule::in(['admin', 'cimbb', 'freguesia', 'agrupamento'])],
            'freguesia_id' => ['nullable', 'exists:freguesias,id'],
            'agrupamento_id' => ['nullable', 'exists:agrupamentos,id'],
        ]);

        // Verificação de integridade: utilizadores de freguesia devem ter uma freguesia associada
        if ($data['perfil'] === 'freguesia' && empty($data['freguesia_id'])) {
            return back()->withErrors([
                'freguesia_id' => 'Selecione uma freguesia para utilizadores do perfil "freguesia".',
            ], 'createUser')->withInput();
        }

        // Verificação de integridade: utilizadores de agrupamento devem ter um agrupamento associado
        if ($data['perfil'] === 'agrupamento' && empty($data['agrupamento_id'])) {
            return back()->withErrors([
                'agrupamento_id' => 'Selecione um agrupamento para utilizadores deste perfil.',
            ], 'createUser')->withInput();
        }

        // Geração de password temporária segura
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

        // Log de criação para rastreabilidade administrativa
        AuditLogger::log('admin_user_create', 'Criou o utilizador '.$novoUtilizador->nome.' (#'.$novoUtilizador->id.').');

        // Envio de email com credenciais ao novo utilizador
        try {
            $novoUtilizador->notify(new \App\Notifications\SendCredentials($novoUtilizador->email, $generatedPassword));
        } catch (\Throwable $exception) {
            Log::error('Falha ao enviar email de credenciais para o utilizador.', [
                'user_id' => $novoUtilizador->id,
                'exception' => $exception->getMessage(),
            ]);

            return back()->with('warning', 'Utilizador criado, mas ocorreu um erro ao enviar o email de credenciais. Atualize a password manualmente e partilhe o acesso.');
        }

        return back()->with('success', 'Utilizador criado e email de boas-vindas enviado com sucesso.');
    }

    // Atualização de dados de utilizador com validação
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

        // Verificação de integridade: utilizadores de freguesia devem ter uma freguesia associada
        if ($data['perfil'] === 'freguesia' && empty($data['freguesia_id'])) {
            return back()->withErrors([
                'freguesia_id' => 'Selecione uma freguesia para utilizadores do perfil "freguesia".',
            ]);
        }

        // Verificação de integridade: utilizadores de agrupamento devem ter um agrupamento associado
        if ($data['perfil'] === 'agrupamento' && empty($data['agrupamento_id'])) {
            return back()->withErrors([
                'agrupamento_id' => 'Selecione um agrupamento para utilizadores deste perfil.',
            ]);
        }

        // Atualização dos dados do utilizador
        $user->update([
            'nome' => $data['nome'],
            'email' => $data['email'],
            'telemovel' => $data['telemovel'],
            'perfil' => $data['perfil'],
            'freguesia_id' => $data['perfil'] === 'freguesia' ? $data['freguesia_id'] : null,
            'agrupamento_id' => $data['perfil'] === 'agrupamento' ? $data['agrupamento_id'] : null,
        ]);

        // Registo da atualização no histórico para rastreabilidade
        AuditLogger::log('admin_user_update', 'Atualizou o utilizador '.$user->nome.' (#'.$user->id.').');

        return back()->with('success', 'Utilizador atualizado com sucesso.');
    }

    // Atualização de password de utilizador com validação
    public function updatePassword(Request $request, User $user): RedirectResponse
    {
        $data = $request->validateWithBag('passwordUser', [
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user->update([
            'password' => Hash::make($data['password']),
        ]);

        // Registro da alteração de password no histórico para segurança
        AuditLogger::log('admin_user_password', 'Alterou a password do utilizador '.$user->nome.' (#'.$user->id.').');

        return back()->with('success', 'Password atualizada com sucesso.');
    }

    // Remoção de utilizador com verificações de segurança
    public function destroy(User $user): RedirectResponse
    {
        // Prevenção de auto-removal para manter o acesso administrativo
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Não pode remover o seu próprio utilizador.');
        }

        $user->delete();

        // Registo da remoção no histórico para rastreabilidade
        AuditLogger::log('admin_user_delete', 'Removeu o utilizador '.$user->nome.' (#'.$user->id.').');

        return back()->with('success', 'Utilizador removido com sucesso.');
    }
}