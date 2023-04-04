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
        Schema::create('user_quiz_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_category_id')->references('id')->on('quiz_categories')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->string('user_name', 100);
            $table->string('user_email', 100);
            $table->date('expires_at');
            $table->uuid('hashed_link')->unique();

            $table->boolean('is_passed')->default(false);
//            $table->string('selected_locale', 2)->nullable();

            $table->timestamp('created_at');
            $table->timestamp('updated_at')->nullable();

            $table->index(['quiz_category_id', 'user_email'], 'user_quiz_requests_quiz_category_id_user_email_index');
            $table->index(['is_passed', 'quiz_category_id'], 'user_quiz_requests_is_passed_quiz_category_id_index');
        });

        Artisan::call('db:seed', array('--class' => 'sergeynilov\QuizzesInit\database\seeders\userQuizRequestsWithInitData'));

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_quiz_requests');
    }
};
