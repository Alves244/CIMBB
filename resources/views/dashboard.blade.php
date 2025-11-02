@extends('layouts.user_type.auth') {{-- Usa o layout correto --}}

@section('content') {{-- Usa a secção 'content' correta --}}

  <div class="container-fluid py-4">
    <div class="row">
      
      {{-- Coluna da Esquerda: Caixa de Boas-Vindas --}}
      <div class="col-lg-7 mb-lg-0 mb-4">
        <div class="card h-100">
          <div class="card-body p-4">
            <div class="row">
              <div class="col-12">
                <div class="d-flex flex-column h-100">
                  {{-- 1. Caixa de Boas-Vindas --}}
                  <h5 class="font-weight-bolder">Bem-vindo, {{ $userName ?? 'Utilizador' }}!</h5>
                  
                  {{-- 2. Descrição do Programa --}}
                  <p class="mb-4">
                    Este é o Sistema de Monitorização da Integração de Residentes Estrangeiros (SMRE) 
                    da Comunidade Intermunicipal da Beira Baixa (CIMBB).
                  </p>
                  <p class="mb-4">
                    Esta plataforma permite o registo e a análise de dados demográficos e 
                    socioeconómicos das famílias estrangeiras residentes no território, 
                    facilitando a tomada de decisões estratégicas.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- Coluna da Direita: Gráfico de Nacionalidades --}}
      <div class="col-lg-5">
        <div class="card h-100">
          <div class="card-header pb-0">
            <h6>Nacionalidades</h6>
            {{-- Filtro (só aparece se for freguesia) --}}
            @if(auth()->user()->isFreguesia())
              <p class="text-sm">
                <i class="fa fa-map-marker-alt text-success me-1"></i>
                <span class="font-weight-bold">Dados da Freguesia:</span> {{ auth()->user()->freguesia->nome ?? 'N/A' }}
              </p>
            @else
              <p class="text-sm">
                <i class="fa fa-globe text-success me-1"></i>
                <span class="font-weight-bold">Dados de Todo o Território</span>
              </p>
            @endif
          </div>
          <div class="card-body p-3 d-flex align-items-center justify-content-center">
            @if($chartValues->count() > 0)
              {{-- O Canvas onde o gráfico será desenhado --}}
              <div class="chart w-100">
                <canvas id="nacionalidadeChart" class="chart-canvas" height="300"></canvas>
              </div>
            @else
              {{-- Mensagem se não houver dados --}}
              <p class="text-sm w-100 text-center">Ainda não existem dados de famílias para mostrar.</p>
            @endif
          </div>
        </div>
      </div>

    </div>
  </div>

@endsection

{{-- Adicionar o Script do Chart.js no final da página --}}
@push('js')
  {{-- O 'chartjs.min.js' já é carregado pelo teu app.blade.php --}}
  <script>
    document.addEventListener("DOMContentLoaded", (event) => {
      var ctx = document.getElementById("nacionalidadeChart");
      if (ctx) {
        var chartCanvas = ctx.getContext("2d");

        // Transforma os dados do PHP (Blade) para JavaScript
        var chartLabels = @json($chartLabels);
        var chartData = @json($chartValues);

        // Define as nossas cores (priorizando o verde)
        var chartColors = [
            '#82d616', // Verde Sucesso
            '#344767', // Cinza Escuro
            '#17c1e8', // Azul Info
            '#fbcf33', // Amarelo Warning
            '#f53939', // Vermelho Danger
            '#a0d36c', // Verde Claro
            '#6c757d', // Cinza
            '#cb0c9f', // Roxo (última opção)
        ];

        new Chart(chartCanvas, {
          type: "doughnut", // Tipo de gráfico circular
          data: {
            labels: chartLabels,
            datasets: [{
              label: "Total Famílias",
              data: chartData,
              backgroundColor: chartColors,
              borderWidth: 0
            }],
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: {
                position: 'bottom', // Põe a legenda em baixo
                labels: {
                  padding: 20,
                  boxWidth: 10,
                  font: {
                    size: 11
                  }
                }
              },
              tooltip: {
                callbacks: {
                  // Personaliza o que aparece quando passas o rato por cima
                  label: function(context) {
                    let label = context.label || '';
                    let value = context.raw || 0;
                    
                    // Calcular a percentagem
                    let total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                    let percentage = (value / total * 100).toFixed(1);

                    // Retorna a string final (ex: "Brasil: 5 (25.0%)")
                    return ` ${label}: ${value} (${percentage}%)`;
                  }
                }
              }
            },
            cutout: '60%' // O "buraco" no meio do gráfico
          }
        });
      }
    });
  </script>
@endpush