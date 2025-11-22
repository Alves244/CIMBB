@extends('layouts.user_type.auth')

@push('js')
    {{-- Precisamos do Chart.js que é carregado no app.blade --}}
    <script>
        // Dados passados do Controller (JSON)
        const nacionalidadesData = {!! $nacionalidadesJson !!};
        const localizacaoData = {!! $localizacaoJson !!};
        const etariaData = {!! $etariaJson !!};
        const setorTopData = {!! $setorTopJson !!};
        // const propriedadeTempoData removida
        
        // Função utilitária para gerar cores aleatórias
        function generateColors(count, type) {
            const colorsBar = ['#4CAF50', '#03A9F4', '#FFC107', '#E91E63', '#00BCD4'];
            const result = [];
            for (let i = 0; i < count; i++) {
                result.push(colorsBar[i % colorsBar.length]);
            }
            return result;
        }

        document.addEventListener("DOMContentLoaded", function(event) {
            
            // --- GRÁFICO 1: TOP 5 NACIONALIDADES (Barra) ---
            if (document.getElementById('chart-nacionalidades')) {
                new Chart(document.getElementById('chart-nacionalidades').getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: nacionalidadesData.map(d => d.label),
                        datasets: [{
                            label: 'Nº de Famílias',
                            data: nacionalidadesData.map(d => d.count),
                            backgroundColor: generateColors(nacionalidadesData.length),
                            borderWidth: 1,
                            borderRadius: 4,
                            barPercentage: 0.7,
                            categoryPercentage: 0.6
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: { y: { beginAtZero: true } }
                    }
                });
            }

            // --- GRÁFICO 2: LOCALIZAÇÃO (Pie/Donut) ---
            if (document.getElementById('chart-localizacao')) {
                new Chart(document.getElementById('chart-localizacao').getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: Object.keys(localizacaoData),
                        datasets: [{
                            data: Object.values(localizacaoData),
                            backgroundColor: ['#4CAF50', '#03A9F4', '#FFC107'],
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                    }
                });
            }

            // --- GRÁFICO 3: DISTRIBUIÇÃO ETÁRIA (Barra Vertical) ---
            if (document.getElementById('chart-etaria')) {
                new Chart(document.getElementById('chart-etaria').getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: Object.keys(etariaData),
                        datasets: [{
                            label: 'Total de Indivíduos',
                            data: Object.values(etariaData),
                            backgroundColor: ['#4CAF50', '#03A9F4', '#E91E63'],
                            borderWidth: 1,
                            borderRadius: 4,
                            barPercentage: 0.7,
                            categoryPercentage: 0.6
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: { y: { beginAtZero: true } }
                    }
                });
            }

            // --- GRÁFICO 4: TOP 5 SETORES DE ATIVIDADE (Barra Horizontal) ---
            if (document.getElementById('chart-setores')) {
                 new Chart(document.getElementById('chart-setores').getContext('2d'), {
                    type: 'horizontalBar',
                    data: {
                        labels: setorTopData.map(d => d.label),
                        datasets: [{
                            label: 'Atividades Registadas',
                            data: setorTopData.map(d => d.count),
                            backgroundColor: generateColors(setorTopData.length),
                            borderWidth: 1,
                            borderRadius: 4,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: { x: { beginAtZero: true } }
                    }
                });
            }

            // --- GRÁFICO 5: EVOLUÇÃO DA PROPRIEDADE REMOVIDO ---

        });
    </script>
@endpush

@section('content')

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <h6 class="mb-3 text-info">Análise da População Estrangeira na Freguesia</h6>
            <p class="text-sm">Total de Famílias Registadas: <span class="text-dark font-weight-bold">{{ $totalFamilias ?? 0 }}</span></p>
        </div>
    </div>

    {{-- Linha 1: Nacionalidades (Barra) e Localização (Pizza) --}}
    <div class="row mt-4">
        {{-- Gráfico 1: Nacionalidades --}}
        <div class="col-lg-7 mb-4">
            <div class="card z-index-2">
                <div class="card-header pb-0 pt-3 bg-transparent">
                    <h6 class="text-capitalize">Top 5 Nacionalidades por Famílias</h6>
                    <p class="text-secondary text-sm">Identifica as comunidades estrangeiras **mais representativas** na freguesia, facilitando o planeamento de recursos.</p>
                </div>
                <div class="card-body p-3">
                    <div class="chart">
                        <canvas id="chart-nacionalidades" class="chart-canvas" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Gráfico 2: Localização --}}
        <div class="col-lg-5 mb-4">
            <div class="card z-index-2">
                <div class="card-header pb-0 pt-3 bg-transparent">
                    <h6 class="text-capitalize">Distribuição por Tipo de Localização</h6>
                    <p class="text-secondary text-sm">Mostra o **padrão de dispersão** da população estrangeira no território da freguesia (urbana vs. rural).</p>
                </div>
                <div class="card-body p-3">
                    <div class="chart">
                        <canvas id="chart-localizacao" class="chart-canvas" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Linha 2: Distribuição Etária e Top Setores --}}
    <div class="row">
        {{-- Gráfico 3: Distribuição Etária --}}
        <div class="col-lg-6 mb-4">
            <div class="card z-index-2">
                <div class="card-header pb-0 pt-3 bg-transparent">
                    <h6 class="text-capitalize">Distribuição Etária Total (Indivíduos)</h6>
                    <p class="text-secondary text-sm">Determina a **pressão demográfica** e as necessidades de serviços (escolas vs. saúde/apoio social).</p>
                </div>
                <div class="card-body p-3">
                    <div class="chart">
                        <canvas id="chart-etaria" class="chart-canvas" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Gráfico 4: Top 5 Setores de Atividade --}}
        <div class="col-lg-6 mb-4">
            <div class="card z-index-2">
                <div class="card-header pb-0 pt-3 bg-transparent">
                    <h6 class="text-capitalize">Top 5 Setores de Atividade</h6>
                    <p class="text-secondary text-sm">Indica as **áreas da economia local** que mais dependem da mão de obra estrangeira (por atividade registada).</p>
                </div>
                <div class="card-body p-3">
                    <div class="chart">
                        <canvas id="chart-setores" class="chart-canvas" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Linha 3: Evolução da Propriedade REMOVIDA --}}

</div>

@endsection