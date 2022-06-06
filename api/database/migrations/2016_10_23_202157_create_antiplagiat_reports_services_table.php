<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAntiplagiatReportsServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::create('antiplagiat_report_services', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('antiplagiat_report_id')->index('arid');
            $table->float('score_white')->nullable();
            $table->float('score_black')->nullable();
            $table->string('name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Schema::drop('antiplagiat_report_services');
    }
}
