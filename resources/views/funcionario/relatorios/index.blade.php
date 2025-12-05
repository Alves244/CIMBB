@extends('layouts.user_type.auth')

@php
  $labelGenero = [
    'masculino' => 'Masculino',
    'feminino' => 'Feminino',
    'nao_declarado' => 'Não declarado',
  ];

  $labelFaixa = [
    'criancas' => 'Crianças',
    'adultos_laboral' => 'Adultos (laboral)',
    'adultos_65' => '65+',
  ];

  $colorPalette = ['#17ad37', '#82d616', '#11cdef', '#2dce89', '#5e72e4', '#f5365c', '#fb6340', '#ffd600'];

  $assignColors = function (array $labels) use ($colorPalette) {
    $paletteSize = max(1, count($colorPalette));
    return collect($labels)->values()->map(function ($label, $index) use ($colorPalette, $paletteSize) {
      return $colorPalette[$index % $paletteSize];
    })->toArray();
  };

  $buildChartConfig = function (array $labels, array $values, string $datasetLabel = 'Total') use ($assignColors) {
    return [
      'labels' => $labels,
      'datasets' => [[
        'label' => $datasetLabel,
        'data' => $values,
        'backgroundColor' => $assignColors($labels),
      ]],
      'defaultType' => 'bar',
    ];
  };

  $chartGeneroData = collect($distribuicoes['genero'] ?? [])->mapWithKeys(function ($valor, $chave) use ($labelGenero) {
    return [$labelGenero[$chave] ?? ucfirst($chave) => (int) $valor];
  });

  $chartFaixaData = collect($distribuicoes['faixa_etaria'] ?? [])->mapWithKeys(function ($valor, $chave) use ($labelFaixa) {
    return [$labelFaixa[$chave] ?? ucfirst($chave) => (int) $valor];
  });

  $chartHabitacaoData = collect($distribuicoes['habitacao'] ?? [])->mapWithKeys(function ($valor, $chave) {
    return [ucfirst($chave ?: 'Indefinido') => (int) $valor];
  });

  $chartPropriedadeData = collect($distribuicoes['propriedade'] ?? [])->mapWithKeys(function ($valor, $chave) {
    return [ucfirst($chave ?: 'Indefinido') => (int) $valor];
  });

  $chartSetoresData = ($distribuicoes['setores'] ?? collect())->mapWithKeys(function ($item) {
    return [$item->nome => (int) $item->total];
  });

  $chartNacionalidadesData = ($distribuicoes['nacionalidades'] ?? collect())->mapWithKeys(function ($item) {
    return [$item->nacionalidade => (int) $item->total];
  });

  $chartConfigsPayload = [];

  if ($chartGeneroData->isNotEmpty()) {
    $chartConfigsPayload['chart-genero'] = $buildChartConfig(
      $chartGeneroData->keys()->toArray(),
      $chartGeneroData->values()->toArray(),
      'Famílias'
    );
  }

  if ($chartFaixaData->isNotEmpty()) {
    $chartConfigsPayload['chart-faixa'] = $buildChartConfig(
      $chartFaixaData->keys()->toArray(),
      $chartFaixaData->values()->toArray(),
      'Pessoas'
    );
  }

  if ($chartHabitacaoData->isNotEmpty()) {
    $chartConfigsPayload['chart-habitacao'] = $buildChartConfig(
      $chartHabitacaoData->keys()->toArray(),
      $chartHabitacaoData->values()->toArray(),
      'Famílias'
    );
  }

  if ($chartPropriedadeData->isNotEmpty()) {
    $chartConfigsPayload['chart-propriedade'] = $buildChartConfig(
      $chartPropriedadeData->keys()->toArray(),
      $chartPropriedadeData->values()->toArray(),
      'Famílias'
    );
  }

  if ($chartSetoresData->isNotEmpty()) {
    $chartConfigsPayload['chart-setores'] = $buildChartConfig(
      $chartSetoresData->keys()->toArray(),
      $chartSetoresData->values()->toArray(),
      'Famílias'
    );
  }

  if ($chartNacionalidadesData->isNotEmpty()) {
    $chartConfigsPayload['chart-nacionalidades'] = $buildChartConfig(
      $chartNacionalidadesData->keys()->toArray(),
      $chartNacionalidadesData->values()->toArray(),
      'Famílias'
    );
  }

@endphp

@section('content')
  <div class="container-fluid py-4">
    <div class="row mb-4">
      <div class="col-12">
        <div class="card border border-success shadow-sm">
          <div class="card-body d-flex flex-column flex-lg-row justify-content-between align-items-start gap-4">
            <div>
              <span class="badge bg-success-subtle text-success text-uppercase mb-2">Estatísticas & Exportações</span>
              <h4 class="mb-2 text-dark">Explora os dados do território CIMBB</h4>
              <p class="mb-0 text-secondary">Filtra por concelho, freguesia ou perfis sociodemográficos para gerar relatórios e exportar evidências.</p>
            </div>
            <div class="text-lg-end">
              <span class="text-xs text-secondary">Ano em análise</span>
              <h2 class="font-weight-bolder text-success mb-1">{{ $anoSelecionado }}</h2>
              <form method="GET" action="{{ route('funcionario.relatorios.index') }}" class="d-flex flex-wrap justify-content-center align-items-center gap-2 mt-3">
                <select name="ano" class="form-select form-select-sm w-auto text-center border-success">
                  @foreach($anosDisponiveis as $ano)
                    <option value="{{ $ano }}" {{ (int) $anoSelecionado === (int) $ano ? 'selected' : '' }}>{{ $ano }}</option>
                  @endforeach
                </select>
                @foreach(request()->query() as $param => $value)
                  @continue($param === 'ano')
                  <input type="hidden" name="{{ $param }}" value="{{ $value }}">
                @endforeach
                <button type="submit" class="btn btn-sm btn-success px-4">Alterar</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-body">
        <form class="row g-3 align-items-end" method="GET" action="{{ route('funcionario.relatorios.index') }}">
          <div class="col-md-2">
            <label class="form-label text-xs text-secondary">Ano</label>
            <select name="ano" class="form-select form-select-sm">
              @foreach($anosDisponiveis as $ano)
                <option value="{{ $ano }}" {{ (int) $filters['ano'] === (int) $ano ? 'selected' : '' }}>{{ $ano }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label text-xs text-secondary">Género</label>
            <select name="genero" class="form-select form-select-sm">
              <option value="all" {{ ($filters['genero'] ?? 'all') === 'all' ? 'selected' : '' }}>Todos</option>
              <option value="masculino" {{ ($filters['genero'] ?? 'all') === 'masculino' ? 'selected' : '' }}>Masculino</option>
              <option value="feminino" {{ ($filters['genero'] ?? 'all') === 'feminino' ? 'selected' : '' }}>Feminino</option>
              <option value="nao_declarado" {{ ($filters['genero'] ?? 'all') === 'nao_declarado' ? 'selected' : '' }}>Não declarado</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label text-xs text-secondary">Faixa etária</label>
            <select name="faixa_etaria" class="form-select form-select-sm">
              <option value="all" {{ ($filters['faixa_etaria'] ?? 'all') === 'all' ? 'selected' : '' }}>Todas</option>
              <option value="criancas" {{ ($filters['faixa_etaria'] ?? 'all') === 'criancas' ? 'selected' : '' }}>Crianças</option>
              <option value="adultos_laboral" {{ ($filters['faixa_etaria'] ?? 'all') === 'adultos_laboral' ? 'selected' : '' }}>Adultos (laboral)</option>
              <option value="adultos_65" {{ ($filters['faixa_etaria'] ?? 'all') === 'adultos_65' ? 'selected' : '' }}>65+</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label text-xs text-secondary">Habitação</label>
            <select name="tipologia_habitacao" class="form-select form-select-sm">
              <option value="all" {{ ($filters['tipologia_habitacao'] ?? 'all') === 'all' ? 'selected' : '' }}>Todas</option>
              <option value="casa" {{ ($filters['tipologia_habitacao'] ?? 'all') === 'casa' ? 'selected' : '' }}>Casa</option>
              <option value="quinta" {{ ($filters['tipologia_habitacao'] ?? 'all') === 'quinta' ? 'selected' : '' }}>Quinta</option>
              <option value="apartamento" {{ ($filters['tipologia_habitacao'] ?? 'all') === 'apartamento' ? 'selected' : '' }}>Apartamento</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label text-xs text-secondary">Estado residência</label>
            <select name="tipologia_propriedade" class="form-select form-select-sm">
              <option value="all" {{ ($filters['tipologia_propriedade'] ?? 'all') === 'all' ? 'selected' : '' }}>Todos</option>
              <option value="propria" {{ ($filters['tipologia_propriedade'] ?? 'all') === 'propria' ? 'selected' : '' }}>Propriedade</option>
              <option value="arrendada" {{ ($filters['tipologia_propriedade'] ?? 'all') === 'arrendada' ? 'selected' : '' }}>Arrendada</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label text-xs text-secondary">Situação inquérito</label>
            <select name="situacao_inquerito" class="form-select form-select-sm">
              <option value="all" {{ ($filters['situacao_inquerito'] ?? 'all') === 'all' ? 'selected' : '' }}>Todos</option>
              <option value="submetido" {{ ($filters['situacao_inquerito'] ?? 'all') === 'submetido' ? 'selected' : '' }}>Submetido</option>
              <option value="pendente" {{ ($filters['situacao_inquerito'] ?? 'all') === 'pendente' ? 'selected' : '' }}>Pendente</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label text-xs text-secondary">Concelho</label>
            <select name="concelho_id" id="concelho_id" class="form-select form-select-sm">
              <option value="all" {{ ($filters['concelho_id'] ?? 'all') === 'all' ? 'selected' : '' }}>Todos</option>
              @foreach($concelhos as $concelho)
                <option value="{{ $concelho->id }}" {{ (string) ($filters['concelho_id'] ?? 'all') === (string) $concelho->id ? 'selected' : '' }}>{{ $concelho->nome }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label text-xs text-secondary">Freguesia</label>
            <select name="freguesia_id" id="freguesia_id" class="form-select form-select-sm">
              <option value="all" {{ ($filters['freguesia_id'] ?? 'all') === 'all' ? 'selected' : '' }}>Todas</option>
              @foreach($freguesias as $freguesia)
                <option value="{{ $freguesia->id }}" data-concelho="{{ $freguesia->conselho_id }}" {{ (string) ($filters['freguesia_id'] ?? 'all') === (string) $freguesia->id ? 'selected' : '' }}>
                  {{ $freguesia->nome }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3 ms-auto">
            <div class="d-flex gap-2">
              <a href="{{ route('funcionario.relatorios.index') }}" class="btn btn-outline-secondary w-50">Limpar</a>
              <button class="btn bg-gradient-success w-50" type="submit">Aplicar filtros</button>
            </div>
          </div>
        </form>
      </div>
    </div>

    @php
      $totalGenero = max(1, array_sum($distribuicoes['genero']));
      $totalFaixa = max(1, array_sum($distribuicoes['faixa_etaria']));
    @endphp

    <div class="row mb-4">
      <div class="col-xl-4 mb-3">
        <div class="card h-100">
          <div class="card-header pb-0">
            <h6 class="mb-0">Distribuição por género</h6>
          </div>
          <div class="card-body">
            @foreach($distribuicoes['genero'] as $label => $valor)
              @php
                $percent = round(($valor / $totalGenero) * 100);
                $labelText = ['masculino' => 'Masculino', 'feminino' => 'Feminino', 'nao_declarado' => 'Não declarado'][$label] ?? ucfirst($label);
              @endphp
              <div class="mb-3">
                <div class="d-flex justify-content-between">
                  <span class="text-sm">{{ $labelText }}</span>
                  <span class="text-sm font-weight-bold">{{ $valor }} ({{ $percent }}%)</span>
                </div>
                <div class="progress">
                  <div class="progress-bar bg-gradient-info" role="progressbar" style="width: {{ $percent }}%;"></div>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </div>
      <div class="col-xl-4 mb-3">
        <div class="card h-100">
          <div class="card-header pb-0">
            <h6 class="mb-0">Faixas etárias</h6>
          </div>
          <div class="card-body">
            @foreach($distribuicoes['faixa_etaria'] as $label => $valor)
              @php
                $map = ['criancas' => 'Crianças', 'adultos_laboral' => 'Adultos (laboral)', 'adultos_65' => '65+'];
                $percent = round(($valor / $totalFaixa) * 100);
              @endphp
              <div class="mb-3">
                <div class="d-flex justify-content-between">
                  <span class="text-sm">{{ $map[$label] ?? ucfirst($label) }}</span>
                  <span class="text-sm font-weight-bold">{{ $valor }} ({{ $percent }}%)</span>
                </div>
                <div class="progress">
                  <div class="progress-bar bg-gradient-success" role="progressbar" style="width: {{ $percent }}%;"></div>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </div>
      <div class="col-xl-4 mb-3">
        <div class="card h-100">
          <div class="card-header pb-0">
            <h6 class="mb-0">Habitação e residência</h6>
          </div>
          <div class="card-body">
            <p class="text-xs text-secondary mb-1">Tipologia da habitação</p>
            @foreach($distribuicoes['habitacao'] as $label => $valor)
              <div class="d-flex justify-content-between text-sm">
                <span>{{ ucfirst($label) }}</span><span class="font-weight-bold">{{ $valor }}</span>
              </div>
            @endforeach
            <hr>
            <p class="text-xs text-secondary mb-1">Estado da residência</p>
            @foreach($distribuicoes['propriedade'] as $label => $valor)
              <div class="d-flex justify-content-between text-sm">
                <span>{{ ucfirst($label) }}</span><span class="font-weight-bold">{{ $valor }}</span>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>

    {{-- Visualizações dinâmicas --}}
    <div class="row mb-4">
      <div class="col-xl-6 mb-4">
        <div class="card h-100">
          <div class="card-header pb-0 d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
              <h6 class="mb-0">Visualização por género</h6>
              <p class="text-xs text-secondary mb-0">Alterna entre barras e gráfico circular para comparar géneros.</p>
            </div>
            <div class="btn-group btn-group-sm" role="group">
              <button type="button" class="btn btn-outline-success chart-toggle" data-chart="chart-genero" data-type="bar" {{ isset($chartConfigsPayload['chart-genero']) ? '' : 'disabled' }}>Barras</button>
              <button type="button" class="btn btn-outline-success chart-toggle" data-chart="chart-genero" data-type="doughnut" {{ isset($chartConfigsPayload['chart-genero']) ? '' : 'disabled' }}>Circular</button>
            </div>
          </div>
          <div class="card-body">
            @if(isset($chartConfigsPayload['chart-genero']))
              <div class="chart" style="height:300px">
                <canvas id="chart-genero" height="300"></canvas>
              </div>
            @else
              <p class="text-sm text-secondary mb-0">Sem dados para este gráfico.</p>
            @endif
          </div>
        </div>
      </div>
      <div class="col-xl-6 mb-4">
        <div class="card h-100">
          <div class="card-header pb-0 d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
              <h6 class="mb-0">Distribuição etária interativa</h6>
              <p class="text-xs text-secondary mb-0">Visualiza a composição etária em barras ou em modo circular.</p>
            </div>
            <div class="btn-group btn-group-sm" role="group">
              <button type="button" class="btn btn-outline-success chart-toggle" data-chart="chart-faixa" data-type="bar" {{ isset($chartConfigsPayload['chart-faixa']) ? '' : 'disabled' }}>Barras</button>
              <button type="button" class="btn btn-outline-success chart-toggle" data-chart="chart-faixa" data-type="doughnut" {{ isset($chartConfigsPayload['chart-faixa']) ? '' : 'disabled' }}>Circular</button>
            </div>
          </div>
          <div class="card-body">
            @if(isset($chartConfigsPayload['chart-faixa']))
              <div class="chart" style="height:300px">
                <canvas id="chart-faixa" height="300"></canvas>
              </div>
            @else
              <p class="text-sm text-secondary mb-0">Sem dados suficientes para mostrar o gráfico.</p>
            @endif
          </div>
        </div>
      </div>
    </div>

    <div class="row mb-4">
      <div class="col-xl-6 mb-4">
        <div class="card h-100">
          <div class="card-header pb-0 d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
              <h6 class="mb-0">Tipologia da habitação</h6>
              <p class="text-xs text-secondary mb-0">Descobre onde vivem as famílias registadas.</p>
            </div>
            <div class="btn-group btn-group-sm" role="group">
              <button type="button" class="btn btn-outline-success chart-toggle" data-chart="chart-habitacao" data-type="bar" {{ isset($chartConfigsPayload['chart-habitacao']) ? '' : 'disabled' }}>Barras</button>
              <button type="button" class="btn btn-outline-success chart-toggle" data-chart="chart-habitacao" data-type="doughnut" {{ isset($chartConfigsPayload['chart-habitacao']) ? '' : 'disabled' }}>Circular</button>
            </div>
          </div>
          <div class="card-body">
            @if(isset($chartConfigsPayload['chart-habitacao']))
              <div class="chart" style="height:300px">
                <canvas id="chart-habitacao" height="300"></canvas>
              </div>
            @else
              <p class="text-sm text-secondary mb-0">Sem dados para este gráfico.</p>
            @endif
          </div>
        </div>
      </div>
      <div class="col-xl-6 mb-4">
        <div class="card h-100">
          <div class="card-header pb-0 d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
              <h6 class="mb-0">Estado da residência</h6>
              <p class="text-xs text-secondary mb-0">Compara propriedades próprias vs. arrendadas.</p>
            </div>
            <div class="btn-group btn-group-sm" role="group">
              <button type="button" class="btn btn-outline-success chart-toggle" data-chart="chart-propriedade" data-type="bar" {{ isset($chartConfigsPayload['chart-propriedade']) ? '' : 'disabled' }}>Barras</button>
              <button type="button" class="btn btn-outline-success chart-toggle" data-chart="chart-propriedade" data-type="doughnut" {{ isset($chartConfigsPayload['chart-propriedade']) ? '' : 'disabled' }}>Circular</button>
            </div>
          </div>
          <div class="card-body">
            @if(isset($chartConfigsPayload['chart-propriedade']))
              <div class="chart" style="height:300px">
                <canvas id="chart-propriedade" height="300"></canvas>
              </div>
            @else
              <p class="text-sm text-secondary mb-0">Sem dados para este gráfico.</p>
            @endif
          </div>
        </div>
      </div>
    </div>

    <div class="row mb-4">
      <div class="col-xl-6 mb-4">
        <div class="card h-100">
          <div class="card-header pb-0 d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
              <h6 class="mb-0">Setores de atividade</h6>
              <p class="text-xs text-secondary mb-0">Identifica as áreas económicas mais presentes.</p>
            </div>
            <div class="btn-group btn-group-sm" role="group">
              <button type="button" class="btn btn-outline-success chart-toggle" data-chart="chart-setores" data-type="bar" {{ isset($chartConfigsPayload['chart-setores']) ? '' : 'disabled' }}>Barras</button>
              <button type="button" class="btn btn-outline-success chart-toggle" data-chart="chart-setores" data-type="doughnut" {{ isset($chartConfigsPayload['chart-setores']) ? '' : 'disabled' }}>Circular</button>
            </div>
          </div>
          <div class="card-body">
            @if(isset($chartConfigsPayload['chart-setores']))
              <div class="chart" style="height:300px">
                <canvas id="chart-setores" height="300"></canvas>
              </div>
            @else
              <p class="text-sm text-secondary mb-0">Sem registos de atividade económica para o filtro.</p>
            @endif
          </div>
        </div>
      </div>
      <div class="col-xl-6 mb-4">
        <div class="card h-100">
          <div class="card-header pb-0 d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
              <h6 class="mb-0">Top nacionalidades</h6>
              <p class="text-xs text-secondary mb-0">Comparação visual das comunidades mais frequentes.</p>
            </div>
            <div class="btn-group btn-group-sm" role="group">
              <button type="button" class="btn btn-outline-success chart-toggle" data-chart="chart-nacionalidades" data-type="bar" {{ isset($chartConfigsPayload['chart-nacionalidades']) ? '' : 'disabled' }}>Barras</button>
              <button type="button" class="btn btn-outline-success chart-toggle" data-chart="chart-nacionalidades" data-type="doughnut" {{ isset($chartConfigsPayload['chart-nacionalidades']) ? '' : 'disabled' }}>Circular</button>
            </div>
          </div>
          <div class="card-body">
            @if(isset($chartConfigsPayload['chart-nacionalidades']))
              <div class="chart" style="height:300px">
                <canvas id="chart-nacionalidades" height="300"></canvas>
              </div>
            @else
              <p class="text-sm text-secondary mb-0">Sem nacionalidades disponíveis para este filtro.</p>
            @endif
          </div>
        </div>
      </div>
    </div>

    <div class="row mb-4">
      <div class="col-xl-6 mb-3">
        <div class="card h-100">
          <div class="card-header pb-0 d-flex justify-content-between align-items-center">
            <div>
              <h6 class="mb-0">Setores de atividade</h6>
              <span class="text-xs text-secondary">Top 8</span>
            </div>
            <button type="button" class="btn btn-link btn-sm p-0 text-success" data-bs-toggle="modal" data-bs-target="#modalSetores">
              Mostrar todas
            </button>
          </div>
          <div class="card-body">
            @if($distribuicoes['setores']->isNotEmpty())
              <ul class="list-group">
                @foreach($distribuicoes['setores']->take(8) as $setor)
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>{{ $setor->nome }}</span>
                    <span class="text-sm text-secondary">{{ $setor->total }} famílias</span>
                  </li>
                @endforeach
              </ul>
            @else
              <p class="text-sm text-secondary mb-0">Sem atividades económicas registadas para este filtro.</p>
            @endif
          </div>
        </div>
      </div>
      <div class="col-xl-6 mb-3">
        <div class="card h-100">
          <div class="card-header pb-0 d-flex justify-content-between align-items-center">
            <div>
              <h6 class="mb-0">Nacionalidades</h6>
              <span class="text-xs text-secondary">Top 10</span>
            </div>
            <button type="button" class="btn btn-link btn-sm p-0 text-success" data-bs-toggle="modal" data-bs-target="#modalNacionalidades">
              Mostrar todas
            </button>
          </div>
          <div class="card-body">
            @if($distribuicoes['nacionalidades']->isNotEmpty())
              <ul class="list-group">
                @foreach($distribuicoes['nacionalidades']->take(10) as $nac)
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>{{ $nac->nacionalidade }}</span>
                    <span class="text-sm text-secondary">{{ $nac->total }} famílias</span>
                  </li>
                @endforeach
              </ul>
            @else
              <p class="text-sm text-secondary mb-0">Sem famílias para o filtro atual.</p>
            @endif
          </div>
        </div>
      </div>
    </div>

    <div class="row mb-4">
      <div class="col-xl-8">
        <div class="card h-100">
          <div class="card-header pb-0 d-flex justify-content-between align-items-center">
            @php
              $listaEhPaginator = $listaFamilias instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator;
              $intervaloInicio = $listaEhPaginator ? ($listaFamilias->firstItem() ?? 0) : 0;
              $intervaloFim = $listaEhPaginator ? ($listaFamilias->lastItem() ?? 0) : 0;
              $totalFamiliasListadas = $listaEhPaginator ? $listaFamilias->total() : count($listaFamilias);
            @endphp
            <div>
              <h6 class="mb-0">Famílias dentro do filtro</h6>
              @if($listaEhPaginator)
                <p class="text-sm text-secondary mb-0">
                  Mostrando {{ $intervaloInicio }}-{{ $intervaloFim }} de {{ $totalFamiliasListadas }} famílias filtradas.
                </p>
              @else
                <p class="text-sm text-secondary mb-0">Amostra limitada às últimas {{ $totalFamiliasListadas }} famílias para consulta rápida.</p>
              @endif
            </div>
            <a href="{{ route('funcionario.relatorios.export', request()->query()) }}" class="btn btn-sm bg-gradient-success text-white">Exportar PDF</a>
          </div>
          <div class="card-body p-0">
            @if($listaEhPaginator ? $listaFamilias->isNotEmpty() : !empty($listaFamilias))
              <div class="table-responsive">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Família</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Concelho / Freguesia</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Habitação</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Membros</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Inquérito</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($listaFamilias as $familia)
                      <tr>
                        <td class="text-sm">
                          <div class="d-flex flex-column">
                            <span class="font-weight-bold">{{ $familia['codigo'] }}</span>
                            <small class="text-secondary">{{ $familia['nacionalidade'] }}</small>
                          </div>
                        </td>
                        <td class="text-sm">
                          <div class="d-flex flex-column">
                            <span>{{ $familia['concelho'] }}</span>
                            <small class="text-secondary">{{ $familia['freguesia'] }}</small>
                          </div>
                        </td>
                        <td class="text-sm">{{ ucfirst($familia['tipologia_habitacao']) }} / {{ ucfirst($familia['tipologia_propriedade']) }}</td>
                        <td class="text-sm">{{ $familia['total_membros'] }}</td>
                        <td class="text-sm">
                          <span class="badge {{ $familia['situacao_inquerito'] === 'Submetido' ? 'bg-gradient-success' : 'bg-gradient-warning' }}">
                            {{ $familia['situacao_inquerito'] }}
                          </span>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            @else
              <div class="p-4 text-center text-secondary">
                <p class="mb-0">Sem famílias dentro do filtro selecionado.</p>
              </div>
            @endif
          </div>
          @if($listaEhPaginator && $listaFamilias->isNotEmpty())
            <x-admin.pagination :paginator="$listaFamilias" />
          @endif
        </div>
      </div>
      <div class="col-xl-4">
        <div class="card h-100">
          <div class="card-header pb-0">
            <h6 class="mb-0">Resumo por freguesia</h6>
            <p class="text-xs text-secondary mb-0">Ano {{ data_get($freguesiasResumo, 'ano', $anoSelecionado) }}</p>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <p class="text-sm text-secondary mb-1">Freguesias consideradas</p>
              <h4 class="text-dark mb-0">{{ number_format(data_get($freguesiasResumo, 'totalConsideradas', 0)) }}</h4>
            </div>
            <div class="mb-3">
              <p class="text-sm text-secondary mb-1">Com inquérito submetido</p>
              <div class="d-flex align-items-baseline gap-2">
                <h4 class="text-success mb-0">{{ number_format(data_get($freguesiasResumo, 'comInquerito', 0)) }}</h4>
                <span class="text-xs text-secondary">de {{ number_format(data_get($freguesiasResumo, 'totalConsideradas', 0)) }}</span>
              </div>
            </div>
            @php
              $pendentesLista = collect(data_get($freguesiasResumo, 'pendentes', []));
            @endphp
            <p class="text-sm text-secondary mb-2">Pendentes ({{ $pendentesLista->count() }})</p>
            @if($pendentesLista->isNotEmpty())
              <ul class="list-group">
                @foreach($pendentesLista as $pendente)
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>{{ $pendente->nome }}</span>
                    <span class="badge bg-gradient-warning">{{ $pendente->codigo ?? '—' }}</span>
                  </li>
                @endforeach
              </ul>
            @else
              <p class="text-sm text-secondary mb-0">Todas as freguesias têm inquérito submetido.</p>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modalSetores" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Todas as atividades económicas</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>
        <div class="modal-body">
          @if($distribuicoes['setores']->isNotEmpty())
            <ul class="list-group">
              @foreach($distribuicoes['setores'] as $setor)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <span>{{ $setor->nome }}</span>
                  <span class="badge bg-gradient-primary">{{ $setor->total }}</span>
                </li>
              @endforeach
            </ul>
          @else
            <p class="text-sm text-secondary mb-0">Sem registos disponíveis.</p>
          @endif
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modalNacionalidades" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Todas as nacionalidades</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>
        <div class="modal-body">
          @if($distribuicoes['nacionalidades']->isNotEmpty())
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th>Nacionalidade</th>
                    <th class="text-end">Famílias</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($distribuicoes['nacionalidades'] as $nac)
                    <tr>
                      <td>{{ $nac->nacionalidade }}</td>
                      <td class="text-end">{{ $nac->total }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @else
            <p class="text-sm text-secondary mb-0">Sem registos disponíveis.</p>
          @endif
        </div>
      </div>
    </div>
  </div>
@endsection

@push('js')
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const chartConfigs = @json($chartConfigsPayload);
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
        chartInstances[chartId] = new Chart(canvas.getContext('2d'), {
          type: chartType,
          data: {
            labels: chartConfig.labels,
            datasets: chartConfig.datasets.map(dataset => ({
              ...dataset,
              borderWidth: dataset.borderWidth ?? 0,
            })),
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            ...chartConfig.options,
          },
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

      const toggleButtons = document.querySelectorAll('.chart-toggle');
      toggleButtons.forEach(button => {
        button.addEventListener('click', event => {
          const target = event.currentTarget;
          const chartId = target.dataset.chart;
          const type = target.dataset.type;
          const config = chartConfigs[chartId];

          if (!config) {
            return;
          }

          initChart(chartId, config, type);

          document.querySelectorAll(`.chart-toggle[data-chart="${chartId}"]`).forEach(btn => {
            btn.classList.toggle('active', btn === target);
          });
        });
      });

      const concelhoSelect = document.getElementById('concelho_id');
      const freguesiaSelect = document.getElementById('freguesia_id');

      if (!concelhoSelect || !freguesiaSelect) {
        return;
      }

      const allOptions = Array.from(freguesiaSelect.options).map(option => option.cloneNode(true));
      const initialFreguesia = @json($filters['freguesia_id'] ?? 'all');

      const rebuildOptions = (selectedValue = initialFreguesia) => {
        freguesiaSelect.innerHTML = '';

        allOptions.forEach(option => {
          if (option.value === 'all') {
            const clone = option.cloneNode(true);
            clone.selected = selectedValue === 'all';
            freguesiaSelect.appendChild(clone);
            return;
          }

          const pertence = concelhoSelect.value === 'all' || option.dataset.concelho === concelhoSelect.value;
          if (pertence) {
            const clone = option.cloneNode(true);
            clone.selected = selectedValue != null && selectedValue.toString() === option.value.toString();
            freguesiaSelect.appendChild(clone);
          }
        });
      };

      rebuildOptions();

      concelhoSelect.addEventListener('change', () => {
        rebuildOptions('all');
      });
    });
  </script>
@endpush
