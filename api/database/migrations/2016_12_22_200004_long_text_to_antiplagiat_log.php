<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LongTextToAntiplagiatLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('antiplagiat_logs', function (Blueprint $table) {
            $table->dropColumn('data');
        });
        Schema::table('antiplagiat_logs', function (Blueprint $table) {
            $table->longText('data');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('antiplagiat_logs', function (Blueprint $table) {
            $table->dropColumn('data');
        });

        Schema::table('antiplagiat_logs', function (Blueprint $table) {
            $table->text('data');
        });
    }
}
