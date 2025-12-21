@extends('layouts.user_type.auth')

@section('content')
  <div class="container-fluid py-4">
    <div class="row">
      <div class="col-12 col-lg-5 mb-4">
        <div class="card h-100">
          <div class="card-header pb-0">
            <h6 class="mb-0">Abrir período anual</h6>
            <p class="text-sm text-secondary mb-0">Escolha o tipo de inquérito, o ano e o intervalo de submissão.</p>
          </div>
          <div class="card-body">
            <form method="POST" action="{{ route('admin.inqueritos.periodos.store') }}">
              @csrf
              <div class="mb-3">
                <label class="form-label text-xs text-uppercase text-secondary">Tipo de inquérito *</label>
                <select name="tipo" class="form-select" required>
                  <option value="">Selecione</option>
                  @foreach($tipos as $valor => $label)
                    <option value="{{ $valor }}" {{ old('tipo') === $valor ? 'selected' : '' }}>{{ $label }}</option>
                  @endforeach
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label text-xs text-uppercase text-secondary">Ano *</label>
                <input type="number" name="ano" min="2000" max="2100" value="{{ old('ano', date('Y')) }}" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label text-xs text-uppercase text-secondary">Início das submissões *</label>
                <input type="datetime-local" name="abre_em" value="{{ old('abre_em') }}" class="form-control" required>
              </div>
              <div class="mb-4">
                <label class="form-label text-xs text-uppercase text-secondary">Fim das submissões *</label>
                <input type="datetime-local" name="fecha_em" value="{{ old('fecha_em') }}" class="form-control" required>
              </div>
              <button type="submit" class="btn bg-gradient-success w-100">Criar período</button>
            </form>
          </div>
        </div>
      </div>
      <div class="col-12 col-lg-7">
        <div class="card h-100">
          <div class="card-header pb-0 d-flex justify-content-between align-items-center">
            <div>
              <h6 class="mb-0">Períodos configurados</h6>
              <p class="text-sm text-secondary mb-0">Atualize as datas ou remova períodos que já não são necessários.</p>
            </div>
          </div>
          <div class="card-body px-0 pt-0">
            <div class="table-responsive">
              <table class="table align-items-center mb-0">
                <thead>
                  <tr>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tipo</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Ano</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Início</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Fim</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Estado</th>
                    <th class="text-secondary opacity-7">Ações</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($periodos as $periodo)
                    <tr>
                      <td><p class="text-sm mb-0">{{ $tipos[$periodo->tipo] ?? $periodo->tipo }}</p></td>
                      <td><p class="text-sm mb-0">{{ $periodo->ano }}</p></td>
                      <td><p class="text-sm mb-0">{{ $periodo->abre_em->format('d/m/Y H:i') }}</p></td>
                      <td><p class="text-sm mb-0">{{ $periodo->fecha_em->format('d/m/Y H:i') }}</p></td>
                      <td>
                        @php $status = $periodo->estaAberto() ? 'Ativo' : (now()->lt($periodo->abre_em) ? 'Agendado' : 'Encerrado'); @endphp
                        <span class="badge {{ $periodo->estaAberto() ? 'bg-gradient-success' : (now()->lt($periodo->abre_em) ? 'bg-gradient-info' : 'bg-gradient-secondary') }}">{{ $status }}</span>
                      </td>
                      <td>
                        <div class="d-flex gap-2">
                          <form method="POST" action="{{ route('admin.inqueritos.periodos.update', $periodo) }}" class="d-flex flex-column flex-sm-row gap-2">
                            @csrf
                            @method('PUT')
                            <input type="datetime-local" name="abre_em" value="{{ $periodo->abre_em->format('Y-m-d\TH:i') }}" class="form-control form-control-sm" required>
                            <input type="datetime-local" name="fecha_em" value="{{ $periodo->fecha_em->format('Y-m-d\TH:i') }}" class="form-control form-control-sm" required>
                            <button class="btn btn-sm bg-gradient-primary">Atualizar</button>
                          </form>
                          <form method="POST" action="{{ route('admin.inqueritos.periodos.destroy', $periodo) }}" onsubmit="return confirm('Remover este período?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Remover</button>
                          </form>
                        </div>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="6" class="text-center text-sm py-4">Nenhum período configurado.</td>
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
