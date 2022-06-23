<?php

namespace App\Models;

use App\Http\Controllers\Ajax;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class Mods extends Model
{
    use HasFactory;
    public static $error;
    public static $maxReviews = 4;
    public static $maxComments = 100;

    public static function generateMod($request, $url) {
        $mod = Mods::where('url', $url)->first();

        if(isset($request->page)) {
            if($request->page > $mod->comments()->lastPage() || $request->page <= 0) {
                abort(404);
            }
        }

        # Редирект с '/mods/{url}?page=1' на '/mods/{url}'
        if($request->page ==  1) return redirect('/mods/' . $mod->url);

        if(!$mod) abort(404);

        # Добавить проверку URL
        if($mod->url != $url) abort(404);

        return view('mods.page', [
            'mod' => $mod,
            'tags' => DB::table('tags')->get(),
        ]);
    }

    # Связь с картинками для галереи
    public function images()
    {
        return $this->hasMany(ModsImages::class, 'mod_id');
    }

    # Связь с отзывами
    public function reviews()
    {
        return $this->hasMany(Reviews::class, 'object_id')->orderByDesc('id');
    }

    # Связь с комментариями
    public function comments(){
        return $this->hasMany(ModsComments::class, 'mod_id')->orderByDesc('created_at')->paginate(2);
    }

    # Отправка комментария
    public static function sendComment($request, $id) {
        # Валидация
        DB::transaction(function () use ($request, $id) {
            DB::table('mods_comments')->insert([
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'user_id' => Auth::id(),
                'mod_id' => $id,
                'content' => $request->input('comment')
            ]);

            $user = User::where('id', Auth::id())->first();
            $user->comments++;
            $user->save();
        }, 5);
        return true;
    }

    public static function commentValidate($request, $id) {
        # Проверка на авторизацию
        if(!Auth::check()) {
            self::$error = Controller::$noAuth;
            return false;
        }

        # Проверка на блокировку
        if(Auth::user()->hasRole('blocked')) {
            self::$error = Controller::$blocked;
            return false;
        }

        # Проверка лимитов для ежедневного ограничения отправки комментариев пользователем, во избежание спама
        $limits = DB::table('mods_comments')->whereDate('created_at', '=', Carbon::today()->format('Y-m-d'))->where('user_id', Auth::id())->get();
        if($limits->count() >= self::$maxComments) {
            self::$error = 'На сегодня лимит комментариев исчерпан!';
            return false;
        }

        $mod = Mods::where('id', $id)->first();
        if(!$mod) {
            self::$error = 'Мод не найден';
            return false;
        }

        $validate = Validator::make($request->all(), [
            'comment' => 'required',
        ],[
            'comment.required' => 'Введите ваше сообщение',
        ])->errors();

        if($validate->any()) {
            self::$error = $validate->all();
            return false;
        }

        return true;
    }

//    public static function sendReview($request, $id) {
//        # Проверка на авторизацию
//        if(!Auth::check()) {
//            self::$error = Controller::$noAuth;
//            return false;
//        }
//
//        # Проверка на блокировку
//        if(Auth::user()->hasRole('blocked')) {
//            self::$error = Controller::$blocked;
//            return false;
//        }
//
//        $user = Auth::user();
//
//        # Проверка на авторизацию
//        if(!$user) {
//            return Ajax::message('Авторизируйтесь на сайте');
//        }
//
//        # Проверка на блокировку
//        if($user->hasRole(['blocked'])){
//            return Ajax::message('Аккаунт заблокирован');
//        }
//
//        # Проверить не отправлял ли уже пользователь отзыв
//        $reviews = Reviews::where('object_id', $id)->get();
//        # Циклом проверим есть ли уже отзыв от юзера на этот мод. Цикл нужен чтобы избежать лишнего запроса на вычислении рейтинга к моду
//        foreach ($reviews as $review) {
//            if($review->user_id == Auth::id()) {
//                return Ajax::message('Вы уже оставляли отзыв на этот мод');
//            }
//        }
//
//        # Проверка лимитов для ежедневного ограничения отправки отзывов пользователем, во избежание спама
//        $limits = DB::table('reviews')->whereDate('created_at', '=', Carbon::today()->format('Y-m-d'))->where('user_id', Auth::id())->get();
//        if($limits->count() >= $this->maxReviews) {
//            return Ajax::message('На сегодня лимит отзывов исчерпан!');
//        }
//
//        # Проверка на метод POST
//        if($request->method() != 'POST') {
//            abort(404);
//        }
//
//        $validate = Validator::make($request->all(), [
//            'title' => 'required|min:3|max:200',
//            'content' => 'required|min:3',
//            'type' => 'numeric',
//        ],[
//            'title.required' => 'Заполните заголовок',
//            'title.min' => 'Заголовок должен содержать минимум 3 символа',
//            'title.max' => 'Заголовок должен содержать максимум 200 символов',
//            'content.required' => 'Отзыв пуст',
//            'content.min' => 'Отзыв должен содержать минимум 200 символов',
//            'type.numeric' => 'Не валидный тип отзыва',
//        ])->errors();
//
//        # Если ошибка валидации, то формируем ajax сообщение с перечислением ошибок функцией valid
//        if($validate->any()) {
//            return Ajax::valid($validate);
//        }
//
//        # Валидация рейтинга и расчёт среднего арифметического
//        switch ($request->input('rating')) {
//            case 1:
//                $rating = 1;
//                break;
//            case 2:
//                $rating = 2;
//                break;
//            case 3:
//                $rating = 3;
//                break;
//            case 4:
//                $rating = 4;
//                break;
//            case 5:
//                $rating = 5;
//                break;
//            case 6:
//                $rating = 6;
//                break;
//            case 7:
//                $rating = 7;
//                break;
//            case 8:
//                $rating = 8;
//                break;
//            case 9:
//                $rating = 9;
//                break;
//            case 10:
//                $rating = 10;
//                break;
//            default:
//                $rating = 10;
//        }
//
//        $pool = 0;
//
//        # Проверка на пустой массив с отзывами - без неё выдаёт ошибку
//        if($reviews->count()) {
//            foreach ($reviews as $review) {
//                $pool += $review->rating;
//            }
//
//            # Добавляем к общему пулу оценок - оценку пользователя и вычисляем средний балл модификации
//            $pool+= $rating;
//            $averageRating = $pool / ($reviews->count() + 1);
//        } else {
//            $averageRating = $rating;
//        }
//
//        # Добавление отзыва
//        DB::table('reviews')->insert([
//            'object_id' => $id,
//            'user_id' => Auth::id(),
//            'title' => $request->input('title'),
//            'content' => $request->input('content'),
//            'rating' => $rating,
//            'created_at' => date("Y-m-d H:i:s"),
//            'updated_at' => date("Y-m-d H:i:s")
//        ]);
//
//        # Обновление оценки мода
//        $mod = Mods::where('id', $id)->first();
//        $mod->rating = $averageRating;
//        $mod->save();
//        return url()->previous();
//    }
}
