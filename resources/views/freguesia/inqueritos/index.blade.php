@extends('layouts.user_type.auth')

@section('content')

  <div class="container-fluid py-4">
    <div class="row">
      <div class="col-12">
        <div class="card mb-4">
          
          {{-- Cabeçalho --}}
          <div class="card-header pb-0 d-flex justify-content-between align-items-center">
            <div>
              <h6 class="mb-0">Histórico de Inquéritos Anuais</h6>
              <p class="text-sm">Freguesia: {{ Auth::user()->freguesia->nome ?? 'N/A' }}</p>
            </div>
            
            {{-- Botão de Preencher Novo (Só aparece se ainda não preencheu e estiver no prazo) --}}
            @if (!$jaPreencheuEsteAno && $dentroDoPrazo)
                <a href="{{ route('freguesia.inqueritos.create') }}" class="btn bg-gradient-success btn-sm mb-0">
                  <i class="fas fa-plus me-1"></i> Preencher Inquérito {{ $anoAtual }}
                </a>
            @elseif($jaPreencheuEsteAno)
                <span class="badge bg-gradient-success">Inquérito {{ $anoAtual }} Submetido</span>
            @else
                <span class="badge bg-gradient-secondary">Prazo para {{ $anoAtual }} Expirou</span>
            @endif
          </div>

          {{-- Tabela --}}
          <div class="card-body px-0 pt-0 pb-2">
            <div class="table-responsive p-0">
              <table class="table align-items-center mb-0">
                <thead>
                  <tr>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Ano</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Data Submissão</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Total Indivíduos</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Estado</th>
                    <th class="text-secondary opacity-7">Ações</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse ($inqueritos as $inquerito)
                    <tr>
                      {{-- Ano --}}
                      <td>
                        <div class="d-flex px-3 py-1">
                          <h6 class="mb-0 text-sm">{{ $inquerito->ano }}</h6>
                        </div>
                      </td>
                      
                      {{-- Data --}}
                      <td>
                        <p class="text-xs font-weight-bold mb-0">{{ $inquerito->created_at->format('d/m/Y H:i') }}</p>
                      </td>

                      {{-- Total (Exemplo de dado rápido) --}}
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ $inquerito->total_adultos }}</span>
                      </td>

                      {{-- Estado (Baseado no prazo) --}}
                      <td class="align-middle text-center text-sm">
                         {{-- Se for do ano atual E estiver no prazo, está "Aberto", senão "Fechado" --}}
                         @if($inquerito->ano == $anoAtual && $dentroDoPrazo)
                            <span class="badge badge-sm bg-gradient-info">Aberto para Edição</span>
                         @else
                            <span class="badge badge-sm bg-gradient-secondary">Fechado</span>
                         @endif
                      </td>

                      {{-- AÇÕES --}}
                      <td class="align-middle">
                        
                        {{-- 1. Botão PRÉ-VISUALIZAR (Sempre visível) --}}
                        {{-- Nota: Usa o 'target="_blank"' se quiseres que abra noutra aba --}}
                        <a href="{{ route('freguesia.inqueritos.show', $inquerito->id) }}" class="btn btn-link text-info px-3 mb-0" data-bs-toggle="tooltip" title="Ver Detalhes">
                            <i class="fas fa-eye text-info me-2"></i> Ver
                        </a>

                        {{-- 2. Botão ATUALIZAR/EDITAR (Com Lógica de Prazo) --}}
                        @if($inquerito->ano == $anoAtual && $dentroDoPrazo)
                            {{-- Se estiver dentro do prazo --}}
                            {{-- Nota: Tens de criar a rota 'edit' depois --}}
                            <a href="{{ route('freguesia.inqueritos.edit', $inquerito->id) }}" class="btn btn-link text-dark px-3 mb-0" data-bs-toggle="tooltip" title="Editar Relatório">
                                <i class="fas fa-pencil-alt text-dark me-2"></i> Editar
                            </a>
                        @else
                            {{-- Se estiver fora do prazo --}}
                            <button class="btn btn-link text-secondary px-3 mb-0" style="cursor: not-allowed; opacity: 0.6;" disabled data-bs-toggle="tooltip" title="O prazo de edição terminou em {{ $dataLimite->format('d/m/Y') }}">
                                <i class="fas fa-lock me-2"></i> Bloqueado
                            </button>
                        @endif

                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="5" class="text-center text-sm py-4">Ainda não submeteu nenhum inquérito anual.</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
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