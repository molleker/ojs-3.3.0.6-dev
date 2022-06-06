<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAntiplagiatExceptionLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('antiplagiat_exception_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('antiplagiat_report_id');
            $table->text('exception')->nullable();
            $table->text('message')->nullable();
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
        Schema::drop('antiplagiat_exception_logs');
    }
}
