<?php
namespace App\States;

use App\Article;
use App\ArticleFile;

abstract class ArticleFileState
{

    const SUBMISSION = 'submission';
    const REVIEW = 'review';
    /**
     * @var ArticleFile
     */
    protected $article_file;

    public function __construct(ArticleFile $article_file)
    {
        $this->article_file = $article_file;
    }

    public function revision(Article $article)
    {
        return $this->lastRevision($article) + 1;
    }

    abstract function nameSuffix();
    abstract function changeArticle(Article $article);
    abstract function lastRevision(Article $article);
    abstract function path();
    abstract function stage();
    abstract function id(Article $article);

}