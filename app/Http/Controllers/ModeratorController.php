<?php

namespace App\Http\Controllers;

use App\Models\Comments;
use App\Models\ModsComments;
use App\Models\NewsComments;
use App\Models\Notification;
use App\Models\Reviews;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ModeratorController extends Controller
{
    protected static $maxBans = 5;

    # ШАБЛОН МОДЕРАЦИИ ДЛЯ ПРОФИЛЯ
    public static function generate() {
        # Проверка на наличие модерки
        if(Auth::user()->hasRole('moderator')) {
            # Берём КОММЕНТАРИЙ уже модерируемый данным пользователем
            $comment = Comments::where('moderation', Auth::id())->orderByDesc('updated_at')->first();
            # Если такого нет - то берём свободный и присваимваем ему ID `moderation`
            if(!$comment) {
                $comment = Comments::where('moderation', null)
                    ->where('user_id', '!=', 1)
                    ->where('type', '!=', 'new')
                    ->where('type', '!=', 'mod')
                    ->orderByDesc('updated_at')
                    ->first();
                if($comment) {
                    $comment->moderation = Auth::id();
                    $comment->save();
                }
            }

            # Берём ОТЗЫВ уже модерируемый данным пользователем
            $review = Reviews::where('moderation', Auth::id())->orderByDesc('updated_at')->first();
            # Если такого нет - то берём свободный и присваимваем ему ID `moderation`
            if(!$review) {
                $review = Reviews::where('moderation', null)->orderByDesc('updated_at')->first();
                if($review) {
                    $review->moderation = Auth::id();
                    $review->save();
                }
            }

            $result = ['comment' => $comment, 'review' => $review];
        } else {
            $result = false;
        }

        return $result;
    }

    # ОБРАБОТКА КОММЕНТАРИЯ
    public function comment(Request $request, $id) {
        # Если Auth ID != moderator_id - выдаём ошибку
        return true;
    }

    # ОБРАБОТКА ОТЗЫВА
    public function review(Request $request, $id) {
//        return true;
        if($request->input('next')) {
            return Ajax::message('next');
        }

        if($request->input('delete')) {
            return Ajax::message('Удаление');
        }

        return abort(404);
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
