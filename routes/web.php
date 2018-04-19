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

Route::get('/documentos/load/{id}', 'DocumentosController@load')->middleware('auth')->name('documentos.load');
Route::get('/documentos/download/{id}', 'DocumentosController@download')->middleware('auth')->name('documentos.download');
Route::resource('/documentos', 'DocumentosController')->middleware('auth');

Route::resource('/historial', 'ComentariosController')->middleware('auth');

Route::get('/publicacion/download/{id}', 'PublicacionesController@download')->middleware('auth')->name('publicacion.download');
Route::resource('/publicacion', 'PublicacionesController')->middleware('auth');
