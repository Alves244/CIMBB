<?php

use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InfoUserController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ResetController;
use App\Http\Controllers\SessionsController;
// --- Adiciona aqui os teus Controllers quando os criares ---
// use App\Http\Controllers\Admin\UserController as AdminUserController;
// use App\Http\Controllers\Funcionario\RelatorioController;
use App\Http\Controllers\Freguesia\FamiliaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas Públicas (Guest) - Para utilizadores não autenticados
|--------------------------------------------------------------------------
*/
Route::group(['middleware' => 'guest'], function () {
    // Registo (Considera remover/proteger se apenas Admins podem registar)
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);

    // Login
    Route::get('/login', [SessionsController::class, 'create'])->name('login');
    Route::post('/session', [SessionsController::class, 'store']);

    // Recuperação de Password
    Route::get('/login/forgot-password', [ResetController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [ResetController::class, 'sendEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetController::class, 'resetPass'])->name('password.reset');
    Route::post('/reset-password', [ChangePasswordController::class, 'changePassword'])->name('password.update');
});

/*
|--------------------------------------------------------------------------
| Rotas Autenticadas (Requerem login)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // Rota inicial e Dashboard genérico (acessível a todos os logados)
    Route::get('/', [HomeController::class, 'home'])->name('home');
    Route::get('/dashboard', function () {
        // Poderias adicionar lógica aqui para redirecionar para dashboards específicos
        // if (auth()->user()->isAdmin()) { return redirect()->route('admin.dashboard'); }
        // elseif (auth()->user()->isFuncionario()) { return redirect()->route('funcionario.dashboard'); }
        // elseif (auth()->user()->isFreguesia()) { return redirect()->route('freguesia.dashboard'); }
        return view('dashboard'); // Ou mostrar um dashboard genérico
    })->name('dashboard');

    // Perfil de utilizador (comum a todos)
    Route::get('profile', function () { return view('profile'); })->name('profile');
    Route::get('/user-profile', [InfoUserController::class, 'create']);
    Route::post('/user-profile', [InfoUserController::class, 'store']);

    // Logout (comum a todos)
    Route::get('/logout', [SessionsController::class, 'destroy'])->name('logout');

    // --- Rotas Específicas por Perfil ---

    /*
    |--------------------------------------------------------------------------
    | Rotas do Admin (Middleware 'admin')
    |--------------------------------------------------------------------------
    */
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        // Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard'); // Exemplo
        Route::get('user-management', function () { return view('laravel-examples/user-management'); })->name('user-management'); // Rota do template
        // TODO: Adicionar rotas para:
        // - Gerir Utilizadores (CRUD)
        // - Configurar Concelhos (CRUD)
        // - Configurar Freguesias (CRUD)
        // - Gerir Tickets (Ver todos, Responder)
        // - Ver Logs
        // - Gerir Parâmetros
    });

    /*
    |--------------------------------------------------------------------------
    | Rotas do Funcionário CIMBB (Middleware 'funcionario' - permite Admin também)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['funcionario'])->prefix('funcionario')->name('funcionario.')->group(function () {
        // Route::get('/dashboard', [FuncionarioDashboardController::class, 'index'])->name('dashboard'); // Exemplo
        // TODO: Adicionar rotas para:
        // - Consultar Dashboard Regional
        // - Gerar Relatórios / Exportar
        // - Analisar Tendências
    });

    /*
    |--------------------------------------------------------------------------
    | Rotas do Funcionário da Freguesia (Middleware 'freguesia')
    |--------------------------------------------------------------------------
    */
    Route::middleware(['freguesia'])->prefix('freguesia')->name('freguesia.')->group(function () {
        // Route::get('/dashboard', [FreguesiaDashboardController::class, 'index'])->name('dashboard'); // Exemplo
        // TODO: Adicionar rotas para:
        // - Familias (CRUD) -> listar apenas da sua freguesia
        // - Atividades Económicas (CRUD) -> associadas às famílias da sua freguesia
        // - Inquérito Anual (CRUD) -> associados às famílias da sua freguesia
        // - Criar/Ver Tickets de Suporte

    
        Route::resource('familias', FamiliaController::class);
        // Esta linha cria automaticamente as rotas para:
        // - GET /freguesia/familias -> index (Listar) -> nome: freguesia.familias.index
        // - GET /freguesia/familias/create -> create (Mostrar formulário de criação) -> nome: freguesia.familias.create
        // - POST /freguesia/familias -> store (Guardar nova família) -> nome: freguesia.familias.store
        // - GET /freguesia/familias/{familia} -> show (Mostrar detalhes) -> nome: freguesia.familias.show
        // - GET /freguesia/familias/{familia}/edit -> edit (Mostrar formulário de edição) -> nome: freguesia.familias.edit
        // - PUT/PATCH /freguesia/familias/{familia} -> update (Atualizar família) -> nome: freguesia.familias.update
        // - DELETE /freguesia/familias/{familia} -> destroy (Apagar família) -> nome: freguesia.familias.destroy   
    });

     // --- Rotas do Template (Avaliar se são necessárias ou movê-las para grupos específicos) ---
     Route::get('billing', function () { return view('billing'); })->name('billing');
     Route::get('tables', function () { return view('tables'); })->name('tables');
     Route::get('virtual-reality', function () { return view('virtual-reality'); })->name('virtual-reality');
     Route::get('rtl', function () { return view('rtl'); })->name('rtl');

});