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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('user_id')
                ->references('id')
                ->on('users');
            $table->longText('content')->nullable();
            $table->boolean('topic_content')->default(FALSE);
            $table->json('rating')->nullable();
            $table->string('type')->nullable();
            $table->integer('moderator_id')->nullable();
            $table->boolean('moderation')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comments');
    }
};
