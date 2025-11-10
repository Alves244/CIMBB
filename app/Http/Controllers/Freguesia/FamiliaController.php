<?php

namespace App\Http\Controllers\Freguesia;

use App\Http\Controllers\Controller;
use App\Models\Familia;
use App\Models\AgregadoFamiliar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
     */
    public function create()
    {
        return view('freguesia.familias.adicionar');
    }

    /**
     * Guarda a nova família na base de dados.
     */
    public function store(Request $request)
    {
        // 1. Validar os dados
        $dadosValidados = $request->validate([
            'ano_instalacao' => 'required|integer|min:1900|max:'.date('Y'),
            'nacionalidade' => 'required|string|max:50',
            'tipologia_habitacao' => 'required|in:casa,quinta,apartamento',
            'tipologia_propriedade' => 'required|in:propria,arrendada',
            'localizacao' => 'required|in:nucleo_urbano,aldeia_anexa,espaco_agroflorestal', // Campo que adicionámos
            'adultos_laboral' => 'required|integer|min:0',
            'adultos_65_mais' => 'required|integer|min:0',
            'criancas' => 'required|integer|min:0',
        ]);
        
        // 2. Gerar o Código Automático
        try {
            $user = Auth::user();
            $freguesia = $user->freguesia->load('conselho');
            $conselho = $freguesia->conselho;
            
            if (!$conselho) {
                throw new \Exception("Não foi possível encontrar o concelho associado à freguesia.");
            }

            $iniciaisFreguesia = $this->gerarIniciais($freguesia->nome);
            $iniciaisConselho = $this->gerarIniciais($conselho->nome);
            
            $iniciaisCompletas = $iniciaisFreguesia;
            if ($iniciaisFreguesia != $iniciaisConselho) {
                $iniciaisCompletas .= $iniciaisConselho;
            }

            $ano = date('Y');
            $prefixo = 'FM'.$iniciaisCompletas.$ano.'-';

            // 3. Encontrar o último número sequencial
            $ultimoCodigo = Familia::where('codigo', 'like', $prefixo.'%')
                                    ->orderBy('codigo', 'desc')
                                    ->first();

            $novoNumero = 1;
            if ($ultimoCodigo) {
                $numero = (int) substr($ultimoCodigo->codigo, -4);
                $novoNumero = $numero + 1;
            }

            // 4. Formatar o novo código
            $novoCodigo = $prefixo.str_pad($novoNumero, 4, '0', STR_PAD_LEFT);

            // 5. Usar uma Transação
            $familia = null; 
            DB::transaction(function () use ($dadosValidados, $novoCodigo, $user, $freguesia, &$familia) {
                
                // 5a. Criar a Família
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

                // 5b. Criar o Agregado Familiar associado
                $familia->agregadoFamiliar()->create([
                    'adultos_laboral' => $dadosValidados['adultos_laboral'],
                    'adultos_65_mais' => $dadosValidados['adultos_65_mais'],
                    'criancas' => $dadosValidados['criancas'],
                ]);
            });

            // 6. ***** LINHA CORRIGIDA *****
            // Removemos o '->id' e passamos o objeto $familia completo.
            return redirect()->route('freguesia.familias.edit', $familia)
                             ->with('success', 'Nova família registada! Pode agora adicionar as atividades económicas.');

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
     */
    public function edit(Familia $familia)
    {
        if ($familia->freguesia_id !== Auth::user()->freguesia_id) {
            abort(403, 'Acesso não autorizado.');
        }

        $familia->load('agregadoFamiliar', 'atividadesEconomicas.setorAtividade');

        return view('freguesia.familias.editar', compact('familia'));
    }

    /**
     * Atualiza a Família e o seu Agregado Familiar
     */
    public function update(Request $request, Familia $familia)
    {
        if ($familia->freguesia_id !== Auth::user()->freguesia_id) {
            abort(403, 'Acesso não autorizado.');
        }

        // 1. Validar os dados da Família (com o novo campo 'localizacao')
        $validatedFamilia = $request->validate([
            'ano_instalacao' => 'required|integer|min:1900|max:'.date('Y'),
            'nacionalidade' => 'required|string|max:50',
            'tipologia_habitacao' => 'required|string|in:casa,quinta,apartamento',
            'tipologia_propriedade' => 'required|string|in:propria,arrendada',
            'localizacao' => 'required|in:nucleo_urbano,aldeia_anexa,espaco_agroflorestal', // <-- CAMPO ADICIONADO
        ]);
        
        // 2. Validar os dados do Agregado
        $validatedAgregado = $request->validate([
            'adultos_laboral' => 'required|integer|min:0',
            'adultos_65_mais' => 'required|integer|min:0',
            'criancas' => 'required|integer|min:0',
        ]);

        // 3. Usar uma Transação
        try {
            DB::transaction(function () use ($familia, $validatedFamilia, $validatedAgregado) {
                
                // 3a. Atualizar a Família
                $familia->update($validatedFamilia);

                // 3b. Atualizar ou Criar o Agregado Familiar associado
                $familia->agregadoFamiliar()->updateOrCreate(
                    ['familia_id' => $familia->id], 
                    $validatedAgregado 
                );
            });
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erro ao guardar as alterações: '.$e->getMessage());
        }

        // 4. Redirecionar de volta com sucesso
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
    
    /**
     * Mostra uma única família (redireciona para a edição)
     */
    public function show(Familia $familia) 
    { 
        return $this->edit($familia);
    }
}