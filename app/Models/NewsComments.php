<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class NewsComments extends Model
{
    use HasFactory;

    # Свзяь с пользователями
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
