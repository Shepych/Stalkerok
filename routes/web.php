<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

# Админ панель
Route::group(['middleware' => ['role:admin']], function () {
    Route::get('/admin/panel', 'AdminController@panel')->name('admin.panel');
    Route::get('/admin/panel/news', 'AdminController@newsList')->name('admin.news.list');
    # Создание новости
    Route::get('/admin/panel/new/create', 'AdminController@newCreate')->name('new.create');
    Route::post('/admin/panel/new/store', 'AdminController@newStore')->name('new.store');
    # Обновление новости
    Route::any('/admin/panel/new/update/{id}', 'AdminController@newUpdate')->name('new.update');
    # Удаление статьи
    Route::post('/admin/panel/new/delete/{id}', 'AdminController@newDelete')->name('new.delete');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
