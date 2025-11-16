@extends('layouts.user_type.auth')

@section('content')

<div>
    <div class="container-fluid">
        {{-- CABEÇALHO com fundo verde claro --}}
        <div class="page-header min-height-300 border-radius-xl mt-4" 
             style="background-image: linear-gradient(310deg, #98e090 0%, #76c76a 100%);">
        </div>
        <div class="card card-body blur shadow-blur mx-4 mt-n6">
            <div class="row gx-4">
                <div class="col-auto my-auto">
                    <div class="h-100">
                        <h5 class="mb-1">
                            {{ auth()->user()->nome }}
                        </h5>
                        <p class="mb-0 font-weight-bold text-sm">
                            {{ ucfirst(auth()->user()->perfil) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid py-4">

        {{-- CARD 1: INFORMAÇÃO DE PERFIL (AGORA EDITA TELEMÓVEL) --}}
        <div class="card">
            <div class="card-header pb-0 px-3">
                <h6 class="mb-0">{{ __('Informação de Perfil') }}</h6>
            </div>
            <div class="card-body pt-4 p-3">
                
                {{-- Mensagem de Sucesso (Perfil) --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert" style="color: white;">
                        <span class="alert-text">{{ session('success') }}</span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                
                {{-- Formulário de Perfil (Nome, Telemóvel) RE-ATIVADO --}}
                <form action="{{ route('user-profile') }}" method="POST" role="form text-left">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="user-name" class="form-control-label">{{ __('Nome Completo') }} (Não editável)</label>
                                <div class="border border-danger-0 rounded-3">
                                    {{-- CORREÇÃO: Removido 'name="nome"' porque o campo é readonly --}}
                                    <input class="form-control" value="{{ auth()->user()->nome }}" type="text" id="user-name" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email" class="form-label">Email (Não editável)</label>
                                <input type="email" class="form-control" id="email" value="{{ auth()->user()->email }}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="user.telemovel" class="form-control-label">{{ __('Telemóvel') }}</label>
                                <div class="@error('telemovel')border border-danger rounded-3 @enderror">
                                    {{-- CAMPO TELEMOVEL AGORA EDITÁVEL --}}
                                    {{-- CORREÇÃO: 'id' alterado de "number" para "user.telemovel" para corresponder ao 'for' da label --}}
                                    <input class="form-control" type="tel" placeholder="912345678" id="user.telemovel" name="telemovel" value="{{ old('telemovel', auth()->user()->telemovel) }}">
                                    @error('telemovel')
                                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- BOTÃO GUARDAR RE-ATIVADO --}}
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn bg-gradient-success btn-md mt-4 mb-4">{{ 'Guardar Alterações' }}</button>
                    </div>
                </form>

            </div>
        </div>


        {{-- ========================================== --}}
        {{-- CARD 2: ALTERAR PALAVRA-PASSE (MANTÉM-SE) --}}
        {{-- ========================================== --}}
        <div class="card mt-4">
            <div class="card-header pb-0 px-3">
                <h6 class="mb-0">Alterar Palavra-passe</h6>
            </div>
            <div class="card-body pt-4 p-3">
                
                {{-- Mensagem de Sucesso (Password) --}}
                @if (session('success_password'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert" style="color: white;">
                        <span class="alert-text">{{ session('success_password') }}</span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                
                {{-- Mensagem de Erro (Password Incorreta) --}}
                @if ($errors->has('current_password'))
                     <div class="alert alert-danger alert-dismissible fade show" role="alert" style="color: white;">
                        <span class="alert-text">{{ $errors->first('current_password') }}</span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                {{-- Formulário de Password --}}
                <form action="{{ route('user-profile.password') }}" method="POST" role="form text-left">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="current_password" class="form-control-label">Password Atual</label>
                                <div class="@error('current_password')border border-danger rounded-3 @enderror">
                                    <input class="form-control" type="password" id="current_password" name="current_password" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="new_password" class="form-control-label">Nova Password</label>
                                <div class="@error('new_password')border border-danger rounded-3 @enderror">
                                    <input class="form-control" type="password" id="new_password" name="new_password" required>
                                    @error('new_password') <p class="text-danger text-xs mt-2">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="new_password_confirmation" class="form-control-label">Confirmar Nova Password</label>
                                <div class="rounded-3">
                                    <input class="form-control" type="password" id="new_password_confirmation" name="new_password_confirmation" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn bg-gradient-success btn-md mt-4 mb-4">Guardar Password</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection