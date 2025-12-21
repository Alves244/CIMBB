@extends('layouts.user_type.auth')

@section('content')

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12 col-lg-8 mx-auto">
                
                {{-- FORMULÁRIO DE EDIÇÃO --}}
                <form action="{{ route('freguesia.inqueritos.update', $inquerito->id) }}" method="POST" role="form text-left">
                    @csrf
                    @method('PUT')
                    
                    <div class="card">
                        <div class="card-header pb-0">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6>Editar Inquérito Anual ({{ $inquerito->ano }})</h6>
                                    <p class="text-sm">Freguesia: {{ Auth::user()->freguesia->nome ?? 'N/A' }}</p>
                                </div>
                                {{-- MUDANÇA 1: Badge Verde --}}
                                <span class="badge bg-gradient-success align-self-center">Modo Edição</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <h6 class="font-weight-bolder text-success mb-1">Parte 1: Dados Quantitativos</h6>
                            <p class="text-sm">Pode corrigir os valores abaixo manualmente.</p>

                            <div class="mt-4">
                                <label for="total_nucleo_urbano" class="form-label fw-semibold">1. Agregados familiares que residem no núcleo urbano da sede da freguesia</label>
                                <input class="form-control" type="number" name="total_nucleo_urbano" id="total_nucleo_urbano" value="{{ old('total_nucleo_urbano', $inquerito->total_nucleo_urbano) }}" min="0" required>
                            </div>

                            <div class="mb-3">
                                <label for="total_aldeia_anexa" class="form-label fw-semibold">2. Agregados familiares que residem em aldeias anexas</label>
                                <input class="form-control" type="number" name="total_aldeia_anexa" id="total_aldeia_anexa" value="{{ old('total_aldeia_anexa', $inquerito->total_aldeia_anexa) }}" min="0" required>
                            </div>

                            <div class="mb-4">
                                <label for="total_agroflorestal" class="form-label fw-semibold">3. Agregados familiares que residem em quintas ou propriedades no espaço agroflorestal</label>
                                <input class="form-control" type="number" name="total_agroflorestal" id="total_agroflorestal" value="{{ old('total_agroflorestal', $inquerito->total_agroflorestal) }}" min="0" required>
                            </div>

                            <div class="mb-4">
                                <h6 class="text-dark fw-semibold">4. Número total de indivíduos</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="total_adultos" class="form-label text-sm mb-1">Total de Adultos</label>
                                        <input class="form-control" type="number" name="total_adultos" id="total_adultos" value="{{ old('total_adultos', $inquerito->total_adultos) }}" min="0" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="total_criancas" class="form-label text-sm mb-1">Total de Crianças/Jovens</label>
                                        <input class="form-control" type="number" name="total_criancas" id="total_criancas" value="{{ old('total_criancas', $inquerito->total_criancas) }}" min="0" required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <h6 class="text-dark fw-semibold">5. Agregados familiares que vivem em casa ou propriedade adquiridas</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="total_propria" class="form-label text-sm mb-1">Nº de Agregados (Propriedade Própria)</label>
                                        <input class="form-control" type="number" name="total_propria" id="total_propria" value="{{ old('total_propria', $inquerito->total_propria) }}" min="0" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="total_arrendada" class="form-label text-sm mb-1">Nº de Agregados (Propriedade Arrendada)</label>
                                        <input class="form-control" type="number" name="total_arrendada" id="total_arrendada" value="{{ old('total_arrendada', $inquerito->total_arrendada) }}" min="0" required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <h6 class="text-dark fw-semibold">6. Nº de atividades por Conta Própria</h6>
                                <div class="row g-3">
                                    @foreach($setoresLista as $setor)
                                        <div class="col-md-6">
                                            <label for="propria_{{ $setor['slug'] }}" class="form-label text-sm">{{ $setor['nome'] }}</label>
                                            <input type="number" class="form-control form-control-sm" name="total_por_setor_propria[{{ $setor['nome'] }}]" id="propria_{{ $setor['slug'] }}" value="{{ old('total_por_setor_propria.'.$setor['nome'], $inquerito->total_por_setor_propria[$setor['nome']] ?? 0) }}" min="0" required>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="mb-4">
                                <h6 class="text-dark fw-semibold">7. Nº de atividades por Conta de Outrem</h6>
                                <div class="row g-3">
                                    @foreach($setoresLista as $setor)
                                        <div class="col-md-6">
                                            <label for="outrem_{{ $setor['slug'] }}" class="form-label text-sm">{{ $setor['nome'] }}</label>
                                            <input type="number" class="form-control form-control-sm" name="total_por_setor_outrem[{{ $setor['nome'] }}]" id="outrem_{{ $setor['slug'] }}" value="{{ old('total_por_setor_outrem.'.$setor['nome'], $inquerito->total_por_setor_outrem[$setor['nome']] ?? 0) }}" min="0" required>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="mb-2">
                                <label for="total_trabalhadores_outrem" class="form-label fw-semibold">8. Número total de trabalhadores por conta de outrem</label>
                                <input class="form-control" type="number" name="total_trabalhadores_outrem" id="total_trabalhadores_outrem" value="{{ old('total_trabalhadores_outrem', $inquerito->total_trabalhadores_outrem) }}" min="0" required>
                                <p class="text-xs text-muted mb-0">Regista o total de pessoas acompanhadas com atividade por conta de outrem.</p>
                            </div>

                        </div>
                    </div> 

                    {{-- Parte 2: QUALITATIVOS --}}
                    <div class="card mt-4">
                        <div class="card-body">
                            {{-- MUDANÇA 3: Texto Verde --}}
                            <h6 class="font-weight-bolder text-success">Parte 2: Percepção da Junta de Freguesia sobre a Integração</h6>

                            {{-- Pergunta 20 --}}
                            <div class="form-group mt-4">
                                <label for="escala_integracao" class="form-control-label h6 text-dark">9. Escala de integração</label>
                                <select class="form-control" name="escala_integracao" id="escala_integracao" required>
                                    @for($i = 1; $i <= 5; $i++)
                                        <option value="{{ $i }}" {{ old('escala_integracao', $inquerito->escala_integracao) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>

                            {{-- Pergunta 21 --}}
                            <div class="form-group">
                                <label for="aspectos_positivos" class="form-control-label h6 text-dark">10. Aspectos positivos</label>
                                <textarea class="form-control" name="aspectos_positivos" rows="4">{{ old('aspectos_positivos', $inquerito->aspectos_positivos) }}</textarea>
                            </div>
                            
                            {{-- Pergunta 22 --}}
                            <div class="form-group">
                                <label for="aspectos_negativos" class="form-control-label h6 text-dark">11. Aspectos negativos</label>
                                <textarea class="form-control" name="aspectos_negativos" rows="4">{{ old('aspectos_negativos', $inquerito->aspectos_negativos) }}</textarea>
                            </div>

                            {{-- Pergunta 23 --}}
                            <div class="form-group">
                                <label for="satisfacao_global" class="form-control-label h6 text-dark">12. Satisfação global</label>
                                <select class="form-control" name="satisfacao_global" id="satisfacao_global" required>
                                    @for($i = 1; $i <= 5; $i++)
                                        <option value="{{ $i }}" {{ old('satisfacao_global', $inquerito->satisfacao_global) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            
                            {{-- Pergunta 24 --}}
                            <div class="form-group">
                                <label for="sugestoes" class="form-control-label h6 text-dark">13. Sugestões</label>
                                <textarea class="form-control" name="sugestoes" rows="4">{{ old('sugestoes', $inquerito->sugestoes) }}</textarea>
                            </div>
                            
                            <div class="text-end">
                                <a href="{{ route('freguesia.inqueritos.index') }}" class="btn btn-secondary mt-4">Cancelar</a>
                                {{-- MUDANÇA 4: Botão Verde --}}
                                <button type="submit" class="btn bg-gradient-success mt-4">Guardar Alterações</button>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>

@endsection