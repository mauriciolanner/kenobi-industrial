<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Forms\FormModeloController;
use App\Http\Controllers\Forms\ModeloExemploController;

Route::middleware(['auth:sanctum', 'verified'])->controller(FormModeloController::class)->group(function () {
    $slung = 'Modelo';
    Route::get('/form/' . $slung . '', 'index')->name('form.' . $slung . '.index');
    Route::post('/form/' . $slung . '/criar', 'store')->name('form.' . $slung . '.criar');
    Route::get('/form/' . $slung . '/detalhes/{id}', 'show')->name('form.' . $slung . '.ver');
    Route::post('/form/' . $slung . '/atualizar', 'update')->name('form.' . $slung . '.update');
    Route::post('/form/' . $slung . '/aprovar', 'toAprove')->name('form.' . $slung . '.toAprove');
    Route::post('/form/' . $slung . '/reprovar', 'toCancel')->name('form.' . $slung . '.toCancel');
    Route::post('/form/' . $slung . '/revisar', 'toRevise')->name('form.' . $slung . '.toRevise');
    Route::get('/PDF/form/' . $slung . '/{id}', 'toPdf')->name('form.' . $slung . '.pdf');
});

Route::middleware(['auth:sanctum', 'verified'])->controller(ModeloExemploController::class)->group(function () {
    $slung = 'modelExemplo';
    Route::get('/form/' . $slung . '', 'index')->name('form.' . $slung . '.index');
    Route::post('/form/' . $slung . '/criar', 'store')->name('form.' . $slung . '.criar');
    Route::get('/form/' . $slung . '/detalhes/{id}', 'show')->name('form.' . $slung . '.ver');
    Route::post('/form/' . $slung . '/atualizar', 'update')->name('form.' . $slung . '.update');
    Route::post('/form/' . $slung . '/aprovar', 'toAprove')->name('form.' . $slung . '.toAprove');
    Route::post('/form/' . $slung . '/reprovar', 'toCancel')->name('form.' . $slung . '.toCancel');
    Route::post('/form/' . $slung . '/revisar', 'toRevise')->name('form.' . $slung . '.toRevise');
    Route::get('/PDF/form/' . $slung . '/{id}', 'toPdf')->name('form.' . $slung . '.pdf');
});
