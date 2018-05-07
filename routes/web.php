<?php

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

Route::get('/', 'LoginController@index')->name('login');
    
Route::post('/login', 'LoginController@login')->name('login.enter');
Route::get('/logout', 'LoginController@logout')->name('logout');

Route::get('/home', 'HomeController@index')->name('home')->middleware('auth');

Route::resource('/usuarios', 'UsuariosController')->middleware('auth');
Route::resource('/entidades', 'EntidadesController')->middleware('auth');


Route::get('/documentos', 'DocumentosController@index')->middleware('auth')->name('documentos.index');
Route::get('/documentos/load/{id}', 'DocumentosController@load')->middleware('auth')->name('documentos.load');
Route::get('/documentos/show/{id}/{type}', 'DocumentosController@show')->middleware('auth')->name('documentos.show');
Route::get('/documentos/download/{id}', 'DocumentosController@download')->middleware('auth')->name('documentos.download');
Route::get('/documentos/create', 'DocumentosController@create')->middleware('auth')->name('documentos.create');
Route::post('/documentos/', 'DocumentosController@store')->middleware('auth')->name('documentos.store');
Route::get('/documentos/change/{id}/edit', 'DocumentosController@edit')->middleware('auth')->name('documentos.edit');
Route::delete('/documentos/{id}', 'DocumentosController@destroy')->middleware('auth')->name('documentos.destroy');
Route::put('/documentos/update/{id}', 'DocumentosController@update')->middleware('auth')->name('documentos.update');


Route::get('/historial/{id}/{type}', 'ComentariosController@show')->middleware('auth')->name('historial.show');
Route::put('/historial/{id}/{action}', 'ComentariosController@update')->middleware('auth')->name('historial.update');
Route::resource('/historial', 'ComentariosController')->middleware('auth');

Route::get('/resumen', 'ResumenController@index')->middleware('auth')->name('resumen.index');
Route::get('/resumen/load', 'ResumenController@load')->middleware('auth')->name('resumen.load');
Route::get('/resumen/download/{id}/{type}', 'ResumenController@download')->middleware('auth')->name('resumen.download');
Route::put('/resumen/reset', 'ResumenController@reset')->middleware('auth')->name('resumen.reset');

Route::resource('/avance', 'AvanceController')->middleware('auth');

Route::resource('/opciones', 'OpcionesController')->middleware('auth');

Route::get('/test', 'TestController@index');