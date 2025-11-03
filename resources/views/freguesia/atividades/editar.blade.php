@extends('layouts.user_type.auth')

@section('content')

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12 col-lg-8 mx-auto">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Editar Atividade Económica</h6>
                        {{-- Mostra a que família pertence --}}
                        <p class="text-sm">Família: {{ $atividade->familia->codigo }}</p>
                    </div>
                    <div class="card-body">
                        
                        {{-- O formulário faz POST para a rota 'update' de atividades --}}
                        <form action="{{ route('freguesia.atividades.update', $atividade->id) }}" method="POST" role="form text-left">
                            @csrf
                            @method('PUT') {{-- Método PUT para atualização --}}

                            <p class="text-sm font-weight-bold">Detalhes da Atividade</p>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tipo" class="form-control-label">Tipo de Atividade *</label>
                                        <select class="form-control" name="tipo" id="tipo" required>
                                            {{-- Preenche com o valor antigo ou o valor guardado --}}
                                            <option value="conta_propria" {{ old('tipo', $atividade->tipo) == 'conta_propria' ? 'selected' : '' }}>Conta Própria (Negócio)</option>
                                            <option value="conta_outrem" {{ old('tipo', $atividade->tipo) == 'conta_outrem' ? 'selected' : '' }}>Conta Outrem (Empregado)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="setor_id" class="form-control-label">Setor de Atividade *</label>
                                        <select class="form-control" name="setor_id" id="setor_id" required>
                                            @foreach ($setores as $setor)
                                                <option value="{{ $setor->id }}" {{ old('setor_id', $atividade->setor_id) == $setor->id ? 'selected' : '' }}>
                                                    {{ $setor->nome }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="descricao" class="form-control-label">Descrição (Opcional)</label>
                                <textarea class="form-control" name="descricao" id="descricao" rows="3">{{ old('descricao', $atividade->descricao) }}</textarea>
                            </div>

                            <div class="text-end">
                                {{-- Link "Cancelar" volta para a página de EDIÇÃO da família --}}
                                <a href="{{ route('freguesia.familias.edit', $atividade->familia_id) }}" class="btn btn-secondary mt-4">Cancelar</a>
                                <button type="submit" class="btn bg-gradient-success mt-4">Guardar Alterações</a
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection