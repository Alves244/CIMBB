<?php

namespace App\Http\Controllers\Freguesia;

use App\Http\Controllers\Controller;
use App\Models\Familia;
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
        $dadosValidados = $request->validate([
            'ano_instalacao' => 'required|integer|min:1900|max:'.date('Y'),
            'nacionalidade' => 'required|string|max:50',
            'tipologia_habitacao' => 'required|in:casa,quinta,apartamento',
            'tipologia_propriedade' => 'required|in:propria,arrendada',
            'adultos_laboral' => 'required|integer|min:0',
            'adultos_65_mais' => 'required|integer|min:0',
            'criancas' => 'required|integer|min:0',
        ]);
        
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
            $ultimoCodigo = Familia::where('codigo', 'like', $prefixo.'%')
                                    ->orderBy('codigo', 'desc')
                                    ->first();
            $novoNumero = 1;
            if ($ultimoCodigo) {
                $numero = (int) substr($ultimoCodigo->codigo, -4);
                $novoNumero = $numero + 1;
            }
            $novoCodigo = $prefixo.str_pad($novoNumero, 4, '0', STR_PAD_LEFT);

            DB::transaction(function () use ($dadosValidados, $novoCodigo, $user, $freguesia) {
                $familia = Familia::create([
                    'codigo' => $novoCodigo,
                    'ano_instalacao' => $dadosValidados['ano_instalacao'],
                    'nacionalidade' => $dadosValidados['nacionalidade'],
                    'freguesia_id' => $freguesia->id,
                    'tipologia_habitacao' => $dadosValidados['tipologia_habitacao'],
                    'tipologia_propriedade' => $dadosValidados['tipologia_propriedade'],
                    'utilizador_registo_id' => $user->id,
                ]);
                $familia->agregadoFamiliar()->create([
                    'adultos_laboral' => $dadosValidados['adultos_laboral'],
                    'adultos_65_mais' => $dadosValidados['adultos_65_mais'],
                    'criancas' => $dadosValidados['criancas'],
                ]);
            });
            return redirect()->route('freguesia.familias.index')->with('success', 'Nova família registada com sucesso!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erro ao guardar a família: '.$e->getMessage());
        }
    }

    /**
     * Função privada para gerar iniciais a partir de um nome.
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
     * Mostra os detalhes de uma família específica.
     * (Redireciona para a edição)
     */
    public function show(Familia $familia)
    {
        // Verificar permissão
        if ($familia->freguesia_id !== Auth::user()->freguesia_id) {
             return redirect()->route('freguesia.familias.index')->with('error', 'Não tem permissão para ver esta família.');
        }
        // Redireciona diretamente para a página de edição
        return redirect()->route('freguesia.familias.edit', $familia->id);
    }

    
    /**
     * Mostra o formulário para editar uma família.
     */
    public function edit(Familia $familia)
    {
        // 1. Verificar Permissão
        if ($familia->freguesia_id !== Auth::user()->freguesia_id) {
             return redirect()->route('freguesia.familias.index')->with('error', 'Não tem permissão para editar esta família.');
        }

        // 2. Carregar o agregado familiar (para preencher o formulário)
        $familia->load('agregadoFamiliar');

        // 3. Retornar a view de edição
        return view('freguesia.familias.editar', compact('familia'));
    }

    
    /**
     * Atualiza uma família na base de dados.
     */
    public function update(Request $request, Familia $familia)
    {
        // 1. Verificar Permissão
        if ($familia->freguesia_id !== Auth::user()->freguesia_id) {
             return redirect()->route('freguesia.familias.index')->with('error', 'Não tem permissão para editar esta família.');
        }

        // 2. Validar os dados
        $dadosValidados = $request->validate([
            'ano_instalacao' => 'required|integer|min:1900|max:'.date('Y'),
            'nacionalidade' => 'required|string|max:50',
            'tipologia_habitacao' => 'required|in:casa,quinta,apartamento',
            'tipologia_propriedade' => 'required|in:propria,arrendada',
            'adultos_laboral' => 'required|integer|min:0',
            'adultos_65_mais' => 'required|integer|min:0',
            'criancas' => 'required|integer|min:0',
        ]);

        try {
            // 3. Usar uma Transação
            DB::transaction(function () use ($dadosValidados, $familia) {
                
                // 3a. Atualizar a Família
                $familia->update([
                    'ano_instalacao' => $dadosValidados['ano_instalacao'],
                    'nacionalidade' => $dadosValidados['nacionalidade'],
                    'tipologia_habitacao' => $dadosValidados['tipologia_habitacao'],
                    'tipologia_propriedade' => $dadosValidados['tipologia_propriedade'],
                ]);

                // 3b. Atualizar ou Criar o Agregado Familiar associado
                $familia->agregadoFamiliar()->updateOrCreate(
                    ['familia_id' => $familia->id], // Condição para encontrar
                    [ // Dados para atualizar ou criar
                        'adultos_laboral' => $dadosValidados['adultos_laboral'],
                        'adultos_65_mais' => $dadosValidados['adultos_65_mais'],
                        'criancas' => $dadosValidados['criancas'],
                    ]
                );
            });

            // 4. Redirecionar
            return redirect()->route('freguesia.familias.index')->with('success', 'Família (Código: '.$familia->codigo.') atualizada com sucesso!');

        } catch (\Exception $e) {
            // 5. Em caso de erro
            return back()->withInput()->with('error', 'Erro ao atualizar a família: '.$e->getMessage());
        }
    }

    /**
     * Remove a família da base de dados.
     */
    public function destroy(Familia $familia)
    {
        if ($familia->freguesia_id !== Auth::user()->freguesia_id) {
             return redirect()->route('freguesia.familias.index')->with('error', 'Não tem permissão para apagar esta família.');
        }
        try {
            $codigo = $familia->codigo;
            $familia->delete();
            return redirect()->route('freguesia.familias.index')->with('success', "Família (Código: {$codigo}) foi apagada com sucesso.");
        } catch (\Exception $e) {
            return redirect()->route('freguesia.familias.index')->with('error', 'Ocorreu um erro ao apagar a família.');
        }
    }
}