<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Familia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User; // <-- 1. ADICIONA ESTE IMPORT NO TOPO

class HomeController extends Controller
{
     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function home()
    {
        // Vamos redirecionar a rota '/' (home) para o novo 'dashboard' com dados
        return redirect()->route('dashboard');
    }

    /**
     * Mostra o dashboard principal da aplicação com dados.
     * (ESTE É O NOVO MÉTODO)
     */
    public function dashboard()
    {
        /** @var \App\Models\User $user */ // <-- 2. ADICIONA ESTA "DICA" PHPDoc
        $user = Auth::user();
        
        // 1. Preparar a consulta de famílias (baseado no perfil)
        $query = Familia::query();

        if ($user->isFreguesia()) { // <-- O sublinhado vermelho deve desaparecer
            // Se for 'freguesia', mostra SÓ os da sua freguesia
            $query->where('freguesia_id', $user->freguesia_id);
        }
        // Se for 'cimbb' ou 'admin', mostra todos (não adiciona filtro)

        // 2. Contar as nacionalidades
        $nacionalidadesData = $query->select('nacionalidade', DB::raw('count(*) as total'))
                                    ->groupBy('nacionalidade')
                                    ->orderBy('total', 'desc')
                                    ->limit(10) // Limitar às 10 principais
                                    ->get();

        // 3. Preparar dados para o Chart.js
        $chartLabels = $nacionalidadesData->pluck('nacionalidade');
        $chartValues = $nacionalidadesData->pluck('total');

        // 4. Passar os dados para a view
        return view('dashboard', [
            'title' => 'Página Inicial', // Isto muda o título na navbar!
            'userName' => $user->nome,
            'chartLabels' => $chartLabels,
            'chartValues' => $chartValues,
        ]);
    }
}