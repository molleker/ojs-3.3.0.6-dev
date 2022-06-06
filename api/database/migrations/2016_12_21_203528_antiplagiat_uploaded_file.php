<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AntiplagiatUploadedFile extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('antiplagiat_uploaded_files', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('antiplagiat_report_id');
            $table->integer('uploaded_file_id');
            $table->text('data');
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
        Schema::drop('antiplagiat_uploaded_files');
    }
}
