@extends('layouts.user_type.auth')

@section('content')
  <div class="container-fluid py-4">
    <div class="row">
      <div class="col-12">
        <div class="card mb-4">
          <div class="card-header pb-0 d-flex justify-content-between align-items-center">
            <div>
              <h6 class="mb-0">Inquéritos Anuais do Agrupamento</h6>
              <p class="text-sm mb-0">Agrupamento: {{ Auth::user()->agrupamento->nome ?? 'N/A' }}</p>
            </div>
            @if(!$jaPreencheuEsteAno && $dentroDoPrazo)
              <a href="{{ route('agrupamento.inqueritos.create') }}" class="btn bg-gradient-success btn-sm">
                <i class="fas fa-plus me-1"></i> Novo Inquérito {{ $anoAtual }}
              </a>
            @elseif($jaPreencheuEsteAno)
              <span class="badge bg-gradient-success">Submetido {{ $anoAtual }}</span>
            @else
              <span class="badge bg-gradient-secondary">Prazo expirado</span>
            @endif
          </div>
          <div class="card-body px-0 pt-0 pb-2">
            <div class="table-responsive p-0">
              <table class="table align-items-center mb-0">
                <thead>
                  <tr>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Ano</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Total Registos</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Total Alunos</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Submetido em</th>
                    <th class="text-secondary opacity-7">Ações</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($inqueritos as $inquerito)
                    <tr>
                      <td>
                        <div class="d-flex px-3 py-1">
                          <h6 class="mb-0 text-sm">{{ $inquerito->ano_referencia }}</h6>
                        </div>
                      </td>
                      <td>
                        <p class="text-xs font-weight-bold mb-0">{{ $inquerito->total_registos }}</p>
                      </td>
                      <td>
                        <p class="text-xs font-weight-bold mb-0">{{ $inquerito->total_alunos }}</p>
                      </td>
                      <td class="text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ optional($inquerito->submetido_em)->format('d/m/Y H:i') ?? '—' }}</span>
                      </td>
                      <td class="align-middle">
                        <a href="{{ route('agrupamento.inqueritos.show', $inquerito->id) }}" class="btn btn-link text-info px-3 mb-0">
                          <i class="fas fa-eye me-2"></i> Ver detalhes
                        </a>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="5" class="text-center text-sm py-4">Ainda não existem inquéritos submetidos.</td>
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
