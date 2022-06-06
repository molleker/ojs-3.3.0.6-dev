<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReviewIdColumnToPublonsReviews extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::table('publons_reviews', function (Blueprint $table) {
            $table->bigInteger('review_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Schema::table('publons_reviews', function (Blueprint $table) {
            $table->dropColumn('review_id');
        });
    }
}
