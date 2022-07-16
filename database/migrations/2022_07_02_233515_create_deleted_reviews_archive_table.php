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
            $table->integer('user_id');
            $table->string('title')->nullable();
            $table->integer('rating')->nullable();
            $table->text('content')->nullable();
            $table->integer('moderator_id')->nullable();
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
