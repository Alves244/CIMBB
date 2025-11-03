<?php

namespace App\Http\Controllers\Freguesia;

use App\Http\Controllers\Controller;
use App\Models\InqueritoFreguesia; // O modelo que criámos
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InqueritoFreguesiaController extends Controller
{
    /**
     * Mostra a lista (histórico) de inquéritos submetidos pela freguesia.
     * (Isto permite ver o que foi feito nos anos anteriores)
     */
    public function index()
    {
        $user = Auth::user();
        $freguesiaId = $user->freguesia_id;

        // Busca todos os inquéritos desta freguesia, ordenados pelo ano mais recente
        $inqueritosPassados = InqueritoFreguesia::where('freguesia_id', $freguesiaId)
                                                ->orderBy('ano', 'desc')
                                                ->get();

        // Verifica se o inquérito para o ano atual já foi preenchido
        $anoAtual = date('Y');
        $jaPreencheuEsteAno = $inqueritosPassados->contains('ano', $anoAtual);

        // Passa os dados para a view
        return view('freguesia.inqueritos.index', [
            'inqueritos' => $inqueritosPassados,
            'jaPreencheuEsteAno' => $jaPreencheuEsteAno,
            'anoAtual' => $anoAtual
        ]);
    }

    /**
     * Mostra o formulário para criar um novo inquérito.
     */
    public function create()
    {
        $anoAtual = date('Y');
        $freguesiaId = Auth::user()->freguesia_id;

        // Segurança: Verifica novamente se já preencheu este ano
        $existe = InqueritoFreguesia::where('freguesia_id', $freguesiaId)
                                    ->where('ano', $anoAtual)
                                    ->exists();

        if ($existe) {
            // Se já preencheu, não pode criar outro. Volta para a lista.
            return redirect()->route('freguesia.inqueritos.index')
                             ->with('error', 'O inquérito para o ano '.$anoAtual.' já foi preenchido.');
        }

        // Mostra o formulário de adição
        return view('freguesia.inqueritos.adicionar', ['anoAtual' => $anoAtual]);
    }

    /**
     * Guarda o novo inquérito na base de dados.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $anoAtual = date('Y');

        // Validação (baseada no PDF)
        $dadosValidados = $request->validate([
            'escala_integracao' => 'required|integer|min:1|max:5',
            'aspectos_positivos' => 'nullable|string|max:2000',
            'aspectos_negativos' => 'nullable|string|max:2000',
            'satisfacao_global' => 'required|integer|min:1E|max:5',
            'sugestoes' => 'nullable|string|max:2000',
        ]);

        try {
            InqueritoFreguesia::create([
                'freguesia_id' => $user->freguesia_id,
                'utilizador_id' => $user->id,
                'ano' => $anoAtual,
                'escala_integracao' => $dadosValidados['escala_integracao'],
                'aspectos_positivos' => $dadosValidados['aspectos_positivos'],
                'aspectos_negativos' => $dadosValidados['aspectos_negativos'],
                'satisfacao_global' => $dadosValidados['satisfacao_global'],
                'sugestoes' => $dadosValidados['sugestoes'],
            ]);

            // Redireciona para a lista (histórico) com sucesso
            return redirect()->route('freguesia.inqueritos.index')
                           ->with('success', 'Inquérito do ano '.$anoAtual.' guardado com sucesso!');

        } catch (\Exception $e) {
            // Se falhar (ex: tentar submeter 2x ao mesmo tempo)
            return back()->withInput()->with('error', 'Erro ao guardar o inquérito: '.$e->getMessage());
        }
    }
}