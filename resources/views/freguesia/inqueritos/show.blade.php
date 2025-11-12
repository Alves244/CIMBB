@extends('layouts.user_type.auth')

@section('content')

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12 col-lg-8 mx-auto"> {{-- Centraliza o card --}}
                
                <div class="card">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Detalhe do Inquérito (Ano: {{ $inquerito->ano }})</h6>
                            <p class="text-sm">Preenchido em: {{ $inquerito->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <a href="{{ route('freguesia.inqueritos.index') }}" class="btn btn-secondary btn-sm mb-0">
                            <i class="fas fa-arrow-left me-1"></i> Voltar à Lista
                        </a>
                    </div>
                    
                    {{-- CARD 1: DADOS QUANTITATIVOS (Guardados) --}}
                    <div class="card-body pt-4">
                        <h6 class="font-weight-bolder text-success">Parte 1: Dados Quantitativos (Snapshot do Ano)</h6>
                        
                        {{-- TAMANHO DA LETRA AUMENTADO AQUI --}}
                        <h6 class="mt-4 text-dark">Perguntas 11-13. Localização dos Agregados</h6>
                        <ul class="list-group">
                            <li class="list-group-item border-0 ps-0 pt-0 text-sm"><strong class="text-dark">Núcleo Urbano (Sede):</strong> {{ $inquerito->total_nucleo_urbano }}</li>
                            <li class="list-group-item border-0 ps-0 pt-0 text-sm"><strong class="text-dark">Aldeias Anexas:</strong> {{ $inquerito->total_aldeia_anexa }}</li>
                            <li class="list-group-item border-0 ps-0 pt-0 text-sm"><strong class="text-dark">Espaço Agroflorestal:</strong> {{ $inquerito->total_agroflorestal }}</li>
                        </ul>
                        
                        {{-- TAMANHO DA LETRA AUMENTADO AQUI --}}
                        <h6 class="mt-4 text-dark">Pergunta 14. Total de Indivíduos</h6>
                        <ul class="list-group">
                            <li class="list-group-item border-0 ps-0 pt-0 text-sm"><strong class="text-dark">Total de Adultos:</strong> {{ $inquerito->total_adultos }}</li>
                            <li class="list-group-item border-0 ps-0 pt-0 text-sm"><strong class="text-dark">Total de Crianças/Jovens:</strong> {{ $inquerito->total_criancas }}</li>
                        </ul>
                        
                        {{-- TAMANHO DA LETRA AUMENTADO AQUI --}}
                        <h6 class="mt-4 text-dark">Pergunta 15. Tipologia de Propriedade</h6>
                        <ul class="list-group">
                            <li class="list-group-item border-0 ps-0 pt-0 text-sm"><strong class="text-dark">Propriedade Própria:</strong> {{ $inquerito->total_propria }}</li>
                            <li class="list-group-item border-0 ps-0 pt-0 text-sm"><strong class="text-dark">Propriedade Arrendada:</strong> {{ $inquerito->total_arrendada }}</li>
                        </ul>
                        
                        {{-- TAMANHO DA LETRA AUMENTADO AQUI --}}
                        <h6 class="mt-4 text-dark">Perguntas 16-19. Nº de Atividades por Setor</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <strong class="text-dark">Conta Própria:</strong>
                                <ul class="list-group">
                                    @foreach($inquerito->total_por_setor_propria ?? [] as $setor => $total)
                                        <li class="list-group-item border-0 ps-0 pt-0 text-sm">{{ $setor }}: <span class="font-weight-bold">{{ $total }}</span></li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <strong class="text-dark">Conta de Outrem:</strong>
                                <ul class="list-group">
                                    @foreach($inquerito->total_por_setor_outrem ?? [] as $setor => $total)
                                        <li class="list-group-item border-0 ps-0 pt-0 text-sm">{{ $setor }}: <span class="font-weight-bold">{{ $total }}</span></li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    {{-- CARD 2: DADOS QUALITATIVOS (Guardados) --}}
                    <div class="card-body pt-0">
                        <hr class="horizontal dark mt-0 mb-4">
                        <h6 class="font-weight-bolder text-success">Parte 2: Dados Qualitativos (Opinião Anual)</h6>
                        
                        <div class="form-group">
                            {{-- TAMANHO DA LETRA AUMENTADO AQUI --}}
                            <label class="form-control-label h6 text-dark">20. Escala de integração (1-5)</label>
                            <input class="form-control" type="text" value="{{ $inquerito->escala_integracao }}" disabled readonly>
                        </div>
                        <div class="form-group">
                            {{-- TAMANHO DA LETRA AUMENTADO AQUI --}}
                            <label class="form-control-label h6 text-dark">21. Aspectos positivos da/na integração</label>
                            <textarea class="form-control" rows="4" disabled readonly>{{ $inquerito->aspectos_positivos ?? '(Não preenchido)' }}</textarea>
                        </div>
                        <div class="form-group">
                            {{-- TAMANHO DA LETRA AUMENTADO AQUI --}}
                            <label class="form-control-label h6 text-dark">22. Aspectos negativos/dificuldades na integração</label>
                            <textarea class="form-control" rows="4" disabled readonly>{{ $inquerito->aspectos_negativos ?? '(Não preenchido)' }}</textarea>
                        </div>
                        <div class="form-group">
                            {{-- TAMANHO DA LETRA AUMENTADO AQUI --}}
                            <label class="form-control-label h6 text-dark">23. Nível de satisfação global (1-5)</label>
                            <input class="form-control" type="text" value="{{ $inquerito->satisfacao_global }}" disabled readonly>
                        </div>
                        <div class="form-group">
                            {{-- TAMANHO DA LETRA AUMENTADO AQUI --}}
                            <label class="form-control-label h6 text-dark">24. Sugestões</label>
                            <textarea class="form-control" rows="4" disabled readonly>{{ $inquerito->sugestoes ?? '(Não preenchido)' }}</textarea>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection