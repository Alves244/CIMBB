@extends('layouts.user_type.auth') {{-- Usa o layout correto --}}

@section('content') {{-- Usa a secção 'content' correta --}}

  <div class="container-fluid py-4">
    @php
      $authUser = Auth::user();
      $mostrarDashboardRegional = $mostrarDashboardRegional ?? false;
      $concelhosResumo = $concelhosResumo ?? collect();
      $dashboardProgress = $dashboardProgress ?? [
        'totalConcelhos' => 0,
        'concelhosComInquerito' => 0,
        'percentual' => 0,
      ];
    @endphp
    
    {{-- ***** CARTÕES RESUMO ***** --}}
    @if($authUser->isFreguesia())
      <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
          <div class="card h-100">
            <div class="card-body p-3">
              <div class="d-flex justify-content-between align-items-start">
                <div class="numbers">
                  <p class="text-sm mb-1 text-capitalize font-weight-bold">Famílias registadas</p>
                  <h5 class="font-weight-bolder mb-0">{{ $totalFamilias }}</h5>
                  <p class="text-xs text-secondary mb-0">Dados apenas da tua freguesia.</p>
                </div>
                <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                  <i class="fas fa-users text-lg opacity-10" aria-hidden="true"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
          <div class="card h-100">
            <div class="card-body p-3 d-flex flex-column">
              <div class="d-flex justify-content-between align-items-start">
                <div class="numbers">
                  <p class="text-sm mb-1 text-capitalize font-weight-bold">Tickets respondidos</p>
                  <h5 class="font-weight-bolder mb-0">{{ $ticketsRespondidos }}</h5>
                  <p class="text-xs text-secondary mb-2">
                    @if($ticketsRespondidos > 0)
                      Existem respostas da CIMBB a aguardar acompanhamento.
                    @else
                      Sem novas respostas.
                    @endif
                  </p>
                </div>
                <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                  <i class="fas fa-headset text-lg opacity-10" aria-hidden="true"></i>
                </div>
              </div>
              <a href="{{ route('freguesia.suporte.index') }}" class="btn btn-sm bg-gradient-secondary mt-auto align-self-start">Abrir suporte</a>
            </div>
          </div>
        </div>
        <div class="col-xl-4 col-md-12 mb-4">
          <div class="card h-100">
            <div class="card-body p-3 d-flex flex-column">
              <div class="numbers">
                <p class="text-sm mb-1 text-capitalize font-weight-bold">Inquérito {{ $inqueritoAnoAtual }}</p>
                @if($inqueritoDisponivel)
                  <h6 class="text-success mb-1">Disponível até {{ optional($inqueritoPrazo)->format('d/m/Y') }}</h6>
                  <p class="text-xs text-secondary mb-3">Submete o questionário anual para atualizar os dados da freguesia.</p>
                  <a href="{{ route('freguesia.inqueritos.create') }}" class="btn btn-sm bg-gradient-success">Preencher agora</a>
                @elseif($jaPreencheuInquerito)
                  <h6 class="text-success mb-1">Inquérito submetido</h6>
                  <p class="text-xs text-secondary mb-3">Obrigado! Podes rever ou atualizar até {{ optional($inqueritoPrazo)->format('d/m/Y') }}.</p>
                  <a href="{{ route('freguesia.inqueritos.index') }}" class="btn btn-sm bg-gradient-secondary">Ver histórico</a>
                @else
                  <h6 class="text-danger mb-1">Prazo expirado</h6>
                  <p class="text-xs text-secondary mb-3">Contacta a CIMBB para regularizar o inquérito deste ano.</p>
                  <a href="{{ route('freguesia.inqueritos.index') }}" class="btn btn-sm bg-gradient-secondary">Mais detalhes</a>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
    @elseif($mostrarDashboardRegional)
      <div class="row">
        <div class="col-xl-3 col-sm-6 mb-4">
          <div class="card">
            <div class="card-body p-3">
              <div class="row">
                <div class="col-8">
                  <div class="numbers">
                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Famílias</p>
                    <h5 class="font-weight-bolder mb-0">{{ $totalFamilias }}</h5>
                  </div>
                </div>
                <div class="col-4 text-end">
                  <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                    <i class="fas fa-users text-lg opacity-10" aria-hidden="true"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-xl-3 col-sm-6 mb-4">
          <div class="card">
            <div class="card-body p-3">
              <div class="row">
                <div class="col-8">
                  <div class="numbers">
                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Membros</p>
                    <h5 class="font-weight-bolder mb-0">{{ $totalMembros }}</h5>
                  </div>
                </div>
                <div class="col-4 text-end">
                  <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                    <i class="fas fa-user-friends text-lg opacity-10" aria-hidden="true"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-xl-3 col-sm-6 mb-4">
          <div class="card">
            <div class="card-body p-3">
              <div class="row">
                <div class="col-8">
                  <div class="numbers">
                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Tickets por responder</p>
                    <h5 class="font-weight-bolder mb-0">{{ $ticketsPendentes }}</h5>
                  </div>
                </div>
                <div class="col-4 text-end">
                  <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                    <i class="fas fa-headset text-lg opacity-10" aria-hidden="true"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-xl-3 col-sm-6 mb-4">
          <div class="card">
            <div class="card-body p-3">
              <div class="d-flex justify-content-between">
                <div>
                  <p class="text-sm mb-0 text-capitalize font-weight-bold">Concelhos com inquérito</p>
                  <h5 class="font-weight-bolder mb-0">
                    {{ $dashboardProgress['concelhosComInquerito'] }} / {{ $dashboardProgress['totalConcelhos'] }}
                  </h5>
                </div>
                <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                  <i class="fas fa-clipboard-check text-lg opacity-10" aria-hidden="true"></i>
                </div>
              </div>
              <div class="mt-3">
                <div class="progress">
                  <div class="progress-bar bg-gradient-info" role="progressbar" style="width: {{ $dashboardProgress['percentual'] }}%;" aria-valuenow="{{ $dashboardProgress['percentual'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <small class="text-muted">{{ $dashboardProgress['percentual'] }}% do território com inquérito submetido.</small>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row mb-4">
        <div class="col-12">
          <div class="card">
            <div class="card-header pb-0 d-flex justify-content-between align-items-center">
              <div>
                <h6 class="mb-0">Progresso por Concelho</h6>
                <p class="text-sm mb-0 text-secondary">Famílias, membros e estado dos inquéritos por concelho.</p>
              </div>
            </div>
            <div class="card-body px-3 pb-3">
              @if($concelhosResumo->isNotEmpty())
                <div class="table-responsive">
                  <table class="table align-items-center mb-0">
                    <thead>
                      <tr>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Concelho</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Famílias</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Membros</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tickets pendentes</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Inquérito</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($concelhosResumo as $concelho)
                        <tr>
                          <td>
                            <div class="d-flex flex-column">
                              <span class="text-sm font-weight-bold">{{ $concelho['nome'] }}</span>
                              <small class="text-xs text-secondary">{{ $concelho['codigo'] ?? '—' }}</small>
                            </div>
                          </td>
                          <td class="text-sm">{{ $concelho['total_familias'] }}</td>
                          <td class="text-sm">{{ $concelho['total_membros'] }}</td>
                          <td class="text-sm">{{ $concelho['tickets_pendentes'] }}</td>
                          <td>
                            <div class="d-flex flex-column">
                              <small class="text-xs text-secondary mb-1">
                                {{ $concelho['freguesias_com_inquerito'] }} / {{ $concelho['total_freguesias'] }} freguesias
                              </small>
                              <div class="progress">
                                <div class="progress-bar bg-gradient-success" role="progressbar" style="width: {{ $concelho['percentual_inquerito'] }}%;" aria-valuenow="{{ $concelho['percentual_inquerito'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                              </div>
                            </div>
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              @else
                <p class="text-sm text-secondary mb-0">Ainda não existem dados para apresentar o progresso regional.</p>
              @endif
            </div>
          </div>
        </div>
      </div>
    @else
      <div class="row">
        <div class="col-xl-6 col-sm-12 mb-4">
          <div class="card">
            <div class="card-body p-3">
              <div class="row">
                <div class="col-8">
                  <div class="numbers">
                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Famílias</p>
                    <h5 class="font-weight-bolder mb-0">{{ $totalFamilias }}</h5>
                  </div>
                </div>
                <div class="col-4 text-end">
                  <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                    <i class="fas fa-users text-lg opacity-10" aria-hidden="true"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-6 col-sm-12 mb-4">
          <div class="card">
            <div class="card-body p-3">
              <div class="row">
                <div class="col-8">
                  <div class="numbers">
                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Tickets por responder</p>
                    <h5 class="font-weight-bolder mb-0">{{ $ticketsPendentes }}</h5>
                  </div>
                </div>
                <div class="col-4 text-end">
                  <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                    <i class="fas fa-headset text-lg opacity-10" aria-hidden="true"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    @endif
    {{-- ***** FIM DA LINHA DE CARTÕES ***** --}}


    <div class="row mt-4">
      
      {{-- Coluna da Esquerda: Caixa de Boas-Vindas (JÁ TINHA) --}}
      <div class="col-lg-7 mb-lg-0 mb-4">
        <div class="card h-100">
          <div class="card-body p-4">
            <div class="row">
              <div class="col-12">
                <div class="d-flex flex-column h-100">
                  <h5 class="font-weight-bolder">Bem-vindo, {{ Auth::user()->nome }}!</h5>
                  <p class="mb-4">
                    Este é o Sistema de Monitorização da Integração de Residentes Estrangeiros (SMRE) 
                    da Comunidade Intermunicipal da Beira Baixa (CIMBB).
                  </p>
                  {{-- Mostra a localidade que está a ver --}}
                  <p class="mb-0">
                    <i class="fa fa-map-marker-alt text-success me-1"></i>
                    A visualizar dados de: <span class="font-weight-bold">{{ $nomeLocalidade }}</span>
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- Coluna da Direita: Gráfico de Nacionalidades (JÁ TINHA) --}}
      <div class="col-lg-5">
        <div class="card h-100">
          <div class="card-header pb-0">
            <h6>Nacionalidades (Top 10)</h6>
            <p class="text-sm">
                <span class="font-weight-bold">{{ $tituloDashboard }}</span>
            </p>
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
  {{-- O 'chartjs.min.js' já é carregado pelo seu app.blade.php --}}
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