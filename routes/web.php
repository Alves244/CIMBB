<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SessionsController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ResetController;
use App\Http\Controllers\InfoUserController;
use App\Http\Controllers\ChangePasswordController; // <--- IMPORTANTE
use App\Http\Controllers\Freguesia\FamiliaController;
use App\Http\Controllers\Freguesia\InqueritoFreguesiaController;
use App\Http\Controllers\Freguesia\TicketSuporteController;
use App\Http\Controllers\Freguesia\GraficosFreguesiaController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminTicketController;
use App\Http\Controllers\Admin\AdminLogController;
use App\Http\Controllers\Admin\AdminConcelhoController;
use App\Http\Controllers\Admin\AdminAgrupamentoController;
use App\Http\Controllers\Admin\AdminFreguesiaController;
use App\Http\Controllers\Admin\InqueritoPeriodoController;
use App\Http\Controllers\Funcionario\DashboardController as FuncionarioDashboardController;
use App\Http\Controllers\Funcionario\RelatorioController;
use App\Http\Controllers\Funcionario\ExportarDadosController;
use App\Http\Controllers\Agrupamento\InqueritoAgrupamentoController;
use App\Http\Controllers\Agrupamento\TicketSuporteController as AgrupamentoTicketSuporteController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- ROTAS PÚBLICAS / GUEST ---
Route::group(['middleware' => 'guest'], function () {
    Route::redirect('/', '/login');
    
    // ESTA LINHA É OBRIGATÓRIA:
    Route::get('login', [SessionsController::class, 'create'])->name('login');
    
    Route::post('session', [SessionsController::class, 'store']);
    Route::get('/forgot-password', [ResetController::class, 'create']);
    Route::post('/forgot-password', [ResetController::class, 'sendEmail']);
    Route::get('/reset-password/{token}', [ResetController::class, 'resetPass'])->name('password.reset');
    Route::post('/reset-password', [SessionsController::class, 'reset']);
});

// --- ROTAS PROTEGIDAS (AUTH) ---
Route::group(['middleware' => 'auth'], function () {
    Route::get('dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
    Route::get('/logout', [SessionsController::class, 'destroy'])->name('logout');
    
    // Perfil de Utilizador
    Route::get('user-profile', [InfoUserController::class, 'create'])->name('user-profile');
    Route::post('user-profile', [InfoUserController::class, 'store']);
    
    // Rota para Alterar Password (A QUE FALTAVA)
    Route::post('/user-profile/password', [ChangePasswordController::class, 'changePassword'])->name('user-profile.password');

});

// --- GRUPO DE ROTAS DA FREGUESIA ---
Route::group(['prefix' => 'freguesia', 'as' => 'freguesia.', 'middleware' => ['auth', 'check_freguesia']], function () {
    
    // Famílias
    Route::get('/familias', [FamiliaController::class, 'index'])->name('familias.index');
    Route::get('/familias/adicionar', [FamiliaController::class, 'create'])->name('familias.create');
    Route::post('/familias', [FamiliaController::class, 'store'])->name('familias.store');
    Route::get('/familias/{familia}/editar', [FamiliaController::class, 'edit'])->name('familias.edit');
    Route::put('/familias/{familia}', [FamiliaController::class, 'update'])->name('familias.update');
    Route::delete('/familias/{familia}', [FamiliaController::class, 'destroy'])->name('familias.destroy');

    // Inquéritos
    Route::get('/inqueritos', [InqueritoFreguesiaController::class, 'index'])->name('inqueritos.index');
    Route::get('/inqueritos/novo', [InqueritoFreguesiaController::class, 'create'])->name('inqueritos.create');
    Route::post('/inqueritos', [InqueritoFreguesiaController::class, 'store'])->name('inqueritos.store');
    Route::get('/inqueritos/{inquerito}', [InqueritoFreguesiaController::class, 'show'])->name('inqueritos.show');
    Route::get('/inqueritos/{inquerito}/editar', [InqueritoFreguesiaController::class, 'edit'])->name('inqueritos.edit');
    Route::put('/inqueritos/{inquerito}', [InqueritoFreguesiaController::class, 'update'])->name('inqueritos.update');

    // Suporte
    Route::get('/suporte', [TicketSuporteController::class, 'index'])->name('suporte.index');
    Route::get('/suporte/novo', [TicketSuporteController::class, 'create'])->name('suporte.create');
    Route::post('/suporte', [TicketSuporteController::class, 'store'])->name('suporte.store');
    Route::get('/suporte/{ticket}', [TicketSuporteController::class, 'show'])->name('suporte.show');
    Route::post('/suporte/{ticket}/mensagens', [TicketSuporteController::class, 'storeMessage'])->name('suporte.mensagens.store');

    // Gráficos
    Route::get('/analise-dados', [GraficosFreguesiaController::class, 'index'])->name('graficos.index');
});

// --- GRUPO DE ROTAS DO ADMINISTRADOR ---
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth', 'check_admin']], function () {
    
    // Gerir Utilizadores
    Route::get('/utilizadores', [AdminUserController::class, 'index'])->name('users.index');
    Route::post('/utilizadores', [AdminUserController::class, 'store'])->name('users.store');
    Route::put('/utilizadores/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::put('/utilizadores/{user}/password', [AdminUserController::class, 'updatePassword'])->name('users.password');
    Route::delete('/utilizadores/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

    // Gerir Concelhos
    Route::get('/concelhos', [AdminConcelhoController::class, 'index'])->name('concelhos.index');
    Route::post('/concelhos', [AdminConcelhoController::class, 'store'])->name('concelhos.store');
    Route::put('/concelhos/{concelho}', [AdminConcelhoController::class, 'update'])->name('concelhos.update');
    Route::delete('/concelhos/{concelho}', [AdminConcelhoController::class, 'destroy'])->name('concelhos.destroy');

    // Gerir Agrupamentos
    Route::get('/agrupamentos', [AdminAgrupamentoController::class, 'index'])->name('agrupamentos.index');
    Route::post('/agrupamentos', [AdminAgrupamentoController::class, 'store'])->name('agrupamentos.store');
    Route::put('/agrupamentos/{agrupamento}', [AdminAgrupamentoController::class, 'update'])->name('agrupamentos.update');
    Route::delete('/agrupamentos/{agrupamento}', [AdminAgrupamentoController::class, 'destroy'])->name('agrupamentos.destroy');

    // Gerir Freguesias
    Route::get('/freguesias', [AdminFreguesiaController::class, 'index'])->name('freguesias.index');
    Route::post('/freguesias', [AdminFreguesiaController::class, 'store'])->name('freguesias.store');
    Route::put('/freguesias/{freguesia}', [AdminFreguesiaController::class, 'update'])->name('freguesias.update');
    Route::delete('/freguesias/{freguesia}', [AdminFreguesiaController::class, 'destroy'])->name('freguesias.destroy');

    // Gerir Suporte
    Route::get('/tickets', [AdminTicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/{ticket}', [AdminTicketController::class, 'show'])->name('tickets.show');
    Route::put('/tickets/{ticket}/reply', [AdminTicketController::class, 'reply'])->name('tickets.reply');

    // Logs do Sistema
    Route::get('/logs', [AdminLogController::class, 'index'])->name('logs.index');

    // Períodos dos inquéritos
    Route::get('/inqueritos/periodos', [InqueritoPeriodoController::class, 'index'])->name('inqueritos.periodos.index');
    Route::post('/inqueritos/periodos', [InqueritoPeriodoController::class, 'store'])->name('inqueritos.periodos.store');
    Route::put('/inqueritos/periodos/{periodo}', [InqueritoPeriodoController::class, 'update'])->name('inqueritos.periodos.update');
    Route::delete('/inqueritos/periodos/{periodo}', [InqueritoPeriodoController::class, 'destroy'])->name('inqueritos.periodos.destroy');
});

// --- GRUPO ROTAS AGRUPAMENTO DE ESCOLAS ---
Route::group([
    'prefix' => 'agrupamento',
    'as' => 'agrupamento.',
    'middleware' => ['auth', 'check_agrupamento'],
], function () {
    Route::get('/inqueritos', [InqueritoAgrupamentoController::class, 'index'])->name('inqueritos.index');
    Route::get('/inqueritos/novo', [InqueritoAgrupamentoController::class, 'create'])->name('inqueritos.create');
    Route::post('/inqueritos', [InqueritoAgrupamentoController::class, 'store'])->name('inqueritos.store');
    Route::get('/inqueritos/{inquerito}', [InqueritoAgrupamentoController::class, 'show'])->name('inqueritos.show');

    Route::get('/suporte', [AgrupamentoTicketSuporteController::class, 'index'])->name('suporte.index');
    Route::get('/suporte/novo', [AgrupamentoTicketSuporteController::class, 'create'])->name('suporte.create');
    Route::post('/suporte', [AgrupamentoTicketSuporteController::class, 'store'])->name('suporte.store');
    Route::get('/suporte/{ticket}', [AgrupamentoTicketSuporteController::class, 'show'])->name('suporte.show');
    Route::post('/suporte/{ticket}/mensagens', [AgrupamentoTicketSuporteController::class, 'storeMessage'])->name('suporte.mensagens.store');
});

// --- GRUPO ROTAS FUNCIONÁRIO CIMBB ---
Route::group([
    'prefix' => 'funcionario',
    'as' => 'funcionario.',
    'middleware' => ['auth', 'check_funcionario'],
], function () {
    Route::get('/dashboard', [FuncionarioDashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/relatorios', [RelatorioController::class, 'index'])->name('relatorios.index');
    Route::get('/relatorios/exportar', [RelatorioController::class, 'export'])->name('relatorios.export');
    Route::get('/relatorios/custom-chart', [RelatorioController::class, 'customChart'])->name('relatorios.custom-chart');
    Route::get('/exportar-dados', [ExportarDadosController::class, 'index'])->name('exportar.index');
    Route::post('/exportar-dados/pdf-concelho', [ExportarDadosController::class, 'exportEstatisticasConcelhoPdf'])->name('exportar.concelho.pdf');
    Route::post('/exportar-dados/pdf-freguesia', [ExportarDadosController::class, 'exportEstatisticasFreguesiaPdf'])->name('exportar.freguesia.pdf');
    Route::post('/exportar-dados/pdf-escolas', [ExportarDadosController::class, 'exportEstatisticasEscolasPdf'])->name('exportar.escolas.pdf');
    Route::post('/exportar-dados/pdf-inqueritos', [ExportarDadosController::class, 'exportInqueritosPdf'])->name('exportar.inqueritos.pdf');
    Route::post('/exportar-dados/pdf-estatisticas', [ExportarDadosController::class, 'exportEstatisticasPdf'])->name('exportar.estatisticas.pdf');
});