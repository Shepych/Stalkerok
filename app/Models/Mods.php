<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class Mods extends Model
{
    use HasFactory;

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

    public function comments(){
        return $this->hasMany(ModsComments::class, 'mod_id')->orderByDesc('created_at');
    }

    # Отправка комментария
    public static function sendComment($request, $id) {
        # Валидация
//        Validator::make()
        DB::table('mods_comments')->insert([
            'created_at' => date('Y-m-d H:i:m'),
            'updated_at' => date('Y-m-d H:i:m'),
            'user_id' => Auth::id(),
            'mod_id' => $id,
            'content' => $request->input('comment')
        ]);
        return true;
    }
}
