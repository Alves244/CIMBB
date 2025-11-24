@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
  <div class="row">
    <div class="col-12">
      <div class="card mb-4">
        <div class="card-header pb-0 d-flex flex-column flex-xl-row gap-3 align-items-xl-center justify-content-xl-between">
          <div>
            <h6>Logs do Sistema</h6>
            <p class="text-sm mb-0">Registo cronológico das ações executadas pelos utilizadores autenticados.</p>
          </div>
          <x-admin.filter-modal modalId="logsFilterModal"
                                :action="route('admin.logs.index')"
                                :clear-url="route('admin.logs.index')"
                                title="Filtrar logs do sistema">
            <div class="col-12 col-md-6">
              <label class="form-label text-xs text-uppercase text-secondary mb-1">Ação</label>
              <select name="acao" class="form-select">
                <option value="">Todas</option>
                @foreach($acoesDisponiveis as $acaoDisponivel)
                  <option value="{{ $acaoDisponivel }}" {{ ($filtros['acao'] ?? '') === $acaoDisponivel ? 'selected' : '' }}>
                    {{ $acaoDisponivel }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label text-xs text-uppercase text-secondary mb-1">Pesquisa</label>
              <input type="text" name="pesquisa" class="form-control" placeholder="Utilizador ou descrição" value="{{ $filtros['pesquisa'] ?? '' }}">
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label text-xs text-uppercase text-secondary mb-1">Início</label>
              <input type="date" name="inicio" class="form-control" value="{{ $filtros['inicio'] ?? '' }}">
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label text-xs text-uppercase text-secondary mb-1">Fim</label>
              <input type="date" name="fim" class="form-control" value="{{ $filtros['fim'] ?? '' }}">
            </div>
          </x-admin.filter-modal>
        </div>
        <div class="card-body px-0 pt-0 pb-2">
          <div class="table-responsive p-0">
            <table class="table align-items-center mb-0">
              <thead>
                <tr>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Data</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Utilizador</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Ação</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">IP</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Descrição</th>
                </tr>
              </thead>
              <tbody>
                @forelse($logs as $log)
                  <tr>
                    <td class="ps-4">
                      <p class="text-xs font-weight-bold mb-0">{{ optional($log->data_hora)->format('d/m/Y H:i') }}</p>
                    </td>
                    <td>
                      <p class="text-xs font-weight-bold mb-0">{{ $log->utilizador->nome ?? 'Utilizador removido' }}</p>
                      <p class="text-xxs text-secondary mb-0">{{ $log->utilizador->email ?? '-' }}</p>
                    </td>
                    <td>
                      <span class="badge badge-sm bg-gradient-secondary text-uppercase">{{ $log->acao }}</span>
                    </td>
                    <td>
                      <p class="text-xs mb-0">{{ $log->ip ?? '-' }}</p>
                    </td>
                    <td>
                      <p class="text-xs mb-0">{{ $log->descricao ? \Illuminate\Support\Str::limit($log->descricao, 120) : '-' }}</p>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5" class="text-center py-4">Ainda não existem logs registados.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
        <x-admin.pagination :paginator="$logs" />
      </div>
    </div>
  </div>
</div>
@endsection
