<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;


class AdminController extends Controller
{
    public $maxFiles = 5;
    public $maxFolders = 3;
    public $dirNews = 'public/news/';

    public function panel() {
        return view('admin.panel');
    }

    # Страница
    public function newsList() {
        return view('admin.news.list', [
            'list' => News::where('published', true)->orderByDesc('id')->get(),
        ]);
    }

    # Страница создания новости
    public function newCreate() {
        $article = News::where('published', false)->first();

        # Создаём не заполненную статью
        if(!$article) {
            $article = new News();
            $article->save();
        }

        # Определяем директорию папки
        $files = Storage::directories($this->dirNews);

        foreach ($files as $file) {
            $dirNumber = str_replace($this->dirNews,'', $file);
            $directories[] = $dirNumber;
        }
        sort($directories);
        # Берём последнюю папку
        $lastDir = $this->dirNews . end($directories);

        # Проверяем количество директорий в папке
        $countDirectories = count(Storage::directories($lastDir));
        # Номер общей папки
        $folder = end($directories);

        # Проверить - если папка не существует - то создать.
        if(!File::exists('storage/news/'. $folder . '/' . $article->id)) {
            if ($countDirectories >= $this->maxFolders) {
                # Создаём новую папку
                $folder = end($directories) + 1;
                $newDir = 'storage/news/' . $folder;
                File::makeDirectory($newDir);

                # А в ней ещё одну папку с id статьи
                File::makeDirectory($newDir . '/' . $article->id);
            } else {
                # А если нет перегрузки файлов, то проверяем есть ли уже папка с id
//            if(!File::exists('storage/news/'. $folder . '/' . $article->id) == false) {
                File::makeDirectory('storage/news/' . $folder . '/' . $article->id);
//            }
            }
        }

        # Сохраняем номер папки в поле `folder`
        $article->folder = $folder;
        $article->save();

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


        $new = News::where('published', false)->first();

        # Загружаем картинку в хранилище
        $path = 'public/news/' . $new->folder . '/' . $new->id;

        # Добавляем статью и загружаем обложку
        $new->title = $request->input('title');
        $new->content = $request->input('content');
        $new->date_time = date("Y-m-d H:i:s");
        $new->img = '/storage/' . str_replace('public/','', $request->file('cover')->store($path));
        $new->published = true;
        $new->save();

        # Чистка неиспользуемых картинок в директории
        Admin::clearDirImages(Storage::files($path), $new, $request->input('content'));

        return redirect()->back()->withSuccess('Статья успешно добавлена');
    }

    # Обновление новости (Страница и обработка {get + post = any})
    public function newUpdate(Request $request, $id)
    {
        # Проверка наличия статьи в базе
        $article = News::where('id', $id)->first();
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

            # Если картинка не меняется - то оставляем её
            $path = $article->img;
            # Путь к папке
            $pathToSave = '/public/news/' . $article->folder . '/' . $article->id;

            # Если выбрана картинка - то заменить её
            if($request->cover) {
                $file = File::exists(public_path($article->img));
                # Проверка наличия файла для игнорирования ошибки
                if($file){
                    # Удаляем предыдущю картинку
                    unlink(public_path($article->img));
                }

                # Загружаем свежую
                $path = '/storage/' . str_replace('public/','', $request->file('cover')->store($pathToSave));
            }

            # Чистка неиспользуемых картинок в content
            Admin::clearDirImages(Storage::files($pathToSave), $article, $request->input('content'));

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
        $new = News::where('id', $id)->first();
        Storage::deleteDirectory('public/news/' . $new->folder . '/' . $new->id);
        $new->delete();
        return redirect()->back()->withSuccess('Статья удалена');
    }

    # Загрузка картинки в textarea ( TinyMCE )
    public function upload(Request $request){
        # С помощью цикла вычисляем id из строки предыдущего url
        $url = url()->previous();
        $length = strlen($url);
        for($i = 1; $i <= $length; $i++) {
            # Ищем разделитель в строке начиная с конца строки
            $string = substr($url, $i - $i * 2);

            # Если находим - удаляем и выходим из цикла с полученным результатом
            if(strpos($string, '/') !== false) {
                $id = str_replace('/', '', $string);
                break;
            }
        }

        switch ($id) {
            case 'create':
                $new = News::where('published', false)->first();
                break;
            default:
                $new = News::where('id', $id)->first();
        }

        $id = $new->id;
        $folder = $new->folder;

        # Должно загружать в папку с id статьи
        $fileName = rand(0, 100000000) . $request->file('file')->getClientOriginalName();
        $path = $request->file('file')->storeAs('news/'. $folder .'/' . $id , $fileName, 'public');
        return response()->json(['location'=>"/storage/$path"]);
    }
}
