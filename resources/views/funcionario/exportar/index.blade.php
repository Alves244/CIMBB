@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
  <div class="row">
    <div class="col-lg-6">
      <div class="card">
        <div class="card-body">
          <h5 class="mb-2">PDF - Estatísticas por Concelho</h5>
          <p class="text-sm text-secondary">Seleciona um concelho específico para obter o recorte completo de famílias, membros e pendências.</p>
          <form method="POST" action="{{ route('funcionario.exportar.concelho.pdf') }}">
            @csrf
            <div class="mb-3">
              <label class="form-label">Ano</label>
              <select name="ano" class="form-select">
                @foreach($anosDisponiveis as $ano)
                  <option value="{{ $ano }}">{{ $ano }}</option>
                @endforeach
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Concelho</label>
              <select name="concelho_id" class="form-select" required>
                <option value="" disabled selected>Selecione um concelho</option>
                @foreach($concelhos as $concelho)
                  <option value="{{ $concelho->id }}">{{ $concelho->nome }}</option>
                @endforeach
              </select>
            </div>
            <button type="submit" class="btn btn-success">Exportar PDF</button>
          </form>
        </div>
      </div>
    </div>
    <div class="col-lg-6 mt-4 mt-lg-0">
      <div class="card">
        <div class="card-body">
          <h5 class="mb-2">PDF - Estatísticas por Freguesia</h5>
          <p class="text-sm text-secondary">Escolha o concelho e a freguesia para gerar um relatório detalhado do território local.</p>
          <form method="POST" action="{{ route('funcionario.exportar.freguesia.pdf') }}">
            @csrf
            <div class="mb-3">
              <label class="form-label">Ano</label>
              <select name="ano" class="form-select">
                @foreach($anosDisponiveis as $ano)
                  <option value="{{ $ano }}">{{ $ano }}</option>
                @endforeach
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Concelho</label>
              <select name="concelho_id" id="concelho_filtro_export" class="form-select" required>
                <option value="" disabled selected>Selecione um concelho</option>
                @foreach($concelhos as $concelho)
                  <option value="{{ $concelho->id }}">{{ $concelho->nome }}</option>
                @endforeach
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Freguesia</label>
              <select name="freguesia_id" id="freguesia_filtro_export" class="form-select" required>
                <option value="" disabled selected>Selecione uma freguesia</option>
                @foreach($freguesias as $freguesia)
                  <option value="{{ $freguesia->id }}" data-concelho="{{ $freguesia->concelho_id }}">{{ $freguesia->nome }}</option>
                @endforeach
              </select>
            </div>
            <button type="submit" class="btn btn-success">Exportar PDF</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="row mt-4">
    <div class="col-lg-6">
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
            <button type="submit" class="btn btn-success">Exportar PDF</button>
          </form>
        </div>
      </div>
    </div>
    <div class="col-lg-6 mt-4 mt-lg-0">
      <div class="card">
        <div class="card-body">
          <h5 class="mb-2">PDF - Estatísticas Regionais</h5>
          <p class="text-sm text-secondary">Exporta os indicadores agregados por concelho (famílias, membros, progresso dos inquéritos).</p>
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
            <button type="submit" class="btn btn-success">Exportar PDF</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="row mt-4">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <h5 class="mb-2">PDF - Estatísticas das Escolas</h5>
          <p class="text-sm text-secondary">Gera um relatório dedicado às escolas, com totais por nacionalidade, níveis de ensino e estabelecimentos. Filtra opcionalmente por concelho ou agrupamento.</p>
          <form method="POST" action="{{ route('funcionario.exportar.escolas.pdf') }}">
            @csrf
            <div class="row g-3">
              <div class="col-md-3">
                <label class="form-label">Ano</label>
                <select name="ano" class="form-select">
                  @foreach($anosDisponiveis as $ano)
                    <option value="{{ $ano }}">{{ $ano }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-4">
                <label class="form-label">Concelho</label>
                <select name="concelho_id" id="escolas_concelho_select" class="form-select">
                  <option value="">Todos</option>
                  @foreach($concelhos as $concelho)
                    <option value="{{ $concelho->id }}">{{ $concelho->nome }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-5">
                <label class="form-label">Agrupamento</label>
                <select name="agrupamento_id" id="escolas_agrupamento_select" class="form-select">
                  <option value="">Todos</option>
                  @foreach($agrupamentos as $agrupamento)
                    <option value="{{ $agrupamento->id }}" data-concelho="{{ $agrupamento->concelho_id }}">{{ $agrupamento->nome }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <button type="submit" class="btn btn-success mt-3">Exportar PDF das Escolas</button>
          </form>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection

@push('js')
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const concelhoSelect = document.getElementById('concelho_filtro_export');
      const freguesiaSelect = document.getElementById('freguesia_filtro_export');

      if (concelhoSelect && freguesiaSelect) {
        const allOptions = Array.from(freguesiaSelect.querySelectorAll('option[data-concelho]'));

        function atualizarFreguesias() {
          const concelhoId = concelhoSelect.value;
          freguesiaSelect.innerHTML = '<option value="" disabled selected>Selecione uma freguesia</option>';

          allOptions.forEach(option => {
            if (!concelhoId || option.dataset.concelho === concelhoId) {
              freguesiaSelect.appendChild(option.cloneNode(true));
            }
          });
        }

        atualizarFreguesias();
        concelhoSelect.addEventListener('change', atualizarFreguesias);
      }

      const concelhoEscolas = document.getElementById('escolas_concelho_select');
      const agrupamentoSelect = document.getElementById('escolas_agrupamento_select');
      if (concelhoEscolas && agrupamentoSelect) {
        const allAgrupamentos = Array.from(agrupamentoSelect.querySelectorAll('option[data-concelho]'));

        function atualizarAgrupamentos() {
          const concelhoId = concelhoEscolas.value;
          agrupamentoSelect.innerHTML = '<option value="">Todos</option>';

          allAgrupamentos.forEach(option => {
            if (!concelhoId || option.dataset.concelho === concelhoId) {
              agrupamentoSelect.appendChild(option.cloneNode(true));
            }
          });
        }

        atualizarAgrupamentos();
        concelhoEscolas.addEventListener('change', atualizarAgrupamentos);
      }
    });
  </script>
@endpush
