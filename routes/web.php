<?php

use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InfoUserController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ResetController;
use App\Http\Controllers\SessionsController;
use App\Http\Controllers\Freguesia\FamiliaController; // Importa o FamiliaController
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas Públicas (Guest)
|--------------------------------------------------------------------------
*/
Route::group(['middleware' => 'guest'], function () {
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
    Route::get('/login', [SessionsController::class, 'create'])->name('login');
    Route::post('/session', [SessionsController::class, 'store']);
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

    Route::get('/', [HomeController::class, 'home'])->name('home');
    
    // ***** ALTERAÇÃO AQUI *****
    // Esta rota agora chama o método 'dashboard' no HomeController
    Route::get('dashboard', [HomeController::class, 'dashboard'])->name('dashboard');

    // Rotas de Perfil (já funcionam)
    Route::get('profile', function () { return view('profile'); })->name('profile');
    Route::get('/user-profile', [InfoUserController::class, 'create']);
    Route::post('/user-profile', [InfoUserController::class, 'store']);
    Route::get('/logout', [SessionsController::class, 'destroy'])->name('logout');

    /*
    |--------------------------------------------------------------------------
    | Rotas do Admin (Middleware 'admin')
    |--------------------------------------------------------------------------
    */
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('user-management', function () { return view('laravel-examples/user-management'); })->name('user-management');
        // ... (futuras rotas admin)
    });

    /*
    |--------------------------------------------------------------------------
    | Rotas do Funcionário CIMBB (Middleware 'funcionario')
    |--------------------------------------------------------------------------
    */
    Route::middleware(['funcionario'])->prefix('funcionario')->name('funcionario.')->group(function () {
        // ... (futuras rotas funcionário)
    });

    /*
    |--------------------------------------------------------------------------
    | Rotas da Freguesia (Middleware 'freguesia')
    |--------------------------------------------------------------------------
    */
    Route::middleware(['freguesia'])->prefix('freguesia')->name('freguesia.')->group(function () {
        // Rota para Famílias (já funciona)
        Route::resource('familias', FamiliaController::class);
    });

     // --- Rotas do Template (Remover se não forem usadas) ---
     Route::get('billing', function () { return view('billing'); })->name('billing');
     Route::get('tables', function () { return view('tables'); })->name('tables');
     Route::get('virtual-reality', function () { return view('virtual-reality'); })->name('virtual-reality');
     Route::get('rtl', function () { return view('rtl'); })->name('rtl');

});