@php
    $editing = isset($familia);
    $agregado = optional($familia)->agregadoFamiliar;
    $adultosExistentes = old('adultos', $editing
        ? $familia->atividadesEconomicas->map(function ($atividade) use ($setores) {
            $setor = $atividade->setorAtividade ?? $setores->firstWhere('id', $atividade->setor_id);

            return [
                'identificador' => $atividade->identificador,
                'situacao' => $atividade->tipo,
                'vinculo' => $atividade->vinculo,
                'setor_id' => $atividade->setor_id,
                'local_trabalho' => $atividade->local_trabalho,
                'descricao' => $atividade->descricao,
                'macro_grupo' => $setor->macro_grupo ?? null,
            ];
        })->toArray()
        : []);

    $adultosLaboralM = old('adultos_laboral_m', $agregado->adultos_laboral_m ?? 0);
    $adultosLaboralF = old('adultos_laboral_f', $agregado->adultos_laboral_f ?? 0);
    $adultosLaboralN = old('adultos_laboral_n', $agregado->adultos_laboral_n ?? 0);
    $adultosSeniorM = old('adultos_senior_m', $agregado->adultos_65_mais_m ?? 0);
    $adultosSeniorF = old('adultos_senior_f', $agregado->adultos_65_mais_f ?? 0);
    $adultosSeniorN = old('adultos_senior_n', $agregado->adultos_65_mais_n ?? 0);
    $criancasM = old('criancas_m', $agregado->criancas_m ?? 0);
    $criancasF = old('criancas_f', $agregado->criancas_f ?? 0);
    $criancasN = old('criancas_n', $agregado->criancas_n ?? 0);
    $adultosLaboralTotal = $adultosLaboralM + $adultosLaboralF + $adultosLaboralN;
    $adultosSeniorTotal = $adultosSeniorM + $adultosSeniorF + $adultosSeniorN;
    $criancasTotal = $criancasM + $criancasF + $criancasN;

    $localizacaoTipo = old('localizacao_tipo', optional($familia)->localizacao_tipo ?? 'sede_freguesia');
    $localizacaoDetalhe = old('localizacao_detalhe', optional($familia)->localizacao_detalhe ?? '');
    $tipologiaHabitacao = old('tipologia_habitacao', optional($familia)->tipologia_habitacao ?? 'moradia');
    $tipologiaPropriedade = old('tipologia_propriedade', optional($familia)->tipologia_propriedade ?? 'propria');
    $condicaoAlojamento = old('condicao_alojamento', optional($familia)->condicao_alojamento ?? 'bom_estado');
    $inscritoCentroSaude = filter_var(old('inscrito_centro_saude', optional($familia)->inscrito_centro_saude ?? false), FILTER_VALIDATE_BOOLEAN);
    $inscritoEscola = old('inscrito_escola', optional($familia)->inscrito_escola ?? 'nao_sei');
    $necessidadesSelecionadas = collect(old('necessidades_apoio', optional($familia)->necessidades_apoio ?? []))->filter()->values()->all();
    $estruturaSelecionada = collect(old('estrutura_familiar', $agregado->estrutura_familiar ?? []))->filter()->values()->all();
    $macroGrupos = $formOptions['macroGrupos'] ?? [];
    $setoresAgrupados = $setores->groupBy('macro_grupo');
    $setoresDataset = $setoresAgrupados->map(function ($colecao) {
        return $colecao->map(function ($setor) {
            return [
                'id' => $setor->id,
                'nome' => $setor->nome,
            ];
        })->values();
    });

    $labelsHabitacao = [
        'moradia' => 'Moradia',
        'apartamento' => 'Apartamento',
        'caravana_tenda' => 'Caravana / Tenda',
        'anexo' => 'Anexo / construção secundária',
        'outro' => 'Outro',
    ];

    $labelsPropriedade = [
        'propria' => 'Própria',
        'arrendada' => 'Arrendada',
        'cedida' => 'Cedida (família / entidade / empregador)',
        'outra' => 'Outra situação',
    ];

    $labelsLocalizacao = [
        'sede_freguesia' => 'Sede da Freguesia',
        'lugar_aldeia' => 'Lugar / aldeia',
        'espaco_agroflorestal' => 'Espaço agroflorestal / quinta isolada',
    ];

    $labelsCondicao = [
        'bom_estado' => 'Bom estado',
        'estado_razoavel' => 'Estado razoável',
        'necessita_reparacoes' => 'Necessita de reparações relevantes',
        'situacao_precaria' => 'Situação precária',
    ];

    $labelsNecessidades = [
        'lingua_portuguesa' => 'Língua portuguesa',
        'acesso_emprego' => 'Acesso a emprego',
        'habitacao' => 'Habitação',
        'regularizacao_administrativa' => 'Regularização administrativa',
        'transporte_mobilidade' => 'Transporte / mobilidade',
        'apoio_social' => 'Apoio social',
    ];

    $labelsEstrutura = [
        'casal_com_filhos' => 'Casal com filhos',
        'casal_sem_filhos' => 'Casal sem filhos',
        'monoparental' => 'Monoparental',
        'familia_alargada' => 'Família alargada',
        'coabitacao_informal' => 'Coabitação informal',
        'outra' => 'Outra',
    ];

    $labelsSituacao = [
        'conta_propria' => 'Atividade por conta própria / empreendedor',
        'conta_outrem' => 'Trabalhador por conta de outrem',
        'prestacao_servicos' => 'Prestação de serviços / recibos verdes',
        'desempregado' => 'Desempregado',
        'estudante' => 'Estudante',
        'outra_situacao' => 'Outra situação',
    ];

    $labelsVinculo = [
        'empregado' => 'Empregado',
        'estagiario' => 'Estagiário',
        'outro' => 'Outro vínculo',
    ];

    $labelsEscola = [
        'sim' => 'Sim',
        'nao' => 'Não',
        'nao_sei' => 'Não sei',
    ];
@endphp

@if ($errors->any())
    <div class="alert alert-danger">
        <p class="mb-2 fw-bold">Foram encontrados alguns erros:</p>
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<p class="text-sm text-muted">Campos assinalados com * são de preenchimento obrigatório.</p>

<div class="mb-4">
    <h6 class="text-dark">1. Identificação da família / alojamento</h6>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label class="form-control-label" for="ano_instalacao">Ano de Instalação *</label>
                <input type="number" class="form-control" id="ano_instalacao" name="ano_instalacao" min="1900" max="{{ date('Y') }}" value="{{ old('ano_instalacao', optional($familia)->ano_instalacao) }}" required>
            </div>
        </div>
        <div class="col-md-8">
            <div class="form-group">
                <label class="form-control-label" for="nacionalidade-select">Nacionalidade principal *</label>
                <select class="form-control" id="nacionalidade-select" name="nacionalidade" required>
                    <option value="" disabled {{ old('nacionalidade', optional($familia)->nacionalidade) ? '' : 'selected' }}>Selecione ou pesquise</option>
                    @foreach ($nacionalidades as $nacionalidade)
                        <option value="{{ $nacionalidade }}" {{ old('nacionalidade', optional($familia)->nacionalidade) === $nacionalidade ? 'selected' : '' }}>{{ $nacionalidade }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>

<div class="mb-4">
    <h6 class="text-dark">2. Localização da habitação *</h6>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="form-control-label" for="localizacao_tipo">Tipo de localização</label>
                <select class="form-control" name="localizacao_tipo" id="localizacao_tipo" required>
                    @foreach ($formOptions['localizacoes'] as $valor)
                        <option value="{{ $valor }}" {{ $localizacaoTipo === $valor ? 'selected' : '' }}>{{ $labelsLocalizacao[$valor] }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6" id="localizacao-detalhe-wrapper">
            <div class="form-group">
                <label class="form-control-label" for="localizacao_detalhe">Detalhe (lugar / aldeia) *</label>
                <input type="text" class="form-control" name="localizacao_detalhe" id="localizacao_detalhe" value="{{ $localizacaoDetalhe }}" placeholder="Ex.: Bairro da Serra">
            </div>
        </div>
    </div>
</div>

<div class="mb-4">
    <h6 class="text-dark">3. Características do alojamento *</h6>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label class="form-control-label" for="tipologia_habitacao">Tipologia</label>
                <select class="form-control" name="tipologia_habitacao" id="tipologia_habitacao" required>
                    @foreach ($formOptions['tipologiasHabitacao'] as $valor)
                        <option value="{{ $valor }}" {{ $tipologiaHabitacao === $valor ? 'selected' : '' }}>{{ $labelsHabitacao[$valor] }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label class="form-control-label" for="tipologia_propriedade">Regime de propriedade</label>
                <select class="form-control" name="tipologia_propriedade" id="tipologia_propriedade" required>
                    @foreach ($formOptions['regimesPropriedade'] as $valor)
                        <option value="{{ $valor }}" {{ $tipologiaPropriedade === $valor ? 'selected' : '' }}>{{ $labelsPropriedade[$valor] }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label class="form-control-label" for="condicao_alojamento">Condição do alojamento</label>
                <select class="form-control" name="condicao_alojamento" id="condicao_alojamento" required>
                    @foreach ($formOptions['condicoesAlojamento'] as $valor)
                        <option value="{{ $valor }}" {{ $condicaoAlojamento === $valor ? 'selected' : '' }}>{{ $labelsCondicao[$valor] }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>

<div class="mb-4">
    <h6 class="text-dark">4. Caracterização do agregado familiar *</h6>
    <div class="table-responsive mb-3">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th class="text-secondary text-xs">Faixa etária</th>
                    <th class="text-secondary text-xs">Masculino</th>
                    <th class="text-secondary text-xs">Feminino</th>
                    <th class="text-secondary text-xs">Não declarado</th>
                    <th class="text-secondary text-xs">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-sm">Adultos idade laboral (18–65)</td>
                    <td style="width: 140px">
                        <input type="number" class="form-control" id="adultos_laboral_m" name="adultos_laboral_m" min="0" value="{{ $adultosLaboralM }}" required>
                    </td>
                    <td style="width: 140px">
                        <input type="number" class="form-control" id="adultos_laboral_f" name="adultos_laboral_f" min="0" value="{{ $adultosLaboralF }}" required>
                    </td>
                    <td style="width: 140px">
                        <input type="number" class="form-control" id="adultos_laboral_n" name="adultos_laboral_n" min="0" value="{{ $adultosLaboralN }}" required>
                    </td>
                    <td style="width: 140px">
                        <input type="number" class="form-control bg-light" id="adultos_laboral_total" value="{{ $adultosLaboralTotal }}" readonly>
                    </td>
                </tr>
                <tr>
                    <td class="text-sm">Adultos seniores (&gt; 65)</td>
                    <td>
                        <input type="number" class="form-control" id="adultos_senior_m" name="adultos_senior_m" min="0" value="{{ $adultosSeniorM }}" required>
                    </td>
                    <td>
                        <input type="number" class="form-control" id="adultos_senior_f" name="adultos_senior_f" min="0" value="{{ $adultosSeniorF }}" required>
                    </td>
                    <td>
                        <input type="number" class="form-control" id="adultos_senior_n" name="adultos_senior_n" min="0" value="{{ $adultosSeniorN }}" required>
                    </td>
                    <td>
                        <input type="number" class="form-control bg-light" id="adultos_senior_total" value="{{ $adultosSeniorTotal }}" readonly>
                    </td>
                </tr>
                <tr>
                    <td class="text-sm">Crianças / jovens (&lt; 18)</td>
                    <td>
                        <input type="number" class="form-control" id="criancas_m" name="criancas_m" min="0" value="{{ $criancasM }}" required>
                    </td>
                    <td>
                        <input type="number" class="form-control" id="criancas_f" name="criancas_f" min="0" value="{{ $criancasF }}" required>
                    </td>
                    <td>
                        <input type="number" class="form-control" id="criancas_n" name="criancas_n" min="0" value="{{ $criancasN }}" required>
                    </td>
                    <td>
                        <input type="number" class="form-control bg-light" id="criancas_total" value="{{ $criancasTotal }}" readonly>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="row mb-3">
        <div class="col-md-3 ms-auto">
            <label class="form-control-label">Total de residentes</label>
            <input type="number" class="form-control bg-light" id="total_residentes" readonly>
        </div>
    </div>
    <div class="mt-3">
        <p class="text-sm mb-2">Estrutura familiar (assinale todas as opções aplicáveis)</p>
        <div class="row">
            @foreach ($formOptions['estruturasFamiliares'] as $valor)
                <div class="col-sm-6 col-lg-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="estrutura_familiar[]" id="estrutura_{{ $valor }}" value="{{ $valor }}" {{ in_array($valor, $estruturaSelecionada, true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="estrutura_{{ $valor }}">{{ $labelsEstrutura[$valor] }}</label>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div class="mb-4">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h6 class="text-dark mb-0">5. Situação socioprofissional dos adultos</h6>
            <p class="text-xs text-muted mb-0">Registe cada adulto em idade laboral. Pode adicionar ou remover linhas conforme necessário.</p>
        </div>
        <button type="button" class="btn btn-outline-primary btn-sm" id="adicionar-adulto">
            <i class="fas fa-plus me-1"></i> Adicionar adulto
        </button>
    </div>
    <div class="mt-3" id="adultos-container" data-next-index="{{ count($adultosExistentes) }}">
        @forelse ($adultosExistentes as $index => $adulto)
            <div class="card card-body border adulto-row mb-3" data-index="{{ $index }}">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <h6 class="mb-0">Adulto #<span class="adulto-label">{{ $index + 1 }}</span></h6>
                    <button type="button" class="btn btn-link text-danger text-sm remove-adulto">Remover</button>
                </div>
                <div class="row g-3">
                    <div class="col-md-2">
                        <label class="form-control-label">Identificador</label>
                        <input type="text" name="adultos[{{ $index }}][identificador]" class="form-control" value="{{ $adulto['identificador'] ?? '' }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-control-label">Situação *</label>
                        <select class="form-control" name="adultos[{{ $index }}][situacao]">
                            <option value="" disabled {{ empty($adulto['situacao']) ? 'selected' : '' }}>Selecione</option>
                            @foreach ($formOptions['situacoesSociais'] as $valor)
                                <option value="{{ $valor }}" {{ ($adulto['situacao'] ?? '') === $valor ? 'selected' : '' }}>{{ $labelsSituacao[$valor] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-control-label">Grande grupo *</label>
                        <select class="form-control macro-grupo-select" name="adultos[{{ $index }}][macro_grupo]" data-selected="{{ $adulto['macro_grupo'] ?? '' }}" data-target="adulto-setor-{{ $index }}">
                            <option value="" {{ empty($adulto['macro_grupo']) ? 'selected' : '' }}>Selecione</option>
                            @foreach ($macroGrupos as $valor => $label)
                                <option value="{{ $valor }}" {{ ($adulto['macro_grupo'] ?? '') === $valor ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-control-label">Vínculo</label>
                        <select class="form-control" name="adultos[{{ $index }}][vinculo]">
                            <option value="" {{ empty($adulto['vinculo']) ? 'selected' : '' }}>Não aplicável</option>
                            @foreach ($formOptions['vinculosProfissionais'] as $valor)
                                <option value="{{ $valor }}" {{ ($adulto['vinculo'] ?? '') === $valor ? 'selected' : '' }}>{{ $labelsVinculo[$valor] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-control-label">Atividade específica *</label>
                        <select class="form-control setor-select" name="adultos[{{ $index }}][setor_id]" id="adulto-setor-{{ $index }}" data-selected="{{ $adulto['setor_id'] ?? '' }}">
                            <option value="" {{ empty($adulto['setor_id']) ? 'selected' : '' }}>Selecione a atividade</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-control-label">Local de trabalho</label>
                        <input type="text" class="form-control" name="adultos[{{ $index }}][local_trabalho]" value="{{ $adulto['local_trabalho'] ?? '' }}" placeholder="Município / empresa">
                    </div>
                    <div class="col-md-8">
                        <label class="form-control-label">Notas / descrição</label>
                        <input type="text" class="form-control" name="adultos[{{ $index }}][descricao]" value="{{ $adulto['descricao'] ?? '' }}" placeholder="Detalhes adicionais (opcional)">
                    </div>
                </div>
            </div>
        @empty
            <p class="text-sm text-muted" id="adultos-empty">Ainda não foram registados adultos.</p>
        @endforelse
    </div>
</div>

<div class="mb-4">
    <h6 class="text-dark">6. Integração e serviços</h6>
    <div class="row g-3">
        <div class="col-md-4">
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" id="inscrito_centro_saude" name="inscrito_centro_saude" value="1" {{ $inscritoCentroSaude ? 'checked' : '' }}>
                <label class="form-check-label" for="inscrito_centro_saude">Inscritos no centro de saúde</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label class="form-control-label" for="inscrito_escola">Inscritos no agrupamento de escolas</label>
                <select class="form-control" id="inscrito_escola" name="inscrito_escola">
                    @foreach ($formOptions['opcoesEscola'] as $valor)
                        <option value="{{ $valor }}" {{ $inscritoEscola === $valor ? 'selected' : '' }}>{{ $labelsEscola[$valor] }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="mt-3">
        <p class="text-sm mb-2">Necessidades de apoio identificadas</p>
        <div class="row">
            @foreach ($formOptions['necessidadesApoio'] as $valor)
                <div class="col-sm-6 col-lg-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="necessidades_apoio[]" id="apoio_{{ $valor }}" value="{{ $valor }}" {{ in_array($valor, $necessidadesSelecionadas, true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="apoio_{{ $valor }}">{{ $labelsNecessidades[$valor] }}</label>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div class="mb-4">
    <h6 class="text-dark">7. Observações</h6>
    <textarea class="form-control" name="observacoes" rows="3" placeholder="Notas adicionais sobre a família, outras nacionalidades, etc.">{{ old('observacoes', optional($familia)->observacoes) }}</textarea>
</div>

<div class="text-end">
    <a href="{{ route('freguesia.familias.index') }}" class="btn btn-secondary me-2">Cancelar</a>
    <button type="submit" class="btn bg-gradient-success">{{ $submitLabel }}</button>
</div>

<template id="adulto-row-template">
    <div class="card card-body border adulto-row mb-3" data-index="__INDEX__">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <h6 class="mb-0">Adulto #<span class="adulto-label">__HUMAN__</span></h6>
            <button type="button" class="btn btn-link text-danger text-sm remove-adulto">Remover</button>
        </div>
        <div class="row g-3">
            <div class="col-md-2">
                <label class="form-control-label">Identificador</label>
                <input type="text" name="adultos[__INDEX__][identificador]" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-control-label">Situação *</label>
                <select class="form-control" name="adultos[__INDEX__][situacao]">
                    <option value="" disabled selected>Selecione</option>
                    @foreach ($formOptions['situacoesSociais'] as $valor)
                        <option value="{{ $valor }}">{{ $labelsSituacao[$valor] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-control-label">Grande grupo *</label>
                <select class="form-control macro-grupo-select" name="adultos[__INDEX__][macro_grupo]" data-target="adulto-setor-__INDEX__">
                    <option value="" selected>Selecione</option>
                    @foreach ($macroGrupos as $valor => $label)
                        <option value="{{ $valor }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-control-label">Vínculo</label>
                <select class="form-control" name="adultos[__INDEX__][vinculo]">
                    <option value="" selected>Não aplicável</option>
                    @foreach ($formOptions['vinculosProfissionais'] as $valor)
                        <option value="{{ $valor }}">{{ $labelsVinculo[$valor] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-control-label">Atividade específica *</label>
                <select class="form-control setor-select" name="adultos[__INDEX__][setor_id]" id="adulto-setor-__INDEX__">
                    <option value="" selected>Selecione a atividade</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-control-label">Local de trabalho</label>
                <input type="text" class="form-control" name="adultos[__INDEX__][local_trabalho]" placeholder="Município / empresa">
            </div>
            <div class="col-md-8">
                <label class="form-control-label">Notas / descrição</label>
                <input type="text" class="form-control" name="adultos[__INDEX__][descricao]" placeholder="Detalhes adicionais (opcional)">
            </div>
        </div>
    </div>
</template>

@once
    @push('css')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
        <style>
            #adultos-container .card {
                box-shadow: none;
            }
        </style>
    @endpush

    @push('js')
        <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const setoresPorGrupo = @json($setoresDataset);
                const encontrarMacroPorSetor = (setorId) => {
                    if (!setorId) {
                        return '';
                    }
                    const alvo = setorId.toString();
                    for (const [grupo, setores] of Object.entries(setoresPorGrupo)) {
                        if (setores.some(setor => setor.id.toString() === alvo)) {
                            return grupo;
                        }
                    }
                    return '';
                };

                const preencherAtividades = (setorSelect, grupo, selectedValue = '') => {
                    if (!setorSelect) {
                        return;
                    }

                    const placeholder = document.createElement('option');
                    placeholder.value = '';
                    placeholder.textContent = 'Selecione a atividade';
                    placeholder.selected = true;
                    placeholder.disabled = false;

                    setorSelect.innerHTML = '';
                    setorSelect.appendChild(placeholder);

                    const lista = setoresPorGrupo[grupo] || [];
                    lista.forEach(setor => {
                        const option = document.createElement('option');
                        option.value = setor.id;
                        option.textContent = setor.nome;
                        if (selectedValue && selectedValue.toString() === setor.id.toString()) {
                            option.selected = true;
                            placeholder.selected = false;
                        }
                        setorSelect.appendChild(option);
                    });

                    if (!lista.length) {
                        setorSelect.value = '';
                    }
                };

                const inicializarGrupoSelect = (row) => {
                    const macroSelect = row.querySelector('.macro-grupo-select');
                    const setorSelect = row.querySelector('.setor-select');
                    if (!macroSelect || !setorSelect) {
                        return;
                    }

                    const setorSelecionado = setorSelect.dataset.selected || setorSelect.value || '';
                    let macroSelecionado = macroSelect.dataset.selected || macroSelect.value || '';

                    if (!macroSelecionado && setorSelecionado) {
                        macroSelecionado = encontrarMacroPorSetor(setorSelecionado);
                    }

                    if (macroSelecionado) {
                        macroSelect.value = macroSelecionado;
                        preencherAtividades(setorSelect, macroSelecionado, setorSelecionado);
                    } else {
                        preencherAtividades(setorSelect, '', setorSelecionado);
                    }

                    macroSelect.addEventListener('change', (event) => {
                        setorSelect.dataset.selected = '';
                        preencherAtividades(setorSelect, event.target.value);
                    });
                };

                const nacionalidadeSelect = document.getElementById('nacionalidade-select');
                if (nacionalidadeSelect) {
                    new Choices(nacionalidadeSelect, {
                        searchPlaceholderValue: 'Pesquisar nacionalidade...',
                        itemSelectText: '',
                        shouldSort: true,
                    });
                }

                const gruposEtarios = [
                    { inputs: ['adultos_laboral_m', 'adultos_laboral_f', 'adultos_laboral_n'], totalId: 'adultos_laboral_total' },
                    { inputs: ['adultos_senior_m', 'adultos_senior_f', 'adultos_senior_n'], totalId: 'adultos_senior_total' },
                    { inputs: ['criancas_m', 'criancas_f', 'criancas_n'], totalId: 'criancas_total' },
                ];
                const totalField = document.getElementById('total_residentes');
                const obterValor = (id) => {
                    const value = parseInt(document.getElementById(id)?.value || '0', 10);
                    return isNaN(value) ? 0 : value;
                };
                const atualizarTotais = () => {
                    let totalResidentes = 0;
                    gruposEtarios.forEach(grupo => {
                        const subtotal = grupo.inputs.reduce((acc, id) => acc + obterValor(id), 0);
                        const destino = document.getElementById(grupo.totalId);
                        if (destino) {
                            destino.value = subtotal;
                        }
                        totalResidentes += subtotal;
                    });
                    if (totalField) {
                        totalField.value = totalResidentes;
                    }
                };
                gruposEtarios
                    .flatMap(grupo => grupo.inputs)
                    .forEach(id => document.getElementById(id)?.addEventListener('input', atualizarTotais));
                atualizarTotais();

                const container = document.getElementById('adultos-container');
                const addButton = document.getElementById('adicionar-adulto');
                const template = document.getElementById('adulto-row-template');
                const emptyState = document.getElementById('adultos-empty');
                let nextIndex = parseInt(container?.dataset.nextIndex || '0', 10);

                const toggleEmptyState = () => {
                    if (!container) return;
                    const hasRows = container.querySelectorAll('.adulto-row').length > 0;
                    if (emptyState) {
                        emptyState.classList.toggle('d-none', hasRows);
                    }
                };

                const attachRowEvents = (row) => {
                    const removeBtn = row.querySelector('.remove-adulto');
                    removeBtn?.addEventListener('click', () => {
                        row.remove();
                        toggleEmptyState();
                    });
                };

                const addRow = (data = {}) => {
                    if (!template || !container) return;
                    const wrapper = document.createElement('div');
                    wrapper.innerHTML = template.innerHTML.replace(/__INDEX__/g, nextIndex).replace(/__HUMAN__/g, nextIndex + 1);
                    const row = wrapper.firstElementChild;
                    container.appendChild(row);

                    const fillField = (selector, value) => {
                        const field = row.querySelector(selector);
                        if (field && value !== undefined && value !== null) {
                            field.value = value;
                        }
                    };

                    fillField(`[name="adultos[${nextIndex}][identificador]"]`, data.identificador || '');
                    fillField(`[name="adultos[${nextIndex}][situacao]"]`, data.situacao || '');
                    fillField(`[name="adultos[${nextIndex}][vinculo]"]`, data.vinculo || '');
                    fillField(`[name="adultos[${nextIndex}][local_trabalho]"]`, data.local_trabalho || '');
                    fillField(`[name="adultos[${nextIndex}][descricao]"]`, data.descricao || '');

                    const macroSelect = row.querySelector(`select[name="adultos[${nextIndex}][macro_grupo]"]`);
                    const setorSelect = row.querySelector(`select[name="adultos[${nextIndex}][setor_id]"]`);
                    if (macroSelect) {
                        macroSelect.dataset.selected = data.macro_grupo || '';
                    }
                    if (setorSelect) {
                        setorSelect.dataset.selected = data.setor_id || '';
                    }

                    attachRowEvents(row);
                    inicializarGrupoSelect(row);
                    toggleEmptyState();
                    nextIndex += 1;
                };

                addButton?.addEventListener('click', () => addRow());
                container?.querySelectorAll('.adulto-row').forEach((row) => {
                    attachRowEvents(row);
                    inicializarGrupoSelect(row);
                });
                toggleEmptyState();

                const localizacaoSelect = document.getElementById('localizacao_tipo');
                const detalheWrapper = document.getElementById('localizacao-detalhe-wrapper');
                const toggleDetalhe = () => {
                    if (!localizacaoSelect || !detalheWrapper) return;
                    if (localizacaoSelect.value === 'lugar_aldeia') {
                        detalheWrapper.classList.remove('d-none');
                    } else {
                        detalheWrapper.classList.add('d-none');
                    }
                };
                localizacaoSelect?.addEventListener('change', toggleDetalhe);
                toggleDetalhe();
            });
        </script>
    @endpush
@endonce
