<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminAndRoles extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            $admin = [
                'name' => 'admin',
                'email' => 'admin@mail.ru',
                'password' => Hash::make('12345678'),
            ],
            $shepych = [
                'name' => 'shepych',
                'email' => 'shepych@mail.ru',
                'password' => Hash::make('12345678'),
            ],
        ];

        # Создание администратора
        DB::table('users')->insert($users);

        $roles = [
            [
                'name' => 'admin',
                'guard_name' => 'web',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            ],
            [
                'name' => 'moderator',
                'guard_name' => 'web',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            ],
            [
                'name' => 'user',
                'guard_name' => 'web',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            ],
            [
                'name' => 'blocked',
                'guard_name' => 'web',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            ],
        ];
        # Добавление ролей
        DB::table('roles')->insert($roles);

        $hasRoles = [
            [
                'role_id' => 1,
                'model_type' => 'App\Models\User',
                'model_id' => 1
            ],
            [
                'role_id' => 2,
                'model_type' => 'App\Models\User',
                'model_id' => 1
            ],
        ];
        # Установка прав админа и модератора
        DB::table('model_has_roles')->insert($hasRoles);
    }
}
