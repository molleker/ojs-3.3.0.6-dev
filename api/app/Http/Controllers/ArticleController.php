<?php

namespace App\Http\Controllers;

use App\Article;
use App\ArticleFile;
use Illuminate\Http\Request;


class ArticleController extends Controller
{
    public function show(Article $article)
    {
        $article->append('submission_file_unlock_available');
        $article->load([
            'submissionFile.antiplagiatReport',
            'journal'
        ]);

        $settings = $article->journal->settings()->where('setting_name', 'antiplagiat')->get();

        return [
            'article_id' => $article-> submission_id,
            'edit_submission_file' => $article->edit_submission_file,
	    'status_submission_file' => $article->status_submission_file,
            'submission_file_unlock_available' => $article->submission_file_unlock_available,
            'journal' => [
                'journal_id' => $article->journal->journal_id,
                'path' => $article->journal->path,
                'settings' => $settings,
            ],
            'submission_file' => [
                'article_id' => $article->submissionFile->submission_id,
                'date_modified' => (string) $article->submissionFile->updated_at,
                'antiplagiat_report' => $article->submissionFile->antiplagiatReport ? [
                    'file_id' => $article->submissionFile->antiplagiatReport->file_id,
                    'id' => $article->submissionFile->antiplagiatReport->id,
                    'link' => $article->submissionFile->antiplagiatReport->link,
                    'linkForEditors' => $article->submissionFile->antiplagiatReport->linkForEditors,
                    'original_score' => $article->submissionFile->antiplagiatReport->original_score,
                    'score' => $article->submissionFile->antiplagiatReport->score,
                    'status_id' => $article->submissionFile->antiplagiatReport->status_id,
                    'wait_time' => $article->submissionFile->antiplagiatReport->wait_time,
                    'white_score' => $article->submissionFile->antiplagiatReport->white_score,
                ] : null,
                'file_id' => $article->submissionFile->file_id,
                'file_name' => $article->submissionFile->file_name, 
            ],
        ];
    }

    public function update(Request $request, Article $article) {
        $article->edit_submission_file = $request->input('edit_submission_file');
        $article->status_submission_file = $request->input('status_submission_file');
        $article->save();
        return $this->show($article);
    }
}
