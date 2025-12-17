@extends('layouts.user_type.auth')

@section('content')
  <div class="container-fluid py-4">
    <div class="row">
      <div class="col-12 col-lg-8 mx-auto">
        <div class="card">
          <div class="card-header pb-0">
            <h6>Abrir Ticket de Suporte</h6>
            <p class="text-sm mb-0">Descreva a situação relacionada com o agrupamento.</p>
          </div>
          <div class="card-body">
            <form action="{{ route('agrupamento.suporte.store') }}" method="POST" enctype="multipart/form-data">
              @csrf
              <div class="row">
                <div class="col-md-8">
                  <div class="form-group">
                    <label for="assunto" class="form-control-label">Assunto *</label>
                    <input class="form-control" type="text" id="assunto" name="assunto" value="{{ old('assunto') }}" required>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="categoria" class="form-control-label">Categoria *</label>
                    <select class="form-control" id="categoria" name="categoria" required>
                      <option value="duvida" {{ old('categoria') == 'duvida' ? 'selected' : '' }}>Dúvida</option>
                      <option value="erro" {{ old('categoria') == 'erro' ? 'selected' : '' }}>Reportar Erro</option>
                      <option value="sugestao" {{ old('categoria') == 'sugestao' ? 'selected' : '' }}>Sugestão</option>
                      <option value="outro" {{ old('categoria') == 'outro' ? 'selected' : '' }}>Outro</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label for="descricao" class="form-control-label">Descrição detalhada *</label>
                <textarea class="form-control" id="descricao" name="descricao" rows="5" required>{{ old('descricao') }}</textarea>
              </div>
              <div class="form-group">
                <label for="anexo" class="form-control-label">Anexo (opcional)</label>
                <input class="form-control" type="file" id="anexo" name="anexo">
                <p class="text-xs text-secondary mb-0">Permitido: PDF, JPG, PNG, ZIP (máx. 2MB).</p>
              </div>
              <div class="text-end">
                <a href="{{ route('agrupamento.suporte.index') }}" class="btn btn-secondary mt-4">Cancelar</a>
                <button type="submit" class="btn bg-gradient-success mt-4">Enviar ticket</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
