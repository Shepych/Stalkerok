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

    # Формирование страницы мода
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
            'activePage' => 'mods',
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

    # Связь с общими комментариями
    public function comments(){
        return $this->hasMany(Comments::class, 'object_id')->where('topic_content', 0)->paginate(2);
    }

    # Связь с топиком
    public function topic() {
        return $this->hasOne(Forum::class, 'id', 'topic_id')->first();
    }

    # Формирование страницы мода
    public static function package($request) {
        $mods = Mods::where('published', true)->orderByDesc('id')->paginate(2);

        # Проверка на существование пагинации
        if(isset($request->page)) {
            if($request->page > $mods->lastPage() || $request->page <= 0) {
                abort(404);
            }
        }

        # Редирект с '/mods?page=1' на '/mods'
        if($request->page ==  1) return redirect('/mods');

        $tags = DB::table('tags')->get();

        return view('mods.list', [
            'mods' => $mods,
            'tags' => $tags,
            'activePage' => 'mods',
        ]);
    }

    # Отправка комментария
    public static function sendComment($request, $id) {
        # Валидация сообщения и ID мода
        if(!self::commentValidate($request, $id)) {
            return Ajax::valid(Mods::$error);
        }

        # Отправка комментария
        DB::transaction(function () use ($request, $id) {
            $comment = new Comments;
            $comment->user_id = Auth::id();
            $comment->object_id = $id;
            $comment->content = $request->input('comment');
            $comment->type = 'mod';
            $comment->save();

            $user = User::where('id', Auth::id())->first();
            $user->comments++;
            $user->save();
        }, 5);

        # Обновление страницы
//        return Ajax::redirect('/mods/' . Mods::where('id', $id)->first()->url);
        return Ajax::redirect(url()->previous());
    }

    # Отправка отзыва
    public static function sendReview($request, $id) {
        # Проверить не отправлял ли уже пользователь отзыв
        $reviews = Reviews::where('object_id', $id)->get();
        # Циклом проверим есть ли уже отзыв от юзера на этот мод. Цикл нужен чтобы избежать лишнего запроса на вычислении рейтинга к моду
        foreach ($reviews as $review) {
            if($review->user_id == Auth::id()) {
                self::$error = 'Вы уже оставляли отзыв на этот мод';
                return false;
            }
        }

        $limits = DB::table('reviews')->whereDate('created_at', '=', Carbon::today()->format('Y-m-d'))->where('user_id', Auth::id())->get();
        # Проверка лимитов для ежедневного ограничения отправки отзывов пользователем, во избежание спама
        if($limits->count() >= Controller::$maxReviews) {
            self::$error = 'На сегодня лимит отзывов исчерпан!';
            return false;
        }

        # Валидация отзыва
        $validate = Validator::make($request->all(), [
            'title' => 'required|min:3|max:200',
            'content' => 'required|min:3',
            'type' => 'numeric',
        ],[
            'title.required' => 'Заполните заголовок',
            'title.min' => 'Заголовок должен содержать минимум 3 символа',
            'title.max' => 'Заголовок должен содержать максимум 200 символов',
            'content.required' => 'Напишите отзыв',
            'content.min' => 'Отзыв должен содержать минимум 200 символов',
            'type.numeric' => 'Не валидный тип отзыва',
        ])->errors();

        # Если ошибка валидации, то формируем ajax сообщение с перечислением ошибок функцией valid
        if($validate->any()) {
            self::$error = $validate->all();
            return false;
        }

        # Валидация рейтинга и расчёт среднего арифметического
        switch ($request->input('rating')) {
            case 1:
                $rating = 1;
                break;
            case 2:
                $rating = 2;
                break;
            case 3:
                $rating = 3;
                break;
            case 4:
                $rating = 4;
                break;
            case 5:
                $rating = 5;
                break;
            case 6:
                $rating = 6;
                break;
            case 7:
                $rating = 7;
                break;
            case 8:
                $rating = 8;
                break;
            case 9:
                $rating = 9;
                break;
            case 10:
                $rating = 10;
                break;
            default:
                $rating = 10;
        }

        $pool = 0;
        # Проверка на пустой массив с отзывами - без неё выдаёт ошибку
        if($reviews->count()) {
            foreach ($reviews as $review) {
                $pool += $review->rating;
            }

            # Добавляем к общему пулу оценок - оценку пользователя и вычисляем средний балл модификации
            $pool+= $rating;
            $averageRating = $pool / ($reviews->count() + 1);
        } else {
            $averageRating = $rating;
        }

        # Транзакция добавления отзыва
        DB::transaction(function () use ($request, $id, $rating, $averageRating) {
            DB::table('reviews')->insert([
                'object_id' => $id,
                'user_id' => Auth::id(),
                'title' => $request->input('title'),
                'content' => $request->input('content'),
                'rating' => $rating,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);

            # Обновление оценки мода
            $mod = Mods::where('id', $id)->first();
            $mod->rating = $averageRating;
            $mod->save();
        }, 5);

        return true;
    }

    # Валидация комментария
    public static function commentValidate($request, $id) {
        $limits = Comments::whereDate('created_at', '=', Carbon::today()->format('Y-m-d'))->where('user_id', Auth::id())->get();
        # Проверка лимитов для ежедневного ограничения отправки комментариев пользователем, во избежание спама
        if($limits->count() >= Controller::$maxComments) {
            self::$error = 'На сегодня лимит комментариев исчерпан!';
            return false;
        }

        # Проверка наличия мода в БД
        if(!Mods::where('id', $id)->first()) {
            self::$error = 'Мод не найден';
            return false;
        }

        $validate = Validator::make($request->all(), [
            'comment' => 'required',
        ],[
            'comment.required' => 'Введите ваше сообщение',
        ])->errors();
        # Валидация вводимых данных
        if($validate->any()) {
            self::$error = $validate->all();
            return false;
        }

        return true;
    }

}
