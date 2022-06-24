<?php

use App\Http\Controllers\NewsController;
use Illuminate\Support\Facades\Route;


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::group(['middleware' => ['role:admin']], function () {
    {   ######## АДМИН-ПАНЕЛЬ ########
        Route::get('/admin/panel', 'AdminController@panel')->name('admin.panel');
        Route::get('/admin/panel/news', 'AdminController@newsList')->name('admin.news.list');
    }

    {   ######## НОВОСТИ ########
        # Создание
        Route::get('/admin/panel/create/new', 'AdminController@newCreate')->name('new.create');
        Route::post('/admin/panel/store/new', 'AdminController@newStore')->name('new.store');
        # Обновление
        Route::any('/admin/panel/update/new/{id}', 'AdminController@newUpdate')->name('new.update');
        # Удаление
        Route::post('/admin/panel/delete/new/{id}', 'AdminController@newDelete')->name('new.delete');
    }

    {   ######## МОДЫ ########
        # Список модов
        Route::get('/admin/panel/mods', 'AdminController@modsList')->name('admin.mods.list');
        # Страница создания мода
        Route::get('/admin/panel/create/mod', 'AdminController@modCreate')->name('mod.create');
        # Обработчик создания мода
        Route::post('/admin/panel/store/mod', 'AdminController@modStore')->name('mod.store');
        # Обновление мода
        Route::any('/admin/panel/update/mod/{id}', 'AdminController@modUpdate')->name('mod.update');
        # Удаление мода
        Route::post('/admin/panel/delete/mod/{id}', 'AdminController@modDelete')->name('mod.delete');
    }

    # Загрузка картинок через TinyMCE
    Route::post('/upload', 'AdminController@upload');
});

{   # МОДЫ
    Route::get('/mods', 'ModsController@modsList')->name('mods.list');
    Route::get('/mods/{url}', 'ModsController@modPage')->name('mod.page');
    Route::post('/mods/review/{id}', 'ModsController@modReview')->name('mod.review');
    Route::post('/mods/comment/{id}', 'ModsController@modComment')->name('mod.comment');
}

{   # НОВОСТИ
    Route::get('/news', 'NewsController@newsList')->name('news.list');
    Route::get('/news/{url}', 'NewsController@newPage')->name('new.page');
    Route::post('/news/comment/{id}', [NewsController::class, 'newComment'])->name('new.comment');
}
