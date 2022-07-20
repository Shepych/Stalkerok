<?php

namespace App\Http\Controllers;

use App\Models\Archive;
use App\Models\Comments;
use App\Models\Forum;
use App\Models\Moderator;
use App\Models\ModsComments;
use App\Models\NewsComments;
use App\Models\Notification;
use App\Models\Reviews;
use App\Models\User;
use BaconQrCode\Common\Mode;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Expr\AssignOp\Mod;

class ModeratorController extends Controller
{
    protected static $maxBans = 5;

    # ШАБЛОН МОДЕРАЦИИ ДЛЯ ПРОФИЛЯ
    public static function generate() {
        # Проверка на наличие модерки
        if(Auth::user()->hasRole('moderator')) {
            $comment = Moderator::generateComment();
            $topic = Moderator::generateTopic();
            $review = Moderator::generateReview();
            $result = ['comment' => $comment, 'review' => $review, 'topic' => $topic];
        } else {
            $result = false;
        }

        return $result;
    }

    # ОБРАБОТКА КОММЕНТАРИЯ
    public function comment(Request $request, $id) {
        # Проверка доступа
        if(!Comments::where('moderation', Auth::id())->first()) {
            $comment = Moderator::generateComment();
            return Ajax::layout(view('ajax.moderator.next_comment', compact('comment'))->render(), 'comment');
        }

        # Пропуск
        if($request->input('next')) {
            # Обновить запись и вернуть Ajax комментарий
            $comment = Moderator::commentNext($id);
            return Ajax::layout(view('ajax.moderator.next_comment', compact('comment'))->render(), 'comment');
        }

        # Удаление
        if($request->input('delete')) {
            # Валидация + обработка
            $archive = new Archive(Archive::$comment);
            if(!$archive->archiving($request, $id)){
                return Ajax::valid(Archive::$error);
            }

            # Отправить в шаблон либо пустоту либо комментарий и обновить в БД moderation
            $comment = Moderator::noModerateComment();

            # Аякс следующего комментария
            return Ajax::layout(view('ajax.moderator.next_comment', compact('comment'))->render(), 'comment');
        }

        return abort(404);
    }

    # ОБРАБОТКА ТОПИКА
    public function topic(Request $request, $id) {
        # Проверка доступа
        if(!Forum::where('moderation', Auth::id())->first()) {
            return Ajax::message("Нет доступа");
        }

        # Пропуск
        if($request->input('next')) {
            # Обновить запись и вернуть Ajax комментарий
            $topic = Moderator::topicNext($id);
            return Ajax::layout(view('ajax.moderator.next_topic', compact('topic'))->render(), 'topic');
        }

        # Удаление
        if($request->input('delete')) {
            # Валидация + обработка
            $archive = new Archive(Archive::$topic);
            if(!$archive->archiving($request, $id)){
                return Ajax::valid(Archive::$error);
            }

            # Отправить в шаблон либо пустоту либо комментарий и обновить в БД moderation
            $topic = Moderator::noModerateTopic();

            # Аякс следующего комментария
            return Ajax::layout(view('ajax.moderator.next_topic', compact('topic'))->render(), 'topic');
//            return Ajax::message('123123');
        }

        return abort(404);
    }

    # ОБРАБОТКА ОТЗЫВА
    public function review(Request $request, $id) {
        # Проверка доступа
        if(!Reviews::where('moderation', Auth::id())->first()) {
            return Ajax::message("Нет доступа");
        }

        # Пропуск
        if ($request->input('next')) {
            $review = Moderator::reviewNext($id);
            return Ajax::layout(view('ajax.moderator.next_review', compact('review'))->render(), 'review');
        }

        # Удаление
        if ($request->input('delete')) {
            # Валидация + обработка
            $archive = new Archive(Archive::$review);
            if(!$archive->archiving($request, $id)){
                return Ajax::valid(Archive::$error);
            }

            # Отправить в шаблон следующий отзыв
            $review = Moderator::noModerateReview();

            # Аякс следующего комментария
            return Ajax::layout(view('ajax.moderator.next_review', compact('review'))->render(), 'comment');
        }

        return abort(404);
    }

    public function userSearch(Request $request) {
        return Ajax::message('Поиск юзера: '. $request->input('login'));
    }

    # БЛОКИРОВКА ПОЛЬЗОВАТЕЛЯ
    public function block(Request $request, $id) {
        $user = User::where('id', $id)->first();

        if($user->hasRole('blocked')) {
            return Ajax::message("Пользователь УЖЕ заблокирован");
        }

        $table = 'blocked_users_archive';
        $limits = DB::table($table)->whereDate('created_at', '=', Carbon::today()->format('Y-m-d'))->where('moderator_id', Auth::id())->get();
        # Проверка лимитов на блокировку
        if($limits->count() >= self::$maxBans) {
            return Ajax::message("На сегодня лимит блокировок исчерпан!");
        }

        # Валидация
        $validate = Validator::make($request->all(), [
            'cause' => 'required',
        ],[
            'cause.required' => 'Введите причину блокировки'
        ])->errors();

        if($validate->any()) {
            return Ajax::valid($validate->all());
        }

        # Блокируем
        $user->assignRole('blocked');

        # Добавляем отчёт в архив
        DB::table($table)->insert([
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'cause' => $request->input('cause'),
            'moderator_id' => Auth::id(),
            'user_id' => $user->id,
        ]);

        # Отправляем уведомление
        $array = [
            'href' => '/mods',
            'type' => 3,
        ];
        Notification::default($user, $array);

        return Ajax::message("Пользователь заблокирован", TRUE);
    }

    # СНЯТИЕ БЛОКИРОВКИ
    public function unblock(Request $request, $id) {
        User::where('id', $id)->first()->removeRole('blocked');
        return Ajax::message("Пользователь разблокирован", TRUE);
    }
}
