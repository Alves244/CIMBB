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
        $freguesiaId = Auth::user()->freguesia_id;

        if (!$freguesiaId) {
            return redirect()->route('dashboard')->with('error', 'Utilizador sem freguesia associada.');
        }

        // Carrega as famílias E o seu agregadoFamiliar associado
        $familias = Familia::with('agregadoFamiliar')
                            ->where('freguesia_id', $freguesiaId)
                            ->orderBy('ano_instalacao', 'desc')
                            ->paginate(15);

        // Volta a colocar a linha original
        return view('freguesia.familias.listar', compact('familias'));
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
