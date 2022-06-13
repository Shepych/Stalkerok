<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

# Админ панель
Route::group(['middleware' => ['role:admin']], function () {
    Route::get('/admin/panel', 'AdminController@panel')->name('admin.panel');
    Route::get('/admin/panel/news', 'AdminController@newsList')->name('admin.news.list');
    # Создание новости
    Route::get('/admin/panel/create/new', 'AdminController@newCreate')->name('new.create');
    Route::post('/admin/panel/store/new', 'AdminController@newStore')->name('new.store');
    # Обновление новости
    Route::any('/admin/panel/update/new/{id}', 'AdminController@newUpdate')->name('new.update');
    # Удаление статьи
    Route::post('/admin/panel/delete/new/{id}', 'AdminController@newDelete')->name('new.delete');
    # Загрузка картинок через TinyMCE
    Route::post('/upload', 'AdminController@upload');


    # Список модов
    Route::get('/admin/panel/mods', 'AdminController@modsList')->name('admin.mods.list');
    # Страница создания мода
    Route::get('/admin/panel/create/mod', 'AdminController@modCreate')->name('mod.create');
    # Обработчик создания мода
    Route::post('/admin/panel/store/mod', 'AdminController@modStore')->name('mod.store');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
