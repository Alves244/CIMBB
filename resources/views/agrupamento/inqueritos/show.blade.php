@extends('layouts.user_type.auth')

@section('content')
  <div class="container-fluid py-4">
    <div class="row">
      <div class="col-12 col-lg-10 mx-auto">
        <div class="card mb-4">
          <div class="card-header pb-0 d-flex justify-content-between align-items-center">
            <div>
              <h6 class="mb-0">Inquérito {{ $inquerito->ano_referencia }}</h6>
              <p class="text-sm mb-0">Total de registos: {{ $inquerito->total_registos }} | Total de alunos: {{ $inquerito->total_alunos }}</p>
            </div>
            <a href="{{ route('agrupamento.inqueritos.index') }}" class="btn btn-secondary btn-sm">
              <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table align-items-center mb-0">
                <thead>
                  <tr>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nacionalidade</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Ano letivo</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Estabelecimento</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Concelho</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Nível</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">N.º Alunos</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($inquerito->registos as $registo)
                    <tr>
                      <td><p class="text-sm font-weight-bold mb-0">{{ $registo->nacionalidade }}</p></td>
                      <td><p class="text-sm mb-0">{{ $registo->ano_letivo }}</p></td>
                      <td><p class="text-sm mb-0">{{ $registo->estabelecimento->nome ?? '—' }}</p></td>
                      <td><p class="text-sm mb-0">{{ $registo->concelho->nome ?? '—' }}</p></td>
                      <td><p class="text-sm mb-0">{{ $registo->nivel_ensino }}</p></td>
                      <td class="text-center"><span class="text-sm font-weight-bold">{{ $registo->numero_alunos }}</span></td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="6" class="text-center text-sm py-4">Sem registos vinculados.</td>
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
