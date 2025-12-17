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
                <p class="text-sm mb-0">Registe os alunos por nacionalidade e estabelecimento.</p>
              </div>
              <span class="badge bg-gradient-success">Agrupamento: {{ Auth::user()->agrupamento->nome ?? 'N/A' }}</span>
            </div>
            <div class="card-body">
              <p class="text-sm text-secondary">Todos os campos de cada registo são obrigatórios. Adicione um registo por combinação de nacionalidade / escola / nível de ensino.</p>

              <div id="registos-wrapper" class="d-flex flex-column gap-4">
                <div class="registo-card border rounded p-3" data-index="0">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Registo <span class="registo-pos">1</span></h6>
                    <button type="button" class="btn btn-link text-danger text-sm d-none remove-registo">Remover</button>
                  </div>
                  <div class="row g-3">
                    <div class="col-md-4">
                      <label class="form-label text-xs text-uppercase text-secondary">Nacionalidade *</label>
                      <input type="text" name="registos[0][nacionalidade]" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                      <label class="form-label text-xs text-uppercase text-secondary">Ano letivo *</label>
                      <input type="text" name="registos[0][ano_letivo]" class="form-control" placeholder="2024/2025" required>
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
                      <label class="form-label text-xs text-uppercase text-secondary">Estabelecimento *</label>
                      <select name="registos[0][estabelecimento_id]" class="form-select estabelecimento-select" required>
                        <option value="">Selecione</option>
                        @foreach($estabelecimentos as $estabelecimento)
                          <option value="{{ $estabelecimento->id }}" data-concelho="{{ $estabelecimento->concelho->nome ?? '' }}">{{ $estabelecimento->nome }}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="col-md-4">
                      <label class="form-label text-xs text-uppercase text-secondary">Concelho</label>
                      <input type="text" class="form-control concelho-display" value="" readonly>
                      <input type="hidden" name="registos[0][concelho_id]" class="concelho-id" required>
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

@push('js')
  <script>
    const estabelecimentos = @json($estabelecimentos->map(fn ($item) => [
        'id' => $item->id,
        'nome' => $item->nome,
        'concelho_id' => $item->concelho_id,
        'concelho_nome' => $item->concelho->nome ?? 'Sem concelho',
    ]));

    const niveisEnsino = @json($niveisEnsino);

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
            <input type="text" name="registos[${index}][nacionalidade]" class="form-control" required>
          </div>
          <div class="col-md-4">
            <label class="form-label text-xs text-uppercase text-secondary">Ano letivo *</label>
            <input type="text" name="registos[${index}][ano_letivo]" class="form-control" placeholder="2024/2025" required>
          </div>
          <div class="col-md-4">
            <label class="form-label text-xs text-uppercase text-secondary">Nível de ensino *</label>
            <select name="registos[${index}][nivel_ensino]" class="form-select" required>
              ${niveisEnsino.map((nivel) => `<option value="${nivel}">${nivel}</option>`).join('')}
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label text-xs text-uppercase text-secondary">Estabelecimento *</label>
            <select name="registos[${index}][estabelecimento_id]" class="form-select estabelecimento-select" required>
              <option value="">Selecione</option>
              ${estabelecimentos.map((school) => `<option value="${school.id}" data-concelho="${school.concelho_nome}">${school.nome}</option>`).join('')}
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label text-xs text-uppercase text-secondary">Concelho</label>
            <input type="text" class="form-control concelho-display" value="" readonly>
            <input type="hidden" name="registos[${index}][concelho_id]" class="concelho-id" required>
          </div>
          <div class="col-md-2">
            <label class="form-label text-xs text-uppercase text-secondary">N.º Alunos *</label>
            <input type="number" min="1" name="registos[${index}][numero_alunos]" class="form-control" required>
          </div>
        </div>
      `;
      wrapper.appendChild(card);
      updatePositions();
    }

    wrapper.addEventListener('change', (event) => {
      if (!event.target.classList.contains('estabelecimento-select')) {
        return;
      }

      const select = event.target;
      const card = select.closest('.registo-card');
      const concelhoInput = card.querySelector('.concelho-display');
      const concelhoIdInput = card.querySelector('.concelho-id');
      const school = estabelecimentos.find((item) => Number(item.id) === Number(select.value));

      if (school) {
        concelhoInput.value = school.concelho_nome;
        concelhoIdInput.value = school.concelho_id;
      } else {
        concelhoInput.value = '';
        concelhoIdInput.value = '';
      }
    });

    wrapper.addEventListener('click', (event) => {
      if (!event.target.classList.contains('remove-registo')) {
        return;
      }
      const card = event.target.closest('.registo-card');
      card.remove();
      updatePositions();
    });

    addButton.addEventListener('click', () => {
      createCard();
    });

    updatePositions();
  </script>
@endpush
