<?php

namespace App\Http\Controllers\Freguesia;

use App\Http\Controllers\Controller;
use App\Models\Familia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;    

class FamiliaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obter o ID da freguesia do utilizador logado
        $freguesiaId = Auth::user()->freguesia_id;

        // Verificar se o utilizador tem uma freguesia associada
        if (!$freguesiaId) {
            // Redirecionar ou mostrar erro se não tiver freguesia (não deveria acontecer para este perfil)
            return redirect()->route('dashboard')->with('error', 'Utilizador sem freguesia associada.');
        }

        // Buscar apenas as famílias pertencentes a essa freguesia
        // Opcional: Ordenar por ano ou código, paginar resultados, etc.
        $familias = Familia::where('freguesia_id', $freguesiaId)
                            ->orderBy('ano_instalacao', 'desc') // Exemplo: ordenar por ano mais recente
                            ->paginate(15); // Exemplo: mostrar 15 por página

        // Retornar a view da listagem, passando os dados das famílias
        // A view estará em resources/views/freguesia/familias/index.blade.php
        return view('freguesia.familias.index', compact('familias'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Familia $familia)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Familia $familia)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Familia $familia)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Familia $familia)
    {
        //
    }
}
