<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mods extends Model
{
    use HasFactory;

    public function images()
    {
        return $this->hasMany(ModsImages::class, 'mod_id');
    }
}
