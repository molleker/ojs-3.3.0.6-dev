<?php

use App\Article;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAntiplagiatAvaible extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->boolean('edit_submission_file')->nullable();
        });
        Article::with('submissionFile.antiplagiatReport')->get()->each(function (Article $article) {
            $article->edit_submission_file = ! ($article->submissionFile && $article->submissionFile->antiplagiatReport);
            $article->save();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropColumn('edit_submission_file');
        });
    }
}
