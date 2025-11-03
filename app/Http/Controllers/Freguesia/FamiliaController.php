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
            // Obter o utilizador e carregar as relações necessárias
            $user = Auth::user();
            // Usar load() para garantir que as relações estão carregadas
            $freguesia = $user->freguesia->load('conselho');
            $conselho = $freguesia->conselho;
            
            if (!$conselho) {
                throw new \Exception("Não foi possível encontrar o concelho associado à freguesia.");
            }

            // ***** LÓGICA DAS INICIAIS ALTERADA *****

            // Gerar as iniciais (Ex: "Aranhas" -> "A" | "Castelo Branco" -> "CB")
            $iniciaisFreguesia = $this->gerarIniciais($freguesia->nome);
            $iniciaisConselho = $this->gerarIniciais($conselho->nome);
            
            // Combinar as iniciais
            // Começa com a inicial da Freguesia
            $iniciaisCompletas = $iniciaisFreguesia;
            
            // Adiciona a inicial do Concelho SÓ SE for diferente da inicial da Freguesia
            // (Ex: Freguesia "Penamacor" (P) e Concelho "Penamacor" (P) -> fica só "P")
            // (Ex: Freguesia "Aranhas" (A) e Concelho "Penamacor" (P) -> fica "AP")
            if ($iniciaisFreguesia != $iniciaisConselho) {
                $iniciaisCompletas .= $iniciaisConselho;
            }

            // ***** FIM DA ALTERAÇÃO *****

            // Obter o ano atual (Ex: 2025)
            $ano = date('Y');
            
            // Construir o prefixo (Ex: "FMAP2025-" ou "FMP2025-")
            $prefixo = 'FM'.$iniciaisCompletas.$ano.'-';

            // 3. Encontrar o último número sequencial para este prefixo
            $ultimoCodigo = Familia::where('codigo', 'like', $prefixo.'%')
                                    ->orderBy('codigo', 'desc')
                                    ->first();

            $novoNumero = 1;
            if ($ultimoCodigo) {
                // Extrai o número do código (Ex: "FMCB2025-0001" -> "0001")
                $numero = (int) substr($ultimoCodigo->codigo, -4);
                $novoNumero = $numero + 1;
            }

            // 4. Formatar o novo código (Ex: "FMAP2025-0001")
            $novoCodigo = $prefixo.str_pad($novoNumero, 4, '0', STR_PAD_LEFT);

            // 5. Usar uma Transação
            DB::transaction(function () use ($dadosValidados, $novoCodigo, $user, $freguesia) {
                
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

            // 6. Redirecionar
            return redirect()->route('freguesia.familias.index')->with('success', 'Nova família registada com sucesso!');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erro ao guardar a família: '.$e->getMessage());
        }
    }

    /**
     * Função privada para gerar iniciais a partir de um nome.
     * Ex: "Castelo Branco" -> "CB"
     * Ex: "Idanha-a-Nova" -> "IAN"
     * Ex: "Sertã" -> "S"
     */
    private function gerarIniciais($nome)
    {
        // Substitui traços por espaços (ex: Idanha-a-Nova -> Idanha a Nova)
        $nomeLimpo = str_replace('-', ' ', $nome);
        
        // Separa o nome por espaços
        $palavras = explode(' ', $nomeLimpo);
        $iniciais = '';

        // Pega na primeira letra de cada palavra
        foreach ($palavras as $palavra) {
            // Ignora palavras pequenas como 'e', 'da', 'do'
            if (strlen($palavra) > 0 && !in_array(strtolower($palavra), ['e', 'da', 'do', 'de', 'das', 'dos'])) {
                $iniciais .= Str::upper(substr($palavra, 0, 1));
            }
        }

        // Se o nome for só uma palavra (ex: "Sertã"), usa só a primeira letra
        if (empty($iniciais) && strlen($nome) > 0) {
            return Str::upper(substr($nome, 0, 1));
        }

        return $iniciais;
    }


    /* --- Resto dos métodos (show, edit, update, destroy) --- */
    
    public function show(Familia $familia) { /* ... */ }
    public function edit(Familia $familia) { /* ... */ }
    public function update(Request $request, Familia $familia) { /* ... */ }
    public function destroy(Familia $familia) { /* ... */ }
}