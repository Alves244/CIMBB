@extends('layouts.user_type.auth')

@section('content')
  <div class="container-fluid py-4">
    <div class="row">
      <div class="col-12 col-lg-10 mx-auto">
        <form action="{{ route('agrupamento.inqueritos.store') }}" method="POST" id="inquerito-agrupamento-form">
          @csrf
          <input type="hidden" name="ano_referencia" value="{{ $anoAtual }}">
          <div class="card">
            <div class="card-header pb-0 d-flex flex-column flex-md-row justify-content-between align-items-md-center">
              <div>
                <h6 class="mb-0">Inquérito {{ $anoAtual }}</h6>
                <p class="text-sm mb-0">Registe os alunos por nacionalidade e nível de ensino.</p>
              </div>
              <span class="badge bg-gradient-success">Agrupamento: {{ Auth::user()->agrupamento->nome ?? 'N/A' }}</span>
            </div>
            <div class="card-body">
              <p class="text-sm text-secondary">Todos os campos de cada registo são obrigatórios. Adicione um registo por combinação de nacionalidade / ano letivo / nível de ensino.</p>
              @isset($periodoAtivo)
                <p class="text-xs text-secondary mb-4">Período aberto até {{ optional($periodoAtivo->fecha_em)->format('d/m/Y H:i') }}.</p>
              @endisset

              <div id="registos-wrapper" class="d-flex flex-column gap-4">
                <div class="registo-card border rounded p-3" data-index="0">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Registo <span class="registo-pos">1</span></h6>
                    <button type="button" class="btn btn-link text-danger text-sm d-none remove-registo">Remover</button>
                  </div>
                  <div class="row g-3">
                    <div class="col-md-4">
                      <label class="form-label text-xs text-uppercase text-secondary">Nacionalidade *</label>
                      <select name="registos[0][nacionalidade]" class="form-select nacionalidade-select" required>
                        <option value="" selected>Selecione</option>
                        @foreach($nacionalidades as $nacionalidade)
                          <option value="{{ $nacionalidade }}">{{ $nacionalidade }}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="col-md-4">
                      <label class="form-label text-xs text-uppercase text-secondary">Ano letivo *</label>
                      <input type="text" name="registos[0][ano_letivo]" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                      <label class="form-label text-xs text-uppercase text-secondary">Nível de ensino *</label>
                      <select name="registos[0][nivel_ensino]" class="form-select" required>
                        @foreach($niveisEnsino as $nivel)
                          <option value="{{ $nivel }}">{{ $nivel }}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label text-xs text-uppercase text-secondary">Concelho</label>
                      <input type="text" class="form-control readonly-plain" value="{{ $concelhoNome }}" readonly>
                      <input type="hidden" name="registos[0][concelho_id]" value="{{ $concelhoId }}" required>
                    </div>
                    <div class="col-md-2">
                      <label class="form-label text-xs text-uppercase text-secondary">N.º Alunos *</label>
                      <input type="number" min="1" name="registos[0][numero_alunos]" class="form-control" required>
                    </div>
                  </div>
                </div>
              </div>

              <div class="text-end mt-4">
                <button type="button" class="btn btn-outline-secondary btn-sm" id="add-registo">
                  <i class="fas fa-plus me-1"></i> Adicionar registo
                </button>
              </div>

              <div class="text-end mt-4">
                <a href="{{ route('agrupamento.inqueritos.index') }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn bg-gradient-success">Submeter inquérito</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection

@push('css')
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
  <style>
    .readonly-plain[readonly] {
      background-color: #fff;
      color: #344767;
      opacity: 1;
    }
  </style>
@endpush

@push('js')
  <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
  <script>
    const concelhoInfo = @json(['id' => $concelhoId, 'nome' => $concelhoNome]);
    const niveisEnsino = @json($niveisEnsino);
    const nacionalidades = @json($nacionalidades);
    const nacionalidadesOptionsHtml = nacionalidades.map((item) => `<option value="${item}">${item}</option>`).join('');

    const nacionalidadeChoices = new Map();

    function enhanceNationalidadeSelect(select) {
      if (!select || select.dataset.enhanced === 'true') {
        return;
      }

      const instance = new Choices(select, {
        searchPlaceholderValue: 'Pesquisar nacionalidade...',
        shouldSort: true,
        itemSelectText: '',
        removeItemButton: false,
      });

      nacionalidadeChoices.set(select, instance);
      select.dataset.enhanced = 'true';
    }

    function destroyNationalidadeSelect(select) {
      const instance = nacionalidadeChoices.get(select);
      if (instance) {
        instance.destroy();
        nacionalidadeChoices.delete(select);
        select.dataset.enhanced = 'false';
      }
    }

    const wrapper = document.getElementById('registos-wrapper');
    const addButton = document.getElementById('add-registo');

    function updatePositions() {
      const cards = wrapper.querySelectorAll('.registo-card');
      cards.forEach((card, index) => {
        card.dataset.index = index;
        card.querySelector('.registo-pos').textContent = index + 1;
        card.querySelectorAll('input, select').forEach((input) => {
          const name = input.getAttribute('name');
          if (!name) {
            return;
          }
          input.setAttribute('name', name.replace(/registos\[[0-9]+\]/, `registos[${index}]`));
        });
        const removeBtn = card.querySelector('.remove-registo');
        removeBtn.classList.toggle('d-none', cards.length === 1);
      });
    }

    function createCard() {
      const index = wrapper.querySelectorAll('.registo-card').length;
      const card = document.createElement('div');
      card.className = 'registo-card border rounded p-3';
      card.dataset.index = index;
      card.innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h6 class="mb-0">Registo <span class="registo-pos">${index + 1}</span></h6>
          <button type="button" class="btn btn-link text-danger text-sm remove-registo">Remover</button>
        </div>
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label text-xs text-uppercase text-secondary">Nacionalidade *</label>
            <select name="registos[${index}][nacionalidade]" class="form-select nacionalidade-select" required>
              <option value="">Selecione</option>
              ${nacionalidadesOptionsHtml}
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label text-xs text-uppercase text-secondary">Ano letivo *</label>
            <input type="text" name="registos[${index}][ano_letivo]" class="form-control" required>
          </div>
          <div class="col-md-4">
            <label class="form-label text-xs text-uppercase text-secondary">Nível de ensino *</label>
            <select name="registos[${index}][nivel_ensino]" class="form-select" required>
              ${niveisEnsino.map((nivel) => `<option value="${nivel}">${nivel}</option>`).join('')}
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label text-xs text-uppercase text-secondary">Concelho</label>
            <input type="text" class="form-control readonly-plain" value="${concelhoInfo.nome}" readonly>
            <input type="hidden" name="registos[${index}][concelho_id]" value="${concelhoInfo.id}" required>
          </div>
          <div class="col-md-2">
            <label class="form-label text-xs text-uppercase text-secondary">N.º Alunos *</label>
            <input type="number" min="1" name="registos[${index}][numero_alunos]" class="form-control" required>
          </div>
        </div>
      `;
      wrapper.appendChild(card);
      enhanceNationalidadeSelect(card.querySelector('.nacionalidade-select'));
      updatePositions();
    }

    wrapper.addEventListener('click', (event) => {
      if (!event.target.classList.contains('remove-registo')) {
        return;
      }
      const card = event.target.closest('.registo-card');
      card.querySelectorAll('.nacionalidade-select').forEach((select) => destroyNationalidadeSelect(select));
      card.remove();
      updatePositions();
    });

    addButton.addEventListener('click', () => {
      createCard();
    });

    updatePositions();
    document.querySelectorAll('.nacionalidade-select').forEach((select) => enhanceNationalidadeSelect(select));
  </script>
@endpush
