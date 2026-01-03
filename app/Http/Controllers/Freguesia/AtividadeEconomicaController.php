<?php

namespace App\Http\Controllers\Freguesia;

use App\Http\Controllers\Controller;
use App\Models\AtividadeEconomica;
use App\Models\Familia;
use App\Models\SetorAtividade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * Controlador para gestão das atividades económicas associadas às famílias.
 */
class AtividadeEconomicaController extends Controller
{
    // Verifica se o utilizador tem permissão para gerir a atividade económica
    private function verificarPermissao(AtividadeEconomica $atividade)
    {
        // Verifica se a atividade pertence a uma família da freguesia do utilizador autenticado
        if ($atividade->familia->freguesia_id !== Auth::user()->freguesia_id) {
            abort(403, 'Acesso não autorizado.');
        }
    }

    // Mostra o formulário para criar uma nova atividade económica  
    public function create(Familia $familia)
    {
        // Verificar Permissão (na família)
        if ($familia->freguesia_id !== Auth::user()->freguesia_id) {
             return redirect()->route('freguesia.familias.index')->with('error', 'Não tem permissão para esta ação.');
        }
        // Carregar os setores de atividade ativos
        $setores = SetorAtividade::where('ativo', true)->orderBy('nome')->get();
        return view('freguesia.atividades.adicionar', compact('familia', 'setores'));
    }

    // Armazena a nova atividade económica na base de dados
    public function store(Request $request, Familia $familia)
    {
        // Verificar Permissão (na família)
        if ($familia->freguesia_id !== Auth::user()->freguesia_id) {
             return redirect()->route('freguesia.familias.index')->with('error', 'Não tem permissão para esta ação.');
        }

        // Validar os dados de entrada
        $dadosValidados = $request->validate([
            'tipo' => ['required', Rule::in(['conta_propria', 'conta_outrem'])],
            'setor_id' => 'required|exists:setor_atividades,id',
            'descricao' => 'nullable|string|max:500',
        ]);

        try {
            // Criar a nova atividade económica associada à família
            $familia->atividadesEconomicas()->create([
                'tipo' => $dadosValidados['tipo'],
                'setor_id' => $dadosValidados['setor_id'],
                'descricao' => $dadosValidados['descricao'],
            ]);

            // Redirecionar de volta para a PÁGINA DE EDIÇÃO DA FAMÍLIA
             return redirect()->route('freguesia.familias.edit', $familia->id)
                           ->with('success', 'Nova atividade económica adicionada!');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erro ao guardar a atividade: '.$e->getMessage());
        }
    }
    
    // Mostra o formulário para editar uma atividade económica existente
    public function edit(AtividadeEconomica $atividade)
    {
        // Verifica permissão
        $this->verificarPermissao($atividade);
        // Carrega os setores de atividade ativos
        $setores = SetorAtividade::where('ativo', true)->orderBy('nome')->get();
        // Retorna a vista de edição com os dados da atividade e os setores
        return view('freguesia.atividades.editar', [
            'atividade' => $atividade,
            'setores' => $setores
        ]);
    }

    // Atualiza a atividade económica na base de dados
    public function update(Request $request, AtividadeEconomica $atividade)
    {
        // Verifica permissão
        $this->verificarPermissao($atividade);

        //  Validação dos dados de entrada
        $dadosValidados = $request->validate([
            'tipo' => ['required', Rule::in(['conta_propria', 'conta_outrem'])],
            'setor_id' => 'required|exists:setor_atividades,id',
            'descricao' => 'nullable|string|max:500',
        ]);
        // Tenta atualizar a atividade económica
        try {
            $atividade->update($dadosValidados);

            // Redirecionar de volta para a PÁGINA DE EDIÇÃO DA FAMÍLIA
            return redirect()->route('freguesia.familias.edit', $atividade->familia_id)
                           ->with('success', 'Atividade económica atualizada.');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erro ao atualizar a atividade: '.$e->getMessage());
        }
    }

    // Apaga a atividade económica da base de dados
    public function destroy(AtividadeEconomica $atividade)
    {
        // Verifica permissão
        $this->verificarPermissao($atividade);
        // Tenta apagar a atividade económica
        try {
            $familia_id = $atividade->familia_id;
            $atividade->delete();

            // Redirecionar de volta para a PÁGINA DE EDIÇÃO DA FAMÍLIA
            return redirect()->route('freguesia.familias.edit', $familia_id)
                           ->with('success', 'Atividade económica foi apagada.');

        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao apagar a atividade.');
        }
    }

    // Mostra os detalhes de uma atividade económica
    public function show(AtividadeEconomica $atividade) 
    { 
        return $this->edit($atividade);
    }
}