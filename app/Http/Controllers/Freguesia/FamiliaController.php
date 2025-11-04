<?php

namespace App\Http\Controllers\Freguesia;

use App\Http\Controllers\Controller;
use App\Models\Familia;
use App\Models\AgregadoFamiliar; // Importante
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Importante para a Transação
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

        // Carrega a relação 'agregadoFamiliar'
        $familias = Familia::with('agregadoFamiliar') 
                            ->where('freguesia_id', $freguesiaId)
                            ->orderBy('ano_instalacao', 'desc')
                            ->paginate(15); // Paginação

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
        // 1. Validar os dados que vêm do formulário
        $dadosValidados = $request->validate([
            'ano_instalacao' => 'required|integer|min:1900|max:'.date('Y'),
            'nacionalidade' => 'required|string|max:50',
            'tipologia_habitacao' => 'required|in:casa,quinta,apartamento',
            'tipologia_propriedade' => 'required|in:propria,arrendada',
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
                    'utilizador_registo_id' => $user->id,
                ]);

                // 5b. Criar o Agregado Familiar associado
                $familia->agregadoFamiliar()->create([
                    'adultos_laboral' => $dadosValidados['adultos_laboral'],
                    'adultos_65_mais' => $dadosValidados['adultos_65_mais'],
                    'criancas' => $dadosValidados['criancas'],
                ]);
            });

            // 6. Redirecionar para a PÁGINA DE EDIÇÃO (fluxo melhorado)
            return redirect()->route('freguesia.familias.edit', $familia->id)
                             ->with('success', 'Nova família registada! Pode agora adicionar as atividades económicas.');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erro ao guardar a família: '.$e->getMessage());
        }
    }

    /**
     * Função privada para gerar iniciais (O seu código original)
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

    
    /* --- ESTES SÃO OS MÉTODOS QUE FALTAVAM --- */
    
    /**
     * Mostra o formulário para editar a Família E as suas Atividades.
     * (Carrega o seu 'editar.blade.php')
     */
    public function edit(Familia $familia)
    {
        // 1. Verificar se a família pertence à freguesia do utilizador
        if ($familia->freguesia_id !== Auth::user()->freguesia_id) {
            abort(403, 'Acesso não autorizado.'); // Proteção
        }

        // 2. Carregar as relações da família de forma eficiente (Eager Loading)
        // (Usa as relações 'agregadoFamiliar' e 'atividadesEconomicas.setorAtividade')
        $familia->load('agregadoFamiliar', 'atividadesEconomicas.setorAtividade');

        // 3. Retorna a view de edição da família
        return view('freguesia.familias.editar', compact('familia'));
    }

    /**
     * Atualiza a Família e o seu Agregado Familiar na base de dados.
     * (Chamado pelo formulário no 'editar.blade.php')
     */
    public function update(Request $request, Familia $familia)
    {
        // 1. Verificar se a família pertence à freguesia do utilizador
        if ($familia->freguesia_id !== Auth::user()->freguesia_id) {
            abort(403, 'Acesso não autorizado.');
        }

        // 2. Validar os dados da Família (Card 1)
        $validatedFamilia = $request->validate([
            'ano_instalacao' => 'required|integer|min:1900|max:'.date('Y'),
            'nacionalidade' => 'required|string|max:50',
            'tipologia_habitacao' => 'required|string|in:casa,quinta,apartamento',
            'tipologia_propriedade' => 'required|string|in:propria,arrendada',
        ]);
        
        // 3. Validar os dados do Agregado (Card 1)
        $validatedAgregado = $request->validate([
            'adultos_laboral' => 'required|integer|min:0',
            'adultos_65_mais' => 'required|integer|min:0',
            'criancas' => 'required|integer|min:0',
        ]);

        // 4. Usar uma Transação para garantir que tudo é salvo
        try {
            DB::transaction(function () use ($familia, $validatedFamilia, $validatedAgregado) {
                
                // 4a. Atualizar a Família
                $familia->update($validatedFamilia);

                // 4b. Atualizar ou Criar o Agregado Familiar associado
                $familia->agregadoFamiliar()->updateOrCreate(
                    ['familia_id' => $familia->id], // Condição para encontrar
                    $validatedAgregado // Dados para atualizar ou criar
                );
            });
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erro ao guardar as alterações: '.$e->getMessage());
        }

        // 6. Redirecionar de volta com sucesso
        return redirect()->route('freguesia.familias.edit', $familia->id)
                         ->with('success', 'Família atualizada com sucesso.');
    }

    /**
     * Remove a Família (e o Agregado/Atividades via 'onDelete: cascade')
     */
    public function destroy(Familia $familia)
    {
        // 1. Verificar se a família pertence à freguesia do utilizador
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
     * Mostra uma única família (pode redirecionar para a edição)
     */
    public function show(Familia $familia) 
    { 
        // Ação padrão é redirecionar para a edição
        return $this->edit($familia);
    }
}