<?php

namespace App\Http\Controllers;

use App\Models\Mods;
use App\Models\Reviews;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ModsController extends Controller
{
    public $maxReviews = 8;
    public $maxComments = 100;

    public function modsList(Request $request) {
        $mods = Mods::where('published', true)->orderByDesc('id')->paginate(2);

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
        ]);
    }

    public function modPage($url) {
        $mod = Mods::where('url', $url)->first();

        $tags = DB::table('tags')->get();
        if(!$mod) {
            abort(404);
        }

        # Добавить проверку URL
        if($mod->url != $url) {
            abort(404);
        }

        return view('mods.page', [
            'mod' => $mod,
            'tags' => $tags,
        ]);
    }

    public function modReview(Request $request, $id) {
        $user = Auth::user();

        # Проверка на авторизацию
        if(!$user) {
            return Ajax::message('Авторизируйтесь на сайте');
        }

        # Проверка на блокировку
        if($user->hasRole(['blocked'])){
            return Ajax::message('Аккаунт заблокирован');
        }

        # Проверить не отправлял ли уже пользователь отзыв
        $reviews = Reviews::where('object_id', $id)->get();
        # Циклом проверим есть ли уже отзыв от юзера на этот мод. Цикл нужен чтобы избежать лишнего запроса на вычислении рейтинга к моду
        foreach ($reviews as $review) {
            if($review->user_id == Auth::id()) {
                return Ajax::message('Вы уже оставляли отзыв на этот мод');
            }
        }

        # Проверка лимитов для ежедневного ограничения отправки отзывов пользователем, во избежание спама
        $limits = DB::table('reviews')->whereDate('created_at', '=', Carbon::today()->format('Y-m-d'))->where('user_id', Auth::id())->get();
        if($limits->count() >= $this->maxReviews) {
            return Ajax::message('На сегодня лимит отзывов исчерпан!');
        }

        # Проверка на метод POST
        if($request->method() != 'POST') {
            abort(404);
        }

        $validate = Validator::make($request->all(), [
            'title' => 'required|min:3|max:200',
            'content' => 'required|min:3',
            'type' => 'numeric',
        ],[
            'title.required' => 'Заполните заголовок',
            'title.min' => 'Заголовок должен содержать минимум 3 символа',
            'title.max' => 'Заголовок должен содержать максимум 200 символов',
            'content.required' => 'Отзыв пуст',
            'content.min' => 'Отзыв должен содержать минимум 200 символов',
            'type.numeric' => 'Не валидный тип отзыва',
        ])->errors();

        # Если ошибка валидации, то формируем ajax сообщение с перечислением ошибок функцией valid
        if($validate->any()) {
            return Ajax::valid($validate);
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

        # Добавление отзыва
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
        return Ajax::redirect(url()->previous());
    }

    public function modComment(Request $request, $id) {
        # Валидация сообщения и ID мода
        if(!Mods::commentValidate($request, $id)){
            return Ajax::valid(Mods::$error);
        }

        # Отправка комментария
        Mods::sendComment($request, $id);

        # Обновление страницы
        return redirect()->back();
    }
}
