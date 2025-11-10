@extends('layouts.user_type.auth')

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
                                        <input class="form-control" type="number" name="ano_instalacao" id="ano_instalacao" placeholder="Ex: 2024" value="{{ old('ano_instalacao') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nacionalidade" class="form-control-label">Nacionalidade *</label>
                                        <input class="form-control" type="text" name="nacionalidade" id="nacionalidade" value="{{ old('nacionalidade') }}" required>
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
                            
                            {{-- ***** NOVO CAMPO ADICIONADO ***** --}}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="localizacao" class="form-control-label">Localização (Perg. 11-13) *</label>
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

                            <p class="text-sm font-weight-bold">Agregado Familiar</p>
                            <div class="row">
                                 <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="adultos_laboral" class="form-control-label">Nº Adultos (Idade Laboral) *</label>
                                        <input class="form-control" type="number" name="adultos_laboral" id="adultos_laboral" value="{{ old('adultos_laboral', 0) }}" min="0" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="adultos_65_mais" class="form-control-label">Nº Adultos (65+ anos) *</label>
                                        <input class="form-control" type="number" name="adultos_65_mais" id="adultos_65_mais" value="{{ old('adultos_65_mais', 0) }}" min="0" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="criancas" class="form-control-label">Nº Crianças / Jovens *</label>
                                        <input class="form-control" type="number" name="criancas" id="criancas" value="{{ old('criancas', 0) }}" min="0" required>
                                    </div>
                                </div>
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