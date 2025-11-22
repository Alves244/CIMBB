<?php

namespace App\Http\Controllers\Freguesia;

use App\Http\Controllers\Controller;
use App\Models\Familia;
use App\Models\AgregadoFamiliar;
use App\Models\AtividadeEconomica;
use App\Models\SetorAtividade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GraficosFreguesiaController extends Controller
{
    /**
     * Prepara os dados para serem visualizados em gráficos.
     */
    public function index()
    {
        $freguesiaId = Auth::user()->freguesia_id;
        
        // 1. Buscar todos os dados relevantes da freguesia
        $familias = Familia::where('freguesia_id', $freguesiaId)->get();
        $familiaIds = $familias->pluck('id');
        $agregados = AgregadoFamiliar::whereIn('familia_id', $familiaIds)->get();
        $atividades = AtividadeEconomica::whereIn('familia_id', $familiaIds)->get();
        $setores = SetorAtividade::all()->pluck('nome', 'id');

        // --- 1. Nacionalidades (Top 5) ---
        $nacionalidadesData = $familias->groupBy('nacionalidade')
            ->map->count()
            ->sortDesc()
            ->take(5)
            ->map(fn($count, $name) => ['label' => $name, 'count' => $count])
            ->values()
            ->toArray();

        // --- 2. Localização (Pie Chart Data) ---
        $localizacaoData = [
            'Nucleo Urbano' => $familias->where('localizacao', 'nucleo_urbano')->count(),
            'Aldeia Anexa' => $familias->where('localizacao', 'aldeia_anexa')->count(),
            'Espaço Agroflorestal' => $familias->where('localizacao', 'espaco_agroflorestal')->count(),
        ];

        // --- 3. Distribuição Etária (Total de Indivíduos) ---
        $etariaData = [
            'Adultos Laboral (18-65)' => $agregados->sum('adultos_laboral'),
            'Adultos Seniores (65+)' => $agregados->sum('adultos_65_mais'),
            'Crianças/Jovens (<18)' => $agregados->sum('criancas'),
        ];
        
        // --- 4. Top 5 Setores de Atividade ---
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

        // --- REMOVIDO: Evolução da Propriedade (Gráfico 5) ---

        // Passar os dados para a View
        return view('freguesia.graficos.index', [
            'nacionalidadesJson' => json_encode($nacionalidadesData),
            'localizacaoJson' => json_encode($localizacaoData),
            'etariaJson' => json_encode($etariaData),
            'setorTopJson' => json_encode($setorTopData),
            'totalFamilias' => $familias->count(),
            // 'propriedadeTempoJson' REMOVIDO
        ]);
    }
}