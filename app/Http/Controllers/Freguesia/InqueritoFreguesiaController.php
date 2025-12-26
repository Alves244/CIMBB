<?php

namespace App\Http\Controllers\Freguesia;

use App\Http\Controllers\Controller;
use App\Models\InqueritoFreguesia;
use App\Models\Familia;
use App\Models\AgregadoFamiliar;
use App\Models\AtividadeEconomica;
use App\Models\SetorAtividade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;

// Gere a recolha anual de dados qualitativos e quantitativos das freguesias (Objetivo 2)
class InqueritoFreguesiaController extends Controller
{
    // Categorias de emprego definidas pelos stakeholders para monitorização socioeconómica (Objetivo 3)
    private const SETORES_PRIORITARIOS = [
        'Administração, serviços técnicos ou trabalho de escritório',
        'Agricultura, silvicultura e pecuária',
        'Comércio e retalho',
        'Construção civil e obras públicas',
        'Indústria transformadora',
        'Restauração, hotelaria e turismo',
        'Serviços pessoais e sociais (inclui limpezas, cuidados de idosos, apoio doméstico)',
        'Transportes e logística',
        'Outro (especificar)',
    ];

    // Apresenta o histórico de inquéritos e valida janelas de submissão (Objetivo 1)
    public function index()
    {
        $user = Auth::user();
        $freguesiaId = $user->freguesia_id;

        $inqueritosPassados = InqueritoFreguesia::where('freguesia_id', $freguesiaId)
                                                ->orderBy('ano', 'desc')
                                                ->get();

        $anoAtual = date('Y');
        $jaPreencheuEsteAno = $inqueritosPassados->contains('ano', $anoAtual);

        // Define o encerramento do ciclo anual de recolha de dados
        $dataLimite = Carbon::create($anoAtual, 12, 31, 23, 59, 59);
        $dentroDoPrazo = now()->lessThanOrEqualTo($dataLimite);

        return view('freguesia.inqueritos.index', [
            'inqueritos' => $inqueritosPassados,
            'jaPreencheuEsteAno' => $jaPreencheuEsteAno,
            'anoAtual' => $anoAtual,
            'dentroDoPrazo' => $dentroDoPrazo,
            'dataLimite' => $dataLimite
        ]);
    }

    // Calcula métricas automáticas para facilitar o preenchimento intuitivo (Objetivo 1)
    public function create()
    {
        $user = Auth::user();
        $freguesiaId = $user->freguesia_id;
        $anoAtual = date('Y');

        // Impede submissões duplicadas para o mesmo período de referência
        $existe = InqueritoFreguesia::where('freguesia_id', $freguesiaId)
                                    ->where('ano', $anoAtual)
                                    ->exists();
        if ($existe) {
            return redirect()->route('freguesia.inqueritos.index')
                             ->with('error', 'O inquérito para o ano '.$anoAtual.' já foi preenchido.');
        }

        // Agregação de dados em tempo real sobre localização, demografia e habitação
        $familiasDaFreguesia = Familia::where('freguesia_id', $freguesiaId)->get();
        $familiaIds = $familiasDaFreguesia->pluck('id');
        $agregadosDaFreguesia = AgregadoFamiliar::whereIn('familia_id', $familiaIds)->get();
        $atividadesDaFreguesia = AtividadeEconomica::whereIn('familia_id', $familiaIds)->get();
        $setoresConfig = collect(self::SETORES_PRIORITARIOS);
        $setoresRegistados = SetorAtividade::where('ativo', true)
            ->whereIn('nome', $setoresConfig->all())
            ->get()
            ->keyBy('nome');
        
        // Distribuição geográfica por tipo de núcleo (Objetivo 3)
        $preenchido = [];
        $preenchido['total_nucleo_urbano'] = $familiasDaFreguesia->where('localizacao_tipo', 'sede_freguesia')->count();
        $preenchido['total_aldeia_anexa'] = $familiasDaFreguesia->where('localizacao_tipo', 'lugar_aldeia')->count();
        $preenchido['total_agroflorestal'] = $familiasDaFreguesia->where('localizacao_tipo', 'espaco_agroflorestal')->count();

        // Totais demográficos baseados na composição das famílias registadas
        $preenchido['total_adultos'] = $agregadosDaFreguesia->sum('adultos_laboral') + $agregadosDaFreguesia->sum('adultos_65_mais');
        $preenchido['total_criancas'] = $agregadosDaFreguesia->sum('criancas');
        
        // Métricas sobre o regime de propriedade das habitações
        $preenchido['total_propria'] = $familiasDaFreguesia->where('tipologia_propriedade', 'propria')->count();
        $preenchido['total_arrendada'] = $familiasDaFreguesia
            ->whereIn('tipologia_propriedade', ['arrendada', 'cedida', 'outra'])
            ->count();

        // Distribuição laboral por setor e tipo de conta (Propria vs Outrem)
        $atividadesPropria = $atividadesDaFreguesia->where('tipo', 'conta_propria');
        $atividadesOutrem = $atividadesDaFreguesia->where('tipo', 'conta_outrem');

        $preenchido['total_por_setor_propria'] = $setoresConfig->mapWithKeys(fn ($nome) => [$nome => 0])->toArray();
        $preenchido['total_por_setor_outrem'] = $setoresConfig->mapWithKeys(fn ($nome) => [$nome => 0])->toArray();

        foreach ($setoresRegistados as $nome => $setor) {
            $preenchido['total_por_setor_propria'][$nome] = $atividadesPropria->where('setor_id', $setor->id)->count();
            $preenchido['total_por_setor_outrem'][$nome] = $atividadesOutrem->where('setor_id', $setor->id)->count();
        }

        $preenchido['total_trabalhadores_outrem'] = array_sum($preenchido['total_por_setor_outrem']);
        $setoresLista = $this->obterSetoresFormulario();

        return view('freguesia.inqueritos.adicionar', compact('anoAtual', 'preenchido', 'setoresLista'));
    }

    // Persiste os dados consolidados e perceções qualitativas dos serviços locais
    public function store(Request $request)
    {
        $user = Auth::user();
        $anoAtual = date('Y');

        $dadosValidados = $this->validarInquerito($request);

        try {
            $dadosValidados['freguesia_id'] = $user->freguesia_id;
            $dadosValidados['utilizador_id'] = $user->id;
            $dadosValidados['ano'] = $anoAtual;
            
            InqueritoFreguesia::create($dadosValidados);

            return redirect()->route('freguesia.inqueritos.index')
                             ->with('success', 'Inquérito do ano '.$anoAtual.' guardado com sucesso!');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erro ao guardar: '.$e->getMessage());
        }
    }

    // Permite a revisão dos dados submetidos pela freguesia
    public function show(InqueritoFreguesia $inquerito)
    {
        if ($inquerito->freguesia_id !== Auth::user()->freguesia_id) {
            abort(403, 'Acesso não autorizado.');
        }
        $inquerito->load('freguesia');
        $setoresLista = $this->obterSetoresFormulario();

        return view('freguesia.inqueritos.show', compact('inquerito', 'setoresLista'));
    }

    // Edição de inquéritos restringida pelo prazo administrativo anual
    public function edit(InqueritoFreguesia $inquerito)
    {
        if ($inquerito->freguesia_id !== Auth::user()->freguesia_id) {
            abort(403, 'Acesso não autorizado.');
        }

        // Valida se o período de edição ainda se encontra aberto
        $dataLimite = Carbon::create($inquerito->ano, 12, 31, 23, 59, 59);
        if (now()->greaterThan($dataLimite)) {
            return redirect()->route('freguesia.inqueritos.index')
                             ->with('error', 'O prazo para editar este inquérito já expirou.');
        }

        $setoresLista = $this->obterSetoresFormulario();

        return view('freguesia.inqueritos.editar', compact('inquerito', 'setoresLista'));
    }

    // Atualiza os registos de monitorização respeitando a segurança territorial
    public function update(Request $request, InqueritoFreguesia $inquerito)
    {
        if ($inquerito->freguesia_id !== Auth::user()->freguesia_id) {
            abort(403);
        }
        
        $dataLimite = Carbon::create($inquerito->ano, 12, 31, 23, 59, 59);
        if (now()->greaterThan($dataLimite)) {
            return redirect()->route('freguesia.inqueritos.index')->with('error', 'Prazo expirado.');
        }

        $dadosValidados = $this->validarInquerito($request);

        try {
            $inquerito->update($dadosValidados);

            return redirect()->route('freguesia.inqueritos.index')
                             ->with('success', 'Inquérito atualizado com sucesso!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erro ao atualizar: '.$e->getMessage());
        }
    }

    // Validação centralizada de métricas e perceções de satisfação global
    private function validarInquerito(Request $request)
    {
        return $request->validate([
            'total_nucleo_urbano' => 'required|integer|min:0',
            'total_aldeia_anexa' => 'required|integer|min:0',
            'total_agroflorestal' => 'required|integer|min:0',
            'total_adultos' => 'required|integer|min:0',
            'total_criancas' => 'required|integer|min:0',
            'total_propria' => 'required|integer|min:0',
            'total_arrendada' => 'required|integer|min:0',
            'total_por_setor_propria' => 'required|array',
            'total_por_setor_outrem' => 'required|array',
            'total_trabalhadores_outrem' => 'required|integer|min:0',
            'escala_integracao' => 'required|integer|min:1|max:5',
            'aspectos_positivos' => 'nullable|string|max:2000',
            'aspectos_negativos' => 'nullable|string|max:2000',
            'satisfacao_global' => 'required|integer|min:1|max:5',
            'sugestoes' => 'nullable|string|max:2000',
        ]);
    }

    // Formata os setores para facilitar a renderização dinâmica no formulário
    private function obterSetoresFormulario(): array
    {
        return collect(self::SETORES_PRIORITARIOS)
            ->map(fn (string $nome) => [
                'nome' => $nome,
                'slug' => Str::slug($nome, '_'),
            ])
            ->toArray();
    }
}