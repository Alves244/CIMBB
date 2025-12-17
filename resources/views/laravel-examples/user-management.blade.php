@extends('layouts.user_type.auth')

@section('content')

@php
    $createPerfil = old('perfil', 'freguesia');
@endphp

<div>
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 mx-4">
                <div class="card-header pb-0">
                    <div class="d-flex flex-row justify-content-between">
                        <div>
                            <h5 class="mb-0">Gestão de Utilizadores</h5>
                        </div>
                        <button class="btn bg-gradient-success btn-sm mb-0" type="button" data-bs-toggle="modal" data-bs-target="#createUserModal">
                            +&nbsp; Novo Utilizador
                        </button>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Utilizador</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Perfil</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Entidade Associada</th>
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
                                        @elseif($user->perfil == 'agrupamento')
                                            <span class="badge badge-sm bg-gradient-dark">Agrupamento</span>
                                        @else
                                            <span class="badge badge-sm bg-gradient-success">Freguesia</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->perfil == 'freguesia')
                                            <p class="text-xs font-weight-bold mb-0">{{ $user->freguesia->nome ?? 'N/A' }}</p>
                                        @elseif($user->perfil == 'agrupamento')
                                            <p class="text-xs font-weight-bold mb-0">{{ $user->agrupamento->nome ?? 'N/A' }}</p>
                                        @else
                                            <p class="text-xs font-weight-bold mb-0">—</p>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="text-secondary text-xs font-weight-bold">{{ $user->created_at->format('d/m/Y') }}</span>
                                    </td>
                                    <td class="text-center">
                                        <a href="#" class="btn btn-link text-dark px-3 mb-0" data-bs-toggle="modal" data-bs-target="#editUserModal-{{ $user->id }}" data-bs-original-title="Editar Utilizador">
                                            <i class="fas fa-user-edit me-2"></i>Editar
                                        </a>

                                        <a href="#" class="btn btn-link text-warning px-3 mb-0" data-bs-toggle="modal" data-bs-target="#resetPasswordModal-{{ $user->id }}" data-bs-original-title="Atualizar Password">
                                            <i class="fas fa-key me-2"></i>Password
                                        </a>

                                        @if(Auth::id() != $user->id)
                                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-link text-danger text-gradient px-3 mb-0"
                                                        onclick="return confirm('Tem a certeza que deseja apagar o utilizador {{ $user->nome }}?')"
                                                        data-bs-toggle="tooltip" data-bs-original-title="Apagar Utilizador">
                                                    <i class="fas fa-trash me-2"></i>Apagar
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
                <x-admin.pagination :paginator="$users" />
            </div>
        </div>
    </div>
</div>

{{-- MODAL: Criar Utilizador --}}
<div class="modal fade" id="createUserModal" tabindex="-1" role="dialog" aria-labelledby="createUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createUserModalLabel">Novo Utilizador</h5>
                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info text-sm" role="alert">
                        A password será gerada automaticamente e enviada por email ao novo utilizador juntamente com uma mensagem de boas-vindas.
                    </div>
                    <div class="form-group">
                        <label for="create-nome" class="form-control-label">Nome *</label>
                        <input type="text" class="form-control" id="create-nome" name="nome" value="{{ old('nome') }}" required>
                        @error('nome', 'createUser')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="create-email" class="form-control-label">Email *</label>
                        <input type="email" class="form-control" id="create-email" name="email" value="{{ old('email') }}" required>
                        @error('email', 'createUser')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="create-telemovel" class="form-control-label">Telemóvel</label>
                        <input type="text" class="form-control" id="create-telemovel" name="telemovel" value="{{ old('telemovel') }}" maxlength="20">
                        @error('telemovel', 'createUser')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="create-perfil" class="form-control-label">Perfil *</label>
                        <select class="form-control perfil-select" name="perfil" id="create-perfil"
                                data-freguesia-wrapper="create-freguesia-wrapper"
                                data-agrupamento-wrapper="create-agrupamento-wrapper"
                                onchange="toggleAssociacoes(this)" required>
                            <option value="freguesia" {{ $createPerfil == 'freguesia' ? 'selected' : '' }}>Freguesia</option>
                            <option value="agrupamento" {{ $createPerfil == 'agrupamento' ? 'selected' : '' }}>Agrupamento de Escolas</option>
                            <option value="cimbb" {{ $createPerfil == 'cimbb' ? 'selected' : '' }}>Funcionário CIMBB</option>
                            <option value="admin" {{ $createPerfil == 'admin' ? 'selected' : '' }}>Administrador</option>
                        </select>
                        @error('perfil', 'createUser')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="form-group" id="create-freguesia-wrapper" style="{{ $createPerfil == 'freguesia' ? '' : 'display:none;' }}">
                        <label for="create-freguesia" class="form-control-label">Freguesia *</label>
                        <select class="form-control" name="freguesia_id" id="create-freguesia">
                            <option value="">-- Selecione uma Freguesia --</option>
                            @foreach ($freguesias as $freguesia)
                                <option value="{{ $freguesia->id }}" {{ old('freguesia_id') == $freguesia->id ? 'selected' : '' }}>
                                    {{ $freguesia->nome }} ({{ $freguesia->concelho->nome ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                        @error('freguesia_id', 'createUser')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="form-group" id="create-agrupamento-wrapper" style="{{ $createPerfil == 'agrupamento' ? '' : 'display:none;' }}">
                        <label for="create-agrupamento" class="form-control-label">Agrupamento *</label>
                        <select class="form-control" name="agrupamento_id" id="create-agrupamento">
                            <option value="">-- Selecione um Agrupamento --</option>
                            @foreach ($agrupamentos as $agrupamento)
                                <option value="{{ $agrupamento->id }}" {{ old('agrupamento_id') == $agrupamento->id ? 'selected' : '' }}>
                                    {{ $agrupamento->nome }} ({{ $agrupamento->concelho->nome ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                        @error('agrupamento_id', 'createUser')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn bg-gradient-success">Criar Utilizador</button>
                </div>
            </form>
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
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nome-{{ $user->id }}" class="form-control-label">Nome *</label>
                        <input type="text" class="form-control" id="nome-{{ $user->id }}" name="nome" value="{{ $user->nome }}" required>
                    </div>
                    <div class="form-group">
                        <label for="email-{{ $user->id }}" class="form-control-label">Email *</label>
                        <input type="email" class="form-control" id="email-{{ $user->id }}" name="email" value="{{ $user->email }}" required>
                    </div>
                    <div class="form-group">
                        <label for="telemovel-{{ $user->id }}" class="form-control-label">Telemóvel</label>
                        <input type="text" class="form-control" id="telemovel-{{ $user->id }}" name="telemovel" value="{{ $user->telemovel }}" maxlength="20">
                    </div>
                    <div class="form-group">
                        <label for="perfil" class="form-control-label">Perfil *</label>
                        <select class="form-control perfil-select" name="perfil" id="perfil-{{ $user->id }}"
                                data-freguesia-wrapper="freguesia-wrapper-{{ $user->id }}"
                                data-agrupamento-wrapper="agrupamento-wrapper-{{ $user->id }}"
                                required onchange="toggleAssociacoes(this)">
                            <option value="freguesia" {{ $user->perfil == 'freguesia' ? 'selected' : '' }}>Freguesia</option>
                            <option value="agrupamento" {{ $user->perfil == 'agrupamento' ? 'selected' : '' }}>Agrupamento de Escolas</option>
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
                                    {{ $freguesia->nome }} ({{ $freguesia->concelho->nome ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" id="agrupamento-wrapper-{{ $user->id }}" style="{{ $user->perfil == 'agrupamento' ? '' : 'display:none;' }}">
                        <label for="agrupamento_id-{{ $user->id }}" class="form-control-label">Agrupamento *</label>
                        <select class="form-control" name="agrupamento_id" id="agrupamento_id-{{ $user->id }}">
                            <option value="">-- Selecione um Agrupamento --</option>
                            @foreach ($agrupamentos as $agrupamento)
                                <option value="{{ $agrupamento->id }}" {{ $user->agrupamento_id == $agrupamento->id ? 'selected' : '' }}>
                                    {{ $agrupamento->nome }} ({{ $agrupamento->concelho->nome ?? 'N/A' }})
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

    {{-- ModaIS: Reset Password --}}
    <div class="modal fade" id="resetPasswordModal-{{ $user->id }}" tabindex="-1" role="dialog" aria-labelledby="resetPasswordModalLabel-{{ $user->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="resetPasswordModalLabel-{{ $user->id }}">Atualizar Password: {{ $user->nome }}</h5>
                    <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.users.password', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="password-{{ $user->id }}" class="form-control-label">Nova Password *</label>
                            <input type="password" class="form-control" id="password-{{ $user->id }}" name="password" required>
                            @error('password', 'passwordUser')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="password-confirmation-{{ $user->id }}" class="form-control-label">Confirmar Password *</label>
                            <input type="password" class="form-control" id="password-confirmation-{{ $user->id }}" name="password_confirmation" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn bg-gradient-success">Guardar Nova Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

@endsection

@push('js')
<script>
    function toggleAssociacoes(selectElement) {
        var freguesiaWrapperId = selectElement.getAttribute('data-freguesia-wrapper');
        var agrupamentoWrapperId = selectElement.getAttribute('data-agrupamento-wrapper');

        if (freguesiaWrapperId) {
            var freguesiaWrapper = document.getElementById(freguesiaWrapperId);
            if (freguesiaWrapper) {
                freguesiaWrapper.style.display = selectElement.value === 'freguesia' ? 'block' : 'none';
            }
        }

        if (agrupamentoWrapperId) {
            var agrupamentoWrapper = document.getElementById(agrupamentoWrapperId);
            if (agrupamentoWrapper) {
                agrupamentoWrapper.style.display = selectElement.value === 'agrupamento' ? 'block' : 'none';
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        var perfilSelects = document.querySelectorAll('.perfil-select');
        perfilSelects.forEach(function (select) {
            toggleAssociacoes(select);
        });
    });
</script>
@endpush