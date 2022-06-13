<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Mods;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;


class AdminController extends Controller
{
    public $maxFolders = 3;
    public $dirNews = 'public/news/';
    public $dirMods = 'public/mods/';

    public function panel() {
        return view('admin.panel');
    }

    ################## НОВОСТИ ##################
    # Список новостей
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

        ################# КОД ДУБЛИРУЕТСЯ В modCreate #################
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

        # Путь к картинке в хранилище
        $path = $this->dirNews . $new->folder . '/' . $new->id;

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
            case 'new':
                $object = News::where('published', false)->first();
                $folderName = 'news';
                break;
            case 'mod':
                $object = Mods::where('published', false)->first();
                $folderName = 'mods';
                break;
            default:
                if(strpos($url, 'new') !== false){
                    $object = News::where('id', $id)->first();
                    $folderName = 'news';
                }
                if(strpos($url, 'mod') !== false){
                    $object = Mods::where('id', $id)->first();
                    $folderName = 'mods';
                }
        }

        $id = $object->id;
        $folder = $object->folder;

        # Должно загружать в папку с id статьи
        $fileName = rand(0, 100000000) . $request->file('file')->getClientOriginalName();
        $path = $request->file('file')->storeAs("$folderName/". $folder .'/' . $id , $fileName, 'public');
        return response()->json(['location'=>"/storage/$path"]);
    }

    ################# МОДЫ #################
    # Страница списка модов
    public function modsList(){
        return view('admin.mods.list', [
            'list' => Mods::where('published', true)->orderByDesc('id')->get(),
        ]);
    }

    # Страница создания мода
    public function modCreate(){
        # Инициализация мода как "Не опубликованного"
        $mod = Mods::where('published', false)->first();

        # Если пустая запись не найдена - то создаём её
        if(!isset($mod)) {
            $mod = new Mods();
            $mod->save();
        }

        ################# КОД ДУБЛИРУЕТСЯ В newCreate #################
        # Определяем директорию папки
        $files = Storage::directories($this->dirMods);

        foreach ($files as $file) {
            $dirNumber = str_replace($this->dirMods,'', $file);
            $directories[] = $dirNumber;
        }
        sort($directories);
        # Берём последнюю папку
        $lastDir = $this->dirMods . end($directories);

        # Проверяем количество директорий в папке
        $countDirectories = count(Storage::directories($lastDir));
        # Номер общей папки
        $folder = end($directories);

        # Проверить - если папка не существует - то создать.
        if(!File::exists('storage/mods/'. $folder . '/' . $mod->id)) {
            if ($countDirectories >= $this->maxFolders) {
                # Создаём новую папку
                $folder = end($directories) + 1;
                $newDir = 'storage/mods/' . $folder;
                File::makeDirectory($newDir);

                # А в ней ещё одну папку с id статьи
                File::makeDirectory($newDir . '/' . $mod->id);
            } else {
                # А если нет перегрузки файлов, то проверяем есть ли уже папка с id
                File::makeDirectory('storage/mods/' . $folder . '/' . $mod->id);
            }
        }

        # Сохраняем номер папки в поле `folder`
        $mod->folder = $folder;
        $mod->save();

        return view('admin.mods.create', [
            'tags' => DB::table('tags')->get(),
            'mod' => $mod,
        ]);
    }

    # Обработчик создания мода
    public function modStore(Request $request){
        # Сохранить данные в запись чтобы они не сбросились после валидации
        $mod = Mods::where('published', false)->first();
        $mod->title = $request->input('title');
        $mod->content = $request->input('content');
        $mod->tags = $request->input('tags');
        $mod->platform = $request->input('platform');
        $mod->save();

        # Валидировать данные
        Validator::make($request->all(), [
            'title' => 'required|min:3',
            'content' => 'required',
            'cover' => 'required',
            'screenshots' => 'required',
            'tags' => 'required',
            'platform' => 'required',
        ],[
            'title.required' => 'Заголовок не заполнен',
            'title.min' => 'Заголовок должен содержать минимум 3 символа',
            'content.required' => 'Контент отсутствует',
            'cover.required' => 'Обложка отсутствует',
            'screenshots.required' => 'Скриншоты отсутствуют',
            'screenshots.min' => 'Минимум 4 скриншота',
            'tags.required' => 'Должен быть минимум один тэг',
            'platform.required' => 'Платформа не выбрана',
        ])->validateWithBag('post');

        # Если валидация проходит - то публикуем мод и сохраняем оставшиеся данные
        $mod->video = $request->input('video');
        $mod->memory = $request->input('memory');
//        $mod->description = $request->input('description');
//        $mod->seo_description = $request->input('seo_description');
        $mod->torrent = $request->input('torrent');
        $mod->yandex = $request->input('yandex');
        $mod->google = $request->input('google');
        $mod->published = true;

        # Добавить файлы в папку (обложку, скриншоты и контент)
        # Обложка
        $path = $this->dirMods . $mod->folder . '/' . $mod->id;
        $mod->img = '/storage/' . str_replace('public/', '', $request->file('cover')->store($path));
        $mod->save();

        # Скриншоты
        $screens = [];
        foreach($request->file('screenshots') as $img) {
            # Добавить картинки в базу + загрузить на сервер { store() }
            $screens[] = ['mod_id' => $mod->id, 'href' => '/storage/' . str_replace('public/', '', $img->store($path))];
        }
        DB::table('mods_images')->insert($screens);

        # Чистка неиспользуемых картинок в директории
        Admin::clearDirImages(Storage::files($path), $mod, $request->input('content'));
        return redirect()->back()->withSuccess('Модификация успешно добавлена');
    }
}
