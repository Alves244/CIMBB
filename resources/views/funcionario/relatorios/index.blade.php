@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
  <div class="row mb-4">
    <div class="col-lg-8">
      <div class="card h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
              <h5 class="mb-1">Relatórios Regionais</h5>
              <p class="text-sm text-secondary mb-0">Selecione o ano para consultar o progresso dos concelhos.</p>
            </div>
            <form method="GET" action="{{ route('funcionario.relatorios.index') }}" class="d-flex align-items-center gap-2">
              <div>
                <label for="ano" class="text-sm text-secondary">Ano</label>
                <select name="ano" id="ano" class="form-select form-select-sm">
                  @for($ano = date('Y'); $ano >= date('Y') - 5; $ano--)
                    <option value="{{ $ano }}" {{ $anoSelecionado == $ano ? 'selected' : '' }}>{{ $ano }}</option>
                  @endfor
                </select>
              </div>
              <button class="btn btn-sm bg-gradient-success mt-3" type="submit">Atualizar</button>
              <a href="{{ route('funcionario.relatorios.export', ['ano' => $anoSelecionado]) }}" class="btn btn-sm bg-gradient-primary mt-3">Exportar CSV</a>
            </form>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4 mt-4 mt-lg-0">
      <div class="card h-100">
        <div class="card-body">
          <h6 class="text-uppercase text-secondary text-xxs font-weight-bolder mb-1">Progresso Global</h6>
          <h4 class="font-weight-bolder mb-0">{{ $dashboardProgress['concelhosComInquerito'] }} / {{ $dashboardProgress['totalConcelhos'] }}</h4>
          <p class="text-sm text-secondary mb-3">Concelhos com todas as freguesias com o inquérito submetido.</p>
          <div class="progress mb-2">
            <div class="progress-bar bg-gradient-info" role="progressbar" style="width: {{ $dashboardProgress['percentual'] }}%;" aria-valuenow="{{ $dashboardProgress['percentual'] }}" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
          <small class="text-muted">{{ $dashboardProgress['percentual'] }}% do território concluído.</small>
        </div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header pb-0">
      <h6 class="mb-0">Resumo por Concelho - {{ $anoSelecionado }}</h6>
    </div>
    <div class="card-body p-0">
      @if($concelhosResumo->isNotEmpty())
        <div class="table-responsive">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Concelho</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Famílias</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Membros</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tickets pendentes</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Inquérito</th>
              </tr>
            </thead>
            <tbody>
              @foreach($concelhosResumo as $concelho)
                <tr>
                  <td class="text-sm">
                    <div class="d-flex flex-column">
                      <span class="font-weight-bold">{{ $concelho['nome'] }}</span>
                      <small class="text-secondary">{{ $concelho['codigo'] ?? '—' }}</small>
                    </div>
                  </td>
                  <td class="text-sm">{{ $concelho['total_familias'] }}</td>
                  <td class="text-sm">{{ $concelho['total_membros'] }}</td>
                  <td class="text-sm">{{ $concelho['tickets_pendentes'] }}</td>
                  <td class="text-sm">
                    <div class="d-flex flex-column">
                      <small class="text-secondary">{{ $concelho['freguesias_com_inquerito'] }} / {{ $concelho['total_freguesias'] }} freguesias</small>
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
        <p class="text-sm text-secondary p-4 mb-0">Ainda não existem dados para este ano.</p>
      @endif
    </div>
  </div>
</div>
@endsection
