@extends('layouts.user_type.auth')

@section('content')

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12 col-lg-8 mx-auto">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Adicionar Atividade Económica</h6>
                        {{-- Este ficheiro SIM, usa a $familia --}}
                        <p class="text-sm">Família: {{ $familia->codigo }} ({{ $familia->nacionalidade }})</p>
                    </div>
                    <div class="card-body">
                        
                        <form action="{{ route('freguesia.familias.atividades.store', $familia->id) }}" method="POST" role="form text-left">
                            @csrf

                            <p class="text-sm font-weight-bold">Detalhes da Atividade</p>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tipo" class="form-control-label">Tipo de Atividade *</label>
                                        <select class="form-control" name="tipo" id="tipo" required>
                                            <option value="" disabled {{ old('tipo') ? '' : 'selected' }}>-- Selecione o tipo --</option>
                                            <option value="conta_propria" {{ old('tipo') == 'conta_propria' ? 'selected' : '' }}>Conta Própria (Negócio)</option>
                                            <option value="conta_outrem" {{ old('tipo') == 'conta_outrem' ? 'selected' : '' }}>Conta Outrem (Empregado)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="setor_id" class="form-control-label">Setor de Atividade *</label>
                                        <select class="form-control" name="setor_id" id="setor_id" required>
                                            <option value="" disabled {{ old('setor_id') ? '' : 'selected' }}>-- Selecione o setor --</option>
                                            @foreach ($setores as $setor)
                                                <option value="{{ $setor->id }}" {{ old('setor_id') == $setor->id ? 'selected' : '' }}>
                                                    {{ $setor->nome }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="descricao" class="form-control-label">Descrição (Opcional)</label>
                                <textarea class="form-control" name="descricao" id="descricao" rows="3">{{ old('descricao') }}</textarea>
                            </div>

                            <div class="text-end">
                                <a href="{{ route('freguesia.familias.edit', $familia->id) }}" class="btn btn-secondary mt-4">Cancelar</a>
                                <button type="submit" class="btn bg-gradient-success mt-4">Guardar Atividade</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection