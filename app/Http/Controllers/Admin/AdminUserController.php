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

// Gestão de contas de utilizadores autorizados para a recolha e análise de dados (Stakeholders)
class AdminUserController extends Controller
{
    // Listagem de utilizadores com carregamento de relações territoriais para auditoria visual
    public function index(): View
    {
        // Obtém utilizadores cruzando dados de freguesias, agrupamentos e respetivos concelhos
        $users = User::with(['freguesia.concelho', 'agrupamento.concelho'])
            ->orderByDesc('created_at')
            ->paginate(10);

        // Carrega entidades geográficas para popular os seletores de criação/edição
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

    // Registo de novo utilizador com geração automática de password e envio de email
    public function store(Request $request): RedirectResponse
    {
        // Validação dos dados de perfil para garantir que cada utilizador está vinculado à sua área de atuação
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

        // Gera uma password segura de 12 caracteres para o primeiro acesso
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

        // Registo de auditoria para controlo de criação de acessos (Requisito de Segurança)
        AuditLogger::log('admin_user_create', 'Criou o utilizador '.$novoUtilizador->nome.' (#'.$novoUtilizador->id.').');

        // Tentativa de envio de credenciais; falha no email não impede a criação, mas gera aviso
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

    // Atualização de perfil e dados de contacto de utilizadores existentes
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

        // Revalida a obrigatoriedade de vínculo territorial conforme o perfil selecionado
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

        // Log de atualização para rastreabilidade de alterações em permissões
        AuditLogger::log('admin_user_update', 'Atualizou o utilizador '.$user->nome.' (#'.$user->id.').');

        return back()->with('success', 'Utilizador atualizado com sucesso.');
    }

    // Método administrativo para reset forçado de password
    public function updatePassword(Request $request, User $user): RedirectResponse
    {
        $data = $request->validateWithBag('passwordUser', [
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user->update([
            'password' => Hash::make($data['password']),
        ]);

        // Registo crítico de segurança: alteração de credenciais por terceiros
        AuditLogger::log('admin_user_password', 'Alterou a password do utilizador '.$user->nome.' (#'.$user->id.').');

        return back()->with('success', 'Password atualizada com sucesso.');
    }

    // Remoção de conta de utilizador com salvaguarda para evitar auto-eliminação
    public function destroy(User $user): RedirectResponse
    {
        // Impede que o administrador remova a sua própria conta, evitando bloqueio de acesso ao sistema
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Não pode remover o seu próprio utilizador.');
        }

        $user->delete();

        // Audit log para manter o histórico de gestão de acessos do portal
        AuditLogger::log('admin_user_delete', 'Removeu o utilizador '.$user->nome.' (#'.$user->id.').');

        return back()->with('success', 'Utilizador removido com sucesso.');
    }
}