@extends('layouts.user_type.auth')

@section('content')

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12 col-lg-8 mx-auto">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Inquérito Anual de Freguesia (Ano: {{ $anoAtual }})</h6>
                        <p class="text-sm">Freguesia: {{ Auth::user()->freguesia->nome ?? 'N/A' }}</p>
                    </div>
                    <div class="card-body">
                        
                        <form action="{{ route('freguesia.inqueritos.store') }}" method="POST" role="form text-left">
                            @csrf 

                            <p class="text-sm font-weight-bold">Perguntas do Inquérito</p>
                            
                            {{-- Pergunta 20 (Título corrigido + Ajuda) --}}
                            <div class="form-group">
                                <label for="escala_integracao" class="form-control-label">1. Escala de integração *</label>
                                <select class="form-control" name="escala_integracao" id="escala_integracao" required>
                                    <option value="" disabled {{ old('escala_integracao') ? '' : 'selected' }}>-- Selecione um valor de 1 a 5 --</option>
                                    <option value="1" {{ old('escala_integracao') == 1 ? 'selected' : '' }}>1 (Muito Baixa)</option>
                                    <option value="2" {{ old('escala_integracao') == 2 ? 'selected' : '' }}>2 (Baixa)</option>
                                    <option value="3" {{ old('escala_integracao') == 3 ? 'selected' : '' }}>3 (Média)</option>
                                    <option value="4" {{ old('escala_integracao') == 4 ? 'selected' : '' }}>4 (Alta)</option>
                                    <option value="5" {{ old('escala_integracao') == 5 ? 'selected' : '' }}>5 (Muito Alta)</option>
                                </select>
                                <small class="form-text text-muted">Como classifica o nível de integração geral dos novos residentes na comunidade?</small>
                            </div>
                            
                            {{-- Pergunta 21 --}}
                            <div class="form-group">
                                <label for="aspectos_positivos" class="form-control-label">2. Aspectos positivos da/na integração</label>
                                <textarea class="form-control" name="aspectos_positivos" id="aspectos_positivos" rows="3" placeholder="Descreva os pontos positivos observados...">{{ old('aspectos_positivos') }}</textarea>
                            </div>

                            {{-- Pergunta 22 --}}
                            <div class="form-group">
                                <label for="aspectos_negativos" class="form-control-label">3. Aspectos negativos/dificuldades na integração</label>
                                <textarea class="form-control" name="aspectos_negativos" id="aspectos_negativos" rows="3" placeholder="Descreva as principais dificuldades ou pontos negativos...">{{ old('aspectos_negativos') }}</textarea>
                            </div>

                            {{-- Pergunta 23 (Título corrigido + Ajuda) --}}
                             <div class="form-group">
                                {{-- Título corrigido para incluir "impacte" (como no PDF) --}}
                                <label for="satisfacao_global" class="form-control-label">4. Nível de satisfação global com a integração/impacte dos novos residentes *</label>
                                <select class="form-control" name="satisfacao_global" id="satisfacao_global" required>
                                    <option value="" disabled {{ old('satisfacao_global') ? '' : 'selected' }}>-- Selecione um valor de 1 a 5 --</option>
                                    <option value="1" {{ old('satisfacao_global') == 1 ? 'selected' : '' }}>1 (Muito Baixa)</option>
                                    <option value="2" {{ old('satisfacao_global') == 2 ? 'selected' : '' }}>2 (Baixa)</option>
                                    <option value="3" {{ old('satisfacao_global') == 3 ? 'selected' : '' }}>3 (Média)</option>
                                    <option value="4" {{ old('satisfacao_global') == 4 ? 'selected' : '' }}>4 (Alta)</option>
                                    <option value="5" {{ old('satisfacao_global') == 5 ? 'selected' : '' }}>5 (Muito Alta)</option>
                                </select>
                                <small class="form-text text-muted">Como classifica o impacto geral (social, económico) dos novos residentes na freguesia?</small>
                            </div>

                            {{-- Pergunta 24 --}}
                            <div class="form-group">
                                <label for="sugestoes" class="form-control-label">5. Apresente as suas sugestões relativamente a medidas ou intervenções necessárias</label>
                                <textarea class="form-control" name="sugestoes" id="sugestoes" rows="3" placeholder="Que medidas ou intervenções poderiam melhorar a integração?">{{ old('sugestoes') }}</textarea>
                            </div>

                            <div class="text-end">
                                <a href="{{ route('freguesia.inqueritos.index') }}" class="btn btn-secondary mt-4">Cancelar</a>
                                <button type="submit" class="btn bg-gradient-success mt-4">Guardar Inquérito</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection