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

  $labelHabitacao = [
    'moradia' => 'Moradia',
    'apartamento' => 'Apartamento',
    'caravana_tenda' => 'Caravana / tenda',
    'anexo' => 'Anexo / construção secundária',
    'outro' => 'Outro',
  ];

  $labelPropriedade = [
    'propria' => 'Própria',
    'arrendada' => 'Arrendada',
    'cedida' => 'Cedida',
    'outra' => 'Outra',
  ];

  $labelLocalizacao = [
    'sede_freguesia' => 'Sede da freguesia',
    'lugar_aldeia' => 'Lugar / aldeia',
    'espaco_agroflorestal' => 'Espaço agroflorestal',
    'nao_informado' => 'Sem registo',
  ];

  $labelCondicao = [
    'bom_estado' => 'Bom estado',
    'estado_razoavel' => 'Estado razoável',
    'necessita_reparacoes' => 'Necessita de reparações',
    'situacao_precaria' => 'Situação precária',
    'nao_informado' => 'Sem registo',
  ];

  $labelNecessidades = [
    'lingua_portuguesa' => 'Língua portuguesa',
    'acesso_emprego' => 'Acesso a emprego',
    'habitacao' => 'Habitação',
    'regularizacao_administrativa' => 'Regularização administrativa',
    'transporte_mobilidade' => 'Transporte / mobilidade',
    'apoio_social' => 'Apoio social',
  ];

  $labelEscola = [
    'sim' => 'Sim',
    'nao' => 'Não',
    'nao_sei' => 'Não sei',
    'nao_informado' => 'Sem resposta',
  ];

  $situacaoInqueritoLabels = [
    'submetido' => 'Submetido',
    'pendente' => 'Pendente',
  ];

  $filterFieldLabels = [
    'genero' => 'Género',
    'faixa_etaria' => 'Faixa etária',
    'tipologia_habitacao' => 'Habitação',
    'tipologia_propriedade' => 'Estado residência',
    'situacao_inquerito' => 'Situação do inquérito',
    'concelho_id' => 'Concelho',
    'freguesia_id' => 'Freguesia',
  ];

  $filterValueResolver = function ($key, $value) use (
    $labelGenero,
    $labelFaixa,
    $labelHabitacao,
    $labelPropriedade,
    $situacaoInqueritoLabels,
    $concelhos,
    $freguesias
  ) {
    switch ($key) {
      case 'genero':
        return $labelGenero[$value] ?? ucfirst($value);
      case 'faixa_etaria':
        return $labelFaixa[$value] ?? ucfirst($value);
      case 'tipologia_habitacao':
        return $labelHabitacao[$value] ?? ucfirst($value);
      case 'tipologia_propriedade':
        return $labelPropriedade[$value] ?? ucfirst($value);
      case 'situacao_inquerito':
        return $situacaoInqueritoLabels[$value] ?? ucfirst($value);
      case 'concelho_id':
        return optional($concelhos->firstWhere('id', (int) $value))->nome;
      case 'freguesia_id':
        return optional($freguesias->firstWhere('id', (int) $value))->nome;
      default:
        return ucfirst(str_replace('_', ' ', $value));
    }
  };

  $activeFilters = collect($filters ?? [])
    ->reject(fn ($value, $key) => $key === 'ano' || $value === null || $value === '' || $value === 'all')
    ->mapWithKeys(function ($value, $key) use ($filterFieldLabels, $filterValueResolver) {
      return [$filterFieldLabels[$key] ?? ucfirst($key) => $filterValueResolver($key, $value)];
    })
    ->filter();

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

  $chartHabitacaoData = collect($distribuicoes['habitacao'] ?? [])->mapWithKeys(function ($valor, $chave) use ($labelHabitacao) {
    return [($labelHabitacao[$chave] ?? ucfirst($chave ?: 'Indefinido')) => (int) $valor];
  });

  $chartPropriedadeData = collect($distribuicoes['propriedade'] ?? [])->mapWithKeys(function ($valor, $chave) use ($labelPropriedade) {
    return [($labelPropriedade[$chave] ?? ucfirst($chave ?: 'Indefinido')) => (int) $valor];
  });

  $localizacaoDistrib = collect($distribuicoes['localizacao'] ?? [])->mapWithKeys(function ($valor, $chave) use ($labelLocalizacao) {
    return [($labelLocalizacao[$chave] ?? ucfirst($chave ?: 'Indefinido')) => (int) $valor];
  });

  $condicaoDistrib = collect($distribuicoes['condicao'] ?? [])->mapWithKeys(function ($valor, $chave) use ($labelCondicao) {
    return [($labelCondicao[$chave] ?? ucfirst($chave ?: 'Indefinido')) => (int) $valor];
  });

  $necessidadesDistrib = collect($distribuicoes['necessidades'] ?? [])->mapWithKeys(function ($valor, $chave) use ($labelNecessidades) {
    return [($labelNecessidades[$chave] ?? ucfirst($chave ?: 'Indefinido')) => (int) $valor];
  })->sortDesc();

  $integracaoSaude = data_get($distribuicoes, 'integracao.centro_saude', 0);
  $integracaoEscola = collect(data_get($distribuicoes, 'integracao.escola', []));
  $totalFamiliasFiltro = max(1, $totais['totalFamilias'] ?? 1);

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
        <form class="filter-stats-form" method="GET" action="{{ route('funcionario.relatorios.index') }}">
          <div class="row g-3">
            <div class="col-12 col-xl-3">
              <div class="border rounded-4 h-100 p-3 bg-light">
                <small class="text-uppercase text-success fw-bold">Janela temporal</small>
                <p class="text-xs text-secondary mb-3">Escolhe o ano de referência antes de aplicar os restantes filtros.</p>
                <label class="form-label text-xs text-secondary mb-1">Ano</label>
                <select name="ano" class="form-select form-select-sm">
                  @foreach($anosDisponiveis as $ano)
                    <option value="{{ $ano }}" {{ (int) $filters['ano'] === (int) $ano ? 'selected' : '' }}>{{ $ano }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-12 col-xl-5">
              <div class="border rounded-4 h-100 p-3">
                <small class="text-uppercase text-secondary">Perfil sociodemográfico</small>
                <div class="row g-3 mt-2">
                  <div class="col-md-6">
                    <label class="form-label text-xs text-secondary">Género</label>
                    <select name="genero" class="form-select form-select-sm">
                      <option value="all" {{ ($filters['genero'] ?? 'all') === 'all' ? 'selected' : '' }}>Todos</option>
                      <option value="masculino" {{ ($filters['genero'] ?? 'all') === 'masculino' ? 'selected' : '' }}>Masculino</option>
                      <option value="feminino" {{ ($filters['genero'] ?? 'all') === 'feminino' ? 'selected' : '' }}>Feminino</option>
                      <option value="nao_declarado" {{ ($filters['genero'] ?? 'all') === 'nao_declarado' ? 'selected' : '' }}>Não declarado</option>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label text-xs text-secondary">Faixa etária</label>
                    <select name="faixa_etaria" class="form-select form-select-sm">
                      <option value="all" {{ ($filters['faixa_etaria'] ?? 'all') === 'all' ? 'selected' : '' }}>Todas</option>
                      <option value="criancas" {{ ($filters['faixa_etaria'] ?? 'all') === 'criancas' ? 'selected' : '' }}>Crianças</option>
                      <option value="adultos_laboral" {{ ($filters['faixa_etaria'] ?? 'all') === 'adultos_laboral' ? 'selected' : '' }}>Adultos (laboral)</option>
                      <option value="adultos_65" {{ ($filters['faixa_etaria'] ?? 'all') === 'adultos_65' ? 'selected' : '' }}>65+</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-12 col-xl-4">
              <div class="border rounded-4 h-100 p-3">
                <small class="text-uppercase text-secondary">Processo do inquérito</small>
                <p class="text-xs text-secondary mb-2">Filtra por estado de submissão das fichas.</p>
                <label class="form-label text-xs text-secondary">Situação inquérito</label>
                <select name="situacao_inquerito" class="form-select form-select-sm">
                  <option value="all" {{ ($filters['situacao_inquerito'] ?? 'all') === 'all' ? 'selected' : '' }}>Todos</option>
                  <option value="submetido" {{ ($filters['situacao_inquerito'] ?? 'all') === 'submetido' ? 'selected' : '' }}>Submetido</option>
                  <option value="pendente" {{ ($filters['situacao_inquerito'] ?? 'all') === 'pendente' ? 'selected' : '' }}>Pendente</option>
                </select>
              </div>
            </div>
            <div class="col-12 col-xl-5">
              <div class="border rounded-4 h-100 p-3">
                <small class="text-uppercase text-secondary">Habitação</small>
                <div class="row g-3 mt-2">
                  <div class="col-md-6">
                    <label class="form-label text-xs text-secondary">Tipologia</label>
                    <select name="tipologia_habitacao" class="form-select form-select-sm">
                      <option value="all" {{ ($filters['tipologia_habitacao'] ?? 'all') === 'all' ? 'selected' : '' }}>Todas</option>
                      @foreach($labelHabitacao as $valor => $label)
                        <option value="{{ $valor }}" {{ ($filters['tipologia_habitacao'] ?? 'all') === $valor ? 'selected' : '' }}>{{ $label }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label text-xs text-secondary">Estado residência</label>
                    <select name="tipologia_propriedade" class="form-select form-select-sm">
                      <option value="all" {{ ($filters['tipologia_propriedade'] ?? 'all') === 'all' ? 'selected' : '' }}>Todos</option>
                      @foreach($labelPropriedade as $valor => $label)
                        <option value="{{ $valor }}" {{ ($filters['tipologia_propriedade'] ?? 'all') === $valor ? 'selected' : '' }}>{{ $label }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-12 col-xl-7">
              <div class="border rounded-4 h-100 p-3">
                <small class="text-uppercase text-secondary">Território</small>
                <div class="row g-3 mt-2">
                  <div class="col-md-6">
                    <label class="form-label text-xs text-secondary">Concelho</label>
                    <select name="concelho_id" id="concelho_id" class="form-select form-select-sm">
                      <option value="all" {{ ($filters['concelho_id'] ?? 'all') === 'all' ? 'selected' : '' }}>Todos</option>
                      @foreach($concelhos as $concelho)
                        <option value="{{ $concelho->id }}" {{ (string) ($filters['concelho_id'] ?? 'all') === (string) $concelho->id ? 'selected' : '' }}>{{ $concelho->nome }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md-6">
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
                  <label class="form-label text-xs text-secondary">Resumo do gráfico</label>
                  <p class="text-sm text-secondary mb-2">A visualização personalizada mostra quantas famílias correspondem aos filtros ativos.</p>
                  <small class="text-xs text-secondary">Ajusta os filtros acima e usa o botão "Gerar gráfico" para atualizar o resultado.</small>
                </div>
                <div class="d-flex flex-column flex-md-row gap-2 w-100 w-xl-auto">
                  <a href="{{ route('funcionario.relatorios.index') }}" class="btn btn-outline-secondary w-100">Limpar</a>
                  <button class="btn bg-gradient-success w-100" type="submit">Aplicar filtros</button>
                  <button class="btn btn-success w-100" type="button" id="btn-gerar-grafico" {{ ((int) ($totais['totalFamilias'] ?? 0)) === 0 ? 'disabled' : '' }}>Gerar gráfico</button>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>

    <div class="card mb-4" id="custom-chart-card">
      <div class="card-header pb-0 d-flex justify-content-between align-items-center">
        <div>
          <h6 class="mb-0">Gráfico do filtro atual</h6>
          <p class="text-xs text-secondary mb-0">Mostra quantas famílias correspondem aos critérios escolhidos.</p>
        </div>
        <span class="badge bg-light border text-primary" id="custom-chart-title">Aguardando geração</span>
      </div>
      <div class="card-body">
        <p class="text-sm text-secondary mb-0" id="custom-chart-placeholder">Utiliza o botão "Gerar gráfico" após definir os filtros.</p>
        <div class="chart d-none mt-3" style="height:320px" id="custom-chart-wrapper">
          <canvas id="chart-custom" height="320"></canvas>
        </div>
        <div class="alert alert-warning border d-none mt-3 text-sm" id="custom-chart-alert"></div>
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
            @forelse($chartHabitacaoData as $label => $valor)
              <div class="d-flex justify-content-between text-sm">
                <span>{{ $label }}</span><span class="font-weight-bold">{{ $valor }}</span>
              </div>
            @empty
              <p class="text-sm text-secondary">Sem dados disponíveis.</p>
            @endforelse
            <hr>
            <p class="text-xs text-secondary mb-1">Estado da residência</p>
            @forelse($chartPropriedadeData as $label => $valor)
              <div class="d-flex justify-content-between text-sm">
                <span>{{ $label }}</span><span class="font-weight-bold">{{ $valor }}</span>
              </div>
            @empty
              <p class="text-sm text-secondary">Sem dados disponíveis.</p>
            @endforelse
            <hr>
            <p class="text-xs text-secondary mb-1">Localização do alojamento</p>
            @forelse($localizacaoDistrib as $label => $valor)
              <div class="d-flex justify-content-between text-sm">
                <span>{{ $label }}</span><span class="font-weight-bold">{{ $valor }}</span>
              </div>
            @empty
              <p class="text-sm text-secondary">Sem dados disponíveis.</p>
            @endforelse
            <hr>
            <p class="text-xs text-secondary mb-1">Condição do alojamento</p>
            @forelse($condicaoDistrib as $label => $valor)
              <div class="d-flex justify-content-between text-sm">
                <span>{{ $label }}</span><span class="font-weight-bold">{{ $valor }}</span>
              </div>
            @empty
              <p class="text-sm text-secondary">Sem dados disponíveis.</p>
            @endforelse
          </div>
        </div>
      </div>
    </div>

    <div class="row mb-4">
      <div class="col-xl-4 mb-3">
        <div class="card h-100">
          <div class="card-header pb-0">
            <h6 class="mb-0">Integração com serviços</h6>
            <p class="text-xs text-secondary mb-0">Adesão da família aos serviços públicos essenciais.</p>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <p class="text-xs text-secondary mb-1">Centro de saúde</p>
              <div class="d-flex justify-content-between align-items-baseline">
                <span class="text-sm">Famílias inscritas</span>
                <h5 class="mb-0">{{ number_format($integracaoSaude) }}</h5>
              </div>
              <span class="text-xs text-secondary">de {{ number_format($totais['totalFamilias'] ?? 0) }} famílias filtradas</span>
            </div>
            <hr>
            <p class="text-xs text-secondary mb-1">Agrupamento de escolas</p>
            @foreach($labelEscola as $valor => $rotulo)
              <div class="d-flex justify-content-between text-sm">
                <span>{{ $rotulo }}</span>
                <span class="font-weight-bold">{{ number_format($integracaoEscola->get($valor, 0)) }}</span>
              </div>
            @endforeach
          </div>
        </div>
      </div>
      <div class="col-xl-8 mb-3">
        <div class="card h-100">
          <div class="card-header pb-0 d-flex justify-content-between align-items-center">
            <div>
              <h6 class="mb-0">Necessidades de apoio identificadas</h6>
              <p class="text-xs text-secondary mb-0">Sinalizações feitas nas fichas da família (máx. 6).</p>
            </div>
          </div>
          <div class="card-body">
            @if($necessidadesDistrib->isNotEmpty())
              <ul class="list-group">
                @foreach($necessidadesDistrib->take(6) as $rotulo => $valor)
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>{{ $rotulo }}</span>
                    <span class="badge bg-gradient-success">{{ $valor }}</span>
                  </li>
                @endforeach
              </ul>
            @else
              <p class="text-sm text-secondary mb-0">Nenhuma necessidade foi assinalada nas fichas para este filtro.</p>
            @endif
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
                        @php
                          $habLabel = $labelHabitacao[$familia['tipologia_habitacao']] ?? ucfirst($familia['tipologia_habitacao']);
                          $propLabel = $labelPropriedade[$familia['tipologia_propriedade']] ?? ucfirst($familia['tipologia_propriedade']);
                          $locLabel = $labelLocalizacao[$familia['localizacao_tipo'] ?? 'nao_informado'] ?? ucfirst($familia['localizacao_tipo'] ?? '');
                          $condLabel = $labelCondicao[$familia['condicao_alojamento'] ?? 'nao_informado'] ?? ucfirst($familia['condicao_alojamento'] ?? '');
                        @endphp
                        <td class="text-sm">
                          <div class="d-flex flex-column">
                            <span>{{ $habLabel }} / {{ $propLabel }}</span>
                            <small class="text-secondary">{{ $locLabel }} · {{ $condLabel }}</small>
                          </div>
                        </td>
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
      const filterTotals = @json(['familias' => $totais['totalFamilias'] ?? 0]);
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

      const customChartElements = {
        button: document.getElementById('btn-gerar-grafico'),
        wrapper: document.getElementById('custom-chart-wrapper'),
        placeholder: document.getElementById('custom-chart-placeholder'),
        alert: document.getElementById('custom-chart-alert'),
        title: document.getElementById('custom-chart-title'),
        card: document.getElementById('custom-chart-card'),
      };

      if (customChartElements.button && customChartElements.card) {
        customChartElements.button.addEventListener('click', () => {
          const totalFamilias = Number(filterTotals.familias || 0);

          if (totalFamilias <= 0) {
            if (customChartElements.alert) {
              customChartElements.alert.textContent = 'Não existem famílias para os filtros selecionados.';
              customChartElements.alert.classList.remove('d-none');
            }
            return;
          }

          if (customChartElements.alert) {
            customChartElements.alert.classList.add('d-none');
            customChartElements.alert.textContent = '';
          }

          const customConfig = {
            labels: ['Famílias'],
            datasets: [{
              label: 'Famílias no filtro',
              data: [totalFamilias],
              backgroundColor: ['#17ad37'],
            }],
            defaultType: 'bar',
          };

          initChart('chart-custom', customConfig);

          if (customChartElements.wrapper) {
            customChartElements.wrapper.classList.remove('d-none');
          }
          if (customChartElements.placeholder) {
            customChartElements.placeholder.classList.add('d-none');
          }
          if (customChartElements.title) {
            customChartElements.title.textContent = `${totalFamilias} família${totalFamilias !== 1 ? 's' : ''}`;
          }

          customChartElements.card.scrollIntoView({ behavior: 'smooth', block: 'center' });
        });
      }

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
