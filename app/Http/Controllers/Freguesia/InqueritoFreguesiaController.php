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

class InqueritoFreguesiaController extends Controller
{
    /**
     * Mostra a lista (histórico) de inquéritos submetidos pela freguesia.
     */
    public function index()
    {
        $user = Auth::user();
        $freguesiaId = $user->freguesia_id;

        $inqueritosPassados = InqueritoFreguesia::where('freguesia_id', $freguesiaId)
                                                ->orderBy('ano', 'desc')
                                                ->get();

        $anoAtual = date('Y');
        $jaPreencheuEsteAno = $inqueritosPassados->contains('ano', $anoAtual);

        return view('freguesia.inqueritos.index', [
            'inqueritos' => $inqueritosPassados,
            'jaPreencheuEsteAno' => $jaPreencheuEsteAno,
            'anoAtual' => $anoAtual
        ]);
    }

    /**
     * Mostra o formulário para criar um novo inquérito,
     * pré-preenchendo com dados do sistema.
     */
    public function create()
    {
        $user = Auth::user();
        $freguesiaId = $user->freguesia_id;
        $anoAtual = date('Y');

        // Segurança: Verifica se já preencheu este ano
        $existe = InqueritoFreguesia::where('freguesia_id', $freguesiaId)
                                    ->where('ano', $anoAtual)
                                    ->exists();
        if ($existe) {
            return redirect()->route('freguesia.inqueritos.index')
                             ->with('error', 'O inquérito para o ano '.$anoAtual.' já foi preenchido.');
        }

        // --- CÁLCULO DOS DADOS PRÉ-PREENCHIDOS ---
        
        // 1. Buscar dados base (Famílias, Agregados, Atividades)
        $familiasDaFreguesia = Familia::where('freguesia_id', $freguesiaId)->get();
        $familiaIds = $familiasDaFreguesia->pluck('id');
        $agregadosDaFreguesia = AgregadoFamiliar::whereIn('familia_id', $familiaIds)->get();
        $atividadesDaFreguesia = AtividadeEconomica::whereIn('familia_id', $familiaIds)->get();
        $setores = SetorAtividade::where('ativo', true)->orderBy('nome')->get();
        
        // 2. Preparar o array de dados
        $preenchido = [];

        // 3. Perguntas 11-13 (Localização)
        $preenchido['total_nucleo_urbano'] = $familiasDaFreguesia->where('localizacao', 'nucleo_urbano')->count();
        $preenchido['total_aldeia_anexa'] = $familiasDaFreguesia->where('localizacao', 'aldeia_anexa')->count();
        $preenchido['total_agroflorestal'] = $familiasDaFreguesia->where('localizacao', 'espaco_agroflorestal')->count();

        // 4. Pergunta 14 (Indivíduos)
        $preenchido['total_adultos'] = $agregadosDaFreguesia->sum('adultos_laboral') + $agregadosDaFreguesia->sum('adultos_65_mais');
        $preenchido['total_criancas'] = $agregadosDaFreguesia->sum('criancas');
        
        // 5. Pergunta 15 (Propriedade)
        $preenchido['total_propria'] = $familiasDaFreguesia->where('tipologia_propriedade', 'propria')->count();
        $preenchido['total_arrendada'] = $familiasDaFreguesia->where('tipologia_propriedade', 'arrendada')->count();

        // 6. Perguntas 16-19 (Sectores) [cite: 440-459]
        // (Contando o número de ATIVIDADES, não de indivíduos, por simplicidade)
        $atividadesPropria = $atividadesDaFreguesia->where('tipo', 'conta_propria');
        $atividadesOutrem = $atividadesDaFreguesia->where('tipo', 'conta_outrem');
        $setorDataPropria = [];
        $setorDataOutrem = [];

        foreach ($setores as $setor) {
            $setorDataPropria[$setor->nome] = $atividadesPropria->where('setor_id', $setor->id)->count();
            $setorDataOutrem[$setor->nome] = $atividadesOutrem->where('setor_id', $setor->id)->count();
        }
        $preenchido['total_por_setor_propria'] = $setorDataPropria;
        $preenchido['total_por_setor_outrem'] = $setorDataOutrem;

        // 7. Mostra o formulário de adição, passando os dados
        return view('freguesia.inqueritos.adicionar', compact('anoAtual', 'preenchido', 'setores'));
    }

    /**
     * Guarda o novo inquérito (completo) na base de dados.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $anoAtual = date('Y');

        // 1. Validar TODOS os campos (Quantitativos e Qualitativos)
        $dadosValidados = $request->validate([
            // Quantitativos (Perg. 11-19)
            'total_nucleo_urbano' => 'required|integer|min:0',
            'total_aldeia_anexa' => 'required|integer|min:0',
            'total_agroflorestal' => 'required|integer|min:0',
            'total_adultos' => 'required|integer|min:0',
            'total_criancas' => 'required|integer|min:0',
            'total_propria' => 'required|integer|min:0',
            'total_arrendada' => 'required|integer|min:0',
            'total_por_setor_propria' => 'required|array', // Valida que é um array
            'total_por_setor_outrem' => 'required|array',
            
            // Qualitativos (Perg. 20-24)
            'escala_integracao' => 'required|integer|min:1|max:5',
            'aspectos_positivos' => 'nullable|string|max:2000',
            'aspectos_negativos' => 'nullable|string|max:2000',
            'satisfacao_global' => 'required|integer|min:1|max:5',
            'sugestoes' => 'nullable|string|max:2000',
        ]);

        try {
            // 2. Adicionar os dados que não vêm do formulário
            $dadosValidados['freguesia_id'] = $user->freguesia_id;
            $dadosValidados['utilizador_id'] = $user->id;
            $dadosValidados['ano'] = $anoAtual;
            
            // O Model tratará do JSON automaticamente

            // 3. Criar o inquérito
            InqueritoFreguesia::create($dadosValidados);

            // 4. Redirecionar para a lista (histórico)
            return redirect()->route('freguesia.inqueritos.index')
                           ->with('success', 'Inquérito do ano '.$anoAtual.' guardado com sucesso!');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erro ao guardar o inquérito: '.$e->getMessage());
        }
    }

    /**
     * Mostra os detalhes de um inquérito preenchido.
     * (Atualizado para mostrar os novos campos)
     */
    public function show(InqueritoFreguesia $inquerito)
    {
        if ($inquerito->freguesia_id !== Auth::user()->freguesia_id) {
            abort(403, 'Acesso não autorizado.');
        }

        // Carrega a relação 'freguesia' para o título (apesar de já termos o ID)
        $inquerito->load('freguesia');

        return view('freguesia.inqueritos.show', compact('inquerito'));
    }
}