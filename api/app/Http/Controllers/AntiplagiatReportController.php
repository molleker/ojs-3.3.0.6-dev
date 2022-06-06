<?php

namespace App\Http\Controllers;

use App\AntiplagiatReportStatus;
use App\Article;
use App\ArticleFile;


class AntiplagiatReportController extends Controller
{
    public function store(Article $article, ArticleFile $article_file)
    {
        if (! $article_file->antiplagiatReport) {
            return $article_file->antiplagiatReport()->create([
                'status_id' => AntiplagiatReportStatus::TREATMENT,
            ]);
        }

        if ($article_file->antiplagiatReport->canRetry()) {
            $article_file->antiplagiatReport->update(['status_id' => AntiplagiatReportStatus::TREATMENT]);
            return $article_file->antiplagiatReport;
        }

        return $article_file->antiplagiatReport;
    }
}
