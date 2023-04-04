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
        Schema::create('user_meetings', function (Blueprint $table) {
            $table->id();

            $table->string('name', 255);
            $table->string('user_name', 100);
            $table->string('user_email', 100);

            $table->bigInteger('user_quiz_request_id')->unsigned();
            $table->foreign('user_quiz_request_id', 'user_meetings_user_quiz_request_id_foreign')->references('id')->on('user_quiz_requests')->onUpdate('RESTRICT')->onDelete('CASCADE');


            $table->timestamp('appointed_at')->nullable();

            $table->enum('status', ['W', 'A', 'M', 'C', 'D'])->comment('W => Waiting for review,  A => Accepted for meeting, M=>Marked for future contacts, C=>Cancelled, D-Declined');
            $table->timestamps();
            $table->index(['status', 'user_email', 'appointed_at'], 'user_meetings_status_user_email_appointed_at_index');
            $table->index(['user_quiz_request_id', 'user_email', 'status'], 'user_meetings_user_quiz_request_id_user_email_status_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_meetings');
    }
};
