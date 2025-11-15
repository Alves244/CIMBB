@extends('layouts.user_type.auth')

@push('css')
    <link href="{{ asset('assets/css/plugins/choices.min.css') }}" rel="stylesheet" />
@endpush

@section('content')

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12 col-lg-8 mx-auto">
                {{-- CARD 1: FORMULÁRIO PRINCIPAL DA FAMÍLIA --}}
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
                                        <input class="form-control" type="number" name="ano_instalacao" id="ano_instalacao" value="{{ old('ano_instalacao', $familia->ano_instalacao) }}" required min="1900">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{-- 2. CAMPO DE NACIONALIDADE --}}
                                        <label for="nacionalidade-select" class="form-control-label">Nacionalidade *</label>
                                        <select class="form-control" name="nacionalidade" id="nacionalidade-select" required>
                                            <option value="" disabled>-- Pesquise ou selecione --</option>
                                            @foreach ($nacionalidades as $nacionalidade)
                                                <option value="{{ $nacionalidade }}" {{ old('nacionalidade', $familia->nacionalidade) == $nacionalidade ? 'selected' : '' }}>
                                                    {{ $nacionalidade }}
                                                </option>
                                            @endforeach
                                        </select>
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
                                        <label for="localizacao" class="form-control-label">Localização*</label>
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
                            @php $agregado = $familia->agregadoFamiliar; @endphp
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-control-label">Adultos (Idade Laboral)</label>
                                    <div class="input-group mb-1"><span class="input-group-text" style="width: 40px;">M</span><input class="form-control" type="number" name="adultos_laboral_m" value="{{ old('adultos_laboral_m', $agregado->adultos_laboral_m ?? 0) }}" min="0" required></div>
                                    <div class="input-group mb-1"><span class="input-group-text" style="width: 40px;">F</span><input class="form-control" type="number" name="adultos_laboral_f" value="{{ old('adultos_laboral_f', $agregado->adultos_laboral_f ?? 0) }}" min="0" required></div>
                                    <div class="input-group mb-3"><span class="input-group-text" style="width: 40px;">N/S</span><input class="form-control" type="number" name="adultos_laboral_n" value="{{ old('adultos_laboral_n', $agregado->adultos_laboral_n ?? 0) }}" min="0" required></div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-control-label">Adultos (65+ anos)</label>
                                    <div class="input-group mb-1"><span class="input-group-text" style="width: 40px;">M</span><input class="form-control" type="number" name="adultos_65_mais_m" value="{{ old('adultos_65_mais_m', $agregado->adultos_65_mais_m ?? 0) }}" min="0" required></div>
                                    <div class="input-group mb-1"><span class="input-group-text" style="width: 40px;">F</span><input class="form-control" type="number" name="adultos_65_mais_f" value="{{ old('adultos_65_mais_f', $agregado->adultos_65_mais_f ?? 0) }}" min="0" required></div>
                                    <div class="input-group mb-3"><span class="input-group-text" style="width: 40px;">N/S</span><input class="form-control" type="number" name="adultos_65_mais_n" value="{{ old('adultos_65_mais_n', $agregado->adultos_65_mais_n ?? 0) }}" min="0" required></div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-control-label">Crianças / Jovens</label>
                                    <div class="input-group mb-1"><span class="input-group-text" style="width: 40px;">M</span><input class="form-control" type="number" name="criancas_m" value="{{ old('criancas_m', $agregado->criancas_m ?? 0) }}" min="0" required></div>
                                    <div class="input-group mb-1"><span class="input-group-text" style="width: 40px;">F</span><input class="form-control" type="number" name="criancas_f" value="{{ old('criancas_f', $agregado->criancas_f ?? 0) }}" min="0" required></div>
                                    <div class="input-group mb-3"><span class="input-group-text" style="width: 40px;">N/S</span><input class="form-control" type="number" name="criancas_n" value="{{ old('criancas_n', $agregado->criancas_n ?? 0) }}" min="0" required></div>
                                </div>
                            </div>
                            
                            <div class="text-end">
                                <a href="{{ route('freguesia.familias.index') }}" class="btn btn-secondary mt-4">Cancelar</a>
                                <button type="submit" class="btn bg-gradient-success mt-4">Guardar Alterações</button>
                            </div>
                        </form>
                    </div>
                </div>
                {{-- ATIVIDADES ECONÓMICAS (Mantém-se igual) --}}
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
    <script src="{{ asset('assets/js/plugins/choices.min.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function(event) {
            if (document.getElementById('nacionalidade-select')) {
                var element = document.getElementById('nacionalidade-select');
                new Choices(element, {
                    searchEnabled: true,
                    searchPlaceholderValue: 'Pesquisar...',
                    itemSelectText: 'Clicas para selecionar',
                    noResultsText: 'Nacionalidade não encontrada',
                });
            }
        });
    </script>
@endpush