@extends('layouts.user_type.auth') 

@section('content') 

    @php
      $labelsEstado = [
        'ativa' => 'Família ativa',
        'desinstalada' => 'Família desinstalada',
      ];
    @endphp

    <div class="container-fluid py-4">
    <div class="row">
      <div class="col-12">
        <div class="card mb-4">
          
          {{-- CABEÇALHO DA TABELA --}}
          <div class="card-header pb-0">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
              <div>
                <h6 class="mb-0">Famílias Registadas</h6>
                <p class="text-sm mb-0">Freguesia: {{ Auth::user()->freguesia->nome ?? 'N/A' }}</p>
              </div>
              <div class="d-flex flex-wrap gap-3 align-items-center justify-content-end">
                <form method="GET" action="{{ route('freguesia.familias.index') }}" class="d-flex align-items-center gap-2 mb-0">
                  <select name="estado" id="estado" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Todas as famílias</option>
                    @foreach (($estadosDisponiveis ?? []) as $estado)
                      <option value="{{ $estado }}" {{ ($estadoSelecionado ?? null) === $estado ? 'selected' : '' }}>{{ $labelsEstado[$estado] ?? ucfirst($estado) }}</option>
                    @endforeach
                  </select>
                  @if (!empty($estadoSelecionado))
                    <a href="{{ route('freguesia.familias.index') }}" class="btn btn-link btn-sm text-secondary">Limpar</a>
                  @endif
                </form>
                <a href="{{ route('freguesia.familias.create') }}" class="btn bg-gradient-success btn-sm">
                  <i class="fas fa-plus me-1"></i> Adicionar Família
                </a>
              </div>
            </div>
          </div>

          {{-- CORPO DA TABELA --}}
          <div class="card-body px-0 pt-0 pb-2">
            <div class="table-responsive p-0">
              <table class="table align-items-center mb-0">
                <thead>
                  <tr>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Código</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Estado</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Ano Inst.</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Nacionalidade</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Membros</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Habitação</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Propriedade</th>
                    <th class="text-secondary opacity-7">Ações</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse ($familias as $familia)
                    <tr>
                      {{-- CÓDIGO --}}
                      <td>
                        <div class="d-flex px-3 py-1">
                          <h6 class="mb-0 text-sm">{{ $familia->codigo }}</h6>
                        </div>
                      </td>
                      
                        {{-- ESTADO --}}
                        <td class="align-middle text-center text-xs">
                        @php
                          $estadoAtual = $familia->estado_acompanhamento;
                          $classeEstado = $estadoAtual === 'desinstalada' ? 'bg-gradient-danger' : 'bg-gradient-success';
                        @endphp
                        <span class="badge badge-sm {{ $classeEstado }}">{{ $labelsEstado[$estadoAtual] ?? ucfirst($estadoAtual) }}</span>
                        @if ($familia->data_desinstalacao)
                          <small class="d-block text-xxs text-muted">desde {{ $familia->data_desinstalacao->format('d/m/Y') }}</small>
                        @elseif ($familia->ano_desinstalacao)
                          <small class="d-block text-xxs text-muted">desde {{ $familia->ano_desinstalacao }}</small>
                        @endif
                        </td>

                        {{-- ANO DE INSTALAÇÃO --}}
                      <td class="align-middle text-center text-sm">
                        <span class="badge badge-sm bg-gradient-secondary">{{ $familia->ano_instalacao }}</span>
                      </td>
                      
                      {{-- NACIONALIDADE --}}
                      <td>
                        <p class="text-xs font-weight-bold mb-0">{{ $familia->nacionalidade }}</p>
                      </td>
                      
                      {{-- MEMBROS --}}
                      <td class="align-middle text-center text-sm">
                        <span class="text-secondary text-xs font-weight-bold">{{ $familia->agregadoFamiliar?->total_membros ?? 'N/A' }}</span>
                      </td>
                      
                      {{-- HABITAÇÃO --}}
                      <td>
                        <p class="text-xs font-weight-bold mb-0">{{ ucfirst($familia->tipologia_habitacao) }}</p>
                      </td>
                      
                      {{-- PROPRIEDADE --}}
                      <td>
                        <p class="text-xs font-weight-bold mb-0">{{ ucfirst($familia->tipologia_propriedade) }}</p>
                      </td>
                      
                      {{-- AÇÕES (BOTÕES CORRIGIDOS) --}}
                      <td class="align-middle">
                        
                        {{-- Botão Editar --}}
                        <a href="{{ route('freguesia.familias.edit', $familia->id) }}" class="btn btn-link text-dark px-3 mb-0" data-bs-toggle="tooltip" data-bs-original-title="Editar Família">
                            <i class="fas fa-pencil-alt text-dark me-2" aria-hidden="true"></i>
                            Editar
                        </a>
                        
                        {{-- Botão Apagar --}}
                        <form action="{{ route('freguesia.familias.destroy', $familia->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-link text-danger text-gradient px-3 mb-0"
                                    onclick="return confirm('Tem a certeza que deseja apagar esta família (Código: {{ $familia->codigo }})? Esta ação não pode ser revertida.')"
                                    data-bs-toggle="tooltip" data-bs-original-title="Apagar Família">
                                <i class="far fa-trash-alt me-2" aria-hidden="true"></i>
                                Apagar
                            </button>
                        </form>

                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="8" class="text-center text-sm py-4">Nenhuma família registada para esta freguesia.</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
          
          {{-- PAGINAÇÃO --}}
          @if ($familias->hasPages())
            <x-admin.pagination :paginator="$familias" />
          @endif
        </div>
      </div>
    </div>
  </div>

@endsection

@push('js')
  <script>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
    })
  </script>
@endpush


