<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class Admin extends Model
{
    use HasFactory;

    # Очистка неиспользуемых картинок в директориях
    public static function clearDirImages($files, $object, $content) {
        # Пускаем цилк по всем картинкам и ищем их вхождение в контенте
        $cover = str_replace('/storage/','', $object->img);
        foreach ($files as $img) {
            # Сохраняем картинку в отдельную переменную, потому что дальше мы её будем менять
            $oldImg = $img;
            $img = str_replace('public/','', $img);

            $screenSearch = false; // Переменная для определения поиска картинки среди скриншотов
            $coverSearch = false; // Переменная для определения поиска картинки среди обложки
            if(stristr($content, $img) === FALSE) {
                $contentSearch = false;
            } else {
                $contentSearch = true; // Переменная для определения поиска картинки среди контента
            }

            # Картинка не найдена - далее проверяем не является ли она обложкой
            if($img == $cover) {
                $coverSearch = true;
            }

            # Затем проверяем не является ли картинка скриншотом если имеются картинки
            if(isset($object->images)){
                foreach ($object->images as $image) {
                    $screen = str_replace('/storage/','', $image->href);
                    if($img == $screen) {
                        $screenSearch = true;
                        break;
                    }
                }
            }

            # Удаляем картинку если она не прошла ни одну проверку
            if(!$coverSearch && !$screenSearch && !$contentSearch) {
                Storage::delete($oldImg);
            }
        }
    }
}
