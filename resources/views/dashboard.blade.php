@extends('layouts.user_type.auth') {{-- Usa o layout correto --}}

@section('content') {{-- Usa a secção 'content' correta --}}

  <div class="container-fluid py-4">
    @php
      $anosSelect = collect($anosDisponiveis ?? [])->filter();
      if ($anosSelect->isEmpty()) {
          $anosSelect = collect([date('Y')]);
      }
      if (! $anosSelect->contains((int) $inqueritoAnoAtual)) {
          $anosSelect->prepend((int) $inqueritoAnoAtual);
      }
      $anosSelect = $anosSelect->unique()->sortDesc()->values();
    @endphp
    @php
      $authUser = Auth::user();
      $mostrarDashboardRegional = $mostrarDashboardRegional ?? false;
      $concelhosResumo = $concelhosResumo ?? collect();
      $dashboardProgress = $dashboardProgress ?? [
        'totalConcelhos' => 0,
        'concelhosComInquerito' => 0,
        'percentual' => 0,
      ];
      $regionalHighlights = $regionalHighlights ?? [
        'totalPendentes' => 0,
        'concelhosComPendencias' => 0,
        'concelhosConcluidos' => 0,
        'familiasMonitorizadas' => 0,
        'ticketsPendentes' => 0,
      ];
      $agrupamentoResumo = $agrupamentoResumo ?? [
        'totalSubmissoes' => 0,
        'ultimoAno' => null,
        'ultimoTotalAlunos' => 0,
      ];
      $escolasPendentes = collect($escolasPendentes ?? []);
      $escolasResumo = $escolasResumo ?? [
        'anoReferencia' => null,
        'totalInqueritos' => 0,
        'agrupamentosComDados' => 0,
        'totalAlunos' => 0,
      ];
      $escolasHighlights = $escolasHighlights ?? [
        'totalAgrupamentos' => 0,
        'agrupamentosComDados' => 0,
        'agrupamentosPendentes' => 0,
        'totalAlunos' => 0,
        'mediaAlunos' => 0,
        'totalInqueritos' => 0,
        'anoReferencia' => null,
      ];
      $escolasPercentual = $escolasHighlights['totalAgrupamentos'] > 0
        ? round(($escolasHighlights['agrupamentosComDados'] / $escolasHighlights['totalAgrupamentos']) * 100)
        : 0;
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
                  <p class="text-sm mb-1 text-capitalize font-weight-bold">Notificações do suporte</p>
                  <h5 class="font-weight-bolder mb-0">{{ $ticketsRespondidos }}</h5>
                  <p class="text-xs text-secondary mb-2">
                    @if($ticketsRespondidos > 0)
                      Existem notificações do suporte a aguardar acompanhamento.
                    @else
                      Sem novas notificações do suporte.
                    @endif
                  </p>
                </div>
                <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                  <i class="fas fa-headset text-lg opacity-10" aria-hidden="true"></i>
                </div>
              </div>
              <a href="{{ route('freguesia.suporte.index') }}" class="btn btn-sm bg-gradient-secondary mt-auto align-self-start">Ver notificações do suporte</a>
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
    @elseif($authUser->isAgrupamento())
      <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
          <div class="card h-100">
            <div class="card-body p-3 d-flex flex-column">
              <div class="numbers">
                <p class="text-sm mb-1 text-capitalize font-weight-bold">Inquérito {{ $inqueritoAnoAtual }}</p>
                @if($inqueritoDisponivel)
                  <h6 class="text-success mb-1">Disponível até {{ optional($inqueritoPrazo)->format('d/m/Y') }}</h6>
                  <p class="text-xs text-secondary mb-3">Registe as nacionalidades por nível de ensino para o ano em curso.</p>
                  <a href="{{ route('agrupamento.inqueritos.create') }}" class="btn btn-sm bg-gradient-success">Preencher agora</a>
                @elseif($jaPreencheuInquerito)
                  <h6 class="text-success mb-1">Inquérito submetido</h6>
                  <p class="text-xs text-secondary mb-3">Pode consultar o histórico e exportar os dados se necessário.</p>
                  <a href="{{ route('agrupamento.inqueritos.index') }}" class="btn btn-sm bg-gradient-secondary">Ver histórico</a>
                @else
                  <h6 class="text-danger mb-1">Prazo expirado</h6>
                  <p class="text-xs text-secondary mb-3">Contacte a CIMBB caso necessite de reabrir o formulário.</p>
                  <a href="{{ route('agrupamento.inqueritos.index') }}" class="btn btn-sm bg-gradient-secondary">Consultar</a>
                @endif
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
          <div class="card h-100">
            <div class="card-body p-3 d-flex flex-column">
              <div class="d-flex justify-content-between align-items-start">
                <div class="numbers">
                  <p class="text-sm mb-1 text-capitalize font-weight-bold">Notificações do suporte</p>
                  <h5 class="font-weight-bolder mb-0">{{ $ticketsRespondidos }}</h5>
                  <p class="text-xs text-secondary mb-2">
                    {{ $ticketsRespondidos > 0 ? 'Existem novas respostas da equipa CIMBB.' : 'Sem novas mensagens no suporte.' }}
                  </p>
                </div>
                <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md info-card-icon">
                  <i class="fas fa-headset text-lg opacity-10" aria-hidden="true"></i>
                </div>
              </div>
              <a href="{{ route('agrupamento.suporte.index') }}" class="btn btn-sm bg-gradient-secondary mt-auto align-self-start">Abrir suporte</a>
            </div>
          </div>
        </div>
        <div class="col-xl-4 col-md-12 mb-4">
          <div class="card h-100">
            <div class="card-body p-4 d-flex flex-column gap-3">
              <p class="text-sm mb-0 text-capitalize font-weight-bold">Alunos estrangeiros registados</p>
              <div class="d-flex justify-content-between align-items-center">
                <div class="numbers">
                  <h2 class="font-weight-bolder mb-0">{{ $agrupamentoResumo['ultimoTotalAlunos'] ?? 0 }}</h2>
                  <p class="text-xs text-secondary mb-0">Último inquérito {{ $agrupamentoResumo['ultimoAno'] ?? '—' }}</p>
                </div>
                <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md info-card-icon">
                  <i class="fas fa-user-graduate text-lg opacity-10" aria-hidden="true"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    @elseif($mostrarDashboardRegional)
      <div class="row mb-4">
        <div class="col-xl-8">
          <div class="card h-100">
            <div class="card-body d-flex flex-column flex-lg-row justify-content-between gap-4">
              <div>
                <p class="text-xs text-uppercase text-secondary mb-1">Visão regional</p>
                <h4 class="mb-2" id="regionalTitle">Estado global do território CIMBB</h4>
                <p class="text-sm text-secondary mb-3" id="regionalDescription">
                  Monitorização em tempo real das freguesias e dos inquéritos para o ano {{ $inqueritoAnoAtual }}.
                </p>
              </div>
              <div class="text-lg-end">
                <span class="text-xs text-secondary">Ano em análise</span>
                <h2 class="font-weight-bolder mb-0">{{ $inqueritoAnoAtual }}</h2>
                <form method="GET" action="{{ route('dashboard') }}" class="d-flex flex-wrap justify-content-center align-items-stretch gap-3 mt-3">
                  <div class="year-control d-flex align-items-center">
                    <select name="ano" class="form-select form-select-sm year-select text-center">
                      @foreach($anosSelect as $ano)
                        <option value="{{ $ano }}" {{ (int) $ano === (int) $inqueritoAnoAtual ? 'selected' : '' }}>{{ $ano }}</option>
                      @endforeach
                    </select>
                  </div>
                  @foreach(request()->query() as $param => $value)
                    @continue($param === 'ano')
                    <input type="hidden" name="{{ $param }}" value="{{ $value }}">
                  @endforeach
                  <button class="btn btn-success px-4 year-control align-self-stretch d-flex align-items-center justify-content-center" type="submit">Alterar</button>
                </form>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-4 mt-4 mt-xl-0 regional-scope-panel regional-scope-freguesias">
          <div class="card h-100">
            <div class="card-body">
              <h6 class="text-uppercase text-secondary text-xxs font-weight-bolder mb-1">Progresso global</h6>
              <h3 class="font-weight-bolder mb-0">{{ $dashboardProgress['concelhosComInquerito'] }} / {{ $dashboardProgress['totalConcelhos'] }}</h3>
              <p class="text-sm text-secondary mb-3">Concelhos com todas as freguesias concluídas.</p>
              <div class="progress mb-2">
                <div class="progress-bar bg-gradient-info" role="progressbar" style="width: {{ $dashboardProgress['percentual'] }}%;" aria-valuenow="{{ $dashboardProgress['percentual'] }}" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
              <small class="text-muted">{{ $dashboardProgress['percentual'] }}% do território concluído.</small>
            </div>
          </div>
        </div>
        <div class="col-xl-4 mt-4 mt-xl-0 regional-scope-panel regional-scope-escolas d-none">
          <div class="card h-100">
            <div class="card-body">
              <h6 class="text-uppercase text-secondary text-xxs font-weight-bolder mb-1">Cobertura dos agrupamentos</h6>
              <h3 class="font-weight-bolder mb-0">{{ $escolasHighlights['agrupamentosComDados'] }} / {{ $escolasHighlights['totalAgrupamentos'] }}</h3>
              <p class="text-sm text-secondary mb-3">Agrupamentos com o inquérito das escolas concluído.</p>
              <div class="progress mb-2">
                <div class="progress-bar bg-gradient-success" role="progressbar" style="width: {{ $escolasPercentual }}%;" aria-valuenow="{{ $escolasPercentual }}" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
              <small class="text-muted">{{ $escolasPercentual }}% dos agrupamentos com dados.</small>
            </div>
          </div>
        </div>
      </div>

      <div class="row mb-4 regional-scope-panel regional-scope-freguesias">
        <div class="col-xl-3 col-sm-6 mb-4">
          <div class="card h-100">
            <div class="card-body">
              <p class="text-xs text-uppercase text-secondary mb-1">Concelhos concluídos</p>
              <h4 class="font-weight-bolder mb-0">{{ $regionalHighlights['concelhosConcluidos'] }}</h4>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-4">
          <div class="card h-100">
            <div class="card-body">
              <p class="text-xs text-uppercase text-secondary mb-1">Concelhos com pendências</p>
              <h4 class="font-weight-bolder mb-0">{{ $regionalHighlights['concelhosComPendencias'] }}</h4>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-4">
          <div class="card h-100">
            <div class="card-body">
              <p class="text-xs text-uppercase text-secondary mb-1">Freguesias em falta</p>
              <h4 class="font-weight-bolder mb-0">{{ $regionalHighlights['totalPendentes'] }}</h4>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-4">
          <div class="card h-100">
            <div class="card-body">
              <p class="text-xs text-uppercase text-secondary mb-1">Famílias monitorizadas</p>
              <h4 class="font-weight-bolder mb-0">{{ $regionalHighlights['familiasMonitorizadas'] }}</h4>
            </div>
          </div>
        </div>
      </div>
      <div class="row mb-4 regional-scope-panel regional-scope-escolas d-none">
        <div class="col-xl-3 col-sm-6 mb-4">
          <div class="card h-100">
            <div class="card-body">
              <p class="text-xs text-uppercase text-secondary mb-1">Agrupamentos com dados</p>
              <h4 class="font-weight-bolder mb-0">{{ $escolasHighlights['agrupamentosComDados'] }}</h4>
              <small class="text-xs text-secondary">de {{ $escolasHighlights['totalAgrupamentos'] }} totais</small>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-4">
          <div class="card h-100">
            <div class="card-body">
              <p class="text-xs text-uppercase text-secondary mb-1">Agrupamentos pendentes</p>
              <h4 class="font-weight-bolder mb-0">{{ $escolasHighlights['agrupamentosPendentes'] }}</h4>
              <small class="text-xs text-secondary">Aguardam submissão do inquérito</small>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-4">
          <div class="card h-100">
            <div class="card-body">
              <p class="text-xs text-uppercase text-secondary mb-1">Total alunos reportados</p>
              <h4 class="font-weight-bolder mb-0">{{ number_format($escolasHighlights['totalAlunos'], 0, ',', ' ') }}</h4>
              <small class="text-xs text-secondary">Inquéritos {{ $escolasHighlights['anoReferencia'] ?? $inqueritoAnoAtual }}</small>
            </div>
          </div>
        </div>
      </div>

      <div class="row mb-4">
        <div class="col-12">
          <div class="card">
            <div class="card-header pb-0 d-flex flex-wrap justify-content-between align-items-center gap-3">
              <div>
                <h6 class="mb-0">Mapa de pendências</h6>
                <p class="text-sm mb-0 text-secondary">Alterna para visualizar freguesias ou escolas em falta.</p>
              </div>
              <div class="btn-group btn-group-sm" role="group" aria-label="Alternar pendências">
                <button type="button" class="btn btn-success regional-view-toggle" data-target="freguesias">Freguesias</button>
                <button type="button" class="btn btn-outline-success regional-view-toggle" data-target="escolas">Escolas</button>
              </div>
            </div>
            <div class="card-body px-3 pb-3">
              <div class="regional-panel regional-panel-freguesias">
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
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-end">Pendentes</th>
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
                            @php
                              $freguesiasPendentes = collect($concelho['freguesias_pendentes'] ?? []);
                              $freguesiasConcluidas = collect($concelho['freguesias_concluidas'] ?? []);
                              $freguesiasPayload = htmlspecialchars(json_encode([
                                'pendentes' => $freguesiasPendentes->values(),
                                'concluidas' => $freguesiasConcluidas->values(),
                              ], JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');
                            @endphp
                            <div class="d-flex flex-column">
                              <small class="text-xs text-secondary mb-1">
                                {{ $concelho['freguesias_com_inquerito'] }} / {{ $concelho['total_freguesias'] }} freguesias
                              </small>
                              <div class="progress">
                                <div class="progress-bar bg-gradient-success" role="progressbar" style="width: {{ $concelho['percentual_inquerito'] }}%;" aria-valuenow="{{ $concelho['percentual_inquerito'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                              </div>
                              @if($freguesiasPendentes->isNotEmpty())
                                <small class="text-xs text-danger mt-2">
                                  {{ $freguesiasPendentes->count() }} freguesias pendentes
                                </small>
                              @else
                                <small class="text-xs text-success mt-2">Todas as freguesias já submeteram.</small>
                              @endif
                            </div>
                          </td>
                          <td class="text-end">
                            @if($freguesiasPendentes->isNotEmpty())
                              <button class="btn btn-sm bg-gradient-danger text-white ver-pendentes-btn"
                                data-bs-toggle="modal"
                                data-bs-target="#pendentesModal"
                                data-concelho="{{ $concelho['nome'] }}"
                                data-total="{{ $freguesiasPendentes->count() }}"
                                data-freguesias="{!! $freguesiasPayload !!}">
                                Pendentes
                              </button>
                            @endif
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
              <div class="regional-panel regional-panel-escolas d-none">
                @if($escolasPendentes->isNotEmpty())
                  <p class="text-sm text-secondary text-center fw-bold">{{ $escolasPendentes->count() }} agrupamentos ainda não entregaram o inquérito {{ $inqueritoAnoAtual }}.</p>
                  <div class="table-responsive">
                    <table class="table align-items-center mb-0">
                      <thead>
                        <tr>
                          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Agrupamento</th>
                          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Concelho</th>
                          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-end">Situação</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($escolasPendentes as $pendente)
                          <tr>
                            <td>
                              <div class="d-flex flex-column">
                                <span class="text-sm font-weight-bold">{{ $pendente['nome'] }}</span>
                                <small class="text-xs text-secondary">Agrupamento pendente</small>
                              </div>
                            </td>
                            <td class="text-sm">{{ $pendente['concelho'] }}</td>
                            <td class="text-end">
                              <button type="button" class="btn btn-sm bg-gradient-danger text-white">Pendente</button>
                            </td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                @else
                  <p class="text-sm text-secondary mb-0">Todos os agrupamentos já submeteram o inquérito das escolas.</p>
                @endif
              </div>
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
                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Total de alunos reportados</p>
                    <h5 class="font-weight-bolder mb-0">{{ number_format($escolasResumo['totalAlunos'] ?? 0, 0, ',', ' ') }}</h5>
                  </div>
                </div>
                <div class="col-4 text-end">
                  <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                    <i class="fas fa-school text-lg opacity-10" aria-hidden="true"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    @endif
    {{-- ***** FIM DA LINHA DE CARTÕES ***** --}}
    
    @if(!$mostrarDashboardRegional)
      <div class="row mt-4">
        
        {{-- Coluna da Esquerda: Caixa de Boas-Vindas (JÁ TINHA) --}}
        <div class="{{ $authUser->isAgrupamento() ? 'col-12 mb-4' : 'col-lg-7 mb-lg-0 mb-4' }}">
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
        @unless($authUser->isAgrupamento())
          {{-- Coluna da Direita: Gráfico de Nacionalidades (mantém para admin/freguesia/funcionário) --}}
          <div class="col-lg-5">
            <div class="card h-100">
              <div class="card-header pb-0">
                <h6>Nacionalidades (Top 10)</h6>
                <p class="text-sm">
                    <span class="font-weight-bold">{{ $tituloDashboard }}</span>
                </p>
              </div>
              <div class="card-body p-3 d-flex align-items-center justify-content-center">
                @if(($chartValues ?? collect())->count() > 0)
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
        @endunless
      </div>
    @endif
  </div>

  {{-- Modal para listar freguesias pendentes do inquérito --}}
  <div class="modal fade" id="pendentesModal" tabindex="-1" aria-labelledby="pendentesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="pendentesModalLabel">Freguesias pendentes</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>
        <div class="modal-body">
          <p class="text-sm text-secondary mb-3" id="pendentesModalSubtitle"></p>
          <div id="pendentesListWrapper" class="pendentes-scroll">
            <div id="pendentesSection" class="mb-4">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="text-xs text-uppercase text-secondary mb-0">Freguesias pendentes</h6>
                <span class="badge bg-danger" id="pendentesCount">0</span>
              </div>
              <div id="pendentesEmptyMessage" class="text-success text-sm mb-2 d-none">Todas as freguesias deste concelho já submeteram.</div>
              <ul class="list-group d-none" id="pendentesList"></ul>
            </div>
            <div id="concluidasSection">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="text-xs text-uppercase text-secondary mb-0">Freguesias concluídas</h6>
                <span class="badge bg-success" id="concluidasCount">0</span>
              </div>
              <div id="concluidasEmptyMessage" class="text-muted text-sm mb-2">Nenhuma freguesia submeteu o inquérito neste concelho.</div>
              <ul class="list-group d-none" id="concluidasList"></ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

@endsection

{{-- Adicionar o Script do Chart.js no final da página --}}
@push('css')
  <style>
    .pendentes-scroll {
      max-height: 320px;
      overflow-y: auto;
      padding-right: 0.25rem;
      scrollbar-width: thin;
      scrollbar-color: #82d616 rgba(130, 214, 22, 0.15);
    }

    .pendentes-scroll::-webkit-scrollbar {
      width: 6px;
    }

    .pendentes-scroll::-webkit-scrollbar-track {
      background: rgba(130, 214, 22, 0.1);
      border-radius: 100px;
    }

    .pendentes-scroll::-webkit-scrollbar-thumb {
      background: #82d616;
      border-radius: 100px;
    }

    .year-control {
      min-width: 150px;
    }

    .year-control .year-select,
    .year-control.btn-success {
      height: 44px;
      width: 100%;
    }

    .year-control.btn-success {
      display: inline-flex;
      align-items: center;
      justify-content: center;
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

    .info-card-icon {
      width: 48px;
      height: 48px;
      border-radius: 16px;
    }
  </style>
@endpush

@push('js')
  {{-- O 'chartjs.min.js' já é carregado pelo seu app.blade.php --}}
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const regionalToggleButtons = document.querySelectorAll('.regional-view-toggle');
      const regionalTitle = document.getElementById('regionalTitle');
      const regionalDescription = document.getElementById('regionalDescription');
      const textsPorEscopo = {
        freguesias: {
          titulo: 'Estado global do território CIMBB',
          descricao: 'Monitorização em tempo real das freguesias e dos inquéritos para o ano {{ $inqueritoAnoAtual }}.',
        },
        escolas: {
          titulo: 'Rede escolar CIMBB',
          descricao: 'Acompanha os agrupamentos e as submissões dos inquéritos das escolas para {{ $inqueritoAnoAtual }}.',
        },
      };
      const updateRegionalPanels = (scope) => {
        document.querySelectorAll('.regional-panel').forEach((panel) => panel.classList.add('d-none'));
        document.querySelectorAll('.regional-panel-' + scope).forEach((panel) => panel.classList.remove('d-none'));
        document.querySelectorAll('.regional-scope-panel').forEach((panel) => panel.classList.add('d-none'));
        document.querySelectorAll('.regional-scope-' + scope).forEach((panel) => panel.classList.remove('d-none'));

        if (regionalTitle && regionalDescription) {
          regionalTitle.textContent = textsPorEscopo[scope]?.titulo || textsPorEscopo.freguesias.titulo;
          regionalDescription.textContent = textsPorEscopo[scope]?.descricao || textsPorEscopo.freguesias.descricao;
        }
      };

      if (regionalToggleButtons.length) {
        regionalToggleButtons.forEach((button) => {
          button.addEventListener('click', () => {
            const target = button.getAttribute('data-target');
            regionalToggleButtons.forEach((btn) => {
              const isActive = btn === button;
              btn.classList.toggle('btn-success', isActive);
              btn.classList.toggle('btn-outline-success', !isActive);
            });

            updateRegionalPanels(target);
          });
        });

        updateRegionalPanels('freguesias');
      }

      var pendentesModal = document.getElementById("pendentesModal");
      if (pendentesModal) {
        var scrollableArea = pendentesModal.querySelector('.pendentes-scroll');

        function bloquearScrollExterior(evento) {
          if (!scrollableArea || !pendentesModal.classList.contains('show')) {
            return;
          }

          if (!scrollableArea.contains(evento.target)) {
            evento.preventDefault();
          }
        }

        function preencherSecao(itens, listElement, emptyMessage, countBadge, badgeClass) {
          if (!listElement || !emptyMessage || !countBadge) {
            return;
          }

          countBadge.textContent = itens.length;
          listElement.innerHTML = '';

          if (!itens.length) {
            emptyMessage.classList.remove('d-none');
            listElement.classList.add('d-none');
            return;
          }

          emptyMessage.classList.add('d-none');
          listElement.classList.remove('d-none');

          itens.forEach(function (item) {
            var li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between align-items-center';
            var nome = item.nome || 'Freguesia sem nome';
            var codigo = item.codigo || '—';
            li.innerHTML = '<span>' + nome + '</span><span class="' + badgeClass + '">' + codigo + '</span>';
            listElement.appendChild(li);
          });
        }

        pendentesModal.addEventListener('wheel', bloquearScrollExterior, { passive: false });

        pendentesModal.addEventListener('show.bs.modal', function (event) {
          var button = event.relatedTarget;
          if (!button) {
            return;
          }

          var concelhoNome = button.getAttribute('data-concelho') || 'Concelho';
          var totalPendentes = button.getAttribute('data-total') || '0';
          var freguesiasRaw = button.getAttribute('data-freguesias') || '{}';
          var pendentesList = [];
          var concluidasList = [];

          try {
            var parsedPayload = JSON.parse(freguesiasRaw);
            pendentesList = parsedPayload.pendentes || [];
            concluidasList = parsedPayload.concluidas || [];
          } catch (error) {
            pendentesList = [];
            concluidasList = [];
          }

          var modalTitle = pendentesModal.querySelector('#pendentesModalLabel');
          var subtitle = pendentesModal.querySelector('#pendentesModalSubtitle');
          var emptyMessage = pendentesModal.querySelector('#pendentesEmptyMessage');
          var pendentesListElement = pendentesModal.querySelector('#pendentesList');
          var concluidasListElement = pendentesModal.querySelector('#concluidasList');
          var concluidasEmptyMessage = pendentesModal.querySelector('#concluidasEmptyMessage');
          var pendentesCount = pendentesModal.querySelector('#pendentesCount');
          var concluidasCount = pendentesModal.querySelector('#concluidasCount');

          if (modalTitle) {
            modalTitle.textContent = 'Freguesias pendentes - ' + concelhoNome;
          }

          if (subtitle) {
            subtitle.textContent = totalPendentes > 0
              ? totalPendentes + ' freguesias ainda não submeteram o inquérito anual.'
              : 'Sem freguesias pendentes para este concelho.';
          }

          preencherSecao(pendentesList, pendentesListElement, emptyMessage, pendentesCount, 'badge bg-secondary');
          preencherSecao(concluidasList, concluidasListElement, concluidasEmptyMessage, concluidasCount, 'badge bg-success');
        });

        pendentesModal.addEventListener('hidden.bs.modal', function () {
          document.body.style.overflow = '';
        });
        pendentesModal.addEventListener('shown.bs.modal', function () {
          document.body.style.overflow = 'hidden';
        });
      }

      @unless($authUser->isAgrupamento())
      var ctx = document.getElementById("nacionalidadeChart");
      if (ctx) {
        var chartCanvas = ctx.getContext("2d");

        // Transforma os dados do PHP (Blade) para JavaScript
        var chartLabels = @json($chartLabels ?? []);
        var chartData = @json($chartValues ?? []);

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
                    let percentage = total ? (value / total * 100).toFixed(1) : 0;

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
      @endunless

    });
  </script>
@endpush