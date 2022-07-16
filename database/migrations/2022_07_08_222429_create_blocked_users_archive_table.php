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
        Schema::create('blocked_users_archive', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('moderator_id')
                ->references('id')
                ->on('users');
            $table->foreignId('user_id')
                ->references('id')
                ->on('users');
            $table->text('cause');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('blocked_users_archive');
    }
};
