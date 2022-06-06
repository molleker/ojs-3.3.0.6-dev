<?php


namespace App\Components;


use App\Article;
use Illuminate\Database\Eloquent\Model;

class ReportArticlesPresenter extends Presenter
{

    /**
     * @param Article $article
     */
    protected function transform(Model $article)
    {
        return [
            'article_id' => $article->article_id,
            'section_name' => $article->section_name,
            'date_submitted' => (string) $article->date_submitted,
            'first_antiplagiat_report_original_score' => $article->first_antiplagiat_report_original_score,
            'first_antiplagiat_report_created_at' => $article->first_antiplagiat_report_created_at,
            'antiplagiat_count' => $article->antiplagiat_count,
            'first_assignment_confirm' => $article->first_assignment_confirm,
            'reviewers_count' => $article->reviewers_count,
            'edit_decision' => $article->edit_decision,
            'edit_decision_date' => $article->edit_decision_date,
            'completed_date' => $article->completed_date,
            'journal' => new JournalPresenter($article->journal),
        ];
    }
}