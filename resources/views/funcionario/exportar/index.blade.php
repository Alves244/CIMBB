@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
  <div class="row">
    <div class="col-lg-6">
      <div class="card">
        <div class="card-body">
          <h5 class="mb-2">Exportar CSV</h5>
          <p class="text-sm text-secondary">Escolha o conjunto de dados que pretende exportar em CSV. O ficheiro é gerado de imediato.</p>
          <form method="POST" action="{{ route('funcionario.exportar.csv') }}">
            @csrf
            <div class="mb-3">
              <label class="form-label">Conjunto de dados</label>
              <select name="dataset" class="form-select">
                <option value="familias">Famílias e agregados</option>
                <option value="inqueritos">Inquéritos das freguesias</option>
                <option value="tickets">Tickets de suporte</option>
              </select>
            </div>
            <button type="submit" class="btn bg-gradient-success">Download CSV</button>
          </form>
        </div>
      </div>
    </div>
    <div class="col-lg-6 mt-4 mt-lg-0">
      <div class="card">
        <div class="card-body">
          <h5 class="mb-2">PDF - Inquéritos</h5>
          <p class="text-sm text-secondary">Gera um relatório em PDF com todos os inquéritos submetidos no ano selecionado.</p>
          <form method="POST" action="{{ route('funcionario.exportar.inqueritos.pdf') }}">
            @csrf
            <div class="mb-3">
              <label class="form-label">Ano</label>
              <select name="ano" class="form-select">
                @foreach($anosDisponiveis as $ano)
                  <option value="{{ $ano }}">{{ $ano }}</option>
                @endforeach
              </select>
            </div>
            <button type="submit" class="btn bg-gradient-primary">Download PDF</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="row mt-4">
    <div class="col-lg-6">
      <div class="card">
        <div class="card-body">
          <h5 class="mb-2">PDF - Estatísticas Regionais</h5>
          <p class="text-sm text-secondary">Exporta os indicadores agregados por concelho (famílias, membros, tickets, progresso dos inquéritos).</p>
          <form method="POST" action="{{ route('funcionario.exportar.estatisticas.pdf') }}">
            @csrf
            <div class="mb-3">
              <label class="form-label">Ano</label>
              <select name="ano" class="form-select">
                @foreach($anosDisponiveis as $ano)
                  <option value="{{ $ano }}">{{ $ano }}</option>
                @endforeach
              </select>
            </div>
            <button type="submit" class="btn bg-gradient-info">Download PDF</button>
          </form>
        </div>
      </div>
    </div>
    <div class="col-lg-6 mt-4 mt-lg-0">
      <div class="card h-100">
        <div class="card-body">
          <h6 class="text-uppercase text-secondary text-xxs font-weight-bolder mb-3">Notas rápidas</h6>
          <ul class="text-sm ps-3 mb-0">
            <li>Cada exportação reflete os dados mais recentes na base.</li>
            <li>Os ficheiros CSV podem ser abertos em Excel ou carregados para BI.</li>
            <li>Os PDFs são gerados automaticamente com base nos filtros acima.</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
