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
        # ТОПИКИ НА ФОРУМЕ
        Schema::create('forum', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('url')->nullable();
            $table->string('title')->nullable();
            $table->foreignId('user_id')
                ->references('id')
                ->on('users');
            $table->string('type')->nullable();
            $table->boolean('closed')->default(FALSE);
            $table->boolean('hidden')->default(FALSE);
        });

        # КЛЮЧ ТОПИКА ДЛЯ КОММЕНТАРИЕВ
        Schema::table('comments', function (Blueprint $table) {
            $table->foreignId('object_id')
                ->references('id')
                ->on('forum')
                ->onDelete('cascade');
        });

        # КЛЮЧ ДЛЯ ТОПИКА МОДОВ
        Schema::table('mods', function (Blueprint $table) {
            $table->foreignId('topic_id')
                ->references('id')
                ->on('forum');
        });

        # КЛЮЧ ДЛЯ ТОПИКА НОВОСТЕЙ
        Schema::table('news', function (Blueprint $table) {
            $table->foreignId('topic_id')
                ->references('id')
                ->on('forum');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('forum');
    }
};
