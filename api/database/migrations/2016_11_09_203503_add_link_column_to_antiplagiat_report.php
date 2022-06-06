<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLinkColumnToAntiplagiatReport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::table('antiplagiat_reports', function (Blueprint $table) {
            $table->string('link')->nullable();
            $table->float('score')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Schema::table('antiplagiat_reports', function (Blueprint $table) {
            $table->dropColumn('link');
            $table->dropColumn('score');
        });
    }
}
