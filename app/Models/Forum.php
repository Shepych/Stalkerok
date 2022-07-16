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
use Illuminate\Support\Str;

class Forum extends Model
{
    use HasFactory;

    protected $table = 'forum';
    public static $error;

    # Формирование страницы топика
    public static function topic($request, $url) {
        $topic = Forum::where('url', $url)->first();
        # Проверка на существование топика
        if(!isset($topic)) {
            return abort(404);
        }

        # Проверка на существование пагинации
        if(isset($request->page)) {
            if($request->page > $topic->comments()->lastPage() || $request->page <= 0) {
                abort(404);
            }
        }

        # Редирект с '/news/url?page=1' на '/news/url'
        if($request->page == 1) return redirect('/forum/' . $url);

        return view('forum.topic', [
            'activePage' => 'forum',
            'topic' => $topic,
        ]);
    }

    # Связь с пользователем
    public function author() {
        return $this->hasOne(User::class, 'id', 'user_id')->first();
    }

    # Связь с комментариями
    public function comments() {
        return $this->hasMany(Comments::class, 'object_id')->paginate(4);
    }

    # Связь с контентом
    public function content() {
        return $this->hasOne(Comments::class, 'object_id', 'id')->where('topic_content', TRUE)->first();
    }

    # Метод формирования списка тем для форума
    public static function generate($request) {
        $topics = Forum::where('type', NULL)->where('hidden', FALSE)->orderByDesc('created_at')->paginate(4);

        # Проверка на существование пагинации
        if(isset($request->page)) {
            if($request->page > $topics->lastPage() || $request->page <= 0) {
                abort(404);
            }
        }

        # Редирект с '/news/url?page=1' на '/news/url'
        if($request->page == 1) return redirect('/forum');

        $result = [
            'topics' => $topics,
            'activePage' => 'forum',
        ];
        return view('forum.forum', $result);
    }

    # Создание топика
    protected static function topicCreate($request) {
        $validate = Validator::make($request->all(), [
            'title' => 'required',
            'content' => 'required|max:100000',
        ],[
            'title.required' => 'Заголовок отсутствует',
            'content.required' => 'Контент отсутствует',
            'content.max' => 'Слишком большое сообщение',
        ])->errors();

        if($validate->any()) {
            self::$error = $validate->all();
            return false;
        }

        # Проверка на уникальность ( отдельно, чтобы не было бага )
        $uniq = Validator::make(['url' => Str::slug($request->input('title'))], [
            'url' => 'unique:forum',
        ],[
            'url.unique' => 'Не уникальный заголовок',
        ])->errors();

        if($uniq->any()) {
            self::$error = $uniq->all();
            return false;
        }

        # Проверка на ограничение 3 созданных тем
        $limits = Forum::whereDate('created_at', '=', Carbon::today()->format('Y-m-d'))->where('user_id', Auth::id())->get();

        # Проверка лимитов для ежедневного ограничения отправки комментариев пользователем, во избежание спама
        if($limits->count() >= Controller::$maxThemes && !Auth::user()->hasRole('admin')) {
            self::$error = 'На сегодня лимит создаваемых тем исчерпан!';
            return false;
        }

        # Создание топика
        $topic = new Forum;
        $topic->title = $request->input('title');
        $topic->url = Str::slug($request->input('title'));
        $topic->user_id = Auth::id();
        $topic->closed = FALSE;
        $topic->hidden = FALSE;
        $topic->save();

        # Создание MAIN комментария
        $comment = new Comments;
        $comment->content = $request->input('content');
        $comment->user_id = Auth::id();
        $comment->topic_content = TRUE;
        $comment->object_id = $topic->id;
        $comment->save();

        return $topic->url;
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
        if(!Forum::where('id', $id)->first()) {
            self::$error = 'Топик не найден';
            return false;
        }

        $limits = Comments::whereDate('created_at', '=', Carbon::today()->format('Y-m-d'))->where('user_id', Auth::id())->get();
        # Проверка лимитов для ежедневного ограничения отправки комментариев пользователем, во избежание спама
        if($limits->count() >= Controller::$maxComments) {
            self::$error = 'На сегодня лимит комментариев исчерпан!';
            return false;
        }

        $validate = Validator::make($request->all(), [
            'content' => 'required|max:100000',
        ],[
            'content.required' => 'Введите ваше сообщение',
            'content.max' => 'Слишком большое сообщение',
        ])->errors();
        # Валидация вводимых данных
        if($validate->any()) {
            self::$error = $validate->all();
            return false;
        }

        return true;
    }

}
