<?php

namespace App\Models;

use App\Http\Controllers\Ajax;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class News extends Model
{
    public static $error;
    # Значения атрибутов по умолчанию
    protected $attributes = [
        'img' => '-',
        'description' => '',
    ];

    use HasFactory;

    # Связь с общими комментариями
    public function comments(){
        return $this->hasMany(Comments::class, 'object_id')->where('topic_content', 0)->paginate(2);
    }

    # Связь с топиком
    public function topic() {
        return $this->hasOne(Forum::class, 'id', 'topic_id')->first();
    }

    # Рендер списка статей
    public static function packageList($request) {
        $articles = News::where('published', true)->orderByDesc('id')->paginate(2);

        # Проверка на существование пагинации
        if(isset($request->page)) {
            if($request->page > $articles->lastPage() || $request->page <= 0) {
                abort(404);
            }
        }

        # Редирект с '/news?page=1 на '/news'
        if($request->page ==  1) {
            return redirect('/news');
        }

        return view('news.list', [
            'articles' => $articles,
            'activePage' => 'news',
        ]);
    }

    # Рендер страницы с данными
    public static function package($request, $url) {
        $article = News::where('url', $url)->first();

        # Проверка на наличие статьи в базе по URL
        if(!$article) {
            abort(404);
        }

        # Проверка на существование пагинации
        if(isset($request->page)) {
            if($request->page > $article->comments()->lastPage() || $request->page <= 0) {
                abort(404);
            }
        }

        # Редирект с '/news/url?page=1' на '/news/url'
        if($request->page == 1) return redirect('/news/' . $article->url);

        return view('news.page', [
            'article' => $article,
            'activePage' => 'news',
        ]);
    }

    # Отправка комментария
    public static function send($request, $id) {
        # Валидация
        if(!self::validate($request, $id)) {
            return Ajax::valid(self::$error);
        }

        # Транзакция на отправку комментария
        DB::transaction(function () use ($request, $id) {
            $comment = new Comments;
            $comment->user_id = Auth::id();
            $comment->object_id = $id;
            $comment->content = $request->input('content');
            $comment->save();

            # Обновить счётчик комментаривев пользователя
            $user = User::where('id', Auth::id())->first();
            $user->comments++;
            $user->save();
        }, 5);

        return Ajax::redirect(url()->previous());
    }

    # Валидация комментария
    protected static function validate($request, $id){
        # Проверка наличия статьи по ID
        if(!News::where('id', $id)->first()) {
            self::$error = 'Статья не найдена';
            return false;
        }

        $limits = Comments::whereDate('created_at', '=', Carbon::today()->format('Y-m-d'))->where('user_id', Auth::id())->get();
        # Проверка лимитов для ежедневного ограничения отправки комментариев пользователем, во избежание спама
        if($limits->count() >= Controller::$maxComments) {
            self::$error = 'На сегодня лимит комментариев исчерпан!';
            return false;
        }

        $validate = Validator::make($request->all(), [
            'content' => 'required',
        ],[
            'content.required' => 'Введите ваше сообщение',
        ])->errors();
        # Валидация вводимых данных
        if($validate->any()) {
            self::$error = $validate->all();
            return false;
        }

        return true;
    }
}
