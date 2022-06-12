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
        foreach ($files as $img) {
            # Сохраняем картинку в отдельную переменную, потому что дальше мы её будем менять
            $oldImg = $img;
            $img = str_replace('public/','', $img);
            $cover = str_replace('/storage/','', $object->img);
            if(stristr($content, $img) === FALSE) {
                # Картинка не найдена - далее проверяем не является ли она обложкой
                if($img != $cover) {
                    # Удаляем предыдущю картинку
                    Storage::delete($oldImg);
                }
            }
        }
    }
}
