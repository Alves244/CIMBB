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
                        @php
                            $locais = collect([
                                'Núcleo Urbano (Sede)' => (int) $inquerito->total_nucleo_urbano,
                                'Aldeias Anexas' => (int) $inquerito->total_aldeia_anexa,
                                'Espaço Agroflorestal' => (int) $inquerito->total_agroflorestal,
                            ])->filter(fn ($valor) => $valor > 0);
                        @endphp
                        <h6 class="mt-4 text-dark">Localização dos agregados</h6>
                        @if($locais->isEmpty())
                            <p class="text-sm text-muted mb-0">Sem registos disponíveis para esta secção.</p>
                        @else
                            <ul class="list-group">
                                @foreach($locais as $label => $valor)
                                    <li class="list-group-item border-0 ps-0 pt-0 text-sm"><strong class="text-dark">{{ $label }}:</strong> {{ $valor }}</li>
                                @endforeach
                            </ul>
                        @endif
                        
                        @php
                            $individuos = collect([
                                'Total de Adultos' => (int) $inquerito->total_adultos,
                                'Total de Crianças/Jovens' => (int) $inquerito->total_criancas,
                            ])->filter(fn ($valor) => $valor > 0);
                        @endphp
                        <h6 class="mt-4 text-dark">Total de indivíduos</h6>
                        @if($individuos->isEmpty())
                            <p class="text-sm text-muted mb-0">Sem registos disponíveis para esta secção.</p>
                        @else
                            <ul class="list-group">
                                @foreach($individuos as $label => $valor)
                                    <li class="list-group-item border-0 ps-0 pt-0 text-sm"><strong class="text-dark">{{ $label }}:</strong> {{ $valor }}</li>
                                @endforeach
                            </ul>
                        @endif
                        
                        @php
                            $tipologias = collect([
                                'Propriedade Própria' => (int) $inquerito->total_propria,
                                'Propriedade Arrendada' => (int) $inquerito->total_arrendada,
                            ])->filter(fn ($valor) => $valor > 0);
                        @endphp
                        <h6 class="mt-4 text-dark">Tipologia de propriedade</h6>
                        @if($tipologias->isEmpty())
                            <p class="text-sm text-muted mb-0">Sem registos disponíveis para esta secção.</p>
                        @else
                            <ul class="list-group">
                                @foreach($tipologias as $label => $valor)
                                    <li class="list-group-item border-0 ps-0 pt-0 text-sm"><strong class="text-dark">{{ $label }}:</strong> {{ $valor }}</li>
                                @endforeach
                            </ul>
                        @endif
                        
                        <h6 class="mt-4 text-dark">Número de atividades por setor</h6>
                        <div class="row">
                            @php
                                $setoresPropria = collect($inquerito->total_por_setor_propria ?? [])->filter(fn ($valor) => (int) $valor > 0);
                                $setoresOutrem = collect($inquerito->total_por_setor_outrem ?? [])->filter(fn ($valor) => (int) $valor > 0);
                            @endphp
                            <div class="col-md-6">
                                <strong class="text-dark">Conta Própria:</strong>
                                @if($setoresPropria->isEmpty())
                                    <p class="text-sm text-muted mb-0">Sem registos neste grupo.</p>
                                @else
                                    <ul class="list-group">
                                        @foreach($setoresPropria as $setor => $total)
                                            <li class="list-group-item border-0 ps-0 pt-0 text-sm">{{ $setor }}: <span class="font-weight-bold">{{ $total }}</span></li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <strong class="text-dark">Conta de Outrem:</strong>
                                @if($setoresOutrem->isEmpty())
                                    <p class="text-sm text-muted mb-0">Sem registos neste grupo.</p>
                                @else
                                    <ul class="list-group">
                                        @foreach($setoresOutrem as $setor => $total)
                                            <li class="list-group-item border-0 ps-0 pt-0 text-sm">{{ $setor }}: <span class="font-weight-bold">{{ $total }}</span></li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>

                        @php $totalTrabalhadoresOutrem = (int) ($inquerito->total_trabalhadores_outrem ?? 0); @endphp
                        @if($totalTrabalhadoresOutrem > 0)
                            <h6 class="mt-4 text-dark">Número total de trabalhadores por conta de outrem</h6>
                            <p class="text-sm mb-0"><strong class="text-dark">{{ $totalTrabalhadoresOutrem }}</strong> trabalhadores acompanhados.</p>
                        @endif
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