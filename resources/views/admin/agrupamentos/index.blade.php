@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
  <div class="row">
    <div class="col-12">
      <div class="card mb-4">
        <div class="card-header pb-0">
          <div class="d-flex flex-column flex-lg-row gap-3 align-items-lg-center justify-content-lg-between">
            <div>
              <h6>Gestão de Agrupamentos</h6>
              <p class="text-sm mb-0">Atualize a lista de agrupamentos/escolas disponíveis para associação de utilizadores e inquéritos.</p>
            </div>
            <div class="d-flex flex-column flex-lg-row gap-2 align-items-lg-center justify-content-end w-100 w-lg-auto">
              <x-admin.filter-modal modalId="agrupamentosFilterModal"
                                    :action="route('admin.agrupamentos.index')"
                                    :clear-url="route('admin.agrupamentos.index')"
                                    title="Filtrar agrupamentos">
                <div class="col-12">
                  <label class="form-label text-xs text-uppercase text-secondary mb-1">Concelho</label>
                  <select name="concelho_id" class="form-select">
                    <option value="">Todos os concelhos</option>
                    @foreach($concelhos as $concelho)
                      <option value="{{ $concelho->id }}" {{ (string)$concelhoSelecionado === (string)$concelho->id ? 'selected' : '' }}>
                        {{ $concelho->nome }}
                      </option>
                    @endforeach
                  </select>
                </div>
              </x-admin.filter-modal>
              <button class="btn bg-gradient-success btn-sm mb-0" type="button" data-bs-toggle="modal" data-bs-target="#createAgrupamentoModal">
                <i class="fas fa-plus me-1"></i> Novo Agrupamento
              </button>
            </div>
          </div>
        </div>
        <div class="card-body px-0 pt-0 pb-2">
          <div class="table-responsive p-0">
            <table class="table align-items-center mb-0">
              <thead>
                <tr>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nome</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Código</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Concelho</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Utilizadores</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Inquéritos</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Ações</th>
                </tr>
              </thead>
              <tbody>
                @forelse($agrupamentos as $agrupamento)
                  <tr>
                    <td>
                      <div class="px-3 py-1">
                        <h6 class="mb-0 text-sm">{{ $agrupamento->nome }}</h6>
                      </div>
                    </td>
                    <td>
                      <p class="text-xs text-secondary mb-0">{{ $agrupamento->codigo ?? '—' }}</p>
                    </td>
                    <td>
                      <p class="text-xs text-secondary mb-0">{{ optional($agrupamento->concelho)->nome ?? '—' }}</p>
                    </td>
                    <td class="text-center">
                      <span class="badge badge-sm bg-gradient-info">{{ $agrupamento->users_count }}</span>
                    </td>
                    <td class="text-center">
                      <span class="badge badge-sm bg-gradient-secondary">{{ $agrupamento->inqueritos_count }}</span>
                    </td>
                    <td class="text-center">
                      <a href="#" class="btn btn-link text-dark px-3 mb-0" data-bs-toggle="modal" data-bs-target="#editAgrupamentoModal-{{ $agrupamento->id }}">
                        <i class="fas fa-pen me-2"></i>Editar
                      </a>
                      <form action="{{ route('admin.agrupamentos.destroy', $agrupamento) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-link text-danger text-gradient px-3 mb-0"
                          onclick="return confirm('Tem a certeza que deseja remover o agrupamento {{ $agrupamento->nome }}?')">
                          <i class="far fa-trash-alt me-2"></i>Apagar
                        </button>
                      </form>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="6" class="text-center text-sm py-4">Ainda não existem agrupamentos registados.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
        <x-admin.pagination :paginator="$agrupamentos" />
      </div>
    </div>
  </div>
</div>

{{-- Modal Criar Agrupamento --}}
<div class="modal fade" id="createAgrupamentoModal" tabindex="-1" role="dialog" aria-labelledby="createAgrupamentoLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="createAgrupamentoLabel">Novo Agrupamento</h5>
        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{ route('admin.agrupamentos.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          @php $agrupamentoCreateHasOld = $errors->hasBag('createAgrupamento'); @endphp
          <div class="form-group mb-3">
            <label class="form-control-label">Nome *</label>
            <input type="text" class="form-control" name="nome" value="{{ $agrupamentoCreateHasOld ? old('nome') : '' }}" required>
            @error('nome', 'createAgrupamento')
              <small class="text-danger">{{ $message }}</small>
            @enderror
          </div>
          <div class="form-group mb-3">
            <label class="form-control-label">Código</label>
            <input type="text" class="form-control" name="codigo" value="{{ $agrupamentoCreateHasOld ? old('codigo') : '' }}" maxlength="20">
            @error('codigo', 'createAgrupamento')
              <small class="text-danger">{{ $message }}</small>
            @enderror
          </div>
          <div class="form-group">
            <label class="form-control-label">Concelho *</label>
            <select class="form-select" name="concelho_id" required>
              <option value="">Selecione...</option>
              @foreach($concelhos as $concelho)
                <option value="{{ $concelho->id }}" {{ $agrupamentoCreateHasOld && (string)old('concelho_id') === (string)$concelho->id ? 'selected' : '' }}>{{ $concelho->nome }}</option>
              @endforeach
            </select>
            @error('concelho_id', 'createAgrupamento')
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
@foreach($agrupamentos as $agrupamento)
  @php $editingAgrupamentoId = old('editing_agrupamento_id'); $isEditing = $editingAgrupamentoId == $agrupamento->id; @endphp
  <div class="modal fade" id="editAgrupamentoModal-{{ $agrupamento->id }}" tabindex="-1" role="dialog" aria-labelledby="editAgrupamentoLabel-{{ $agrupamento->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editAgrupamentoLabel-{{ $agrupamento->id }}">Editar Agrupamento</h5>
          <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="{{ route('admin.agrupamentos.update', $agrupamento) }}" method="POST">
          @csrf
          @method('PUT')
          <input type="hidden" name="editing_agrupamento_id" value="{{ $agrupamento->id }}">
          <div class="modal-body">
            <div class="form-group mb-3">
              <label class="form-control-label">Nome *</label>
              <input type="text" class="form-control" name="nome" value="{{ $isEditing ? old('nome') : $agrupamento->nome }}" required>
              @if($isEditing)
                @error('nome', 'editAgrupamento')
                  <small class="text-danger">{{ $message }}</small>
                @enderror
              @endif
            </div>
            <div class="form-group mb-3">
              <label class="form-control-label">Código</label>
              <input type="text" class="form-control" name="codigo" value="{{ $isEditing ? old('codigo') : $agrupamento->codigo }}" maxlength="20">
              @if($isEditing)
                @error('codigo', 'editAgrupamento')
                  <small class="text-danger">{{ $message }}</small>
                @enderror
              @endif
            </div>
            <div class="form-group">
              <label class="form-control-label">Concelho *</label>
              <select class="form-select" name="concelho_id" required>
                @foreach($concelhos as $concelho)
                  <option value="{{ $concelho->id }}"
                    {{ $isEditing ? ((string)old('concelho_id') === (string)$concelho->id ? 'selected' : '') : ($agrupamento->concelho_id == $concelho->id ? 'selected' : '') }}>
                    {{ $concelho->nome }}
                  </option>
                @endforeach
              </select>
              @if($isEditing)
                @error('concelho_id', 'editAgrupamento')
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
    @if ($errors->hasBag('createAgrupamento'))
      var createModal = new bootstrap.Modal(document.getElementById('createAgrupamentoModal'));
      createModal.show();
    @endif

    @if ($errors->hasBag('editAgrupamento'))
      var editingAgrupamentoId = "{{ old('editing_agrupamento_id') }}";
      if (editingAgrupamentoId) {
        var modal = document.getElementById('editAgrupamentoModal-' + editingAgrupamentoId);
        if (modal) {
          var editModal = new bootstrap.Modal(modal);
          editModal.show();
        }
      }
    @endif
  });
</script>
@endpush
