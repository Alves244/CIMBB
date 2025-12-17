<?php

namespace App\Http\Controllers\Agrupamento;

use App\Http\Controllers\Controller;
use App\Models\EstabelecimentoEnsino;
use App\Models\InqueritoAgrupamento;
use App\Models\InqueritoAgrupamentoRegisto;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class InqueritoAgrupamentoController extends Controller
{
    private array $niveisEnsino = [
        'Pré-escolar',
        '1.º ciclo',
        '2.º ciclo',
        '3.º ciclo',
        'Secundário',
        'Profissional',
        'Outro',
    ];

    public function index(): View
    {
        $user = Auth::user();
        $agrupamentoId = $user->agrupamento_id;
        $anoAtual = (int) date('Y');

        $inqueritos = InqueritoAgrupamento::withCount('registos')
            ->where('agrupamento_id', $agrupamentoId)
            ->orderByDesc('ano_referencia')
            ->get();

        $jaPreencheuEsteAno = $inqueritos->contains('ano_referencia', $anoAtual);
        $dataLimite = now()->copy()->setDate($anoAtual, 12, 31)->endOfDay();
        $dentroDoPrazo = now()->lessThanOrEqualTo($dataLimite);

        return view('agrupamento.inqueritos.index', [
            'inqueritos' => $inqueritos,
            'anoAtual' => $anoAtual,
            'jaPreencheuEsteAno' => $jaPreencheuEsteAno,
            'dentroDoPrazo' => $dentroDoPrazo,
            'dataLimite' => $dataLimite,
        ]);
    }

    public function create(): View
    {
        $user = Auth::user();
        $agrupamentoId = $user->agrupamento_id;
        $anoAtual = (int) date('Y');

        $jaPreencheu = InqueritoAgrupamento::where('agrupamento_id', $agrupamentoId)
            ->where('ano_referencia', $anoAtual)
            ->exists();

        abort_if($jaPreencheu, 403, 'O inquérito deste ano já foi submetido.');

        $estabelecimentos = EstabelecimentoEnsino::with('concelho')
            ->where('agrupamento_id', $agrupamentoId)
            ->orderBy('nome')
            ->get();

        abort_if($estabelecimentos->isEmpty(), 403, 'Ainda não existem estabelecimentos associados ao seu agrupamento.');

        return view('agrupamento.inqueritos.create', [
            'anoAtual' => $anoAtual,
            'estabelecimentos' => $estabelecimentos,
            'niveisEnsino' => $this->niveisEnsino,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $agrupamentoId = $user->agrupamento_id;
        $anoAtual = (int) date('Y');

        $estabelecimentos = EstabelecimentoEnsino::where('agrupamento_id', $agrupamentoId)
            ->get()
            ->keyBy('id');

        if ($estabelecimentos->isEmpty()) {
            return redirect()->route('agrupamento.inqueritos.index')->with('error', 'Não existem estabelecimentos associados ao seu agrupamento.');
        }

        $validated = $request->validate([
            'ano_referencia' => ['required', 'integer', 'min:2000', 'max:'.($anoAtual + 1)],
            'registos' => ['required', 'array', 'min:1'],
            'registos.*.nacionalidade' => ['required', 'string', 'max:120'],
            'registos.*.ano_letivo' => ['required', 'string', 'max:15'],
            'registos.*.estabelecimento_id' => ['required', 'integer', Rule::in($estabelecimentos->keys()->all())],
            'registos.*.concelho_id' => ['required', 'integer', 'exists:concelhos,id'],
            'registos.*.nivel_ensino' => ['required', 'string', 'max:120'],
            'registos.*.numero_alunos' => ['required', 'integer', 'min:1', 'max:5000'],
        ]);

        $jaPreencheu = InqueritoAgrupamento::where('agrupamento_id', $agrupamentoId)
            ->where('ano_referencia', $validated['ano_referencia'])
            ->exists();

        if ($jaPreencheu) {
            return redirect()->route('agrupamento.inqueritos.index')->with('error', 'O inquérito para este ano já foi submetido.');
        }

        $registos = collect($validated['registos'])->map(function (array $registo) use ($estabelecimentos) {
            $estabelecimento = $estabelecimentos[$registo['estabelecimento_id']] ?? null;

            if (! $estabelecimento) {
                abort(422, 'Estabelecimento inválido.');
            }

            $registo['concelho_id'] = $estabelecimento->concelho_id;

            return $registo;
        });

        $totalRegistos = $registos->count();
        $totalAlunos = $registos->sum('numero_alunos');

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
                    'estabelecimento_id' => $registo['estabelecimento_id'],
                    'concelho_id' => $registo['concelho_id'],
                    'nivel_ensino' => $registo['nivel_ensino'],
                    'numero_alunos' => $registo['numero_alunos'],
                ]);
            }

            AuditLogger::log('agrupamento_inquerito_create', 'Submeteu o inquérito '.$validated['ano_referencia'].' com '.$totalRegistos.' registos.');
        });

        return redirect()->route('agrupamento.inqueritos.index')->with('success', 'Inquérito submetido com sucesso.');
    }

    public function show(InqueritoAgrupamento $inquerito): View
    {
        $user = Auth::user();
        abort_if($inquerito->agrupamento_id !== $user->agrupamento_id, 403);

        $inquerito->load(['registos.estabelecimento.concelho']);

        return view('agrupamento.inqueritos.show', [
            'inquerito' => $inquerito,
        ]);
    }
}
