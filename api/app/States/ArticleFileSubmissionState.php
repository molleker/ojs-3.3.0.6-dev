<?php


namespace App\States;


use App\Article;

class ArticleFileSubmissionState extends ArticleFileState
{
    public function nameSuffix()
    {
        return 'SM';
    }

    public function changeArticle(Article $article)
    {
        $article->submission_file_id = $this->article_file->file_id;
    }

    public function lastRevision(Article $article)
    {
        return $article->submissionFile->revision;
    }

    public function path()
    {
        return 'submission/original';
    }

    public function stage()
    {
        return 1;
    }

    public function id(Article $article)
    {
        return $this->article_file->file_id;
    }
}