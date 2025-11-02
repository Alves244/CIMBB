@extends('layouts.user_type.auth')

@section('content')

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12 col-lg-8 mx-auto">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Editar Família</h6>
                        <p class="text-sm">Código: {{ $familia->codigo }}</p>
                    </div>
                    <div class="card-body">
                        {{-- O formulário faz POST para a rota 'update', mas usa o método PUT --}}
                        <form action="{{ route('freguesia.familias.update', $familia->id) }}" method="POST" role="form text-left">
                            @csrf
                            @method('PUT') {{-- Importante para dizer ao Laravel que é uma atualização --}}

                            <p class="text-sm font-weight-bold">Informação Base da Família</p>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="codigo_display" class="form-control-label">Código Único</label>
                                        {{-- O código não pode ser editado --}}
                                        <input class="form-control" type="text" id="codigo_display" value="{{ $familia->codigo }}" disabled readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="ano_instalacao" class="form-control-label">Ano de Instalação *</label>
                                        {{-- Preenche com o valor antigo (se a validação falhar) OU com o valor atual da família --}}
                                        <input class="form-control" type="number" name="ano_instalacao" id="ano_instalacao" value="{{ old('ano_instalacao', $familia->ano_instalacao) }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nacionalidade" class="form-control-label">Nacionalidade *</label>
                                        <input class="form-control" type="text" name="nacionalidade" id="nacionalidade" value="{{ old('nacionalidade', $familia->nacionalidade) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tipologia_habitacao" class="form-control-label">Tipologia de Habitação *</label>
                                        <select class="form-control" name="tipologia_habitacao" id="tipologia_habitacao" required>
                                            <option value="casa" {{ old('tipologia_habitacao', $familia->tipologia_habitacao) == 'casa' ? 'selected' : '' }}>Casa</option>
                                            <option value="quinta" {{ old('tipologia_habitacao', $familia->tipologia_habitacao) == 'quinta' ? 'selected' : '' }}>Quinta / Monte</option>
                                            <option value="apartamento" {{ old('tipologia_habitacao', $familia->tipologia_habitacao) == 'apartamento' ? 'selected' : '' }}>Apartamento</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tipologia_propriedade" class="form-control-label">Tipologia de Propriedade *</label>
                                        <select class="form-control" name="tipologia_propriedade" id="tipologia_propriedade" required>
                                            <option value="propria" {{ old('tipologia_propriedade', $familia->tipologia_propriedade) == 'propria' ? 'selected' : '' }}>Própria</option>
                                            <option value="arrendada" {{ old('tipologia_propriedade', $familia->tipologia_propriedade) == 'arrendada' ? 'selected' : '' }}>Arrendada</option>
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
                                        <input class="form-control" type="number" name="adultos_laboral" id="adultos_laboral" value="{{ old('adultos_laboral', $familia->agregadoFamiliar->adultos_laboral ?? 0) }}" min="0" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="adultos_65_mais" class="form-control-label">Nº Adultos (65+ anos) *</label>
                                        <input class="form-control" type="number" name="adultos_65_mais" id="adultos_65_mais" value="{{ old('adultos_65_mais', $familia->agregadoFamiliar->adultos_65_mais ?? 0) }}" min="0" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="criancas" class="form-control-label">Nº Crianças / Jovens *</label>
                                        <input class="form-control" type="number" name="criancas" id="criancas" value="{{ old('criancas', $familia->agregadoFamiliar->criancas ?? 0) }}" min="0" required>
                                    </div>
                                </div>
                            </div>

                            <div class="text-end">
                                <a href="{{ route('freguesia.familias.index') }}" class="btn btn-secondary mt-4">Cancelar</a>
                                <button type="submit" class="btn bg-gradient-success mt-4">Guardar Alterações</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
