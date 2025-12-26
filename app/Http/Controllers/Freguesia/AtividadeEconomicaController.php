<?php

namespace App\Http\Controllers\Freguesia;

use App\Http\Controllers\Controller;
use App\Models\AtividadeEconomica;
use App\Models\Familia;
use App\Models\SetorAtividade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

// Gere a recolha de dados qualitativos sobre a situação laboral das famílias na freguesia
class AtividadeEconomicaController extends Controller
{
    // Validação de segurança para garantir que os dados pertencem à área geográfica do utilizador
    private function verificarPermissao(AtividadeEconomica $atividade)
    {
        // Impede que um utilizador de uma freguesia aceda a dados de outra (Requisito de Segurança)
        if ($atividade->familia->freguesia_id !== Auth::user()->freguesia_id) {
            abort(403, 'Acesso não autorizado.');
        }
    }

    // Apresenta o formulário para registar a dinâmica económica de um agregado familiar
    public function create(Familia $familia)
    {
        // Verifica se a família reside no território de competência do utilizador autenticado
        if ($familia->freguesia_id !== Auth::user()->freguesia_id) {
             return redirect()->route('freguesia.familias.index')->with('error', 'Não tem permissão para esta ação.');
        }

        // Obtém os setores definidos pelos stakeholders para garantir a consistência estatística
        $setores = SetorAtividade::where('ativo', true)->orderBy('nome')->get();
        return view('freguesia.atividades.adicionar', compact('familia', 'setores'));
    }

    // Guarda os dados da atividade, permitindo monitorizar o tipo de inserção no mercado de trabalho
    public function store(Request $request, Familia $familia)
    {
        // Reforço da validação geográfica antes da persistência dos dados
        if ($familia->freguesia_id !== Auth::user()->freguesia_id) {
             return redirect()->route('freguesia.familias.index')->with('error', 'Não tem permissão para esta ação.');
        }

        // Valida se a ocupação é por conta própria ou outrem, conforme os parâmetros do estudo
        $dadosValidados = $request->validate([
            'tipo' => ['required', Rule::in(['conta_propria', 'conta_outrem'])],
            'setor_id' => 'required|exists:setor_atividades,id',
            'descricao' => 'nullable|string|max:500',
        ]);

        try {
            // Associa a nova atividade à família para análise qualitativa do impacto local
            $familia->atividadesEconomicas()->create([
                'tipo' => $dadosValidados['tipo'],
                'setor_id' => $dadosValidados['setor_id'],
                'descricao' => $dadosValidados['descricao'],
            ]);

            return redirect()->route('freguesia.familias.edit', $familia->id)
                           ->with('success', 'Nova atividade económica adicionada!');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erro ao guardar a atividade: '.$e->getMessage());
        }
    }
    
    // Permite a atualização de dados laborais em caso de mudança na situação da família
    public function edit(AtividadeEconomica $atividade)
    {
        $this->verificarPermissao($atividade);

        $setores = SetorAtividade::where('ativo', true)->orderBy('nome')->get();
        
        return view('freguesia.atividades.editar', [
            'atividade' => $atividade,
            'setores' => $setores
        ]);
    }

    // Atualiza a informação económica garantindo que os dados permanecem atuais e pertinentes
    public function update(Request $request, AtividadeEconomica $atividade)
    {
        $this->verificarPermissao($atividade);

        $dadosValidados = $request->validate([
            'tipo' => ['required', Rule::in(['conta_propria', 'conta_outrem'])],
            'setor_id' => 'required|exists:setor_atividades,id',
            'descricao' => 'nullable|string|max:500',
        ]);

        try {
            $atividade->update($dadosValidados);

            return redirect()->route('freguesia.familias.edit', $atividade->familia_id)
                           ->with('success', 'Atividade económica atualizada.');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erro ao atualizar a atividade: '.$e->getMessage());
        }
    }

    // Remove registos económicos para manter a base de dados limpa e consistente
    public function destroy(AtividadeEconomica $atividade)
    {
        $this->verificarPermissao($atividade);
        
        try {
            $familia_id = $atividade->familia_id;
            $atividade->delete();

            return redirect()->route('freguesia.familias.edit', $familia_id)
                           ->with('success', 'Atividade económica foi apagada.');

        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao apagar a atividade.');
        }
    }

    // Redireciona para a edição para centralizar a gestão da informação
    public function show(AtividadeEconomica $atividade) 
    { 
        return $this->edit($atividade);
    }
}