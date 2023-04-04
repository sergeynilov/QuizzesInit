<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_meeting_user_quizzes_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_meeting_id')->references('id')->on('user_meetings')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->bigInteger('user_quizzes_history_id')->unsigned();
            $table->foreign('user_quizzes_history_id', 'user_meeting_user_quizzes_history_foreign')->references('id')->on('user_quizzes_history')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->timestamp('created_at');
            $table->index(['user_meeting_id', 'user_quizzes_history_id'], 'user_meeting_user_quizzes_history_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_meeting_user_quizzes_history');
    }
};
