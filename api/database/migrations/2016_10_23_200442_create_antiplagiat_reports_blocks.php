<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAntiplagiatReportsBlocks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::create('antiplagiat_report_blocks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('antiplagiat_report_id')->index('ariid');
            $table->integer('offset');
            $table->integer('length');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Schema::drop('antiplagiat_report_blocks');
    }
}
