<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class AdminController extends Controller
{
    public $maxFiles = 5;
    public $dirNews = 'public/news/';

    public function panel() {
        return view('admin.panel');
    }

    # Страница
    public function newsList() {
        return view('admin.news.list', [
            'list' => News::all(),
        ]);
    }

    # Страница создания новости
    public function newCreate() {
        return view('admin.news.create');
    }

    # Обработка данных и создание новости
    public function newStore(Request $request) {
        # Валидация данных
        Validator::make($request->all(), [
            'title' => 'required|max:255',
            'content' => 'required',
            'cover' => 'required',
        ],[
            'title.required' => 'Введите заголовок',
            'title.max' => 'Максимальная длинна заголовка',
            'content.required' => 'Заполните статью контентом',
            'cover.required' => 'Загрузите обложку',
        ])->validateWithBag('post');

        $files = Storage::directories($this->dirNews);

        foreach ($files as $file) {
            $dirNumber = str_replace($this->dirNews,'', $file);
            $directories[] = $dirNumber;
        }

        sort($directories);
        # Берём последнюю папку
        $lastDir = $this->dirNews . end($directories);
        # Проверяем количество файлов в папке
        $countFiles = count(Storage::files($lastDir));
        if($countFiles >= $this->maxFiles) {
            // Обрезаем строку, получаем номер папки, добавляем +1 и создаём новую папку
            $newDir = $this->dirNews . (str_replace($this->dirNews,'', $lastDir) + 1);
            $path = '/storage/' . str_replace('public/','', $request->file('cover')->store($newDir));
        } else {
            $path = '/storage/' . str_replace('public/','', $request->file('cover')->store($lastDir));
        }

        # Добавляем статью
        $new = new News();
        $new->title = $request->input('title');
        $new->content = $request->input('content');
        $new->date_time = date("Y-m-d H:i:s");
        $new->img = $path;
        $new->save();

        return redirect()->back()->withSuccess('Статья успешно добавлена');
    }

    # Обновление новости (Страница и обработка {get + post = any})
    public function newUpdate(Request $request, $id)
    {
        # Проверка наличия статьи в базе
        $article = News::where('id', $id)->first();
//        dd(public_path($article->img));
        if (!$article) {
            abort(404);
        }

        if ($request->isMethod('post')) {
            # Валидация данных
            Validator::make($request->all(), [
                'title' => 'required|max:255',
                'content' => 'required',
            ], [
                'title.required' => 'Введите заголовок',
                'title.max' => 'Максимальная длинна заголовка',
                'content.required' => 'Заполните статью контентом',
            ])->validateWithBag('post');

            # Если выбрана картинка - то заменить её
            if($request->cover) {
                $file = file_exists(public_path($article->img));

                # Проверка наличия файла для игнорирования ошибки
                if($file){
                    # Удаляем предыдущю картинку
                    unlink(public_path($article->img));
                }

                $files = Storage::directories($this->dirNews);

                foreach ($files as $file) {
                    $dirNumber = str_replace($this->dirNews,'', $file);
                    $directories[] = $dirNumber;
                }

                sort($directories);
                $lastDir = $this->dirNews . end($directories);
                $countFiles = count(Storage::files($lastDir));
                if($countFiles >= $this->maxFiles) {
                    # Обрезаем строку, получаем номер папки, добавляем +1 и создаём новую папку
                    $newDir = $this->dirNews . (str_replace($this->dirNews,'', $lastDir) + 1);
                    $path = '/storage/' . str_replace('public/','', $request->file('cover')->store($newDir));
                } else {
                    $path = '/storage/' . str_replace('public/','', $request->file('cover')->store($lastDir));
                }
//                dd($path);
            }

            # Если картинка не меняется - то оставляем её
            if(!isset($path)) {
                $path = $article->img;
            }

            # Обновляем данные
            News::where('id', $id)->update([
                'title' => $request->input('title'),
                'content' => $request->input('content'),
                'img' => $path,
            ]);

            # Сообщение об успешном сохранении
            return redirect()->back()->withSuccess('Изменения сохранены');
        }

        return view('admin.news.update', [
            'article' => $article,
        ]);
    }

    # Удаление новости
    public function newDelete($id) {
        News::where('id', $id)->delete();
        return redirect()->back()->withSuccess('Статья удалена');
    }
}
