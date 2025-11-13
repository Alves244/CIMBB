<?php

namespace App\Http\Controllers\Freguesia;

use App\Http\Controllers\Controller;
use App\Models\Familia;
use App\Models\AgregadoFamiliar;
use App\Models\SetorAtividade; // Importar Setores
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule; // Importar a regra de validação

class FamiliaController extends Controller
{
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
                            ->paginate(15);
        return view('freguesia.familias.listar', compact('familias'));
    }

    /**
     * Mostra o formulário para criar uma nova família.
     * (ATUALIZADO: Carrega setores E nacionalidades)
     */
    public function create()
    {
        $setores = SetorAtividade::where('ativo', true)->orderBy('nome')->get();
        
        // Carregar a lista do novo ficheiro config
        $nacionalidades = config('nacionalidades');
        
        return view('freguesia.familias.adicionar', compact('setores', 'nacionalidades'));
    }

    /**
     * Guarda a nova família na base de dados.
     * (ATUALIZADO: Validação da nacionalidade)
     */
    public function store(Request $request)
    {
        // 1. Validar todos os dados
        $dadosValidados = $request->validate([
            // Família
            'ano_instalacao' => 'required|integer|min:1900|max:'.date('Y'),
            // Valida se a nacionalidade enviada EXISTE no ficheiro config/nacionalidades.php
            'nacionalidade' => ['required', 'string', Rule::in(config('nacionalidades'))],
            'tipologia_habitacao' => 'required|in:casa,quinta,apartamento',
            'tipologia_propriedade' => 'required|in:propria,arrendada',
            'localizacao' => 'required|in:nucleo_urbano,aldeia_anexa,espaco_agroflorestal',
            
            // Agregado (com géneros)
            'adultos_laboral_m' => 'required|integer|min:0',
            'adultos_laboral_f' => 'required|integer|min:0',
            'adultos_laboral_n' => 'required|integer|min:0',
            'adultos_65_mais_m' => 'required|integer|min:0',
            'adultos_65_mais_f' => 'required|integer|min:0',
            'adultos_65_mais_n' => 'required|integer|min:0',
            'criancas_m' => 'required|integer|min:0',
            'criancas_f' => 'required|integer|min:0',
            'criancas_n' => 'required|integer|min:0',

            // Atividade Económica (Opcional)
            'atividade_tipo' => 'nullable|required_with:atividade_setor_id|in:conta_propria,conta_outrem',
            'atividade_setor_id' => 'nullable|required_with:atividade_tipo|exists:setor_atividades,id',
            'atividade_descricao' => 'nullable|string|max:500',
        ]);
        
        // 2. Gerar o Código Automático
        try {
            $user = Auth::user();
            $freguesia = $user->freguesia->load('conselho');
            $conselho = $freguesia->conselho;
            if (!$conselho) { throw new \Exception("Não foi possível encontrar o concelho associado."); }

            $iniciaisFreguesia = $this->gerarIniciais($freguesia->nome);
            $iniciaisConselho = $this->gerarIniciais($conselho->nome);
            $iniciaisCompletas = $iniciaisFreguesia;
            if ($iniciaisFreguesia != $iniciaisConselho) { $iniciaisCompletas .= $iniciaisConselho; }
            $ano = date('Y');
            $prefixo = 'FM'.$iniciaisCompletas.$ano.'-';
            
            $ultimoCodigo = Familia::where('codigo', 'like', $prefixo.'%')->orderBy('codigo', 'desc')->first();
            $novoNumero = $ultimoCodigo ? ((int) substr($ultimoCodigo->codigo, -4)) + 1 : 1;
            $novoCodigo = $prefixo.str_pad($novoNumero, 4, '0', STR_PAD_LEFT);

            // 3. Usar uma Transação para guardar tudo
            $familia = null; 
            DB::transaction(function () use ($dadosValidados, $novoCodigo, $user, $freguesia, &$familia, $request) {
                
                $familia = Familia::create([
                    'codigo' => $novoCodigo,
                    'ano_instalacao' => $dadosValidados['ano_instalacao'],
                    'nacionalidade' => $dadosValidados['nacionalidade'],
                    'freguesia_id' => $freguesia->id,
                    'tipologia_habitacao' => $dadosValidados['tipologia_habitacao'],
                    'tipologia_propriedade' => $dadosValidados['tipologia_propriedade'],
                    'localizacao' => $dadosValidados['localizacao'],
                    'utilizador_registo_id' => $user->id,
                ]);

                $familia->agregadoFamiliar()->create([
                    'adultos_laboral_m' => $dadosValidados['adultos_laboral_m'],
                    'adultos_laboral_f' => $dadosValidados['adultos_laboral_f'],
                    'adultos_laboral_n' => $dadosValidados['adultos_laboral_n'],
                    'adultos_65_mais_m' => $dadosValidados['adultos_65_mais_m'],
                    'adultos_65_mais_f' => $dadosValidados['adultos_65_mais_f'],
                    'adultos_65_mais_n' => $dadosValidados['adultos_65_mais_n'],
                    'criancas_m' => $dadosValidados['criancas_m'],
                    'criancas_f' => $dadosValidados['criancas_f'],
                    'criancas_n' => $dadosValidados['criancas_n'],
                ]);

                if ($request->filled('atividade_tipo') && $request->filled('atividade_setor_id')) {
                    $familia->atividadesEconomicas()->create([
                        'tipo' => $dadosValidados['atividade_tipo'],
                        'setor_id' => $dadosValidados['atividade_setor_id'],
                        'descricao' => $dadosValidados['atividade_descricao'],
                    ]);
                }
            });

            return redirect()->route('freguesia.familias.index')
                             ->with('success', 'Nova família ('.$novoCodigo.') registada com sucesso!');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erro ao guardar a família: '.$e->getMessage());
        }
    }

    /**
     * Função privada para gerar iniciais
     */
    private function gerarIniciais($nome)
    {
        $nomeLimpo = str_replace('-', ' ', $nome);
        $palavras = explode(' ', $nomeLimpo);
        $iniciais = '';
        foreach ($palavras as $palavra) {
            if (strlen($palavra) > 0 && !in_array(strtolower($palavra), ['e', 'da', 'do', 'de', 'das', 'dos'])) {
                $iniciais .= Str::upper(substr($palavra, 0, 1));
            }
        }
        if (empty($iniciais) && strlen($nome) > 0) {
            return Str::upper(substr($nome, 0, 1));
        }
        return $iniciais;
    }

    /**
     * Mostra o formulário para editar a Família
     * (ATUALIZADO: Carrega as nacionalidades)
     */
    public function edit(Familia $familia)
    {
        if ($familia->freguesia_id !== Auth::user()->freguesia_id) {
            abort(403, 'Acesso não autorizado.');
        }
        
        // Carregar a lista de nacionalidades
        $nacionalidades = config('nacionalidades');
        
        $familia->load('agregadoFamiliar', 'atividadesEconomicas.setorAtividade');
        
        return view('freguesia.familias.editar', compact('familia', 'nacionalidades'));
    }

    /**
     * Atualiza a Família e o seu Agregado Familiar
     * (ATUALIZADO: Validação da nacionalidade)
     */
    public function update(Request $request, Familia $familia)
    {
        if ($familia->freguesia_id !== Auth::user()->freguesia_id) {
            abort(403, 'Acesso não autorizado.');
        }

        // 1. Validar os dados da Família
        $validatedFamilia = $request->validate([
            'ano_instalacao' => 'required|integer|min:1900|max:'.date('Y'),
            'nacionalidade' => ['required', 'string', Rule::in(config('nacionalidades'))],
            'tipologia_habitacao' => 'required|string|in:casa,quinta,apartamento',
            'tipologia_propriedade' => 'required|string|in:propria,arrendada',
            'localizacao' => 'required|in:nucleo_urbano,aldeia_anexa,espaco_agroflorestal',
        ]);
        
        // 2. Validar os dados do Agregado
        $validatedAgregado = $request->validate([
            'adultos_laboral_m' => 'required|integer|min:0',
            'adultos_laboral_f' => 'required|integer|min:0',
            'adultos_laboral_n' => 'required|integer|min:0',
            'adultos_65_mais_m' => 'required|integer|min:0',
            'adultos_65_mais_f' => 'required|integer|min:0',
            'adultos_65_mais_n' => 'required|integer|min:0',
            'criancas_m' => 'required|integer|min:0',
            'criancas_f' => 'required|integer|min:0',
            'criancas_n' => 'required|integer|min:0',
        ]);

        // 3. Usar uma Transação
        try {
            DB::transaction(function () use ($familia, $validatedFamilia, $validatedAgregado) {
                $familia->update($validatedFamilia);
                $familia->agregadoFamiliar()->updateOrCreate(
                    ['familia_id' => $familia->id], 
                    $validatedAgregado 
                );
            });
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erro ao guardar as alterações: '.$e->getMessage());
        }

        // 4. Redirecionar de volta
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
}