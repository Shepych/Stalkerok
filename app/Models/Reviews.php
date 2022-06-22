<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reviews extends Model
{
    use HasFactory;

    # Связь с пользователем
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
