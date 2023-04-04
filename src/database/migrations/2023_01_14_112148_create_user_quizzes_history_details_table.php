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
        Schema::create('user_quizzes_history_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_quizzes_history_id')->references('id')->on('user_quizzes_history')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreignId('quiz_answer_id')->references('id')->on('quiz_answers')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->string('text', 255)->comment('This column is used for i18n support');
            $table->boolean('is_correct')->default(false);
            $table->tinyInteger('quiz_points')->unsigned();

            $table->timestamp('created_at');

            $table->index(['user_quizzes_history_id', 'quiz_answer_id', 'is_correct'], 'user_quizzes_history_details_3fields_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_quizzes_history_details');
    }
};
