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

/**
 * Controlador para gestão dos inquéritos anuais das freguesias.
 */
class InqueritoFreguesiaController extends Controller
{
    // Setores de atividade prioritários para o inquérito
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

    // Listagem dos inquéritos submetidos pela freguesia autenticada
    public function index()
    {
        // Obtém a freguesia do utilizador autenticado
        $user = Auth::user();
        $freguesiaId = $user->freguesia_id;

        $inqueritosPassados = InqueritoFreguesia::where('freguesia_id', $freguesiaId)
                                                ->orderBy('ano', 'desc')
                                                ->get();

        $anoAtual = date('Y');
        $jaPreencheuEsteAno = $inqueritosPassados->contains('ano', $anoAtual);

        // Obtém a data limite do inquérito para o ano atual
        $dataLimite = Carbon::create($anoAtual, 12, 31, 23, 59, 59);
        
        // Verifica se ainda está dentro do prazo
        $dentroDoPrazo = now()->lessThanOrEqualTo($dataLimite);

        // Retorna a vista com os dados necessários
        return view('freguesia.inqueritos.index', [
            'inqueritos' => $inqueritosPassados,
            'jaPreencheuEsteAno' => $jaPreencheuEsteAno,
            'anoAtual' => $anoAtual,
            'dentroDoPrazo' => $dentroDoPrazo,
            'dataLimite' => $dataLimite
        ]);
    }

    // Mostra o formulário para criar um novo inquérito
    public function create()
    {
        $user = Auth::user();
        $freguesiaId = $user->freguesia_id;
        $anoAtual = date('Y');

        // Verifica se o inquérito para o ano atual já foi preenchido
        $existe = InqueritoFreguesia::where('freguesia_id', $freguesiaId)
                                    ->where('ano', $anoAtual)
                                    ->exists();
        if ($existe) {
            return redirect()->route('freguesia.inqueritos.index')
                             ->with('error', 'O inquérito para o ano '.$anoAtual.' já foi preenchido.');
        }

        // Preenche os dados iniciais para o formulário com base nas famílias da freguesia
        $familiasDaFreguesia = Familia::where('freguesia_id', $freguesiaId)->get();
        $familiaIds = $familiasDaFreguesia->pluck('id');
        $agregadosDaFreguesia = AgregadoFamiliar::whereIn('familia_id', $familiaIds)->get();
        $atividadesDaFreguesia = AtividadeEconomica::whereIn('familia_id', $familiaIds)->get();
        $setoresConfig = collect(self::SETORES_PRIORITARIOS);
        $setoresRegistados = SetorAtividade::where('ativo', true)
            ->whereIn('nome', $setoresConfig->all())
            ->get()
            ->keyBy('nome');
        // Calcula os totais preenchidos
        $preenchido = [];
        $preenchido['total_nucleo_urbano'] = $familiasDaFreguesia->where('localizacao_tipo', 'sede_freguesia')->count();
        $preenchido['total_aldeia_anexa'] = $familiasDaFreguesia->where('localizacao_tipo', 'lugar_aldeia')->count();
        $preenchido['total_agroflorestal'] = $familiasDaFreguesia->where('localizacao_tipo', 'espaco_agroflorestal')->count();
        // Soma adultos e crianças
        $preenchido['total_adultos'] = $agregadosDaFreguesia->sum('adultos_laboral') + $agregadosDaFreguesia->sum('adultos_65_mais');
        $preenchido['total_criancas'] = $agregadosDaFreguesia->sum('criancas');
        // Tipologia de propriedade
        $preenchido['total_propria'] = $familiasDaFreguesia->where('tipologia_propriedade', 'propria')->count();
        $preenchido['total_arrendada'] = $familiasDaFreguesia
            ->whereIn('tipologia_propriedade', ['arrendada', 'cedida', 'outra'])
            ->count();
        // Atividades económicas
        $atividadesPropria = $atividadesDaFreguesia->where('tipo', 'conta_propria');
        $atividadesOutrem = $atividadesDaFreguesia->where('tipo', 'conta_outrem');
        // Totais por setor
        $preenchido['total_por_setor_propria'] = $setoresConfig->mapWithKeys(fn ($nome) => [$nome => 0])->toArray();
        $preenchido['total_por_setor_outrem'] = $setoresConfig->mapWithKeys(fn ($nome) => [$nome => 0])->toArray();
        // Preenche os totais por setor com base nos setores registados
        foreach ($setoresRegistados as $nome => $setor) {
            $preenchido['total_por_setor_propria'][$nome] = $atividadesPropria->where('setor_id', $setor->id)->count();
            $preenchido['total_por_setor_outrem'][$nome] = $atividadesOutrem->where('setor_id', $setor->id)->count();
        }
        // Total de trabalhadores por conta de outrem
        $preenchido['total_trabalhadores_outrem'] = array_sum($preenchido['total_por_setor_outrem']);
        $setoresLista = $this->obterSetoresFormulario();
        // Renderiza o formulário com os dados pré-preenchidos
        return view('freguesia.inqueritos.adicionar', compact('anoAtual', 'preenchido', 'setoresLista'));
    }

    // Armazena o novo inquérito na base de dados
    public function store(Request $request)
    {
        // Obtém o utilizador autenticado
        $user = Auth::user();
        $anoAtual = date('Y');

        $dadosValidados = $this->validarInquerito($request);
        // Tenta criar o inquérito
        try {
            $dadosValidados['freguesia_id'] = $user->freguesia_id;
            $dadosValidados['utilizador_id'] = $user->id;
            $dadosValidados['ano'] = $anoAtual;
            // Cria o inquérito na base de dados
            InqueritoFreguesia::create($dadosValidados);
            // Redireciona com sucesso
            return redirect()->route('freguesia.inqueritos.index')
                             ->with('success', 'Inquérito do ano '.$anoAtual.' guardado com sucesso!');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erro ao guardar: '.$e->getMessage());
        }
    }

    // Mostra os detalhes de um inquérito específico
    public function show(InqueritoFreguesia $inquerito)
    {
        // Verifica se o inquérito pertence à freguesia do utilizador autenticado
        if ($inquerito->freguesia_id !== Auth::user()->freguesia_id) {
            abort(403, 'Acesso não autorizado.');
        }
        $inquerito->load('freguesia');
        $setoresLista = $this->obterSetoresFormulario();
        // Renderiza a vista de detalhes do inquérito
        return view('freguesia.inqueritos.show', compact('inquerito', 'setoresLista'));
    }

    // Mostra o formulário para editar um inquérito existente
    public function edit(InqueritoFreguesia $inquerito)
    {
        // Verificar propriedade
        if ($inquerito->freguesia_id !== Auth::user()->freguesia_id) {
            abort(403, 'Acesso não autorizado.');
        }

        // Verifica se o prazo para edição já expirou
        $dataLimite = Carbon::create($inquerito->ano, 12, 31, 23, 59, 59);
        if (now()->greaterThan($dataLimite)) {
            return redirect()->route('freguesia.inqueritos.index')
                             ->with('error', 'O prazo para editar este inquérito já expirou.');
        }

        // Obtém os setores para o formulário
        $setoresLista = $this->obterSetoresFormulario();

        return view('freguesia.inqueritos.editar', compact('inquerito', 'setoresLista'));
    }

    // Atualiza um inquérito existente na base de dados
    public function update(Request $request, InqueritoFreguesia $inquerito)
    {
        // Verificar propriedade
        if ($inquerito->freguesia_id !== Auth::user()->freguesia_id) {
            abort(403);
        }
        // Verifica se o prazo para edição já expirou
        $dataLimite = Carbon::create($inquerito->ano, 12, 31, 23, 59, 59);
        if (now()->greaterThan($dataLimite)) {
            return redirect()->route('freguesia.inqueritos.index')->with('error', 'Prazo expirado.');
        }

        $dadosValidados = $this->validarInquerito($request);
        // Tenta atualizar o inquérito
        try {
            $inquerito->update($dadosValidados);

            return redirect()->route('freguesia.inqueritos.index')
                             ->with('success', 'Inquérito atualizado com sucesso!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erro ao atualizar: '.$e->getMessage());
        }
    }

    // Validação dos dados do inquérito 
    private function validarInquerito(Request $request)
    {
        // Validação rigorosa dos campos do inquérito
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

    // Obtém os setores de atividade para o formulário
    private function obterSetoresFormulario(): array
    {
        // Prepara a lista de setores com nome e slug
        return collect(self::SETORES_PRIORITARIOS)
            ->map(fn (string $nome) => [
                'nome' => $nome,
                'slug' => Str::slug($nome, '_'),
            ])
            ->toArray();
    }
}