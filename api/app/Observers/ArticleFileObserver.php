<?php
namespace App\Observers;

use App\ArticleFile;
use Carbon\Carbon;

class ArticleFileObserver
{

    public function creating(ArticleFile $article_file)
    {
        $article_file->date_modified = Carbon::now();
        $article_file->date_uploaded = Carbon::now();
        $article_file->round = $article_file->round ?: 1;
        $article_file->file_stage = $article_file->file_stage ?: 1;
        $article_file->freshRevision();
        $article_file->freshStage();
        $article_file->freshId();
    }

}