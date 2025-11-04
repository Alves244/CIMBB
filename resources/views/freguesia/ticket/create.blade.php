@extends('layouts.user_type.auth')

@section('content')

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12 col-lg-8 mx-auto"> {{-- Centraliza o formulário --}}
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Abrir Novo Ticket de Suporte</h6>
                        <p class="text-sm">Descreva a sua dúvida, sugestão ou erro.</p>
                    </div>
                    <div class="card-body">
                        {{-- Formulário com upload de ficheiro (enctype) --}}
                        <form action="{{ route('freguesia.suporte.store') }}" method="POST" role="form text-left" enctype="multipart/form-data">
                            @csrf 

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="assunto" class="form-control-label">Assunto *</label>
                                        <input class="form-control" type="text" name="assunto" id="assunto" value="{{ old('assunto') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="categoria" class="form-control-label">Categoria *</label>
                                        <select class="form-control" name="categoria" id="categoria" required>
                                            <option value="duvida" {{ old('categoria') == 'duvida' ? 'selected' : '' }}>Dúvida</option>
                                            <option value="erro" {{ old('categoria') == 'erro' ? 'selected' : '' }}>Reportar Erro</option>
                                            <option value="sugestao" {{ old('categoria') == 'sugestao' ? 'selected' : '' }}>Sugestão</option>
                                            <option value="outro" {{ old('categoria') == 'outro' ? 'selected' : '' }}>Outro</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="descricao" class="form-control-label">Descrição Detalhada *</label>
                                <textarea class="form-control" name="descricao" id="descricao" rows="5" required>{{ old('descricao') }}</textarea>
                            </div>

                            <div class="form-group">
                                <label for="anexo" class="form-control-label">Anexo (Opcional)</label>
                                <input class="form-control" type="file" name="anexo" id="anexo">
                                <p class="text-xs text-secondary mb-0">Tipos permitidos: PDF, JPG, PNG, ZIP (Max: 2MB)</p>
                            </div>

                            <div class="text-end">
                                <a href="{{ route('freguesia.suporte.index') }}" class="btn btn-secondary mt-4">Cancelar</a>
                                <button type="submit" class="btn bg-gradient-success mt-4">Enviar Ticket</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection