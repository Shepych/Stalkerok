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
        return $this->hasMany(NewsComments::class, 'new_id')->paginate(2);
    }

    # Рендер страницы с данными
    public static function package($request, $url) {
        $article = News::where('url', $url)->first();

        # Проверка на наличие статьи в базе по URL
        if(!$article) {
            abort(404);
        }

        if(isset($request->page)) {
            if($request->page > $article->comments()->lastPage() || $request->page <= 0) {
                abort(404);
            }
        }

        # Редирект с '/news/url?page=1' на '/news/url'
        if($request->page == 1) return redirect('/news/' . $article->url);

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
