<?php

use Illuminate\Http\Request;

Route::post('/login', 'AuthController@login');

Route::post('/logout', 'AuthController@logout');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/especialidades', 
    'EspecialidadesController@getEspecialidades')
        ->name('disponibilidades.get');

Route::post('/disponibilidades/salvar',
    'DisponibilidadesController@salvar')
        ->name('disponibilidades.salvar');

Route::get('/minhasDisponiblidades', 
    'DisponibilidadesController@minhasDisponiblidades')
        ->name('disponibilidades.get');

Route::get('/oportunidades/listar', 
    'OportunidadesController@listar')
        ->name('oportunidades.get');

Route::post('/oportunidades/candidatarse', 
    'OportunidadesController@candidatarse')
        ->name('oportunidades.candidatarse');

Route::get('/oportunidades/medicoInteressado', 
    'OportunidadesController@index')
        ->name('oportunidades.medicoInteressado');
    
