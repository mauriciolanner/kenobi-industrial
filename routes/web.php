<?php

use App\Http\Controllers\AllUsersController;
use App\Http\Controllers\FormConfigController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\APIUteisController;
use App\Http\Controllers\PushNotificationController;
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
