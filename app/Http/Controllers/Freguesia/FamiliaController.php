<?php

namespace App\Http\Controllers\Freguesia;

use App\Http\Controllers\Controller;
use App\Models\Familia;
use App\Models\AgregadoFamiliar;
use App\Models\SetorAtividade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class FamiliaController extends Controller
{
    private const TIPOLOGIAS_HABITACAO = ['moradia', 'apartamento', 'caravana_tenda', 'anexo', 'outro'];
    private const REGIMES_PROPRIEDADE = ['propria', 'arrendada', 'cedida', 'outra'];
    private const LOCALIZACOES = ['sede_freguesia', 'lugar_aldeia', 'espaco_agroflorestal'];
    private const ESCOLA_OPCOES = ['sim', 'nao', 'nao_sei'];
    private const NECESSIDADES_APOIO = ['lingua_portuguesa', 'acesso_emprego', 'habitacao', 'regularizacao_administrativa', 'transporte_mobilidade', 'apoio_social', 'outra'];
    private const ESTRUTURAS_FAMILIARES = ['casal_com_filhos', 'casal_sem_filhos', 'monoparental', 'familia_alargada', 'coabitacao_informal', 'outra'];
    private const SITUACOES_SOCIOPROFISSIONAIS = ['conta_propria', 'conta_outrem', 'prestacao_servicos', 'desempregado', 'estudante', 'outra_situacao'];
    private const MACRO_GRUPOS = [
        'producao' => 'Produção, Construção e Agricultura',
        'servicos' => 'Serviços, Comércio e Turismo',
    ];
    private const ESTADOS_ACOMPANHAMENTO = ['ativa', 'desinstalada'];

    /**
     * Mostra a lista de famílias da freguesia.
     */
    public function index(Request $request)
    {
        $freguesiaId = Auth::user()->freguesia_id;
        if (!$freguesiaId) {
            return redirect()->route('dashboard')->with('error', 'Utilizador sem freguesia associada.');
        }
        $estadoFiltro = $request->query('estado');
        $estadosDisponiveis = self::ESTADOS_ACOMPANHAMENTO;

        $query = Familia::with('agregadoFamiliar')
            ->where('freguesia_id', $freguesiaId);

        if ($estadoFiltro && in_array($estadoFiltro, $estadosDisponiveis, true)) {
            $query->where('estado_acompanhamento', $estadoFiltro);
        }

        $familias = $query
            ->orderBy('ano_instalacao', 'desc')
            ->paginate(10)
            ->appends($request->only('estado'));

        return view('freguesia.familias.listar', [
            'familias' => $familias,
            'estadosDisponiveis' => $estadosDisponiveis,
            'estadoSelecionado' => in_array($estadoFiltro, $estadosDisponiveis, true) ? $estadoFiltro : null,
        ]);
    }

    /**
     * Mostra o formulário para criar uma nova família.
     * 
     */
    public function create()
    {
        $setores = SetorAtividade::where('ativo', true)
            ->orderBy('macro_grupo')
            ->orderBy('nome')
            ->get();
        
        // Carregar a lista do novo ficheiro config
        $nacionalidades = config('nacionalidades');
        $formOptions = $this->formOptions();

        return view('freguesia.familias.adicionar', compact('setores', 'nacionalidades', 'formOptions'));
    }

    /**
     * Guarda a nova família na base de dados.
     * 
     */
    public function store(Request $request)
    {
        $dadosValidados = $request->validate($this->regrasFormulario());
        $this->validarNecessidadesOutro($dadosValidados);
        $this->validarEstadoDesinstalacao($dadosValidados);
        $this->validarEleitores($dadosValidados);

        try {
            $user = Auth::user();
            $freguesia = $user->freguesia->load('concelho');
            $concelho = $freguesia->concelho;

            if (!$concelho) {
                throw new \Exception('Não foi possível encontrar o concelho associado.');
            }

            if (empty($freguesia->codigo)) {
                throw new \Exception('Freguesia sem código oficial associado.');
            }

            $prefixo = 'FM-'.$freguesia->codigo.'-';
            $ultimoCodigo = Familia::where('codigo', 'like', $prefixo.'%')->orderBy('codigo', 'desc')->first();
            $novoNumero = $ultimoCodigo ? ((int) substr($ultimoCodigo->codigo, -4)) + 1 : 1;
            $novoCodigo = $prefixo.str_pad($novoNumero, 4, '0', STR_PAD_LEFT);

            $dadosFamilia = array_merge(
                $this->montarDadosFamilia($dadosValidados),
                [
                    'codigo' => $novoCodigo,
                    'freguesia_id' => $freguesia->id,
                    'utilizador_registo_id' => $user->id,
                ]
            );

            $agregadoPayload = $this->montarAgregado($dadosValidados);
            $adultos = $this->normalizarAdultos($dadosValidados['adultos'] ?? []);

            DB::transaction(function () use ($dadosFamilia, $agregadoPayload, $adultos) {
                $familia = Familia::create($dadosFamilia);
                $familia->agregadoFamiliar()->create($agregadoPayload);
                $this->sincronizarAdultos($familia, $adultos);
            });

            return redirect()->route('freguesia.familias.index')
                ->with('success', 'Nova família ('.$novoCodigo.') registada com sucesso!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erro ao guardar a família: '.$e->getMessage());
        }
    }

    /**
     * Mostra o formulário para editar a Família
     * 
     */
    public function edit(Familia $familia)
    {
        if ($familia->freguesia_id !== Auth::user()->freguesia_id) {
            abort(403, 'Acesso não autorizado.');
        }
        
        // Carregar a lista de nacionalidades
        $nacionalidades = config('nacionalidades');
        $setores = SetorAtividade::where('ativo', true)
            ->orderBy('macro_grupo')
            ->orderBy('nome')
            ->get();
        $formOptions = $this->formOptions();
        
        $familia->load('agregadoFamiliar', 'atividadesEconomicas.setorAtividade');
        
        return view('freguesia.familias.editar', compact('familia', 'nacionalidades', 'setores', 'formOptions'));
    }

    /**
     * Atualiza a Família e o seu Agregado Familiar
     * 
     */
    public function update(Request $request, Familia $familia)
    {
        if ($familia->freguesia_id !== Auth::user()->freguesia_id) {
            abort(403, 'Acesso não autorizado.');
        }

        $dadosValidados = $request->validate($this->regrasFormulario());
        $this->validarNecessidadesOutro($dadosValidados);
        $this->validarEstadoDesinstalacao($dadosValidados);
        $this->validarEleitores($dadosValidados);

        $dadosFamilia = $this->montarDadosFamilia($dadosValidados);
        $agregadoPayload = $this->montarAgregado($dadosValidados);
        $adultos = $this->normalizarAdultos($dadosValidados['adultos'] ?? []);

        try {
            DB::transaction(function () use ($familia, $dadosFamilia, $agregadoPayload, $adultos) {
                $familia->update($dadosFamilia);
                $familia->agregadoFamiliar()->updateOrCreate(
                    ['familia_id' => $familia->id],
                    $agregadoPayload
                );
                $this->sincronizarAdultos($familia, $adultos);
            });
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erro ao guardar as alterações: '.$e->getMessage());
        }

        return redirect()->route('freguesia.familias.index')
            ->with('success', 'Família atualizada com sucesso.');
    }

    /**
     * Remove a Família
     */
    public function destroy(Familia $familia)
    {
        if ($familia->freguesia_id !== Auth::user()->freguesia_id) {
            abort(403, 'Acesso não autorizado.');
        }
        try {
            $familia->delete();
            return redirect()->route('freguesia.familias.index')
                             ->with('success', 'Família (Código: '.$familia->codigo.') foi apagada com sucesso.');
        } catch (\Exception $e) {
            return back()->with('error', 'Não foi possível apagar a família. Verifique se existem dados associados.');
        }
    }
    
    public function show(Familia $familia) 
    { 
        return $this->edit($familia);
    }

    private function regrasFormulario(): array
    {
        $anoAtual = (int) date('Y');
        $nacionalidades = config('nacionalidades', []);

        return [
            'ano_instalacao' => 'required|integer|min:1900|max:'.$anoAtual,
            'nacionalidade' => ['required', 'string', Rule::in($nacionalidades)],
            'tipologia_habitacao' => ['required', Rule::in(self::TIPOLOGIAS_HABITACAO)],
            'tipologia_propriedade' => ['required', Rule::in(self::REGIMES_PROPRIEDADE)],
            'localizacao_tipo' => ['required', Rule::in(self::LOCALIZACOES)],
            'localizacao_detalhe' => 'nullable|string|max:255|required_if:localizacao_tipo,lugar_aldeia',
            'estado_acompanhamento' => ['required', Rule::in(self::ESTADOS_ACOMPANHAMENTO)],
            'data_desinstalacao' => 'nullable|date',
            'ano_desinstalacao' => 'nullable|integer|min:1900|max:'.$anoAtual,
            'inscrito_centro_saude' => 'nullable|in:0,1,nao_sei',
            'inscrito_escola' => ['nullable', Rule::in(self::ESCOLA_OPCOES)],
            'necessidades_apoio' => 'nullable|array',
            'necessidades_apoio.*' => ['string', Rule::in(self::NECESSIDADES_APOIO)],
            'necessidades_apoio_outro' => 'nullable|string|max:255',
            'observacoes' => 'nullable|string|max:2000',
            'adultos_laboral_m' => 'required|integer|min:0',
            'adultos_laboral_f' => 'required|integer|min:0',
            'adultos_senior_m' => 'required|integer|min:0',
            'adultos_senior_f' => 'required|integer|min:0',
            'criancas_m' => 'required|integer|min:0',
            'criancas_f' => 'required|integer|min:0',
            'membros_sem_informacao' => 'required|integer|min:0',
            'eleitores_repenicados' => 'nullable|integer|min:0',
            'estrutura_familiar' => 'nullable|array',
            'estrutura_familiar.*' => ['string', Rule::in(self::ESTRUTURAS_FAMILIARES)],
            'adultos' => 'nullable|array',
            'adultos.*.macro_grupo' => ['nullable', Rule::in(array_keys(self::MACRO_GRUPOS))],
            'adultos.*.identificador' => 'nullable|string|max:20',
            'adultos.*.situacao' => ['nullable', Rule::in(self::SITUACOES_SOCIOPROFISSIONAIS)],
            'adultos.*.setor_id' => 'nullable|exists:setor_atividades,id',
            'adultos.*.descricao' => 'nullable|string|max:500',
        ];
    }

    private function montarDadosFamilia(array $dados): array
    {
        $necessidades = $dados['necessidades_apoio'] ?? null;

        $apoioOutro = trim((string) ($dados['necessidades_apoio_outro'] ?? ''));

        $payload = [
            'ano_instalacao' => $dados['ano_instalacao'],
            'estado_acompanhamento' => $dados['estado_acompanhamento'],
            'nacionalidade' => $dados['nacionalidade'],
            'tipologia_habitacao' => $dados['tipologia_habitacao'],
            'tipologia_propriedade' => $dados['tipologia_propriedade'],
            'localizacao_tipo' => $dados['localizacao_tipo'],
            'localizacao_detalhe' => $dados['localizacao_detalhe'] ?? null,
            'inscrito_centro_saude' => $this->normalizarInscricaoCentroSaude($dados['inscrito_centro_saude'] ?? null),
            'inscrito_escola' => $dados['inscrito_escola'] ?? null,
            'necessidades_apoio' => !empty($necessidades) ? array_values(array_unique($necessidades)) : null,
            'necessidades_apoio_outro' => $apoioOutro !== '' ? $apoioOutro : null,
            'observacoes' => $dados['observacoes'] ?? null,
        ];

        if ($dados['estado_acompanhamento'] === 'desinstalada') {
            $payload['data_desinstalacao'] = !empty($dados['data_desinstalacao']) ? $dados['data_desinstalacao'] : null;
            $payload['ano_desinstalacao'] = $this->calcularAnoDesinstalacao($dados);
        } else {
            $payload['data_desinstalacao'] = null;
            $payload['ano_desinstalacao'] = null;
        }

        if (array_key_exists('condicao_alojamento', $dados)) {
            $payload['condicao_alojamento'] = $dados['condicao_alojamento'];
        }

        return $payload;
    }

    private function normalizarInscricaoCentroSaude($valor): ?int
    {
        if ($valor === 'nao_sei' || $valor === null || $valor === '') {
            return null;
        }

        return ($valor === '1' || $valor === 1 || $valor === true) ? 1 : 0;
    }

    private function montarAgregado(array $dados): array
    {
        $estrutura = $dados['estrutura_familiar'] ?? null;
        $estrutura = !empty($estrutura) ? array_values(array_unique($estrutura)) : null;

        return [
            'adultos_laboral_m' => (int) ($dados['adultos_laboral_m'] ?? 0),
            'adultos_laboral_f' => (int) ($dados['adultos_laboral_f'] ?? 0),
            'adultos_65_mais_m' => (int) ($dados['adultos_senior_m'] ?? 0),
            'adultos_65_mais_f' => (int) ($dados['adultos_senior_f'] ?? 0),
            'criancas_m' => (int) ($dados['criancas_m'] ?? 0),
            'criancas_f' => (int) ($dados['criancas_f'] ?? 0),
            'membros_sem_informacao' => (int) ($dados['membros_sem_informacao'] ?? 0),
            'eleitores_repenicados' => array_key_exists('eleitores_repenicados', $dados) && $dados['eleitores_repenicados'] !== null
                ? (int) $dados['eleitores_repenicados']
                : null,
            'estrutura_familiar' => $estrutura,
        ];
    }

    private function normalizarAdultos(?array $adultos): array
    {
        if (empty($adultos)) {
            return [];
        }

        return collect($adultos)
            ->map(function ($adulto) {
                $situacao = $adulto['situacao'] ?? null;

                if (!$situacao) {
                    return null;
                }

                return [
                    'identificador' => $adulto['identificador'] ?? null,
                    'tipo' => $situacao,
                    'setor_id' => $adulto['setor_id'] ?? null,
                    'descricao' => $adulto['descricao'] ?? null,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    private function sincronizarAdultos(Familia $familia, array $adultos): void
    {
        $familia->atividadesEconomicas()->delete();

        if (!empty($adultos)) {
            $familia->atividadesEconomicas()->createMany($adultos);
        }
    }

    private function formOptions(): array
    {
        return [
            'tipologiasHabitacao' => self::TIPOLOGIAS_HABITACAO,
            'regimesPropriedade' => self::REGIMES_PROPRIEDADE,
            'localizacoes' => self::LOCALIZACOES,
            'necessidadesApoio' => self::NECESSIDADES_APOIO,
            'estruturasFamiliares' => self::ESTRUTURAS_FAMILIARES,
            'opcoesEscola' => self::ESCOLA_OPCOES,
            'situacoesSociais' => self::SITUACOES_SOCIOPROFISSIONAIS,
            'macroGrupos' => self::MACRO_GRUPOS,
            'estadosAcompanhamento' => self::ESTADOS_ACOMPANHAMENTO,
        ];
    }

    private function validarNecessidadesOutro(array $dados): void
    {
        $selecionados = collect($dados['necessidades_apoio'] ?? []);
        $outroSelecionado = $selecionados->contains('outra');
        $descricaoOutro = trim((string) ($dados['necessidades_apoio_outro'] ?? ''));

        if ($outroSelecionado && $descricaoOutro === '') {
            throw ValidationException::withMessages([
                'necessidades_apoio_outro' => 'Indique qual o apoio ou serviço solicitado.',
            ]);
        }

        if (!$outroSelecionado && $descricaoOutro !== '') {
            throw ValidationException::withMessages([
                'necessidades_apoio_outro' => 'Selecione "Outra" para descrever um apoio específico.',
            ]);
        }
    }

    private function validarEstadoDesinstalacao(array $dados): void
    {
        $estado = $dados['estado_acompanhamento'] ?? 'ativa';

        if ($estado !== 'desinstalada') {
            return;
        }

        $ano = $dados['ano_desinstalacao'] ?? null;
        $data = $dados['data_desinstalacao'] ?? null;

        if (empty($ano) && empty($data)) {
            throw ValidationException::withMessages([
                'estado_acompanhamento' => 'Indique pelo menos o ano ou a data de desinstalação.',
            ]);
        }

        if (!empty($ano) && !empty($data)) {
            $anoData = (int) date('Y', strtotime($data));
            if ((int) $ano !== $anoData) {
                throw ValidationException::withMessages([
                    'ano_desinstalacao' => 'O ano indicado tem de coincidir com a data de desinstalação.',
                ]);
            }
        }
    }

    private function calcularAnoDesinstalacao(array $dados): ?int
    {
        $data = $dados['data_desinstalacao'] ?? null;
        if (!empty($data)) {
            return (int) date('Y', strtotime($data));
        }

        return (isset($dados['ano_desinstalacao']) && $dados['ano_desinstalacao'] !== '')
            ? (int) $dados['ano_desinstalacao']
            : null;
    }

    private function validarEleitores(array $dados): void
    {
        if (!array_key_exists('eleitores_repenicados', $dados) || $dados['eleitores_repenicados'] === null) {
            return;
        }

        $eleitores = (int) $dados['eleitores_repenicados'];
        $adultosLaboral = (int) ($dados['adultos_laboral_m'] ?? 0) + (int) ($dados['adultos_laboral_f'] ?? 0);
        $adultosSenior = (int) ($dados['adultos_senior_m'] ?? 0) + (int) ($dados['adultos_senior_f'] ?? 0);
        $adultosTotais = $adultosLaboral + $adultosSenior;

        if ($eleitores > $adultosTotais) {
            throw ValidationException::withMessages([
                'eleitores_repenicados' => 'O número de eleitores não pode ser superior ao total de adultos (idade laboral + seniores).',
            ]);
        }
    }
}