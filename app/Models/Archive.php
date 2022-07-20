<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use phpDocumentor\Reflection\Types\This;

class Archive extends Model
{
    use HasFactory;

    protected $table;
    public static $error;

    # Таблицы для архивации
    public static $comment = 'deleted_comments_archive';
    public static $topic = 'deleted_topics_archive';
    public static $review = 'deleted_reviews_archive';
    public static $user = 'blocked_users_archive';

    public function __construct($type) {
        $this->table = $type;
    }

    public function archiving($request, $id) {
        switch ($this->table) {
            case self::$topic:
                $result = self::topic($request, $id);
                break;
            case self::$comment:
                $result = self::comment($request, $id);
                break;
            case self::$review:
                $result = self::review($request, $id);
                break;
            default:
                $result = 'Ошибка';
        }

        return $result;
    }

    # Архивация отзыва
    protected function review($request, $id) {
        # Получить отзыв по ID
        $review = Reviews::where('id', $id)->first();
        # Проверка на существование комментария
        if(!isset($review)) {
            self::$error = 'Отзыв не существует';
            return false;
        }

        $validate = Validator::make($request->all(), [
            'cause' => 'required',
        ],[
            'cause.required' => 'Введите причину',
        ])->errors();
        # Валидация
        if($validate->any()) {
            self::$error = $validate->all();
            return false;
        }

        # Формируем массив для отправки в БД
        $array = [
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'user_id' => $review->user_id,
            'title' => $review->title,
            'rating' => $review->rating,
            'object_id' => $id,
            'content' => $review->content,
            'moderator_id' => Auth::id(),
            'cause' => $request->input('cause'),
        ];

        # Вставляем запись в БД
        self::insert($array);

        # Отправляем уведомление
        Notification::default(User::where('id', $review->user_id)->first(), ['type' => 5, 'url' => $review->topic()->url]);

        # Удаляем комментарий
        $review->delete();
        return true;
    }

    # Архивация комментария
    protected function comment($request, $id) {
        # Получить комментарий по ID
        $comment = Comments::where('id', $id)->first();
        # Проверка на существование комментария
        if(!isset($comment)) {
            self::$error = 'Комментарий не существует';
            return false;
        }

        $validate = Validator::make($request->all(), [
            'cause' => 'required',
        ],[
            'cause.required' => 'Введите причину',
        ])->errors();
        # Валидация
        if($validate->any()) {
            self::$error = $validate->all();
            return false;
        }

        # Формируем массив для отправки в БД
        $array = [
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'user_id' => $comment->user_id,
            'object_id' => $id,
            'content' => $comment->content,
            'moderator_id' => Auth::id(),
            'cause' => $request->input('cause'),
        ];

        # Вставляем запись в БД
        self::insert($array);

        # Отправляем уведомление
        Notification::default(User::where('id', $comment->user_id)->first(), ['type' => 4, 'url' => $comment->topic()->url]);

        # Удаляем комментарий
        $comment->delete();
        return true;
    }

    # Архивация топика
    protected function topic($request, $id) {
        # Получить комментарий по ID
        $topic = Forum::where('id', $id)->first();
        # Проверка на существование комментария
        if(!isset($topic)) {
            self::$error = 'Тема не существует';
            return false;
        }

        $validate = Validator::make($request->all(), [
            'cause' => 'required',
        ],[
            'cause.required' => 'Введите причину',
        ])->errors();
        # Валидация
        if($validate->any()) {
            self::$error = $validate->all();
            return false;
        }

        # Формируем массив для отправки в БД
        $array = [
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'url' => $topic->url,
            'title' => $topic->title,
            'user_id' => $topic->user_id,
            'type' => 0,
            'closed' => $topic->closed,
            'hidden' => 0,
            'moderator_id' => Auth::id(),
            'cause' => $request->input('cause'),
        ];

        # Вставляем запись в БД
        self::insert($array);

        # Отправляем уведомление
        Notification::default(User::where('id', $topic->user_id)->first(), ['type' => 6, 'url' => $topic->url, 'title' => $topic->title]);

        # Удаляем комментарий
        $topic->delete();
        return true;
    }

    # Архивация блокировки
    protected function block() {
        return true;
    }
}
