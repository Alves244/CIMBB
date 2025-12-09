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

class FamiliaController extends Controller
{
    private const TIPOLOGIAS_HABITACAO = ['moradia', 'apartamento', 'caravana_tenda', 'anexo', 'outro'];
    private const REGIMES_PROPRIEDADE = ['propria', 'arrendada', 'cedida', 'outra'];
    private const LOCALIZACOES = ['sede_freguesia', 'lugar_aldeia', 'espaco_agroflorestal'];
    private const CONDICOES_ALOJAMENTO = ['bom_estado', 'estado_razoavel', 'necessita_reparacoes', 'situacao_precaria'];
    private const ESCOLA_OPCOES = ['sim', 'nao', 'nao_sei'];
    private const NECESSIDADES_APOIO = ['lingua_portuguesa', 'acesso_emprego', 'habitacao', 'regularizacao_administrativa', 'transporte_mobilidade', 'apoio_social'];
    private const ESTRUTURAS_FAMILIARES = ['casal_com_filhos', 'casal_sem_filhos', 'monoparental', 'familia_alargada', 'coabitacao_informal', 'outra'];
    private const SITUACOES_SOCIOPROFISSIONAIS = ['conta_propria', 'conta_outrem', 'prestacao_servicos', 'desempregado', 'estudante', 'outra_situacao'];
    private const VINCULOS = ['empregado', 'estagiario', 'outro'];
    private const MACRO_GRUPOS = [
        'producao' => 'Produção, Construção e Agricultura',
        'servicos' => 'Serviços, Comércio e Turismo',
    ];

    /**
     * Mostra a lista de famílias da freguesia.
     */
    public function index()
    {
        $freguesiaId = Auth::user()->freguesia_id;
        if (!$freguesiaId) {
            return redirect()->route('dashboard')->with('error', 'Utilizador sem freguesia associada.');
        }
        $familias = Familia::with('agregadoFamiliar') 
                    ->where('freguesia_id', $freguesiaId)
                    ->orderBy('ano_instalacao', 'desc')
                    ->paginate(10);
        return view('freguesia.familias.listar', compact('familias'));
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

        try {
            $user = Auth::user();
            $freguesia = $user->freguesia->load('conselho');
            $conselho = $freguesia->conselho;

            if (!$conselho) {
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

        return redirect()->route('freguesia.familias.edit', $familia->id)
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
            'condicao_alojamento' => ['required', Rule::in(self::CONDICOES_ALOJAMENTO)],
            'inscrito_centro_saude' => 'nullable|boolean',
            'inscrito_escola' => ['nullable', Rule::in(self::ESCOLA_OPCOES)],
            'necessidades_apoio' => 'nullable|array',
            'necessidades_apoio.*' => ['string', Rule::in(self::NECESSIDADES_APOIO)],
            'observacoes' => 'nullable|string|max:2000',
            'adultos_laboral_m' => 'required|integer|min:0',
            'adultos_laboral_f' => 'required|integer|min:0',
            'adultos_laboral_n' => 'required|integer|min:0',
            'adultos_senior_m' => 'required|integer|min:0',
            'adultos_senior_f' => 'required|integer|min:0',
            'adultos_senior_n' => 'required|integer|min:0',
            'criancas_m' => 'required|integer|min:0',
            'criancas_f' => 'required|integer|min:0',
            'criancas_n' => 'required|integer|min:0',
            'estrutura_familiar' => 'nullable|array',
            'estrutura_familiar.*' => ['string', Rule::in(self::ESTRUTURAS_FAMILIARES)],
            'adultos' => 'nullable|array',
            'adultos.*.macro_grupo' => ['nullable', Rule::in(array_keys(self::MACRO_GRUPOS))],
            'adultos.*.identificador' => 'nullable|string|max:20',
            'adultos.*.situacao' => ['nullable', Rule::in(self::SITUACOES_SOCIOPROFISSIONAIS)],
            'adultos.*.vinculo' => ['nullable', Rule::in(self::VINCULOS)],
            'adultos.*.setor_id' => 'nullable|exists:setor_atividades,id',
            'adultos.*.local_trabalho' => 'nullable|string|max:120',
            'adultos.*.descricao' => 'nullable|string|max:500',
        ];
    }

    private function montarDadosFamilia(array $dados): array
    {
        $necessidades = $dados['necessidades_apoio'] ?? null;

        return [
            'ano_instalacao' => $dados['ano_instalacao'],
            'nacionalidade' => $dados['nacionalidade'],
            'tipologia_habitacao' => $dados['tipologia_habitacao'],
            'tipologia_propriedade' => $dados['tipologia_propriedade'],
            'localizacao_tipo' => $dados['localizacao_tipo'],
            'localizacao_detalhe' => $dados['localizacao_detalhe'] ?? null,
            'condicao_alojamento' => $dados['condicao_alojamento'],
            'inscrito_centro_saude' => (bool) ($dados['inscrito_centro_saude'] ?? false),
            'inscrito_escola' => $dados['inscrito_escola'] ?? null,
            'necessidades_apoio' => !empty($necessidades) ? array_values(array_unique($necessidades)) : null,
            'observacoes' => $dados['observacoes'] ?? null,
        ];
    }

    private function montarAgregado(array $dados): array
    {
        $estrutura = $dados['estrutura_familiar'] ?? null;
        $estrutura = !empty($estrutura) ? array_values(array_unique($estrutura)) : null;

        return [
            'adultos_laboral_m' => (int) ($dados['adultos_laboral_m'] ?? 0),
            'adultos_laboral_f' => (int) ($dados['adultos_laboral_f'] ?? 0),
            'adultos_laboral_n' => (int) ($dados['adultos_laboral_n'] ?? 0),
            'adultos_65_mais_m' => (int) ($dados['adultos_senior_m'] ?? 0),
            'adultos_65_mais_f' => (int) ($dados['adultos_senior_f'] ?? 0),
            'adultos_65_mais_n' => (int) ($dados['adultos_senior_n'] ?? 0),
            'criancas_m' => (int) ($dados['criancas_m'] ?? 0),
            'criancas_f' => (int) ($dados['criancas_f'] ?? 0),
            'criancas_n' => (int) ($dados['criancas_n'] ?? 0),
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
                    'vinculo' => $adulto['vinculo'] ?? null,
                    'local_trabalho' => $adulto['local_trabalho'] ?? null,
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
            'condicoesAlojamento' => self::CONDICOES_ALOJAMENTO,
            'necessidadesApoio' => self::NECESSIDADES_APOIO,
            'estruturasFamiliares' => self::ESTRUTURAS_FAMILIARES,
            'opcoesEscola' => self::ESCOLA_OPCOES,
            'situacoesSociais' => self::SITUACOES_SOCIOPROFISSIONAIS,
            'vinculosProfissionais' => self::VINCULOS,
            'macroGrupos' => self::MACRO_GRUPOS,
        ];
    }
}