<?php


namespace App\Observers;


use App\Article;
use Auth;

class ArticleObserver
{
    public function updated(Article $article)
    {
        if ($article->isDirty('submission_file_id')) {
            $article->submissionFileLogs()->create([
                'old_file_id' => $article->getOriginal('submission_file_id'),
                'new_file_id' => $article->submission_file_id,
                'user_id' => Auth::user()->user_id,
            ]);
        }
    }

}