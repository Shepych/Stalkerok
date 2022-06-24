<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class News extends Model
{
    # Значения атрибутов по умолчанию
    protected $attributes = [
        'img' => '-',
        'description' => '',
    ];

    use HasFactory;

    # Связь с комментариями
    public function comments() {
        return $this->hasMany(NewsComments::class, 'new_id');
    }

    # Рендер страницы с данными
    public static function package($url) {
        $article = News::where('url', $url)->first();
        # Проверка на наличие статьи в базе по URL
        if(!$article) {
            abort(404);
        }

        return view('news.page', [
            'article' => $article,
        ]);
    }

    # Отправка комментария
    public static function send($request, $id) {
        NewsComments::insert([
            'user_id' => Auth::id(),
            'new_id' => $id,
            'content' => $request->input('content'),
            'date_time' => date('Y-m-d H:i:s'),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
