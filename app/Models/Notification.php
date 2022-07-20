<?php

namespace App\Models;

use Faker\Provider\en_UG\PhoneNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use phpDocumentor\Reflection\Types\This;

class Notification extends Model
{
    use HasFactory;

    protected static $type = 'default';

    protected static $count = 0;

    # СПИСОК УВЕДОМЛЕНИЙ
    public static function list() {
        $viewed_notifications = json_decode(Auth::user()->viewed_notifications);
        $list = self::whereRaw("addresses LIKE '%[". Auth::id() ."]%' OR general = 1")->orderByDesc('id')->get();

        # Пускаем цикл по всем уведомлениям юзер
        $i = 0;
        $updateList = [];
        $json = [];
        foreach ($list as $alert) {
            # Обновление списка
            $updateList[$i] = $alert;

            if(empty($viewed_notifications)) {
                $updateList[$i]['fresh'] = true;
            } else {
                # Если сообщение не прочитано - то маркеруем его
                if (!in_array($alert->id, $viewed_notifications)) {
                    $updateList[$i]['fresh'] = true;
                }
            }

            # Формирование JSON массива
            $json[] = $alert->id;

            $i++;
        }

        # Все id из $list в JSON формате сохранить во viewed_notifications
        User::where('id', Auth::id())->update(['viewed_notifications' => json_encode($json)]);

        return $updateList;
    }

    # КОЛИЧЕСТВО НОВЫХ УВЕДОМЛЕНИЙ
    public static function count() {
        $viewed_notifications = json_decode(Auth::user()->viewed_notifications);
        $list = self::whereRaw("addresses LIKE '%[". Auth::id() ."]%' OR general = 1")->orderByDesc('id')->get();

        if(empty($viewed_notifications)) {
            self::$count = count($list);
        } else {
            # Пускаем цикл по всем уведомлениям юзера
            foreach ($list as $alert) {
                if(!in_array($alert->id, $viewed_notifications)) {
                    self::$count++;
                }
            }
        }

        return self::$count;
    }

    # Отправка уведомления
    public static function default($user, $array) {
        $from = Auth::user();

        # Определяем тип и на основе его формируем сообщение
        $content = NULL;
        switch ($array['type']) {
            case 1:
                $content = 'Пользователь ' . $from->name . ' ответил на ваше <a href="/">сообщение</a>';
                break;
            case 2:
                $content = 'Пользователь <a href="'. route('home', $from->id) .'">' . $from->name . '</a> оценил ваше <a href="/news">сообщение</a>';
                break;
            case 3:
                $content = 'Ваш аккаунт заблокирован';
                break;
            case 4:
                $content = 'Ваш комментарий в <a href="' . route('topic', $array['url']) . '">теме</a> был удалён';
                break;
            case 5:
                $content = 'Ваш отзыв для <a href="' . route('mod', $array['url']) . '">мода</a> был удалён';
                break;
            case 6:
                $content = 'Ваша тема «' . $array['title'] . '» была удалена';
                break;
        }

        # Отправляем запись в БД
        $notification = [
            'user_id' => $user->id,
            'content' => $content,
            'type' => $array['type'],
            'general' => FALSE,
            'addresses' => json_encode([[$user->id]]), # Модифицировать в будущем пол массив
        ];

        Notification::insert($notification);
    }

    # Глобальное уведомление
    public static function general($content) {
        # Отправляем запись в БД от лица админа
        $notification = [
            'user_id' => 1,
            'content' => $content,
            'type' => 4,
            'general' => TRUE,
            'addresses' => NULL,
        ];
        Notification::insert($notification);
    }
}
