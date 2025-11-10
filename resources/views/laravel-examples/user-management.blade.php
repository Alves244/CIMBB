@extends('layouts.user_type.auth')

@section('content')

<div>
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 mx-4">
                <div class="card-header pb-0">
                    <div class="d-flex flex-row justify-content-between">
                        <div>
                            <h5 class="mb-0">Gestão de Utilizadores</h5>
                        </div>
                        {{-- TODO: Adicionar a funcionalidade de Criar Utilizador (requer mais lógica) --}}
                        <a href="#" class="btn bg-gradient-success btn-sm mb-0" type="button" disabled>+&nbsp; Novo Utilizador</a>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Utilizador</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Perfil</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Freguesia Associada</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Data Criação</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Loop dinâmico pelos utilizadores --}}
                                @forelse ($users as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex px-3 py-1">
                                            {{-- (Pode adicionar uma foto de perfil aqui se quiser) --}}
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $user->nome }}</h6>
                                                <p class="text-xs text-secondary mb-0">{{ $user->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($user->perfil == 'admin')
                                            <span class="badge badge-sm bg-gradient-danger">Admin</span>
                                        @elseif($user->perfil == 'cimbb')
                                            <span class="badge badge-sm bg-gradient-info">CIMBB</span>
                                        @else
                                            <span class="badge badge-sm bg-gradient-success">Freguesia</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{-- Acede à relação 'freguesia' que definimos no Model User --}}
                                        <p class="text-xs font-weight-bold mb-0">{{ $user->freguesia->nome ?? 'N/A' }}</p>
                                    </td>
                                    <td class="text-center">
                                        <span class="text-secondary text-xs font-weight-bold">{{ $user->created_at->format('d/m/Y') }}</span>
                                    </td>
                                    <td class="text-center">
                                        {{-- Botão Editar (abre o modal) --}}
                                        <a href="#" class="mx-3" data-bs-toggle="modal" data-bs-target="#editUserModal-{{ $user->id }}" data-bs-original-title="Editar Utilizador">
                                            <i class="fas fa-user-edit text-success text-gradient"></i>
                                        </a>
                                        
                                        {{-- Botão Apagar (só mostra se NÃO for o user atual) --}}
                                        @if(Auth::id() != $user->id)
                                        <form action="{{ route('admin.user-management.destroy', $user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-link text-danger text-gradient px-1 mb-0"
                                                    onclick="return confirm('Tem a certeza que deseja apagar o utilizador {{ $user->nome }}?')"
                                                    data-bs-toggle="tooltip" data-bs-original-title="Apagar Utilizador">
                                                <i class="fas fa-trash text-sm"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-sm py-4">Nenhum utilizador encontrado.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- Paginação --}}
                @if ($users->hasPages())
                    <div class="card-footer px-3 border-0 d-flex align-items-center justify-content-between">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- MODAIS DE EDIÇÃO (Um para cada utilizador) --}}
@foreach ($users as $user)
<div class="modal fade" id="editUserModal-{{ $user->id }}" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel-{{ $user->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel-{{ $user->id }}">Editar Utilizador: {{ $user->nome }}</h5>
                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.user-management.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="perfil" class="form-control-label">Perfil *</label>
                        <select class="form-control" name="perfil" id="perfil-{{ $user->id }}" required onchange="toggleFreguesia(this)">
                            <option value="freguesia" {{ $user->perfil == 'freguesia' ? 'selected' : '' }}>Freguesia</option>
                            <option value="cimbb" {{ $user->perfil == 'cimbb' ? 'selected' : '' }}>Funcionário CIMBB</option>
                            <option value="admin" {{ $user->perfil == 'admin' ? 'selected' : '' }}>Administrador</option>
                        </select>
                    </div>

                    {{-- O dropdown de freguesias só aparece se o perfil 'freguesia' estiver selecionado --}}
                    <div class="form-group" id="freguesia-wrapper-{{ $user->id }}" style="{{ $user->perfil == 'freguesia' ? '' : 'display:none;' }}">
                        <label for="freguesia_id" class="form-control-label">Freguesia *</label>
                        <select class="form-control" name="freguesia_id" id="freguesia_id-{{ $user->id }}">
                            <option value="">-- Selecione uma Freguesia --</option>
                            @foreach ($freguesias as $freguesia)
                                <option value="{{ $freguesia->id }}" {{ $user->freguesia_id == $freguesia->id ? 'selected' : '' }}>
                                    {{ $freguesia->nome }} ({{ $freguesia->conselho->nome ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn bg-gradient-success">Guardar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection

@push('js')
<script>
    function toggleFreguesia(selectElement) {
        var userId = selectElement.id.split('-')[1];
        var wrapper = document.getElementById('freguesia-wrapper-' + userId);
        
        if (selectElement.value == 'freguesia') {
            wrapper.style.display = 'block';
        } else {
            wrapper.style.display = 'none';
        }
    }
</script>
@endpush