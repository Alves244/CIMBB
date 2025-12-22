@extends('layouts.user_type.auth')

@php
  $filters = $filters ?? [];
  $totalAlunos = (int) ($totaisEscolas['totalAlunos'] ?? 0);
  $totalInqueritos = (int) ($totaisEscolas['totalInqueritos'] ?? 0);
  $nivelEnsinoDistrib = collect($distribuicoesEscolas['nivel_ensino'] ?? [])->filter();
  $nacionalidadeDistrib = collect($distribuicoesEscolas['nacionalidade'] ?? [])->filter();
  $concelhoDistrib = collect($distribuicoesEscolas['concelho'] ?? [])->filter();
  $agrupamentosDistrib = collect($distribuicoesEscolas['agrupamentos'] ?? [])->filter();

  $escopoQuery = collect(request()->query())->except(['escopo', 'page'])->toArray();
  $freguesiasToggleUrl = route('funcionario.relatorios.index', array_merge($escopoQuery, ['escopo' => 'freguesias']));
  $escolasToggleUrl = route('funcionario.relatorios.index', array_merge($escopoQuery, ['escopo' => 'escolas']));
  $exportQuery = collect(request()->query())->except(['escopo', 'page'])->toArray();
  $exportFreguesiasUrl = route('funcionario.relatorios.export', array_merge($exportQuery, ['escopo' => 'freguesias']));
  $exportEscolasUrl = route('funcionario.relatorios.export', array_merge($exportQuery, ['escopo' => 'escolas']));

  $escolaFilterLabels = [
    'concelho_id' => 'Concelho',
    'agrupamento_id' => 'Agrupamento',
    'nivel_ensino' => 'Nível de ensino',
    'nacionalidade' => 'Nacionalidade',
  ];

  $activeFilters = collect($filters)
    ->except(['ano'])
    ->reject(fn ($value) => $value === null || $value === '' || $value === 'all')
    ->mapWithKeys(function ($value, $key) use ($escolaFilterLabels, $concelhos, $agrupamentos) {
      switch ($key) {
        case 'concelho_id':
          $nome = optional($concelhos->firstWhere('id', (int) $value))->nome;
          return [$escolaFilterLabels[$key] ?? ucfirst($key) => $nome ?: '—'];
        case 'agrupamento_id':
          $nome = optional($agrupamentos->firstWhere('id', (int) $value))->nome;
          return [$escolaFilterLabels[$key] ?? ucfirst($key) => $nome ?: '—'];
        case 'nivel_ensino':
          return [$escolaFilterLabels[$key] ?? ucfirst($key) => ucfirst(str_replace('_', ' ', $value))];
        default:
          return [$escolaFilterLabels[$key] ?? ucfirst($key) => $value];
      }
    })
    ->filter();

  $chartColorPalette = ['#17ad37', '#82d616', '#2dce89', '#5e72e4', '#f5365c', '#fb6340', '#ffd600', '#11cdef'];

  $assignColors = function (int $count) use ($chartColorPalette) {
    $colors = [];
    $paletteSize = max(1, count($chartColorPalette));
    for ($i = 0; $i < $count; $i++) {
      $colors[] = $chartColorPalette[$i % $paletteSize];
    }
    return $colors;
  };

  $buildChartConfig = function ($labels, $values, $datasetLabel = 'Alunos') use ($assignColors) {
    $labels = array_values($labels);
    $values = array_map('intval', array_values($values));
    return [
      'labels' => $labels,
      'datasets' => [[
        'label' => $datasetLabel,
        'data' => $values,
        'backgroundColor' => $assignColors(count($labels)),
        'borderWidth' => 0,
      ]],
      'defaultType' => 'bar',
    ];
  };

  $chartConfigsEscolas = [];

  if ($nivelEnsinoDistrib->isNotEmpty()) {
    $chartConfigsEscolas['chart-escolas-nivel'] = $buildChartConfig(
      $nivelEnsinoDistrib->keys()->map(fn ($nivel) => ucfirst(str_replace('_', ' ', $nivel ?: '—')))->toArray(),
      $nivelEnsinoDistrib->values()->toArray(),
      'Alunos'
    );
  }

  if ($agrupamentosDistrib->isNotEmpty()) {
    $chartConfigsEscolas['chart-escolas-agrupamentos'] = $buildChartConfig(
      $agrupamentosDistrib->keys()->toArray(),
      $agrupamentosDistrib->values()->toArray(),
      'Alunos'
    );
  }

  if ($nacionalidadeDistrib->isNotEmpty()) {
    $chartConfigsEscolas['chart-escolas-nacionalidade'] = $buildChartConfig(
      $nacionalidadeDistrib->keys()->map(fn ($nacionalidade) => $nacionalidade ?: '—')->toArray(),
      $nacionalidadeDistrib->values()->toArray(),
      'Alunos'
    );
  }

  if ($concelhoDistrib->isNotEmpty()) {
    $chartConfigsEscolas['chart-escolas-concelhos'] = $buildChartConfig(
      $concelhoDistrib->keys()->toArray(),
      $concelhoDistrib->values()->toArray(),
      'Alunos'
    );
  }

  $chartConfigsEscolas['chart-escolas-total-global'] = [
    'labels' => ['Total global'],
    'datasets' => [[
      'label' => 'Alunos reportados',
      'data' => [$totalAlunos],
      'backgroundColor' => ['#17ad37'],
      'borderWidth' => 0,
    ]],
    'defaultType' => 'bar',
  ];
@endphp

@section('content')
  <div class="container-fluid py-4">
    <div class="d-flex justify-content-center mb-4">
      <div class="btn-group scope-toggle" role="group">
        <a href="{{ $freguesiasToggleUrl }}" class="btn btn-sm {{ $escopoDados === 'freguesias' ? 'btn-success' : 'btn-outline-success' }}">Freguesias</a>
        <a href="{{ $escolasToggleUrl }}" class="btn btn-sm {{ $escopoDados === 'escolas' ? 'btn-success' : 'btn-outline-success' }}">Escolas</a>
      </div>
    </div>

    <div class="row mb-4">
      <div class="col-12">
        <div class="card border border-success shadow-sm position-relative overflow-hidden">
          <div class="card-body d-flex flex-column flex-lg-row justify-content-between align-items-start gap-4">
            <div>
              <h4 class="mb-2 text-dark">Monitoriza a integração e capacidade escolar</h4>
              <p class="mb-0 text-secondary">Filtra por concelho, agrupamento ou nível de ensino para obter indicadores específicos das escolas do território CIMBB.</p>
            </div>
            <div class="text-lg-end">
              <span class="text-xs text-secondary">Ano em análise</span>
              <h2 class="font-weight-bolder text-success mb-1">{{ $anoSelecionado }}</h2>
              <form method="GET" action="{{ route('funcionario.relatorios.index') }}" class="d-flex align-items-stretch gap-3 mt-3">
                <input type="hidden" name="escopo" value="escolas">
                <div class="year-control d-flex align-items-center">
                  <select name="ano" class="form-select year-select w-100">
                    @foreach($anosDisponiveis as $ano)
                      <option value="{{ $ano }}" {{ (int) $anoSelecionado === (int) $ano ? 'selected' : '' }}>{{ $ano }}</option>
                    @endforeach
                  </select>
                </div>
                @foreach(request()->query() as $param => $value)
                  @continue(in_array($param, ['ano', 'escopo'], true))
                  <input type="hidden" name="{{ $param }}" value="{{ $value }}">
                @endforeach
                <button type="submit" class="btn btn-success px-4 year-control text-white align-self-stretch d-flex align-items-center justify-content-center">Alterar</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-body">
        <form class="filter-schools-form" method="GET" action="{{ route('funcionario.relatorios.index') }}">
          <input type="hidden" name="escopo" value="escolas">
          <div class="row g-3">
            <div class="col-12 col-lg-3">
              <div class="border rounded-4 h-100 p-3">
                <small class="text-uppercase text-secondary fw-bold">Janela temporal</small>
                <label class="form-label text-xs text-secondary mt-3 mb-1">Ano</label>
                <select name="ano" class="form-select form-select-sm year-select">
                  @foreach($anosDisponiveis as $ano)
                    <option value="{{ $ano }}" {{ (int) $filters['ano'] === (int) $ano ? 'selected' : '' }}>{{ $ano }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-12 col-lg-5">
              <div class="border rounded-4 h-100 p-3">
                <small class="text-uppercase text-secondary fw-bold">Território</small>
                <div class="row g-3 mt-2">
                  <div class="col-md-6">
                    <label class="form-label text-xs text-secondary">Concelho</label>
                    <select name="concelho_id" id="filtro_concelho" class="form-select form-select-sm">
                      <option value="all" {{ ($filters['concelho_id'] ?? 'all') === 'all' ? 'selected' : '' }}>Todos</option>
                      @foreach($concelhos as $concelho)
                        <option value="{{ $concelho->id }}" {{ (string) ($filters['concelho_id'] ?? 'all') === (string) $concelho->id ? 'selected' : '' }}>{{ $concelho->nome }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label text-xs text-secondary">Agrupamento</label>
                    <select name="agrupamento_id" id="filtro_agrupamento" class="form-select form-select-sm">
                      <option value="all" {{ ($filters['agrupamento_id'] ?? 'all') === 'all' ? 'selected' : '' }}>Todos</option>
                      @foreach($agrupamentos as $agrupamento)
                        <option value="{{ $agrupamento->id }}" data-concelho="{{ $agrupamento->concelho_id }}" {{ (string) ($filters['agrupamento_id'] ?? 'all') === (string) $agrupamento->id ? 'selected' : '' }}>
                          {{ $agrupamento->nome }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-12 col-lg-4">
              <div class="border rounded-4 h-100 p-3">
                <small class="text-uppercase text-secondary fw-bold">Caracterização</small>
                <div class="row g-3 mt-2">
                  <div class="col-md-6">
                    <label class="form-label text-xs text-secondary">Nível de ensino</label>
                    <select name="nivel_ensino" class="form-select form-select-sm">
                      <option value="all" {{ ($filters['nivel_ensino'] ?? 'all') === 'all' ? 'selected' : '' }}>Todos</option>
                      @foreach($niveisEnsino as $nivel)
                        <option value="{{ $nivel }}" {{ ($filters['nivel_ensino'] ?? 'all') === $nivel ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $nivel)) }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label text-xs text-secondary">Nacionalidade</label>
                    <select name="nacionalidade" class="form-select form-select-sm">
                      <option value="all" {{ ($filters['nacionalidade'] ?? 'all') === 'all' ? 'selected' : '' }}>Todas</option>
                      @foreach($nacionalidadesEscolas as $nacionalidade)
                        <option value="{{ $nacionalidade }}" {{ ($filters['nacionalidade'] ?? 'all') === $nacionalidade ? 'selected' : '' }}>{{ $nacionalidade }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>
            </div>

            @if($activeFilters->isNotEmpty())
              <div class="col-12">
                <div class="alert alert-light border d-flex flex-wrap gap-2 mb-0">
                  <span class="text-xs text-secondary text-uppercase">Filtros ativos ({{ $activeFilters->count() }})</span>
                  @foreach($activeFilters as $label => $valor)
                    <span class="badge bg-success-subtle text-success">{{ $label }}: {{ $valor }}</span>
                  @endforeach
                </div>
              </div>
            @endif

            <div class="col-12">
              <div class="d-flex flex-column flex-xl-row gap-3 align-items-stretch align-items-xl-end border-top pt-3 mt-1">
                <div class="flex-grow-1">
                  <p class="text-sm text-secondary mb-0">Aplica os filtros para atualizar todos os indicadores desta página.</p>
                </div>
                <div class="d-flex flex-column flex-md-row gap-2 w-100 w-xl-auto">
                  <a href="{{ route('funcionario.relatorios.index', ['escopo' => 'escolas']) }}" class="btn btn-outline-secondary w-100">Limpar</a>
                  <button class="btn bg-gradient-success text-white w-100" type="submit">Aplicar filtros</button>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>

    <div class="row g-3 mb-4">
      <div class="col-md-3">
        <div class="card shadow-sm border-0 h-100">
          <div class="card-body">
            <p class="text-xs text-secondary text-uppercase mb-1">Inquéritos submetidos</p>
            <h3 class="mb-0">{{ number_format($totaisEscolas['totalInqueritos'] ?? 0, 0, ',', ' ') }}</h3>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card shadow-sm border-0 h-100">
          <div class="card-body">
            <p class="text-xs text-secondary text-uppercase mb-1">Agrupamentos abrangidos</p>
            <h3 class="mb-0">{{ number_format($totaisEscolas['totalAgrupamentos'] ?? 0, 0, ',', ' ') }}</h3>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card shadow-sm border-0 h-100">
          <div class="card-body">
            <p class="text-xs text-secondary text-uppercase mb-1">Alunos reportados</p>
            <h3 class="mb-0">{{ number_format($totalAlunos, 0, ',', ' ') }}</h3>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card shadow-sm border-0 h-100">
          <div class="card-body">
            <p class="text-xs text-secondary text-uppercase mb-1">Média de alunos / inquérito</p>
            <h3 class="mb-0">{{ number_format($totaisEscolas['mediaAlunos'] ?? 0, 0, ',', ' ') }}</h3>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-4 mb-4">
      <div class="col-12 col-lg-6">
        <div class="card h-100">
          <div class="card-header pb-0 d-flex justify-content-between align-items-start gap-3">
            <div>
              <h6 class="mb-0">Totais por nível de ensino</h6>
              <span class="text-xs text-secondary">Alunos distribuídos pelos ciclos</span>
            </div>
            @if($nivelEnsinoDistrib->isNotEmpty())
              <div class="btn-group btn-group-sm chart-toggle-group" role="group" aria-label="Tipo de gráfico">
                <button type="button" class="btn btn-outline-success chart-toggle" data-chart="chart-escolas-nivel" data-type="bar">Barras</button>
                <button type="button" class="btn btn-outline-success chart-toggle" data-chart="chart-escolas-nivel" data-type="pie">Circular</button>
              </div>
            @endif
          </div>
          <div class="card-body">
            @if($nivelEnsinoDistrib->isNotEmpty())
              <div class="chart-wrapper">
                <canvas id="chart-escolas-nivel"></canvas>
              </div>
            @else
              <p class="text-sm text-secondary mb-0">Sem registos para os filtros selecionados.</p>
            @endif
          </div>
        </div>
      </div>
      <div class="col-12 col-lg-6">
        <div class="card h-100">
          <div class="card-header pb-0 d-flex justify-content-between align-items-start gap-3">
            <div>
              <h6 class="mb-0">Totais por nacionalidade</h6>
              <span class="text-xs text-secondary">Principais países reportados</span>
            </div>
            @if($nacionalidadeDistrib->isNotEmpty())
              <div class="btn-group btn-group-sm chart-toggle-group" role="group" aria-label="Tipo de gráfico">
                <button type="button" class="btn btn-outline-success chart-toggle" data-chart="chart-escolas-nacionalidade" data-type="bar">Barras</button>
                <button type="button" class="btn btn-outline-success chart-toggle" data-chart="chart-escolas-nacionalidade" data-type="pie">Circular</button>
              </div>
            @endif
          </div>
          <div class="card-body">
            @if($nacionalidadeDistrib->isNotEmpty())
              <div class="chart-wrapper">
                <canvas id="chart-escolas-nacionalidade"></canvas>
              </div>
            @else
              <p class="text-sm text-secondary mb-0">Sem registos para os filtros selecionados.</p>
            @endif
          </div>
        </div>
      </div>
    </div>

    <div class="row g-4 mb-4">
      <div class="col-lg-6">
        <div class="card h-100">
          <div class="card-header pb-0 d-flex justify-content-between align-items-start gap-3">
            <div>
              <h6 class="mb-0">Totais por estabelecimento de ensino</h6>
              <span class="text-xs text-secondary">Top agrupamentos com maior número de alunos</span>
            </div>
            @if($agrupamentosDistrib->isNotEmpty())
              <div class="btn-group btn-group-sm chart-toggle-group" role="group" aria-label="Tipo de gráfico">
                <button type="button" class="btn btn-outline-success chart-toggle" data-chart="chart-escolas-agrupamentos" data-type="bar">Barras</button>
                <button type="button" class="btn btn-outline-success chart-toggle" data-chart="chart-escolas-agrupamentos" data-type="pie">Circular</button>
              </div>
            @endif
          </div>
          <div class="card-body">
            @if($agrupamentosDistrib->isNotEmpty())
              <div class="chart-wrapper">
                <canvas id="chart-escolas-agrupamentos"></canvas>
              </div>
            @else
              <p class="text-sm text-secondary mb-0">Sem registos para os filtros selecionados.</p>
            @endif
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="card h-100">
          <div class="card-header pb-0 d-flex justify-content-between align-items-start gap-3">
            <div>
              <h6 class="mb-0">Totais por concelho</h6>
              <span class="text-xs text-secondary">Comparativo territorial de matrículas</span>
            </div>
            @if($concelhoDistrib->isNotEmpty())
              <div class="btn-group btn-group-sm chart-toggle-group" role="group" aria-label="Tipo de gráfico">
                <button type="button" class="btn btn-outline-success chart-toggle" data-chart="chart-escolas-concelhos" data-type="bar">Barras</button>
                <button type="button" class="btn btn-outline-success chart-toggle" data-chart="chart-escolas-concelhos" data-type="pie">Circular</button>
              </div>
            @endif
          </div>
          <div class="card-body">
            @if($concelhoDistrib->isNotEmpty())
              <div class="chart-wrapper">
                <canvas id="chart-escolas-concelhos"></canvas>
              </div>
            @else
              <p class="text-sm text-secondary mb-0">Sem registos para os filtros selecionados.</p>
            @endif
          </div>
        </div>
      </div>
    </div>

    <div class="row g-4 mb-4">
      <div class="col-lg-4 col-md-6 mx-auto">
        <div class="card h-100">
          <div class="card-header pb-0 d-flex justify-content-between align-items-start gap-3">
            <div>
              <h6 class="mb-0">Total global</h6>
              <span class="text-xs text-secondary">Alunos reportados no período</span>
            </div>
            <div class="btn-group btn-group-sm chart-toggle-group" role="group" aria-label="Tipo de gráfico">
              <button type="button" class="btn btn-outline-success chart-toggle" data-chart="chart-escolas-total-global" data-type="bar">Barras</button>
              <button type="button" class="btn btn-outline-success chart-toggle" data-chart="chart-escolas-total-global" data-type="pie">Circular</button>
            </div>
          </div>
          <div class="card-body">
            <div class="chart-wrapper">
              <canvas id="chart-escolas-total-global"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-header pb-0">
        <h6 class="mb-0">Alunos por concelho</h6>
        <span class="text-xs text-secondary">Total reportado pelos agrupamentos filtrados</span>
      </div>
      <div class="card-body">
        @if($concelhoDistrib->isNotEmpty())
          <div class="table-responsive">
            <table class="table align-items-center mb-0">
              <thead>
                <tr>
                  <th>Concelho</th>
                  <th class="text-end">Alunos</th>
                </tr>
              </thead>
              <tbody>
                @foreach($concelhoDistrib as $concelhoNome => $total)
                  <tr>
                    <td>{{ $concelhoNome }}</td>
                    <td class="text-end fw-bold">{{ number_format($total, 0, ',', ' ') }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <p class="text-sm text-secondary mb-0">Sem registos para os filtros selecionados.</p>
        @endif
      </div>
    </div>

    <div class="card">
      <div class="card-header pb-0">
        <h6 class="mb-0">Inquéritos submetidos</h6>
        <span class="text-xs text-secondary">Lista paginada das submissões por agrupamento</span>
      </div>
      <div class="card-body">
        @if($listaInqueritos->count())
          <div class="table-responsive">
            <table class="table align-items-center mb-3">
              <thead>
                <tr>
                  <th>Agrupamento</th>
                  <th>Concelho</th>
                  <th>Ano</th>
                  <th class="text-end">Alunos reportados</th>
                </tr>
              </thead>
              <tbody>
                @foreach($listaInqueritos as $inquerito)
                  <tr>
                    <td class="fw-semibold">{{ optional($inquerito->agrupamento)->nome ?? '—' }}</td>
                    <td>{{ optional(optional($inquerito->agrupamento)->concelho)->nome ?? '—' }}</td>
                    <td>{{ $inquerito->ano_referencia }}</td>
                    <td class="text-end">{{ number_format($inquerito->alunos_reportados ?? 0, 0, ',', ' ') }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          {{ $listaInqueritos->links() }}
        @else
          <p class="text-sm text-secondary mb-0">Nenhum inquérito encontrado para os filtros selecionados.</p>
        @endif
      </div>
    </div>
  </div>
@endsection

@push('css')
  <style>
    .year-control {
      min-width: 150px;
    }

    .year-control .year-select,
    .year-control.btn-success {
      height: 44px;
      width: 100%;
    }

    .year-select {
      border: 1px solid #cad1d7 !important;
      color: #344767;
      font-weight: 600;
      border-radius: 0.85rem;
      text-align: center;
      padding-right: 2.5rem;
      background-position: right 0.9rem center;
    }

    .year-select:focus {
      border-color: #82d616 !important;
      box-shadow: 0 0 0 0.2rem rgba(130, 214, 22, 0.25);
    }

    .scope-toggle .btn {
      min-width: 140px;
      font-weight: 700;
      border-radius: 999px !important;
    }

    .export-toggle .btn {
      min-width: 120px;
      font-weight: 600;
    }

    .chart-wrapper {
      position: relative;
      height: 280px;
    }

    .chart-toggle-group .btn {
      font-weight: 600;
    }

    .chart-toggle-group .btn.active {
      background-color: #17ad37;
      color: #fff;
    }
  </style>
@endpush

@push('js')
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const chartConfigs = @json($chartConfigsEscolas);
      const chartInstances = {};

      const initChart = (chartId, chartConfig, forcedType = null) => {
        if (typeof Chart === 'undefined') {
          return;
        }

        const canvas = document.getElementById(chartId);
        if (!canvas) {
          return;
        }

        if (chartInstances[chartId]) {
          chartInstances[chartId].destroy();
        }

        const chartType = forcedType || chartConfig.defaultType || 'bar';
        const isCircular = ['pie', 'doughnut', 'polarArea'].includes(chartType);
        const datasets = chartConfig.datasets.map(dataset => ({ ...dataset }));

        const options = {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: true,
              position: 'bottom',
            },
          },
        };

        if (!isCircular) {
          options.scales = {
            x: {
              ticks: { color: '#67748e', font: { size: 11 } },
              grid: { display: false },
            },
            y: {
              beginAtZero: true,
              ticks: { color: '#67748e', font: { size: 11 } },
              grid: { color: 'rgba(94, 114, 228, 0.1)' },
            },
          };
        }

        chartInstances[chartId] = new Chart(canvas.getContext('2d'), {
          type: chartType,
          data: {
            labels: chartConfig.labels,
            datasets,
          },
          options,
        });
      };

      Object.entries(chartConfigs).forEach(([chartId, cfg]) => {
        initChart(chartId, cfg);
        const defaultType = cfg.defaultType || 'bar';
        const defaultButton = document.querySelector(`.chart-toggle[data-chart="${chartId}"][data-type="${defaultType}"]`);
        if (defaultButton) {
          defaultButton.classList.add('active');
        }
      });

      document.querySelectorAll('.chart-toggle').forEach(button => {
        button.addEventListener('click', event => {
          const target = event.currentTarget;
          const chartId = target.dataset.chart;
          const type = target.dataset.type;
          const config = chartConfigs[chartId];

          if (!config) {
            return;
          }

          initChart(chartId, config, type);

          const group = target.closest('.chart-toggle-group');
          if (group) {
            group.querySelectorAll('.chart-toggle').forEach(btn => btn.classList.remove('active'));
          }
          target.classList.add('active');
        });
      });

      const concelhoSelect = document.getElementById('filtro_concelho');
      const agrupamentoSelect = document.getElementById('filtro_agrupamento');

      if (!concelhoSelect || !agrupamentoSelect) {
        return;
      }

      const allOptions = Array.from(agrupamentoSelect.options).map(option => option.cloneNode(true));
      const initialValue = agrupamentoSelect.value;

      const rebuildOptions = (selectedValue = initialValue) => {
        agrupamentoSelect.innerHTML = '';

        allOptions.forEach(option => {
          if (option.value === 'all') {
            const clone = option.cloneNode(true);
            clone.selected = selectedValue === 'all';
            agrupamentoSelect.appendChild(clone);
            return;
          }

          const pertence = concelhoSelect.value === 'all' || option.dataset.concelho === concelhoSelect.value;
          if (pertence) {
            const clone = option.cloneNode(true);
            clone.selected = selectedValue != null && selectedValue.toString() === option.value.toString();
            agrupamentoSelect.appendChild(clone);
          }
        });
      };

      rebuildOptions(initialValue);

      concelhoSelect.addEventListener('change', () => {
        rebuildOptions('all');
      });
    });
  </script>
@endpush
