@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
  <div class="row">
    <div class="col-12">
      <div class="card mb-4">
        <div class="card-header pb-0 d-flex flex-column flex-lg-row gap-3 align-items-lg-center justify-content-lg-between">
          <div>
            <h6>Gestão de Freguesias</h6>
            <p class="text-sm mb-0">Associe cada freguesia ao respetivo concelho e mantenha os dados atualizados.</p>
          </div>
          <div class="d-flex flex-column flex-lg-row gap-2 align-items-lg-center">
            <x-admin.filter-modal modalId="freguesiasFilterModal"
                                  :action="route('admin.freguesias.index')"
                                  :clear-url="route('admin.freguesias.index')"
                                  title="Filtrar freguesias">
              <div class="col-12">
                <label class="form-label text-xs text-uppercase text-secondary mb-1">Concelho</label>
                <select name="concelho_id" class="form-select">
                  <option value="">Todos os concelhos</option>
                  @foreach($concelhos as $concelho)
                      <option value="{{ $concelho->id }}" {{ (string) $concelhoSelecionado === (string) $concelho->id ? 'selected' : '' }}>
                        {{ $concelho->nome }}
                    </option>
                  @endforeach
                </select>
              </div>
            </x-admin.filter-modal>
            <button class="btn bg-gradient-success btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#createFreguesiaModal">
              <i class="fas fa-plus me-1"></i> Nova Freguesia
            </button>
          </div>
        </div>
        <div class="card-body px-0 pt-0 pb-2">
          <div class="table-responsive p-0">
            <table class="table align-items-center mb-0">
              <thead>
                <tr>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Freguesia</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Concelho</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Código</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Utilizadores</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Famílias</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Ações</th>
                </tr>
              </thead>
              <tbody>
                @forelse($freguesias as $freguesia)
                  <tr>
                    <td>
                      <div class="d-flex px-3 py-1">
                        <div class="d-flex flex-column justify-content-center">
                          <h6 class="mb-0 text-sm">{{ $freguesia->nome }}</h6>
                        </div>
                      </div>
                    </td>
                    <td>
                      <p class="text-xs text-secondary mb-0">{{ $freguesia->concelho->nome ?? '—' }}</p>
                    </td>
                    <td>
                      <p class="text-xs text-secondary mb-0">{{ $freguesia->codigo ?? '—' }}</p>
                    </td>
                    <td class="text-center">
                      <span class="badge badge-sm bg-gradient-info">{{ $freguesia->users_count }}</span>
                    </td>
                    <td class="text-center">
                      <span class="badge badge-sm bg-gradient-secondary">{{ $freguesia->familias_count }}</span>
                    </td>
                    <td class="text-center">
                      <a href="#" class="btn btn-link text-dark px-3 mb-0" data-bs-toggle="modal" data-bs-target="#editFreguesiaModal-{{ $freguesia->id }}">
                        <i class="fas fa-pen me-2"></i>Editar
                      </a>
                      <form action="{{ route('admin.freguesias.destroy', $freguesia) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-link text-danger text-gradient px-3 mb-0"
                                onclick="return confirm('Tem a certeza que deseja remover a freguesia {{ $freguesia->nome }}?')"
                                data-bs-toggle="tooltip" data-bs-original-title="Apagar Freguesia">
                          <i class="far fa-trash-alt me-2"></i>Apagar
                        </button>
                      </form>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="6" class="text-center text-sm py-4">Ainda não existem freguesias registadas.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
        <x-admin.pagination :paginator="$freguesias" />
      </div>
    </div>
  </div>
</div>

{{-- Modal Criar Freguesia --}}
<div class="modal fade" id="createFreguesiaModal" tabindex="-1" role="dialog" aria-labelledby="createFreguesiaLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="createFreguesiaLabel">Nova Freguesia</h5>
        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{ route('admin.freguesias.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          @php $freguesiaCreateHasOld = $errors->hasBag('createFreguesia'); @endphp
          <div class="form-group mb-3">
            <label class="form-control-label">Nome *</label>
            <input type="text" class="form-control" name="nome" value="{{ $freguesiaCreateHasOld ? old('nome') : '' }}" required>
            @error('nome', 'createFreguesia')
              <small class="text-danger">{{ $message }}</small>
            @enderror
          </div>
          <div class="form-group mb-3">
            <label class="form-control-label">Concelho *</label>
            <select name="concelho_id" class="form-control" required>
              <option value="">-- Selecione --</option>
              @foreach($concelhos as $concelho)
                <option value="{{ $concelho->id }}" {{ $freguesiaCreateHasOld && old('concelho_id') == $concelho->id ? 'selected' : '' }}>
                  {{ $concelho->nome }}
                </option>
              @endforeach
            </select>
            @error('concelho_id', 'createFreguesia')
              <small class="text-danger">{{ $message }}</small>
            @enderror
          </div>
          <div class="form-group">
            <label class="form-control-label">Código</label>
            <input type="text" class="form-control" name="codigo" value="{{ $freguesiaCreateHasOld ? old('codigo') : '' }}" maxlength="10">
            @error('codigo', 'createFreguesia')
              <small class="text-danger">{{ $message }}</small>
            @enderror
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn bg-gradient-success">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Modais de Edição --}}
@foreach($freguesias as $freguesia)
  @php $editingFreguesiaId = old('editing_freguesia_id'); $isEditingFreguesia = $editingFreguesiaId == $freguesia->id; @endphp
  <div class="modal fade" id="editFreguesiaModal-{{ $freguesia->id }}" tabindex="-1" role="dialog" aria-labelledby="editFreguesiaLabel-{{ $freguesia->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editFreguesiaLabel-{{ $freguesia->id }}">Editar Freguesia</h5>
          <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="{{ route('admin.freguesias.update', $freguesia) }}" method="POST">
          @csrf
          @method('PUT')
          <input type="hidden" name="editing_freguesia_id" value="{{ $freguesia->id }}">
          <div class="modal-body">
            <div class="form-group mb-3">
              <label class="form-control-label">Nome *</label>
              <input type="text" class="form-control" name="nome" value="{{ $isEditingFreguesia ? old('nome') : $freguesia->nome }}" required>
              @if($isEditingFreguesia)
                @error('nome', 'editFreguesia')
                  <small class="text-danger">{{ $message }}</small>
                @enderror
              @endif
            </div>
            <div class="form-group mb-3">
              <label class="form-control-label">Concelho *</label>
              <select name="concelho_id" class="form-control" required>
                <option value="">-- Selecione --</option>
                @foreach($concelhos as $concelho)
                  <option value="{{ $concelho->id }}"
                    {{ ($isEditingFreguesia ? old('concelho_id') : $freguesia->concelho_id) == $concelho->id ? 'selected' : '' }}>
                    {{ $concelho->nome }}
                  </option>
                @endforeach
              </select>
              @if($isEditingFreguesia)
                @error('concelho_id', 'editFreguesia')
                  <small class="text-danger">{{ $message }}</small>
                @enderror
              @endif
            </div>
            <div class="form-group">
              <label class="form-control-label">Código</label>
              <input type="text" class="form-control" name="codigo" value="{{ $isEditingFreguesia ? old('codigo') : $freguesia->codigo }}" maxlength="10">
              @if($isEditingFreguesia)
                @error('codigo', 'editFreguesia')
                  <small class="text-danger">{{ $message }}</small>
                @enderror
              @endif
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn bg-gradient-success">Atualizar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endforeach
@endsection

@push('js')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    @if ($errors->hasBag('createFreguesia'))
      var createModal = new bootstrap.Modal(document.getElementById('createFreguesiaModal'));
      createModal.show();
    @endif

    var editingFreguesiaId = "{{ old('editing_freguesia_id') }}";
    @if ($errors->hasBag('editFreguesia'))
      if (editingFreguesiaId) {
        var modal = document.getElementById('editFreguesiaModal-' + editingFreguesiaId);
        if (modal) {
          var editModal = new bootstrap.Modal(modal);
          editModal.show();
        }
      }
    @endif
  });
</script>
@endpush
