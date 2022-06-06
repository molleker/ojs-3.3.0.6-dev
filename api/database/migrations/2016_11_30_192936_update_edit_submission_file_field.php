<?php

use App\Article;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateEditSubmissionFileField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Article::with(['submissionFile.antiplagiatReport', 'editors'])->get()->each(function (Article $article) {

            if ($article->editors->count() > 0) {
                $article->edit_submission_file = false;
            } else {
                $article->edit_submission_file = ! ($article->submissionFile && $article->submissionFile->antiplagiatReport);
            }

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
        //
    }
}
