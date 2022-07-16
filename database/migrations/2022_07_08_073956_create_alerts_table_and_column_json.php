<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        # ТАБЛИЦА УВЕДОМЛЕНИЙ
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('user_id')
                ->references('id')
                ->on('users');
            $table->text('content')->nullable();
            $table->integer('type')->nullable();
            $table->boolean('general')->nullable();
            $table->json('addresses')->nullable();
        });

        # ПОЛЕ С МАССИВОМ ПРОСМОТРЕННЫХ УВЕДОМЛЕНИЙ
        Schema::table('users', function (Blueprint $table) {
            $table->json('viewed_notifications')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('alerts_table_and_column_json');
    }
};
