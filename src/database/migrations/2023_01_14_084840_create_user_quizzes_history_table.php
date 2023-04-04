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
        Schema::create('user_quizzes_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_quiz_request_id')->references('id')->on('user_quiz_requests')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreignId('quiz_category_id')->references('id')->on('quiz_categories')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->string('quiz_category_name', 255);
            $table->string('user_name', 100);
            $table->string('user_email', 100);

            $table->string('selected_locale', 2)->nullable();
            $table->smallInteger('time_spent')->nullable()->comment('In seconds');
            $table->tinyInteger('summary_points')->unsigned();
            $table->tinyInteger('max_summary_points')->unsigned();

            $table->boolean('is_reviewed')->default(false);
            $table->enum('action', ['A', 'M', 'D', 'N'])->default("N")->comment(' A => Accept for meeting, M=>Mark for future contacts, D=>Deny, N-No Action');

            $table->timestamp('created_at');
            $table->timestamp('updated_at')->nullable();

            $table->index(['quiz_category_id', 'user_email'], 'user_quizzes_history_quiz_category_id_user_email_index');
            $table->index(['is_reviewed', 'quiz_category_id', 'selected_locale'], 'user_quizzes_history_3fields_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_quizzes_history');
    }
};
