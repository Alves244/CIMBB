<?php

namespace App\Http\Controllers\Freguesia;

use App\Http\Controllers\Controller;
use App\Models\AtividadeEconomica;
use App\Models\Familia;
use App\Models\SetorAtividade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule; // Adicionado para validação do 'in'

class AtividadeEconomicaController extends Controller
{
    /**
     * Helper para verificar se o utilizador atual pode aceder a esta atividade.
     * (Verifica se a atividade pertence a uma família da freguesia do utilizador)
     */
    private function verificarPermissao(AtividadeEconomica $atividade)
    {
        // Carrega a família da atividade e compara a freguesia_id com a do utilizador
        // (Usa a relação 'familia')
        if ($atividade->familia->freguesia_id !== Auth::user()->freguesia_id) {
            abort(403, 'Acesso não autorizado.'); // Pára a execução
        }
    }

    /**
     * Mostra o formulário para criar uma nova atividade para uma família específica.
     * (Carrega o seu 'adicionar.blade.php')
     */
    public function create(Familia $familia)
    {
        // Verificar permissão (na família)
        if ($familia->freguesia_id !== Auth::user()->freguesia_id) {
             return redirect()->route('freguesia.familias.index')->with('error', 'Não tem permissão para esta ação.');
        }

        $setores = SetorAtividade::where('ativo', true)->orderBy('nome')->get();
        return view('freguesia.atividades.adicionar', compact('familia', 'setores'));
    }

    /**
     * Guarda a nova atividade económica na base de dados.
     */
    public function store(Request $request, Familia $familia)
    {
        // 1. Verificar Permissão (na família)
        if ($familia->freguesia_id !== Auth::user()->freguesia_id) {
             return redirect()->route('freguesia.familias.index')->with('error', 'Não tem permissão para esta ação.');
        }

        // 2. Validar
        $dadosValidados = $request->validate([
            'tipo' => ['required', Rule::in(['conta_propria', 'conta_outrem'])], // Validação correta
            'setor_id' => 'required|exists:setor_atividades,id',
            'descricao' => 'nullable|string|max:500',
        ]);

        try {
            // 3. Criar (Usa a relação 'atividadesEconomicas')
            $familia->atividadesEconomicas()->create([
                'tipo' => $dadosValidados['tipo'],
                'setor_id' => $dadosValidados['setor_id'],
                'descricao' => $dadosValidados['descricao'],
            ]);

            // 4. Redirecionar para a PÁGINA DE EDIÇÃO DA FAMÍLIA
             return redirect()->route('freguesia.familias.edit', $familia->id)
                           ->with('success', 'Nova atividade económica adicionada!');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erro ao guardar a atividade: '.$e->getMessage());
        }
    }
    
    /**
     * Mostra o formulário para editar uma atividade existente.
     * (Carrega o seu 'editar.blade.php')
     */
    public function edit(AtividadeEconomica $atividade)
    {
        $this->verificarPermissao($atividade); // Verifica se o user pode editar esta atividade

        $setores = SetorAtividade::where('ativo', true)->orderBy('nome')->get();
        
        return view('freguesia.atividades.editar', [
            'atividade' => $atividade,
            'setores' => $setores
        ]);
    }

    /**
     * Atualiza a atividade económica na base de dados.
     */
    public function update(Request $request, AtividadeEconomica $atividade)
    {
        $this->verificarPermissao($atividade); // Verifica permissão

        // Validar os dados
        $dadosValidados = $request->validate([
            'tipo' => ['required', Rule::in(['conta_propria', 'conta_outrem'])],
            'setor_id' => 'required|exists:setor_atividades,id',
            'descricao' => 'nullable|string|max:500',
        ]);

        try {
            $atividade->update($dadosValidados);

            // Redirecionar de volta para a PÁGINA DE EDIÇÃO DA FAMÍLIA
            return redirect()->route('freguesia.familias.edit', $atividade->familia_id)
                           ->with('success', 'Atividade económica atualizada.');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erro ao atualizar a atividade: '.$e->getMessage());
        }
    }

    /**
     * Apaga a atividade económica da base de dados.
     */
    public function destroy(AtividadeEconomica $atividade)
    {
        $this->verificarPermissao($atividade); // Verifica permissão
        
        try {
            $familia_id = $atividade->familia_id; // Guarda o ID da família
            $atividade->delete();

            // Redirecionar de volta para a PÁGINA DE EDIÇÃO DA FAMÍLIA
            return redirect()->route('freguesia.familias.edit', $familia_id)
                           ->with('success', 'Atividade económica foi apagada.');

        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao apagar a atividade.');
        }
    }

    // O método show() não é necessário se não o estiver a usar
    public function show(AtividadeEconomica $atividade) 
    { 
        return $this->edit($atividade); // Por agora, redireciona para a edição
    }
}