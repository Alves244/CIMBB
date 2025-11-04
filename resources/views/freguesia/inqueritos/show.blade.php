@extends('layouts.user_type.auth')

@section('content')

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12 col-lg-8 mx-auto"> {{-- Centraliza o card --}}
                <div class="card">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <div>
                            {{-- Título --}}
                            <h6 class="mb-0">Inquérito Anual (Ano: {{ $inquerito->ano }})</h6>
                            <p class="text-sm">Preenchido em: {{ $inquerito->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        {{-- Botão Voltar --}}
                        <a href="{{ route('freguesia.inqueritos.index') }}" class="btn btn-secondary btn-sm mb-0">
                            <i class="fas fa-arrow-left me-1"></i> Voltar à Lista
                        </a>
                    </div>
                    <div class="card-body">
                        
                        {{-- Pergunta 20 --}}
                        <div class="form-group">
                            <label class="form-control-label">20. Escala de integração (1=Muito Baixa, 5=Muito Alta)</label>
                            <input class="form-control" type="text" value="{{ $inquerito->escala_integracao }}" disabled readonly>
                        </div>

                        {{-- Pergunta 21 --}}
                        <div class="form-group">
                            <label class="form-control-label">21. Aspectos positivos da/na integração</label>
                            <textarea class="form-control" rows="4" disabled readonly>{{ $inquerito->aspectos_positivos ?? '(Não preenchido)' }}</textarea>
                        </div>

                        {{-- Pergunta 22 --}}
                        <div class="form-group">
                            <label class="form-control-label">22. Aspectos negativos/dificuldades na integração</label>
                            <textarea class="form-control" rows="4" disabled readonly>{{ $inquerito->aspectos_negativos ?? '(Não preenchido)' }}</textarea>
                        </div>

                        {{-- Pergunta 23 --}}
                        <div class="form-group">
                            <label class="form-control-label">23. Nível de satisfação global com a integração (1=Muito Baixa, 5=Muito Alta)</label>
                            <input class="form-control" type="text" value="{{ $inquerito->satisfacao_global }}" disabled readonly>
                        </div>

                        {{-- Pergunta 24 --}}
                        <div class="form-group">
                            <label class="form-control-label">24. Sugestões</label>
                            <textarea class="form-control" rows="4" disabled readonly>{{ $inquerito->sugestoes ?? '(Não preenchido)' }}</textarea>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection