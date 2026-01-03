@push('css')
<link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700" rel="stylesheet">
<style>
  body, .container-fluid, .card, .card-header, .card-body, h6, p, span, div {
      font-family: 'Nunito', Arial, Helvetica, sans-serif !important;
  }
</style>
@endpush
@extends('layouts.user_type.auth')

@section('content')

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <h6 class="mb-3 text-info">Análise Estatística da Freguesia</h6>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-lg-6 col-xl-6 mb-4">
            <div class="card z-index-2 h-100">
                <div class="card-header pb-0 pt-3 bg-transparent">
                    <h6 class="text-capitalize">Distribuição Etária</h6>
                    <p class="text-secondary text-sm">Adultos, idosos e crianças (formulário atualizado).</p>
                </div>
                <div class="card-body p-3">
                    <div class="chart">
                        <canvas id="chart-etaria" class="chart-canvas" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-xl-6 mb-4">
            <div class="row h-100">
                <div class="col-md-12 col-lg-12 mb-4">
                    <div class="card z-index-2 h-100">
                        <div class="card-header pb-0 pt-3 bg-transparent">
                            <h6 class="text-capitalize">Necessidades de Apoio</h6>
                            <p class="text-secondary text-sm">Necessidades de apoio mais sinalizadas pelas famílias.</p>
                        </div>
                        <div class="card-body p-3">
                            <div class="chart">
                                <canvas id="chart-necessidades" class="chart-canvas" height="220"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 mb-4">
                    <div class="card z-index-2 h-100">
                        <div class="card-header pb-0 pt-3 bg-transparent">
                            <h6 class="text-capitalize">Centro de Saúde</h6>
                            <p class="text-secondary text-sm">Famílias inscritas vs. por inscrever.</p>
                        </div>
                        <div class="card-body p-3">
                            <div class="chart">
                                <canvas id="chart-centro-saude" class="chart-canvas" height="180"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 mb-4">
                    <div class="card z-index-2 h-100">
                        <div class="card-header pb-0 pt-3 bg-transparent">
                            <h6 class="text-capitalize">Agrupamento de Escolas</h6>
                            <p class="text-secondary text-sm">Situação de inscrição das crianças/jovens.</p>
                        </div>
                        <div class="card-body p-3">
                            <div class="chart">
                                <canvas id="chart-escola" class="chart-canvas" height="180"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    // Dados do backend
    const etariaData = {!! $etariaJson !!};
    const setorTopData = {!! $setorTopJson !!};
    const necessidadesData = {!! $necessidadesJson !!};
    const centroSaudeData = {!! $centroSaudeJson !!};
    const escolaData = {!! $escolaJson !!};

    function generateColors(count) {
        const colors = ['#4CAF50', '#03A9F4', '#FFC107', '#E91E63', '#00BCD4', '#fd7e14', '#adb5bd'];
        return Array.from({length: count}, (_, i) => colors[i % colors.length]);
    }

    document.addEventListener("DOMContentLoaded", function() {
        // 1. Gráfico de barras empilhadas: distribuição etária
        if (document.getElementById('chart-etaria')) {
            new Chart(document.getElementById('chart-etaria').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: Object.keys(etariaData),
                    datasets: [{
                        label: 'Total',
                        data: Object.values(etariaData),
                        backgroundColor: generateColors(Object.keys(etariaData).length),
                        borderWidth: 1,
                        borderRadius: 4,
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

        // Gráfico de setores de atividade removido conforme solicitado

        // 3. Gráfico de barras: necessidades de apoio
        if (document.getElementById('chart-necessidades')) {
            new Chart(document.getElementById('chart-necessidades').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: necessidadesData.map(item => item.label),
                    datasets: [{
                        label: 'Sinalizações',
                        data: necessidadesData.map(item => item.count),
                        backgroundColor: generateColors(necessidadesData.length),
                        borderRadius: 4,
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

        // 4. Gráfico de donut: inscritos saúde
        if (document.getElementById('chart-centro-saude')) {
            new Chart(document.getElementById('chart-centro-saude').getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: Object.keys(centroSaudeData),
                    datasets: [{
                        data: Object.values(centroSaudeData),
                        backgroundColor: generateColors(Object.keys(centroSaudeData).length),
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } }
                }
            });
        }

        // 5. Gráfico de donut: situação escolar
        if (document.getElementById('chart-escola')) {
            new Chart(document.getElementById('chart-escola').getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: Object.keys(escolaData),
                    datasets: [{
                        data: Object.values(escolaData),
                        backgroundColor: generateColors(Object.keys(escolaData).length),
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } }
                }
            });
        }
    });
</script>
@endpush
