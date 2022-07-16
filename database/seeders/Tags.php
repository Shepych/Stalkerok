<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Tags extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tags = [
            [
                'title' => 'Хардкорный',
            ],
            [
                'title' => 'Оружейный',
            ],
            [
                'title' => 'Свободный',
            ],
        ];
        # Теги для модов
        DB::table('tags')->insert($tags);
    }
}
