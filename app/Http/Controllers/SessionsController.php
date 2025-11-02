<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class SessionsController extends Controller
{
    public function create()
    {
        return view('session.login-session');
    }

    public function store()
    {
        $attributes = request()->validate([
            'email'=>'required|email',
            'password'=>'required' 
        ]);

        if(Auth::attempt($attributes))
        {
            session()->regenerate();
            // MENSAGEM 1 TRADUZIDA:
            return redirect('dashboard')->with(['success'=>'Iniciou sessão com sucesso.']);
        }
        else{
            // MENSAGEM 2 TRADUZIDA:
            // Nota: Se criaste o ficheiro lang/pt/auth.php, podias usar:
            // return back()->withErrors(['email'=> trans('auth.failed')]);
            return back()->withErrors(['email'=>'Email ou password inválidos.']);
        }
    }
    
    public function destroy()
    {

        Auth::logout();

        // MENSAGEM 3 TRADUZIDA (A que procuravas):
        return redirect('/login')->with(['success'=>'Terminou a sessão com sucesso.']);
    }
}