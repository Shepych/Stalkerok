<?php

use App\Http\Controllers\ForumController;
use App\Http\Controllers\NewsController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::get('/pda/{id}', [App\Http\Controllers\HomeController::class, 'profile'])->name('home');
Route::middleware(['auth'])->get('/notifications', [App\Http\Controllers\HomeController::class, 'notifications'])->name('notifications');
Route::middleware(['auth'])->get('/settings', [App\Http\Controllers\HomeController::class, 'settings'])->name('settings');


Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('index');

Auth::routes();

Route::middleware(['role:admin'])->name('admin.')->group( function () {
    {   ######## АДМИН-ПАНЕЛЬ ########
        Route::get('/admin/panel', 'AdminController@panel')->name('panel');
        Route::get('/admin/panel/news', 'AdminController@newsList')->name('news.list');
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
        Route::get('/admin/panel/mods', 'AdminController@modsList')->name('mods.list');
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
}

{   # НОВОСТИ
    Route::get('/news', 'NewsController@newsList')->name('news.list');
    Route::get('/news/{url}', 'NewsController@newPage')->name('new.page');
}

{   # ФОРУМ
    Route::get('/forum', 'ForumController@forum')->name('forum');
    Route::get('/forum/{url}', 'ForumController@topic')->name('topic');
    Route::get('/forum/topic/create', [ForumController::class, 'createTopic'])->name('topic.create');
}

{   # МАГАЗИН
    Route::get('/store', 'StoreController@store')->name('store');
}


Route::group(['middleware' => ['guest_or_blocked']], function () {
    {   ######## Возможности пользователя ########
        Route::post('/mods/review/{id}', 'ModsController@modReview')->name('mod.review');
        Route::post('/mods/comment/{id}', 'ModsController@modComment')->name('mod.comment');
        Route::post('/news/comment/{id}', [NewsController::class, 'newComment'])->name('new.comment');
        Route::post('/forum/topic/create', [ForumController::class, 'createTopic'])->name('topic.create');
        Route::post('/forum/topic/comment/{id}', [ForumController::class, 'sendComment'])->name('topic.comment');
    }
});

Route::middleware(['guest_or_blocked', 'moderator'])->name('moderator.')->group(function () {
    {   ######## ФУНКЦИОНАЛ МОДЕРАТОРА ########
        Route::post('/moderator/comment', 'ModeratorController@comment')->name('comment');
        Route::post('/moderator/review', 'ModeratorController@review')->name('review');
        Route::post('/moderator/block/{id}', 'ModeratorController@block')->name('block');
        Route::post('/moderator/unblock/{id}', 'ModeratorController@unblock')->name('unblock');
    }
});


