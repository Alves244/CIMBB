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
                        <h6 class="font-weight-bolder text-success mb-1">Parte 1: Dados Quantitativos (Snapshot do Ano)</h6>
                        <p class="text-sm text-muted">Resumo automático construído com base nos registos anuais.</p>

                        <div class="mt-4">
                            <h6 class="text-dark fw-semibold mb-1">1. Agregados familiares que residem no núcleo urbano da sede da freguesia</h6>
                            <p class="text-sm mb-0"><strong class="text-dark">{{ (int) ($inquerito->total_nucleo_urbano ?? 0) }}</strong> agregados.</p>
                        </div>

                        <div class="mt-4">
                            <h6 class="text-dark fw-semibold mb-1">2. Agregados familiares que residem em aldeias anexas</h6>
                            <p class="text-sm mb-0"><strong class="text-dark">{{ (int) ($inquerito->total_aldeia_anexa ?? 0) }}</strong> agregados.</p>
                        </div>

                        <div class="mt-4">
                            <h6 class="text-dark fw-semibold mb-1">3. Agregados familiares que residem em quintas ou propriedades no espaço agroflorestal</h6>
                            <p class="text-sm mb-0"><strong class="text-dark">{{ (int) ($inquerito->total_agroflorestal ?? 0) }}</strong> agregados.</p>
                        </div>

                        <div class="mt-4">
                            <h6 class="text-dark fw-semibold">4. Número total de indivíduos</h6>
                            <ul class="list-group">
                                <li class="list-group-item border-0 ps-0 pt-0 text-sm">Total de Adultos: <strong class="text-dark">{{ (int) ($inquerito->total_adultos ?? 0) }}</strong></li>
                                <li class="list-group-item border-0 ps-0 pt-0 text-sm">Total de Crianças/Jovens: <strong class="text-dark">{{ (int) ($inquerito->total_criancas ?? 0) }}</strong></li>
                            </ul>
                        </div>

                        <div class="mt-4">
                            <h6 class="text-dark fw-semibold">5. Agregados familiares que vivem em casa ou propriedade adquiridas</h6>
                            <ul class="list-group">
                                <li class="list-group-item border-0 ps-0 pt-0 text-sm">Propriedade Própria: <strong class="text-dark">{{ (int) ($inquerito->total_propria ?? 0) }}</strong></li>
                                <li class="list-group-item border-0 ps-0 pt-0 text-sm">Propriedade Arrendada: <strong class="text-dark">{{ (int) ($inquerito->total_arrendada ?? 0) }}</strong></li>
                            </ul>
                        </div>

                        @php
                            $setoresPropria = collect($setoresLista)->map(function ($setor) use ($inquerito) {
                                return [
                                    'nome' => $setor['nome'],
                                    'valor' => (int) ($inquerito->total_por_setor_propria[$setor['nome']] ?? 0),
                                ];
                            })->filter(fn ($item) => $item['valor'] > 0);

                            $setoresOutrem = collect($setoresLista)->map(function ($setor) use ($inquerito) {
                                return [
                                    'nome' => $setor['nome'],
                                    'valor' => (int) ($inquerito->total_por_setor_outrem[$setor['nome']] ?? 0),
                                ];
                            })->filter(fn ($item) => $item['valor'] > 0);
                        @endphp

                        <div class="mt-4">
                            <h6 class="text-dark fw-semibold">6. Nº de atividades por Conta Própria</h6>
                            @if($setoresPropria->isEmpty())
                                <p class="text-sm text-muted mb-0">Sem registos disponíveis para esta secção.</p>
                            @else
                                <ul class="list-group">
                                    @foreach($setoresPropria as $setor)
                                        <li class="list-group-item border-0 ps-0 pt-0 text-sm">{{ $setor['nome'] }}: <strong class="text-dark">{{ $setor['valor'] }}</strong></li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>

                        <div class="mt-4">
                            <h6 class="text-dark fw-semibold">7. Nº de atividades por Conta de Outrem</h6>
                            @if($setoresOutrem->isEmpty())
                                <p class="text-sm text-muted mb-0">Sem registos disponíveis para esta secção.</p>
                            @else
                                <ul class="list-group">
                                    @foreach($setoresOutrem as $setor)
                                        <li class="list-group-item border-0 ps-0 pt-0 text-sm">{{ $setor['nome'] }}: <strong class="text-dark">{{ $setor['valor'] }}</strong></li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>

                        <div class="mt-4">
                            <h6 class="text-dark fw-semibold">8. Número total de trabalhadores por conta de outrem</h6>
                            <p class="text-sm mb-0"><strong class="text-dark">{{ (int) ($inquerito->total_trabalhadores_outrem ?? 0) }}</strong> trabalhadores acompanhados.</p>
                        </div>
                    </div>
                    
                    {{-- CARD 2: DADOS QUALITATIVOS (Guardados) --}}
                    <div class="card-body pt-0">
                        <hr class="horizontal dark mt-0 mb-4">
                        <h6 class="font-weight-bolder text-success">Parte 2: Percepção da Junta de Freguesia sobre a Integração</h6>
                        
                        <div class="form-group">
                            {{-- TAMANHO DA LETRA AUMENTADO AQUI --}}
                            <label class="form-control-label h6 text-dark">Escala de integração (1-5)</label>
                            <input class="form-control" type="text" value="{{ $inquerito->escala_integracao }}" disabled readonly>
                        </div>
                        <div class="form-group">
                            {{-- TAMANHO DA LETRA AUMENTADO AQUI --}}
                            <label class="form-control-label h6 text-dark">Aspectos positivos da/na integração</label>
                            <textarea class="form-control" rows="4" disabled readonly>{{ $inquerito->aspectos_positivos ?? '(Não preenchido)' }}</textarea>
                        </div>
                        <div class="form-group">
                            {{-- TAMANHO DA LETRA AUMENTADO AQUI --}}
                            <label class="form-control-label h6 text-dark">Aspectos negativos/dificuldades na integração</label>
                            <textarea class="form-control" rows="4" disabled readonly>{{ $inquerito->aspectos_negativos ?? '(Não preenchido)' }}</textarea>
                        </div>
                        <div class="form-group">
                            {{-- TAMANHO DA LETRA AUMENTADO AQUI --}}
                            <label class="form-control-label h6 text-dark">Nível de satisfação global (1-5)</label>
                            <input class="form-control" type="text" value="{{ $inquerito->satisfacao_global }}" disabled readonly>
                        </div>
                        <div class="form-group">
                            {{-- TAMANHO DA LETRA AUMENTADO AQUI --}}
                            <label class="form-control-label h6 text-dark">Sugestões</label>
                            <textarea class="form-control" rows="4" disabled readonly>{{ $inquerito->sugestoes ?? '(Não preenchido)' }}</textarea>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection