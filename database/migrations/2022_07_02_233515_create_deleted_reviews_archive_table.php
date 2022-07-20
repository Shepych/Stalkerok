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
        Schema::create('deleted_reviews_archive', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('object_id');
            $table->foreignId('user_id')
                ->references('id')
                ->on('users');
            $table->string('title')->nullable();
            $table->integer('rating')->nullable();
            $table->text('content')->nullable();
            $table->foreignId('moderator_id')
                ->references('id')
                ->on('users');
            $table->text('cause')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deleted_reviews_archive');
    }
};
