<?php


namespace App\States;


use App\Article;

class ArticleFileReviewState extends ArticleFileState
{
    public function nameSuffix()
    {
        return 'RV';
    }

    public function changeArticle(Article $article)
    {
    }

    public function lastRevision(Article $article)
    {
        return $article->reviewFiles->max('revision');
    }

    public function path()
    {
        return 'submission/review';
    }

    public function stage()
    {
        return 2;
    }

    public function id(Article $article)
    {
        return $article->review_file_id;
    }
}