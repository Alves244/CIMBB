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
        <div class="card">
            <div class="card-header pb-0 px-3">
                <h6 class="mb-0">{{ __('Informação de Perfil') }}</h6>
            </div>
            <div class="card-body pt-4 p-3">
                <form action="/user-profile" method="POST" role="form text-left">
                    @csrf
                    
                    {{-- Blocos de Erro e Sucesso REMOVIDOS DAQUI --}}

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="user-name" class="form-control-label">{{ __('Nome Completo') }}</label>
                                <div class="@error('nome')border border-danger rounded-3 @enderror">
                                    <input class="form-control" value="{{ old('nome', auth()->user()->nome) }}" type="text" placeholder="Nome" id="user-name" name="nome">
                                    @error('nome')
                                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="user-email" class="form-control-label">{{ __('Email') }}</label>
                                <div class="@error('email')border border-danger rounded-3 @enderror">
                                    <input class="form-control" value="{{ old('email', auth()->user()->email) }}" type="email" placeholder="@example.com" id="user-email" name="email">
                                    @error('email')
                                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="user.telemovel" class="form-control-label">{{ __('Telemóvel') }}</label>
                                <div class="@error('telemovel')border border-danger rounded-3 @enderror">
                                    <input class="form-control" type="tel" placeholder="912345678" id="number" name="telemovel" value="{{ old('telemovel', auth()->user()->telemovel) }}">
                                    @error('telemovel')
                                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn bg-gradient-success btn-md mt-4 mb-4">{{ 'Guardar Alterações' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection