<?php

namespace App\Http\Controllers;

use App\Article;
use App\ArticleFile;
use App\Http\Requests\StoreArticleFileRequest;
use App\States\ArticleFileState;
use App\ArticleStatusSubmissionFile;
use Illuminate\Http\Request;

use App\Notifications\AuthorReplaceSubmissionFile;
use App\JournalSettings;


class ArticleFileController extends Controller
{
    public function store(StoreArticleFileRequest $request, Article $article)
    {
        $file = $request->file('file');
	$status_submission_file = $article->submissionFile->antiplagiatReport ? ArticleStatusSubmissionFile::AUTHOR_REPLACED_AFTER_ANTIPLAGIAT : ArticleStatusSubmissionFile::AUTHOR_REPLACED_BEFORE_ANTIPLAGIAT;
	$article->status_submission_file = $status_submission_file;
	$article->save();
        $saved_file = $article->attachFile($file, $request->input('type'));

	if(strpos($_SERVER['HTTP_REFERER'],'author')) {//if user an author
	        $email_setting = $article->journal->setting('contactEmail');
		$email_setting->notify(new AuthorReplaceSubmissionFile($article));

	}

        return $saved_file;
    }
}
