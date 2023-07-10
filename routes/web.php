<?php

use App\Http\Controllers\AllUsersController;
use App\Http\Controllers\FormConfigController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\APIUteisController;
use App\Http\Controllers\PushNotificationController;
use App\Http\Controllers\ApontamentoImpressoController;
use App\Http\Controllers\ConsultaApontamentoController;
use App\Http\Controllers\ConsultaApontamentoMatriz;
use App\Http\Controllers\ConsultaSaldoLOController;
use App\Http\Controllers\MecaluxController;
use App\Http\Controllers\TesteController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;




/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

//rotas de formulário
require 'form.php';

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/', fn () => Inertia::render('Dashboard'))->name('dashboard');
});


Route::middleware(['auth:sanctum', 'verified'])->controller(PushNotificationController::class)->group(function () {
    Route::get('API/notificacao/todas', 'index')->name('notifications.all');
    Route::get('/notificacao/usuario/deletar/{id}', 'delete')->name('notifications.delet');
    Route::get('/notificacao/usuario/limparTudo', 'deleteAll')->name('notifications.clear');
    Route::get('/notificacao/formularios', 'opemTask')->name('notifications.opemTask');
});

//Rotas gerais de formulários
Route::middleware(['auth:sanctum', 'verified'])->controller(FormController::class)->group(function () {
    Route::get('/dashboard', 'dashboard')->name('forms.dashboard');
    Route::get('/formularios', 'index')->name('forms.index');
    Route::get('/form/ver/{id}', 'show')->name('form.view');
    Route::post('/form/destroy/{id}', 'destroy')->name('form.destroy');
    //rotas de API para uso do Axios
    Route::get('/API/allForms', 'allForms')->name('api.forms.allFormsList');
});

//Configurações de formulário
Route::middleware(['auth:sanctum', 'verified'])->controller(FormConfigController::class)->group(function () {
    Route::get('/formulario/configuracoes', 'index')->name('form.config');
    Route::post('/formulario/criar', 'store')->name('form.config.store');
    Route::get('/formulario/configuracao/{id}', 'configView')->name('form.config.config');
    Route::post('/formulario/criarTarefa', 'storeTask')->name('form.config.create');
    Route::get('/formulario/deletaTarefa/{id}', 'delet')->name('form.config.delet');
    Route::post('/formulario/editar', 'edit')->name('form.config.edit');
});


Route::middleware(['auth:sanctum', 'verified'])->controller(AllUsersController::class)->group(function () {
    Route::get('/usuarios/master', 'index')->name('usuarios.master');
    Route::post('/usuarios/store', 'store')->name('usuarios.store');
    Route::post('/usuarios/altarar/senha', 'password')->name('usuarios.password');
    Route::get('/usuario/bloquear/{id}', 'block')->name('usuarios.bloquear');
    Route::get('/logoutUser', 'logout')->name('deslogar');
    Route::post('/usuario/perfil/alteraColigada', 'alteraColigada')->name('usuarios.alteraColigada');
});

//Rotas APIS uteis
Route::middleware(['auth:sanctum', 'verified'])->controller(APIUteisController::class)->group(function () {
    Route::get('/API/usuarios', 'usuarios')->name('API.utei.usuarios');
    Route::get('/API/roles', 'roles')->name('API.utei.roles');
    Route::get('/API/funcionarios', 'funcionarios')->name('API.utei.funcionarios');
});

//rotas de Fardo
Route::middleware(['auth:sanctum', 'verified'])->controller(TesteController::class)->group(function () {
    Route::get('/consultafardo', 'index')->name('consulta.fardo');
    Route::post('/fardo/pdf/imprimir/EtiquetaFardo', 'fardoPdf')->name('fardo.pdf');
    Route::post('/fardo/pdf/EtiquetaFardo/reimpressao', 'fardoPdfReimpressao')->name('fardo.reimprimir.pdf');
    Route::get('/fardo/consulta/{op}/impressoes', 'consultaNumeroDeImpressoes')->name('fardo.impressoes');
    Route::get('/fardo/pdf/{id}/{op}/{matricula}/{turno}/EtiquetaDupla', 'etiquetaduplaPdf')->name('etiquetadupla.pdf');
});

//rotas de Apontamento
Route::middleware(['auth:sanctum', 'verified'])->controller(ConsultaApontamentoController::class)->group(function () {
    Route::get('/consultaapontamento', 'index')->name('consulta.apontamento');
    Route::get('/apontamento/pdf/{recno}/{op}/EtiquetaApontamento', 'apontamentoPdf')->name('apontamento.pdf');
    Route::get('/API/consultaapontamento', 'APIconsulta')->name('apontamento.APIconsulta');
});

Route::middleware(['auth:sanctum', 'verified'])->controller(ConsultaApontamentoMatriz::class)->group(function () {
    Route::get('/consultaapontamento/matriz', 'index')->name('consulta.apontamento.matriz');
    Route::get('/apontamento/pdf/{op}/EtiquetaApontamento/matriz', 'apontamentoPdf')->name('apontamento.pdf.matriz');
    Route::get('/API/consultaapontamento/matriz', 'APIconsulta')->name('apontamento.APIconsulta.matriz');
});

//rotas de Apontamento já impressos
Route::middleware(['auth:sanctum', 'verified'])->controller(ConsultaSaldoLOController::class)->group(function () {
    Route::get('/consultasaldo', 'index')->name('consulta.saldo');
    Route::get('/consultasaldo/{produto_id}/{lote}')->name('apontamento.pdf');
});

//rotas do Relatorio de Estoque
Route::middleware(['auth:sanctum', 'verified'])->controller(RelatorioEstoqueController::class)->group(function () {
    Route::get('/relatorioestoque', 'Index')->name('consulta.relatoriodeestoque');
    Route::get('/controledeestoque/post', 'relatoriodeestoque')->name('post.relatoriodeestoque');
    Route::get('/controledeestoque/sql', 'consultarelatorio')->name('post.relatoriodeestoque.teste');
});

//rotas de Apontamento já impressos
Route::middleware(['auth:sanctum', 'verified'])->controller(ApontamentoImpressoController::class)->group(function () {
    Route::get('/apontamentoimpresso', 'index')->name('consulta.impressas');
    Route::get('/impressas/pdf/{recno}/{op}/{numseq}/ApontamentoImpresso', 'impressasPdf')->name('apontamento.impressas.pdf');
});

//rotas de Apontamento já impressos
Route::middleware(['auth:sanctum', 'verified'])->controller(MecaluxController::class)->group(function () {
    Route::get('/etiquetaMecalux', 'index')->name('mecalux.index');
    Route::get('/Api/etiquetaMecalux', 'APIMecaluxRecurso')->name('mecalux.apiEtiquetas');
});
