@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
  <div class="row">
    <div class="col-12">
      <div class="card mb-4">
        <div class="card-header pb-0 d-flex flex-column flex-md-row gap-3 align-items-md-center justify-content-md-between">
          <div>
            <h6>Gestão de Concelhos</h6>
            <p class="text-sm mb-0">Mantenha a lista de concelhos disponível para associação das freguesias.</p>
          </div>
          <button class="btn bg-gradient-success btn-sm mb-0" type="button" data-bs-toggle="modal" data-bs-target="#createConselhoModal">
            <i class="fas fa-plus me-1"></i> Novo Conselho
          </button>
        </div>
        <div class="card-body px-0 pt-0 pb-2">
          <div class="table-responsive p-0">
            <table class="table align-items-center mb-0">
              <thead>
                <tr>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nome</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Código</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">N.º Freguesias</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Criado em</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Ações</th>
                </tr>
              </thead>
              <tbody>
                @forelse($conselhos as $conselho)
                  <tr>
                    <td>
                      <div class="d-flex px-3 py-1">
                        <div class="d-flex flex-column justify-content-center">
                          <h6 class="mb-0 text-sm">{{ $conselho->nome }}</h6>
                        </div>
                      </div>
                    </td>
                    <td>
                      <p class="text-xs text-secondary mb-0">{{ $conselho->codigo ?? '—' }}</p>
                    </td>
                    <td class="text-center">
                      <span class="badge badge-sm bg-gradient-secondary">{{ $conselho->freguesias_count }}</span>
                    </td>
                    <td class="text-center">
                      <span class="text-secondary text-xs font-weight-bold">{{ optional($conselho->created_at)->format('d/m/Y') ?? '—' }}</span>
                    </td>
                    <td class="text-center">
                      <a href="#" class="btn btn-link text-dark px-3 mb-0" data-bs-toggle="modal" data-bs-target="#editConselhoModal-{{ $conselho->id }}">
                        <i class="fas fa-pen me-2"></i>Editar
                      </a>
                      <form action="{{ route('admin.concelhos.destroy', $conselho) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-link text-danger text-gradient px-3 mb-0"
                                onclick="return confirm('Tem a certeza que deseja remover o conselho {{ $conselho->nome }}?')"
                                data-bs-toggle="tooltip" data-bs-original-title="Apagar Conselho">
                          <i class="far fa-trash-alt me-2"></i>Apagar
                        </button>
                      </form>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5" class="text-center text-sm py-4">Ainda não existem concelhos registados.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
        <x-admin.pagination :paginator="$conselhos" />
      </div>
    </div>
  </div>
</div>

{{-- Modal Criar Conselho --}}
<div class="modal fade" id="createConselhoModal" tabindex="-1" role="dialog" aria-labelledby="createConselhoLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="createConselhoLabel">Novo Conselho</h5>
        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{ route('admin.concelhos.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          @php $conselhoCreateHasOld = $errors->hasBag('createConselho'); @endphp
          <div class="form-group mb-3">
            <label class="form-control-label">Nome *</label>
            <input type="text" class="form-control" name="nome" value="{{ $conselhoCreateHasOld ? old('nome') : '' }}" required>
            @error('nome', 'createConselho')
              <small class="text-danger">{{ $message }}</small>
            @enderror
          </div>
          <div class="form-group">
            <label class="form-control-label">Código</label>
            <input type="text" class="form-control" name="codigo" value="{{ $conselhoCreateHasOld ? old('codigo') : '' }}" maxlength="10">
            @error('codigo', 'createConselho')
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
@foreach($conselhos as $conselho)
  @php $editingConselhoId = old('editing_conselho_id'); $isEditing = $editingConselhoId == $conselho->id; @endphp
  <div class="modal fade" id="editConselhoModal-{{ $conselho->id }}" tabindex="-1" role="dialog" aria-labelledby="editConselhoLabel-{{ $conselho->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editConselhoLabel-{{ $conselho->id }}">Editar Conselho</h5>
          <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="{{ route('admin.concelhos.update', $conselho) }}" method="POST">
          @csrf
          @method('PUT')
          <input type="hidden" name="editing_conselho_id" value="{{ $conselho->id }}">
          <div class="modal-body">
            <div class="form-group mb-3">
              <label class="form-control-label">Nome *</label>
              <input type="text" class="form-control" name="nome" value="{{ $isEditing ? old('nome') : $conselho->nome }}" required>
              @if($isEditing)
                @error('nome', 'editConselho')
                  <small class="text-danger">{{ $message }}</small>
                @enderror
              @endif
            </div>
            <div class="form-group">
              <label class="form-control-label">Código</label>
              <input type="text" class="form-control" name="codigo" value="{{ $isEditing ? old('codigo') : $conselho->codigo }}" maxlength="10">
              @if($isEditing)
                @error('codigo', 'editConselho')
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

    @if ($errors->hasBag('createConselho'))
      var createModal = new bootstrap.Modal(document.getElementById('createConselhoModal'));
      createModal.show();
    @endif

    var editingConselhoId = "{{ old('editing_conselho_id') }}";
    @if ($errors->hasBag('editConselho'))
      if (editingConselhoId) {
        var modal = document.getElementById('editConselhoModal-' + editingConselhoId);
        if (modal) {
          var editModal = new bootstrap.Modal(modal);
          editModal.show();
        }
      }
    @endif
  });
</script>
@endpush
