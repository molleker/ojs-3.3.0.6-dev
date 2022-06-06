<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAntiplagiatReportsServicesSourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::create('antiplagiat_report_service_sources', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('antiplagiat_report_service_id')->index('arsid');
            $table->string('source_hash')->nullable();
            $table->float('score_by_report')->nullable();
            $table->float('score_by_source')->nullable();
            $table->string('author')->nullable();
            $table->string('url')->nullable();
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
        \Schema::drop('antiplagiat_report_service_sources');
    }
}
