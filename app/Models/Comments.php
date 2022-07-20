<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comments extends Model
{
    use HasFactory;

    # Связь с пользователями
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    # Связь с топиком
    public function topic() {
        return $this->hasOne(Forum::class, 'id', 'object_id')->first();
    }
}
