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
        Schema::create('deleted_topics_archive', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('url')->nullable();
            $table->string('title')->nullable();
            $table->foreignId('user_id')
                ->references('id')
                ->on('users');
            $table->string('type')->nullable();
            $table->boolean('closed')->nullable();
            $table->boolean('hidden')->nullable();
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
        Schema::dropIfExists('deleted_topics_archive');
    }
};
