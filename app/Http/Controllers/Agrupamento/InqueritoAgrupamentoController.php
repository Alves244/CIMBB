<?php

namespace App\Http\Controllers\Agrupamento;

use App\Http\Controllers\Controller;
use App\Models\InqueritoAgrupamento;
use App\Models\InqueritoAgrupamentoRegisto;
use App\Models\InqueritoPeriodo;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

// Responsável pela submissão e consulta de inquéritos por parte dos Agrupamentos de Escolas
class InqueritoAgrupamentoController extends Controller
{
    // Definição dos parâmetros qualitativos de ensino conforme as diretrizes dos stakeholders
    private array $niveisEnsino = [
        'Pré-Escolar',
        '1.º Ciclo do EB',
        '2.º Ciclo do EB',
        '3.º Ciclo do EB',
        'Ensino Secundário',
        'Ensino Profissional',
        'Ensino Superior',
    ];

    // Dashboard inicial do agrupamento para gestão dos seus inquéritos submetidos
    public function index(): View
    {
        $user = Auth::user();
        $agrupamentoId = $user->agrupamento_id;
        
        // Verifica se existe uma janela de recolha ativa definida pela CIMBB
        $periodoAtual = InqueritoPeriodo::periodoAtivo(InqueritoPeriodo::TIPO_AGRUPAMENTO);
        $anoAtual = $periodoAtual?->ano ?? (int) date('Y');

        // Obtém o histórico de participações do agrupamento para monitorização temporal
        $inqueritos = InqueritoAgrupamento::withCount('registos')
            ->where('agrupamento_id', $agrupamentoId)
            ->orderByDesc('ano_referencia')
            ->get();

        // Controlos de interface baseados nos prazos e submissões anteriores
        $jaPreencheuEsteAno = $inqueritos->contains('ano_referencia', $anoAtual);
        $dataLimite = $periodoAtual?->fecha_em ?? now()->copy()->setDate($anoAtual, 12, 31)->endOfDay();
        $dentroDoPrazo = $periodoAtual?->estaAberto() ?? false;

        return view('agrupamento.inqueritos.index', [
            'inqueritos' => $inqueritos,
            'anoAtual' => $anoAtual,
            'jaPreencheuEsteAno' => $jaPreencheuEsteAno,
            'dentroDoPrazo' => $dentroDoPrazo,
            'dataLimite' => $dataLimite,
            'periodoAtual' => $periodoAtual,
        ]);
    }

    // Prepara o formulário de recolha de dados para o período anual em curso
    public function create(): View
    {
        $user = Auth::user();
        $agrupamentoId = $user->agrupamento_id;
        $periodoAtivo = InqueritoPeriodo::periodoAtivo(InqueritoPeriodo::TIPO_AGRUPAMENTO);

        // Bloqueio de segurança: impede o acesso se o período de recolha não estiver aberto
        abort_if(! $periodoAtivo, 403, 'Não existe período aberto para submissão. Contacte a CIMBB.');

        $anoAtual = $periodoAtivo->ano;
        $agrupamento = $user->agrupamento?->load('concelho');

        // Validação de integridade: exige vínculo territorial para correta análise de impacto local
        abort_if(! $agrupamento, 403, 'Não foi possível identificar o seu agrupamento.');
        abort_if(! $agrupamento->concelho, 403, 'Associe um concelho ao agrupamento antes de submeter o inquérito.');

        // Garante que o agrupamento apenas submete uma participação por ciclo anual
        $jaPreencheu = InqueritoAgrupamento::where('agrupamento_id', $agrupamentoId)
            ->where('ano_referencia', $anoAtual)
            ->exists();

        abort_if($jaPreencheu, 403, 'O inquérito deste ano já foi submetido.');

        return view('agrupamento.inqueritos.create', [
            'anoAtual' => $anoAtual,
            'concelhoNome' => $agrupamento->concelho->nome,
            'concelhoId' => $agrupamento->concelho_id,
            'niveisEnsino' => $this->niveisEnsino,
            'nacionalidades' => config('nacionalidades', []),
            'periodoAtivo' => $periodoAtivo,
        ]);
    }

    // Processa a submissão dos dados quantitativos sobre a população estrangeira escolar
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $agrupamentoId = $user->agrupamento_id;
        $periodoAtivo = InqueritoPeriodo::periodoAtivo(InqueritoPeriodo::TIPO_AGRUPAMENTO);

        if (! $periodoAtivo) {
            return redirect()->route('agrupamento.inqueritos.index')->with('error', 'Não existe período aberto para submissão.');
        }

        $agrupamento = $user->agrupamento?->load('concelho');

        if (! $agrupamento || ! $agrupamento->concelho) {
            return redirect()->route('agrupamento.inqueritos.index')->with('error', 'Associe um concelho ao agrupamento antes de submeter o inquérito.');
        }

        // Validação da estrutura de dados para assegurar a consistência da informação recolhida
        $validated = $request->validate([
            'ano_referencia' => ['required', 'integer', Rule::in([$periodoAtivo->ano])],
            'registos' => ['required', 'array', 'min:1'],
            'registos.*.nacionalidade' => ['required', 'string', 'max:120'],
            'registos.*.ano_letivo' => ['required', 'string', 'max:15'],
            'registos.*.nivel_ensino' => ['required', 'string', 'max:120'],
            'registos.*.numero_alunos' => ['required', 'integer', 'min:1', 'max:5000'],
        ]);

        $validated['ano_referencia'] = $periodoAtivo->ano;

        // Verificação final de duplicados antes de persistir os dados
        $jaPreencheu = InqueritoAgrupamento::where('agrupamento_id', $agrupamentoId)
            ->where('ano_referencia', $validated['ano_referencia'])
            ->exists();

        if ($jaPreencheu) {
            return redirect()->route('agrupamento.inqueritos.index')->with('error', 'O inquérito para este ano já foi submetido.');
        }

        $concelhoId = $agrupamento->concelho_id;

        // Mapeia os registos injetando o ID do concelho para facilitar a análise regional posterior
        $registos = collect($validated['registos'])->map(function (array $registo) use ($concelhoId) {
            $registo['concelho_id'] = $concelhoId;
            return $registo;
        });

        $totalRegistos = $registos->count();
        $totalAlunos = $registos->sum('numero_alunos');

        // Executa a gravação em transação para garantir que o cabeçalho e detalhes são gravados em conjunto
        DB::transaction(function () use ($registos, $agrupamentoId, $user, $validated, $totalRegistos, $totalAlunos) {
            $inquerito = InqueritoAgrupamento::create([
                'agrupamento_id' => $agrupamentoId,
                'utilizador_id' => $user->id,
                'ano_referencia' => $validated['ano_referencia'],
                'total_registos' => $totalRegistos,
                'total_alunos' => $totalAlunos,
                'submetido_em' => now(),
            ]);

            foreach ($registos as $registo) {
                InqueritoAgrupamentoRegisto::create([
                    'inquerito_id' => $inquerito->id,
                    'nacionalidade' => $registo['nacionalidade'],
                    'ano_letivo' => $registo['ano_letivo'],
                    'concelho_id' => $registo['concelho_id'],
                    'nivel_ensino' => $registo['nivel_ensino'],
                    'numero_alunos' => $registo['numero_alunos'],
                ]);
            }

            // Registo de auditoria para monitorizar a atividade de carregamento de dados
            AuditLogger::log('agrupamento_inquerito_create', 'Submeteu o inquérito '.$validated['ano_referencia'].' com '.$totalRegistos.' registos.');
        });

        return redirect()->route('agrupamento.inqueritos.index')->with('success', 'Inquérito submetido com sucesso.');
    }

    // Permite ao agrupamento rever os dados que submeteu para um determinado ano
    public function show(InqueritoAgrupamento $inquerito): View
    {
        $user = Auth::user();
        // Segurança: impede a visualização de dados de outros agrupamentos
        abort_if($inquerito->agrupamento_id !== $user->agrupamento_id, 403);

        $inquerito->load(['registos.concelho']);

        return view('agrupamento.inqueritos.show', [
            'inquerito' => $inquerito,
        ]);
    }
}