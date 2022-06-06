<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSubmissionFileLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('submission_file_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('article_id');
            $table->integer('old_file_id')->nullable();
            $table->integer('new_file_id');
            $table->integer('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('submission_file_logs');
    }
}
