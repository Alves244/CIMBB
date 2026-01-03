<?php

namespace App\Http\Controllers\Freguesia;

use App\Http\Controllers\Controller;
use App\Models\Familia;
use App\Models\AgregadoFamiliar;
use App\Models\AtividadeEconomica;
use App\Models\SetorAtividade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controlador para geração de gráficos estatísticos da freguesia.
 */
class GraficosFreguesiaController extends Controller
{
    // Gera os dados para os gráficos estatísticos da freguesia
    public function index()
    {
        $freguesiaId = Auth::user()->freguesia_id;
        
        // Carrega as famílias, agregados e atividades económicas da freguesia
        $familias = Familia::where('freguesia_id', $freguesiaId)->get();
        $familiaIds = $familias->pluck('id');
        $agregados = AgregadoFamiliar::whereIn('familia_id', $familiaIds)->get();
        $atividades = AtividadeEconomica::whereIn('familia_id', $familiaIds)->get();
        $setores = SetorAtividade::all()->pluck('nome', 'id');

        // Carrega o inquérito mais recente da freguesia
        $inquerito = \App\Models\InqueritoFreguesia::where('freguesia_id', $freguesiaId)->latest('ano')->first();

        // Garantir que os campos existem e são arrays/inteiros válidos
        $totalAdultos = $inquerito && isset($inquerito->total_adultos) ? (int)$inquerito->total_adultos : 0;
        $totalNegocioProprio = $inquerito && isset($inquerito->total_individuos_negocio_proprio) ? (int)$inquerito->total_individuos_negocio_proprio : 0;
        $setorPropria = $inquerito && is_array($inquerito->total_por_setor_propria) ? $inquerito->total_por_setor_propria : [];

        // Garantir que o JSON é decodificado corretamente
        if ($inquerito && is_string($inquerito->total_por_setor_propria)) {
            $setorPropria = json_decode($inquerito->total_por_setor_propria, true) ?? [];
        }

        // --- 1. Nacionalidades (Top 5) ---
        $nacionalidadesData = $familias->groupBy('nacionalidade')
            ->map->count()
            ->sortDesc()
            ->take(5)
            ->map(fn($count, $name) => ['label' => $name, 'count' => $count])
            ->values()
            ->toArray();

        // --- 2. Tipo de Localização ---
        $localizacaoLabels = [
            'sede_freguesia' => 'Sede da freguesia',
            'lugar_aldeia' => 'Lugar / aldeia',
            'espaco_agroflorestal' => 'Espaço agroflorestal',
        ];

        $localizacaoData = [];
        foreach ($localizacaoLabels as $valor => $label) {
            $localizacaoData[$label] = $familias->where('localizacao_tipo', $valor)->count();
        }

        // --- Condição do Alojamento ---
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

        // --- 3. Estrutura Etária ---
        $etariaData = [
            'Crianças/Jovens (<18)' => $agregados->sum('criancas'),
            'Adultos Laboral (18-65)' => $agregados->sum('adultos_laboral'),
            'Idosos (65+)' => $agregados->sum('adultos_65_mais'),
        ];
        
        // --- 4. Setores de Atividade ---
        $setorTopData = [];
        foreach ($setores as $setorId => $setorNome) {
            $totalIndividuos = $atividades->where('setor_id', $setorId)->sum('n_individuos');
            $setorTopData[] = [
                'label' => $setorNome,
                'count' => $totalIndividuos,
            ];
        }
        $setorTopData = collect($setorTopData)->sortByDesc('count')->values()->toArray();

        // --- 5. Inscrição em Centro de Saúde e Escola ---
        $centroSaudeData = [
            'Inscritas' => $familias->where('inscrito_centro_saude', true)->count(),
            'Por inscrever' => $familias->where('inscrito_centro_saude', false)->count(),
            'Sem identificação' => $familias->whereNull('inscrito_centro_saude')->count() + $familias->where('inscrito_centro_saude', '')->count(),
        ];

        $escolaLabels = [
            'sim' => 'Inscritas',
            'nao' => 'Não inscritas',
            'nao_sei' => 'Sem indicação',
        ];

        $escolaData = [];
        foreach ($escolaLabels as $valor => $label) {
            $escolaData[$label] = $familias->where('inscrito_escola', $valor)->count();
        }

        // --- 6. Necessidades de Apoio ---
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

        // Retorna a vista com os dados preparados para os gráficos
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
            'formularioAtualizado' => true,
        ]);
    }
}