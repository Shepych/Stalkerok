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
        Schema::create('mods', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('title')->nullable();
            $table->string('url')->nullable();
            $table->string('img')->nullable();
            $table->text('description')->nullable();
            $table->text('seo_description')->nullable();
            $table->text('content')->nullable();
            $table->string('platform')->nullable();
            $table->text('rating')->nullable();
            $table->string('tags')->nullable();
            $table->string('memory')->nullable();
            $table->string('video')->nullable();
            $table->integer('downloads')->nullable();
            $table->string('torrent')->nullable();
            $table->string('yandex')->nullable();
            $table->string('google')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mods');
    }
};
