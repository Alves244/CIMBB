@extends('layouts.user_type.auth')

@section('content')

  <div class="container-fluid py-4">
    <div class="row">
      <div class="col-12">
        <div class="card mb-4">
          <div class="card-header pb-0 d-flex justify-content-between align-items-center">
            <div>
              <h6 class="mb-0">Histórico de Inquéritos</h6>
              <p class="text-sm">Freguesia: {{ Auth::user()->freguesia->nome ?? 'N/A' }}</p>
            </div>

            {{-- O botão só aparece se o inquérito deste ano AINDA NÃO foi preenchido --}}
            @if(!$jaPreencheuEsteAno)
                <a href="{{ route('freguesia.inqueritos.create') }}" class="btn bg-gradient-success btn-sm mb-0">
                    <i class="fas fa-plus me-1"></i> Preencher Inquérito ({{ $anoAtual }})
                </a>
            @else
                <button class="btn btn-secondary btn-sm mb-0" disabled>
                    Inquérito de {{ $anoAtual }} já preenchido
                </button>
            @endif
          </div>
          <div class="card-body px-0 pt-0 pb-2">
            <div class="table-responsive p-0">
              <table class="table align-items-center mb-0">
                <thead>
                  <tr>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Ano</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Data de Preenchimento</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Satisfação Global (1-5)</th>
                    <th class="text-secondary opacity-7">Ações</th>
                  </tr>
                </thead>
                <tbody>
                  {{-- Este 'forelse' está correto e não tem 'dd()' --}}
                  @forelse ($inqueritos as $inquerito)
                    <tr>
                      <td>
                        <div class="d-flex px-3 py-1">
                          <h6 class="mb-0 text-sm">{{ $inquerito->ano }}</h6>
                        </div>
                      </td>
                      <td>
                        <p class="text-xs font-weight-bold mb-0">{{ $inquerito->created_at->format('d/m/Y H:i') }}</p>
                      </td>
                      <td class="align-middle text-center text-sm">
                        <span class="badge badge-sm bg-gradient-info">{{ $inquerito->satisfacao_global }}</span>
                      </td>
                      <td class="align-middle">
                        
                        {{-- ***** BOTÃO "VER" CORRIGIDO ***** --}}
                        {{-- O href aponta para a nova rota 'show' --}}
                        <a href="{{ route('freguesia.inqueritos.show', $inquerito->id) }}" class="btn btn-link text-info text-gradient px-1 mb-0" data-bs-toggle="tooltip" data-bs-original-title="Ver Inquérito">
                            <i class="fas fa-eye text-sm"></i>
                        </a>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="4" class="text-center text-sm py-4">Nenhum inquérito preenchido anteriormente.</td>
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
  {{-- Script para ativar os tooltips (ex: 'Ver Inquérito') --}}
  <script>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
    })
  </script>
@endpush