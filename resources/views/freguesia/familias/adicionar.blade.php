@extends('layouts.user_type.auth')

@push('css')
    {{-- Usar CDN para garantir que o estilo carrega corretamente --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    
    <style>
        /* CSS EXTRA para meter o Choices.js bonito e igual ao tema Soft UI */
        .choices__inner {
            background-color: #fff !important;
            border: 1px solid #d2d6da !important; /* Cor da borda do tema */
            border-radius: 0.5rem !important;      /* Arredondamento */
            padding: 0.5rem 0.75rem !important;
            min-height: 40px !important;
        }
        
        .choices__input {
            background-color: transparent !important;
            margin-bottom: 0 !important;
        }

        .choices__list--dropdown {
            border: 1px solid #d2d6da !important;
            border-radius: 0.5rem !important;
            margin-top: 5px !important;
        }
        
        /* Esconder o select original para não haver duplicados */
        .choices[data-type*="select-one"] .choices__input {
            border-bottom: none !important;
        }
    </style>
@endpush

@section('content')

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12 col-lg-8 mx-auto">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Formulário de Registo de Nova Família</h6>
                        <p class="text-sm">Freguesia: {{ Auth::user()->freguesia->nome ?? 'N/A' }}</p>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('freguesia.familias.store') }}" method="POST" role="form text-left">
                            @csrf 

                            <p class="text-sm font-weight-bold">Informação Base da Família</p>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="ano_instalacao" class="form-control-label">Ano de Instalação *</label>
                                        <input class="form-control" type="number" name="ano_instalacao" id="ano_instalacao" placeholder="Ex: 2024" value="{{ old('ano_instalacao') }}" required min="1900">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nacionalidade-select" class="form-control-label">Nacionalidade *</label>
                                        {{-- Removi a class form-control para não conflitar com o plugin --}}
                                        <select class="form-control" name="nacionalidade" id="nacionalidade-select" required>
                                            <option value="">Selecione uma nacionalidade</option>
                                            @foreach ($nacionalidades as $nacionalidade)
                                                <option value="{{ $nacionalidade }}" {{ old('nacionalidade') == $nacionalidade ? 'selected' : '' }}>
                                                    {{ $nacionalidade }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tipologia_habitacao" class="form-control-label">Tipologia de Habitação *</label>
                                        <select class="form-control" name="tipologia_habitacao" id="tipologia_habitacao" required>
                                            <option value="" disabled {{ old('tipologia_habitacao') ? '' : 'selected' }}>-- Selecione uma opção --</option>
                                            <option value="casa" {{ old('tipologia_habitacao') == 'casa' ? 'selected' : '' }}>Casa</option>
                                            <option value="quinta" {{ old('tipologia_habitacao') == 'quinta' ? 'selected' : '' }}>Quinta / Monte</option>
                                            <option value="apartamento" {{ old('tipologia_habitacao') == 'apartamento' ? 'selected' : '' }}>Apartamento</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tipologia_propriedade" class="form-control-label">Tipologia de Propriedade *</label>
                                        <select class="form-control" name="tipologia_propriedade" id="tipologia_propriedade" required>
                                            <option value="" disabled {{ old('tipologia_propriedade') ? '' : 'selected' }}>-- Selecione uma opção --</option>
                                            <option value="propria" {{ old('tipologia_propriedade') == 'propria' ? 'selected' : '' }}>Própria</option>
                                            <option value="arrendada" {{ old('tipologia_propriedade') == 'arrendada' ? 'selected' : '' }}>Arrendada</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="localizacao" class="form-control-label">Localização*</label>
                                        <select class="form-control" name="localizacao" id="localizacao" required>
                                            <option value="" disabled {{ old('localizacao') ? '' : 'selected' }}>-- Selecione uma opção --</option>
                                            <option value="nucleo_urbano" {{ old('localizacao') == 'nucleo_urbano' ? 'selected' : '' }}>Núcleo Urbano (Sede Freguesia)</option>
                                            <option value="aldeia_anexa" {{ old('localizacao') == 'aldeia_anexa' ? 'selected' : '' }}>Aldeia Anexa</option>
                                            <option value="espaco_agroflorestal" {{ old('localizacao') == 'espaco_agroflorestal' ? 'selected' : '' }}>Quinta / Espaço Agroflorestal</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <hr class="horizontal dark mt-4">
                            <p class="text-sm font-weight-bold">Agregado Familiar (Perg. 14)</p>
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-control-label">Adultos (Idade Laboral)</label>
                                    <div class="input-group mb-1"><span class="input-group-text" style="width: 40px;">M</span><input class="form-control" type="number" name="adultos_laboral_m" value="{{ old('adultos_laboral_m', 0) }}" min="0" required></div>
                                    <div class="input-group mb-1"><span class="input-group-text" style="width: 40px;">F</span><input class="form-control" type="number" name="adultos_laboral_f" value="{{ old('adultos_laboral_f', 0) }}" min="0" required></div>
                                    <div class="input-group mb-3"><span class="input-group-text" style="width: 40px;">N/S</span><input class="form-control" type="number" name="adultos_laboral_n" value="{{ old('adultos_laboral_n', 0) }}" min="0" required></div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-control-label">Adultos (65+ anos)</label>
                                    <div class="input-group mb-1"><span class="input-group-text" style="width: 40px;">M</span><input class="form-control" type="number" name="adultos_65_mais_m" value="{{ old('adultos_65_mais_m', 0) }}" min="0" required></div>
                                    <div class="input-group mb-1"><span class="input-group-text" style="width: 40px;">F</span><input class="form-control" type="number" name="adultos_65_mais_f" value="{{ old('adultos_65_mais_f', 0) }}" min="0" required></div>
                                    <div class="input-group mb-3"><span class="input-group-text" style="width: 40px;">N/S</span><input class="form-control" type="number" name="adultos_65_mais_n" value="{{ old('adultos_65_mais_n', 0) }}" min="0" required></div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-control-label">Crianças / Jovens</label>
                                    <div class="input-group mb-1"><span class="input-group-text" style="width: 40px;">M</span><input class="form-control" type="number" name="criancas_m" value="{{ old('criancas_m', 0) }}" min="0" required></div>
                                    <div class="input-group mb-1"><span class="input-group-text" style="width: 40px;">F</span><input class="form-control" type="number" name="criancas_f" value="{{ old('criancas_f', 0) }}" min="0" required></div>
                                    <div class="input-group mb-3"><span class="input-group-text" style="width: 40px;">N/S</span><input class="form-control" type="number" name="criancas_n" value="{{ old('criancas_n', 0) }}" min="0" required></div>
                                </div>
                            </div>

                            <hr class="horizontal dark mt-4">
                            <p class="text-sm font-weight-bold">Atividade Económica Principal (Opcional)</p>
                            <p class="text-xs">Pode adicionar a atividade principal aqui. Atividades adicionais podem ser inseridas na página de edição.</p>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="atividade_tipo" class="form-control-label">Tipo de Atividade</label>
                                        <select class="form-control" name="atividade_tipo" id="atividade_tipo">
                                            <option value="">-- Nenhuma --</option>
                                            <option value="conta_propria" {{ old('atividade_tipo') == 'conta_propria' ? 'selected' : '' }}>Conta Própria</option>
                                            <option value="conta_outrem" {{ old('atividade_tipo') == 'conta_outrem' ? 'selected' : '' }}>Conta de Outrem</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="atividade_setor_id" class="form-control-label">Setor de Atividade</label>
                                        <select class="form-control" name="atividade_setor_id" id="atividade_setor_id">
                                            <option value="">-- Nenhuma --</option>
                                            @foreach ($setores as $setor)
                                                <option value="{{ $setor->id }}" {{ old('atividade_setor_id') == $setor->id ? 'selected' : '' }}>
                                                    {{ $setor->nome }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="atividade_descricao" class="form-control-label">Descrição da Atividade (opcional)</label>
                                <textarea class="form-control" name="atividade_descricao" id="atividade_descricao" rows="2" placeholder="Descreva brevemente a atividade...">{{ old('atividade_descricao') }}</textarea>
                            </div>

                            <div class="text-end">
                                <a href="{{ route('freguesia.familias.index') }}" class="btn btn-secondary mt-4">Cancelar</a>
                                <button type="submit" class="btn bg-gradient-success mt-4">Guardar Família</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    {{-- Usar CDN JS para garantir compatibilidade --}}
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    
    <script>
        document.addEventListener("DOMContentLoaded", function(event) {
            if (document.getElementById('nacionalidade-select')) {
                var element = document.getElementById('nacionalidade-select');
                const choices = new Choices(element, {
                    searchEnabled: true,
                    searchPlaceholderValue: 'Pesquisar nacionalidade...',
                    itemSelectText: '',
                    noResultsText: 'Nenhuma nacionalidade encontrada',
                    shouldSort: false, // Mantém a ordem alfabética que vem do backend
                });
            }
        });
    </script>
@endpush