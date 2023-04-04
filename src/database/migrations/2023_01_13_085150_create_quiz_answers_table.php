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
        Schema::create('quiz_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->references('id')->on('quizzes')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->json('text')->comment('This column is used for i18n support');
            $table->boolean('is_correct')->default(false);
            $table->timestamps();

            $table->index(['quiz_id', 'is_correct'], 'quiz_answers_quiz_id_is_correct_index');
        });

        Artisan::call('db:seed', array('--class' => 'sergeynilov\QuizzesInit\database\seeders\quizAnswersWithInitData'));

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quiz_answers');
    }
};
