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

/**
 * Controlador para gestão dos inquéritos submetidos pelos agrupamentos.
 */
class InqueritoAgrupamentoController extends Controller
{
    // Níveis de ensino reconhecidos para categorização dos dados
    private array $niveisEnsino = [
        'Pré-Escolar',
        '1.º Ciclo do EB',
        '2.º Ciclo do EB',
        '3.º Ciclo do EB',
        'Ensino Secundário',
        'Ensino Profissional',
        'Ensino Superior',
    ];

    // Listagem dos inquéritos submetidos pelo agrupamento autenticado
    public function index(): View
    {
        $user = Auth::user();
        $agrupamentoId = $user->agrupamento_id;
        
        // Obtém o período de inquérito ativo para agrupamentos
        $periodoAtual = InqueritoPeriodo::periodoAtivo(InqueritoPeriodo::TIPO_AGRUPAMENTO);
        $anoAtual = $periodoAtual?->ano ?? (int) date('Y');

        // Consulta dos inquéritos submetidos pelo agrupamento
        $inqueritos = InqueritoAgrupamento::withCount('registos')
            ->where('agrupamento_id', $agrupamentoId)
            ->orderByDesc('ano_referencia')
            ->get();

        // Verifica se o agrupamento já submeteu o inquérito para o ano atual
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

    // Formulário para submissão de um novo inquérito pelo agrupamento
    public function create(): View
    {
        $user = Auth::user();
        $agrupamentoId = $user->agrupamento_id;
        $periodoAtivo = InqueritoPeriodo::periodoAtivo(InqueritoPeriodo::TIPO_AGRUPAMENTO);

        // Validação de integridade: assegura que existe um período ativo para submissão
        abort_if(! $periodoAtivo, 403, 'Não existe período aberto para submissão. Contacte a CIMBB.');

        $anoAtual = $periodoAtivo->ano;
        $agrupamento = $user->agrupamento?->load('concelho');

        // Validação de integridade: assegura que o agrupamento e o concelho estão corretamente associados
        abort_if(! $agrupamento, 403, 'Não foi possível identificar o seu agrupamento.');
        abort_if(! $agrupamento->concelho, 403, 'Associe um concelho ao agrupamento antes de submeter o inquérito.');

        // Verifica se o agrupamento já submeteu o inquérito para o ano atual
        $jaPreencheu = InqueritoAgrupamento::where('agrupamento_id', $agrupamentoId)
            ->where('ano_referencia', $anoAtual)
            ->exists();

        abort_if($jaPreencheu, 403, 'O inquérito deste ano já foi submetido.');

        // Renderiza o formulário de submissão com os dados necessários
        return view('agrupamento.inqueritos.create', [
            'anoAtual' => $anoAtual,
            'concelhoNome' => $agrupamento->concelho->nome,
            'concelhoId' => $agrupamento->concelho_id,
            'niveisEnsino' => $this->niveisEnsino,
            'nacionalidades' => config('nacionalidades', []),
            'periodoAtivo' => $periodoAtivo,
        ]);
    }

    // Processa a submissão do inquérito pelo agrupamento 
    public function store(Request $request): RedirectResponse
    {
        // Obtém o utilizador autenticado e o período de inquérito ativo
        $user = Auth::user();
        $agrupamentoId = $user->agrupamento_id;
        $periodoAtivo = InqueritoPeriodo::periodoAtivo(InqueritoPeriodo::TIPO_AGRUPAMENTO);

        // Validação de integridade: assegura que existe um período ativo para submissão
        if (! $periodoAtivo) {
            return redirect()->route('agrupamento.inqueritos.index')->with('error', 'Não existe período aberto para submissão.');
        }

        $agrupamento = $user->agrupamento?->load('concelho');
        // Validação de integridade: assegura que o agrupamento e o concelho estão corretamente associados
        if (! $agrupamento || ! $agrupamento->concelho) {
            return redirect()->route('agrupamento.inqueritos.index')->with('error', 'Associe um concelho ao agrupamento antes de submeter o inquérito.');
        }

        // Validação dos dados de entrada
        $validated = $request->validate([
            'ano_referencia' => ['required', 'integer', Rule::in([$periodoAtivo->ano])],
            'registos' => ['required', 'array', 'min:1'],
            'registos.*.nacionalidade' => ['required', 'string', 'max:120'],
            'registos.*.ano_letivo' => ['required', 'string', 'max:15'],
            'registos.*.nivel_ensino' => ['required', 'string', 'max:120'],
            'registos.*.numero_alunos' => ['required', 'integer', 'min:1', 'max:5000'],
        ]);

        $validated['ano_referencia'] = $periodoAtivo->ano;

        // Verifica se o agrupamento já submeteu o inquérito para o ano indicado
        $jaPreencheu = InqueritoAgrupamento::where('agrupamento_id', $agrupamentoId)
            ->where('ano_referencia', $validated['ano_referencia'])
            ->exists();

        if ($jaPreencheu) {
            return redirect()->route('agrupamento.inqueritos.index')->with('error', 'O inquérito para este ano já foi submetido.');
        }

        $concelhoId = $agrupamento->concelho_id;

        // Prepara os registos para inserção, associando o concelho do agrupamento
        $registos = collect($validated['registos'])->map(function (array $registo) use ($concelhoId) {
            $registo['concelho_id'] = $concelhoId;
            return $registo;
        });

        $totalRegistos = $registos->count();
        $totalAlunos = $registos->sum('numero_alunos');

        // Utiliza uma transação para garantir a integridade dos dados durante a criação do inquérito e dos seus registos
        DB::transaction(function () use ($registos, $agrupamentoId, $user, $validated, $totalRegistos, $totalAlunos) {
            $inquerito = InqueritoAgrupamento::create([
                'agrupamento_id' => $agrupamentoId,
                'utilizador_id' => $user->id,
                'ano_referencia' => $validated['ano_referencia'],
                'total_registos' => $totalRegistos,
                'total_alunos' => $totalAlunos,
                'submetido_em' => now(),
            ]);
            // Criação dos registos associados ao inquérito
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

            // Registo da ação no log de auditoria para rastreabilidade
            AuditLogger::log('agrupamento_inquerito_create', 'Submeteu o inquérito '.$validated['ano_referencia'].' com '.$totalRegistos.' registos.');
        });

        return redirect()->route('agrupamento.inqueritos.index')->with('success', 'Inquérito submetido com sucesso.');
    }

    // Visualização detalhada de um inquérito submetido pelo agrupamento
    public function show(InqueritoAgrupamento $inquerito): View
    {
        $user = Auth::user();
        // Garante que o inquérito pertence ao agrupamento do utilizador autenticado
        abort_if($inquerito->agrupamento_id !== $user->agrupamento_id, 403);

        $inquerito->load(['registos.concelho']);

        return view('agrupamento.inqueritos.show', [
            'inquerito' => $inquerito,
        ]);
    }
}