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
                        <form action="{{ route('freguesia.familias.update', $familia->id) }}" method="POST" role="form text-left">
                            @csrf
                            @method('PUT')
                            
                            <p class="text-sm font-weight-bold">Informação Base da Família</p>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="codigo_display" class="form-control-label">Código Único</label>
                                        <input class="form-control" type="text" id="codigo_display" value="{{ $familia->codigo }}" disabled readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="ano_instalacao" class="form-control-label">Ano de Instalação *</label>
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
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="localizacao" class="form-control-label">Localização (Perg. 11-13) *</label>
                                        <select class="form-control" name="localizacao" id="localizacao" required>
                                            <option value="nucleo_urbano" {{ old('localizacao', $familia->localizacao) == 'nucleo_urbano' ? 'selected' : '' }}>Núcleo Urbano (Sede Freguesia)</option>
                                            <option value="aldeia_anexa" {{ old('localizacao', $familia->localizacao) == 'aldeia_anexa' ? 'selected' : '' }}>Aldeia Anexa</option>
                                            <option value="espaco_agroflorestal" {{ old('localizacao', $familia->localizacao) == 'espaco_agroflorestal' ? 'selected' : '' }}>Quinta / Espaço Agroflorestal</option>
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

                <div class="card mt-4">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Atividades Económicas</h6>
                        <a href="{{ route('freguesia.familias.atividades.create', $familia->id) }}" class="btn bg-gradient-success btn-sm mb-0">
                            <i class="fas fa-plus me-1"></i> Adicionar Atividade
                        </a>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tipo</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Setor de Atividade</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Descrição</th>
                                        <th class="text-secondary opacity-7">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($familia->atividadesEconomicas as $atividade)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-3 py-1">
                                                    <h6 class="mb-0 text-sm">{{ $atividade->tipo == 'conta_propria' ? 'Conta Própria' : 'Conta Outrem' }}</h6>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">{{ $atividade->setorAtividade->nome ?? 'N/A' }}</p>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">{{ Str::limit($atividade->descricao, 50) ?? 'N/A' }}</p>
                                            </td>
                                            <td class="align-middle">
                                                <a href="{{ route('freguesia.atividades.edit', $atividade->id) }}" class="btn btn-link text-success text-gradient px-1 mb-0">
                                                    <i class="fas fa-pencil-alt text-sm"></i>
                                                </a>
                                                <form action="{{ route('freguesia.atividades.destroy', $atividade->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-link text-danger text-gradient px-1 mb-0"
                                                            onclick="return confirm('Tem a certeza que deseja apagar esta atividade?')">
                                                        <i class="fas fa-trash text-sm"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-sm py-4">Nenhuma atividade económica registada para esta família.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('js')
  <script>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
    })
  </script>
@endpush