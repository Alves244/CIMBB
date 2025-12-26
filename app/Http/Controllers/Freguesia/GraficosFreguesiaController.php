<?php

namespace App\Http\Controllers\Freguesia;

use App\Http\Controllers\Controller;
use App\Models\Familia;
use App\Models\AgregadoFamiliar;
use App\Models\AtividadeEconomica;
use App\Models\SetorAtividade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Responsável por agregar dados estatísticos para visualização gráfica no portal (Objetivo 2)
class GraficosFreguesiaController extends Controller
{
    // Prepara e estrutura os dados para os gráficos de monitorização da freguesia
    public function index()
    {
        $freguesiaId = Auth::user()->freguesia_id;
        
        // Recupera os dados base filtrados pela área geográfica do utilizador
        $familias = Familia::where('freguesia_id', $freguesiaId)->get();
        $familiaIds = $familias->pluck('id');
        $agregados = AgregadoFamiliar::whereIn('familia_id', $familiaIds)->get();
        $atividades = AtividadeEconomica::whereIn('familia_id', $familiaIds)->get();
        $setores = SetorAtividade::all()->pluck('nome', 'id');

        // Estrutura o Top 5 de nacionalidades para analisar fluxos migratórios (Objetivo 2)
        $nacionalidadesData = $familias->groupBy('nacionalidade')
            ->map->count()
            ->sortDesc()
            ->take(5)
            ->map(fn($count, $name) => ['label' => $name, 'count' => $count])
            ->values()
            ->toArray();

        // Mapeia a distribuição das famílias pelo território da freguesia (Objetivo 3)
        $localizacaoLabels = [
            'sede_freguesia' => 'Sede da freguesia',
            'lugar_aldeia' => 'Lugar / aldeia',
            'espaco_agroflorestal' => 'Espaço agroflorestal',
        ];

        $localizacaoData = [];
        foreach ($localizacaoLabels as $valor => $label) {
            $localizacaoData[$label] = $familias->where('localizacao_tipo', $valor)->count();
        }

        // Analisa a vertente qualitativa das condições de habitabilidade (Objetivo 2)
        $condicaoLabels = [
            'bom_estado' => 'Bom estado',
            'estado_razoavel' => 'Estado razoável',
            'necessita_reparacoes' => 'Necessita reparações',
            'situacao_precaria' => 'Situação precária',
        ];

        $condicaoData = [];
        foreach ($condicaoLabels as $valor => $label) {
            $condicaoData[$label] = $familias->where('condicao_alojamento', $valor)->count();
        }

        // Consolida a distribuição etária para análise demográfica da Beira Baixa (Objetivo 3)
        $etariaData = [
            'Adultos Laboral (18-65)' => $agregados->sum('adultos_laboral'),
            'Adultos Seniores (65+)' => $agregados->sum('adultos_65_mais'),
            'Crianças/Jovens (<18)' => $agregados->sum('criancas'),
        ];
        
        // Identifica os principais setores de inserção laboral (Objetivo 3)
        $setorDataRaw = $atividades->groupBy('setor_id')
            ->map->count()
            ->sortDesc();

        $setorTopData = $setorDataRaw->take(5)->map(function ($count, $setorId) use ($setores) {
            return [
                'label' => $setores[$setorId] ?? 'Setor Não Definido',
                'count' => $count,
            ];
        })
        ->values()
        ->toArray();

        // Monitoriza a integração nos serviços públicos de saúde (Objetivo 2)
        $centroSaudeData = [
            'Inscritas' => $familias->where('inscrito_centro_saude', true)->count(),
            'Por inscrever' => $familias->where('inscrito_centro_saude', false)->count(),
        ];

        // Avalia a taxa de escolarização dos novos residentes (Objetivo 3)
        $escolaLabels = [
            'sim' => 'Inscritas',
            'nao' => 'Não inscritas',
            'nao_sei' => 'Sem indicação',
        ];

        $escolaData = [];
        foreach ($escolaLabels as $valor => $label) {
            $escolaData[$label] = $familias->where('inscrito_escola', $valor)->count();
        }

        // Quantifica as necessidades de apoio para orientar intervenções públicas (Objetivo 3)
        $necessidadesData = $familias
            ->pluck('necessidades_apoio')
            ->filter()
            ->flatMap(function ($necessidades) {
                return collect($necessidades);
            })
            ->countBy()
            ->sortDesc()
            ->map(function ($total, $chave) {
                $map = [
                    'lingua_portuguesa' => 'Língua portuguesa',
                    'acesso_emprego' => 'Acesso a emprego',
                    'habitacao' => 'Habitação',
                    'regularizacao_administrativa' => 'Regularização administrativa',
                    'transporte_mobilidade' => 'Transporte / mobilidade',
                    'apoio_social' => 'Apoio social',
                ];
                return [
                    'label' => $map[$chave] ?? ucfirst(str_replace('_', ' ', $chave)),
                    'count' => $total,
                ];
            })
            ->values()
            ->toArray();

        // Disponibiliza os dados formatados em JSON para os componentes de gráficos (ex: Chart.js)
        return view('freguesia.graficos.index', [
            'nacionalidadesJson' => json_encode($nacionalidadesData),
            'localizacaoJson' => json_encode($localizacaoData),
            'condicaoJson' => json_encode($condicaoData),
            'etariaJson' => json_encode($etariaData),
            'setorTopJson' => json_encode($setorTopData),
            'centroSaudeJson' => json_encode($centroSaudeData),
            'escolaJson' => json_encode($escolaData),
            'necessidadesJson' => json_encode($necessidadesData),
            'totalFamilias' => $familias->count(),
        ]);
    }
}