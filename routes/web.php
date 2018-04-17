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

Route::get('/documentos/load', 'DocumentosController@load')->middleware('auth')->name('documentos.load');
Route::resource('/documentos', 'DocumentosController')->middleware('auth');

Route::resource('/historial', 'ComentariosController')->middleware('auth');
