<?php


namespace App\Components;


use App\AntiplagiatReport;
use App\Article;
use App\ArticleStatus;
use App\EditDecision;
use App\Journal;
use Closure;
use DB;
use Illuminate\Support\Collection;
use JsonSerializable;

class JournalReport implements JsonSerializable
{
    static $articles_with_decisions_cache = [];
    /**
     * @var DateRange
     */
    private $date_range;
    /**
     * @var Journal
     */
    private $journals;

    private $article_submission_files;

    public function __construct(DateRange $date_range, $journal_ids)
    {
        $this->date_range = $date_range;
        $this->journals = Journal::findMany($journal_ids);
    }

    private function query()
    {
        return Article::whereIn('context_id', $this->journals->pluck('journal_id'))
            ->whereBetween('date_submitted', $this->date_range->queryRange());
    }

    public function incomingArticles()
    {
        return $this->query()->count();
    }

    private function startRange()
    {
        return $this->date_range->queryRange()[0];
    }

    private function endRange()
    {
        return $this->date_range->queryRange()[1];
    }

    private function articlesWithDecisionInPeriod($decisions = [])
    {
        $unique_key = implode('-', $decisions);
        if (! array_get(self::$articles_with_decisions_cache, $unique_key)) {
            $decision = implode(',', $decisions);
            $inner_query = "
                        SELECT MAX(date_decided) as date_decided, submission_id 
                            FROM edit_decisions
                            WHERE date_decided >= '{$this->startRange()}' AND date_decided <= '{$this->endRange()}'
                        GROUP BY submission_id";

            $raw = "
                SELECT ed.submission_id as submission_id FROM ($inner_query) r
                INNER JOIN edit_decisions ed ON 
                    ed.date_decided = r.date_decided AND 
                    ed.submission_id = r.submission
                 WHERE decision IN ({$decision})
               ";

            $article_ids = [];
            foreach (DB::select($raw) as $row) {
                $article_ids[] = $row->submission_id;
            }
            array_set(self::$articles_with_decisions_cache, $unique_key, $article_ids);
        }


        return $this->query()->whereIn('submission_id', array_get(self::$articles_with_decisions_cache, $unique_key));
    }

    private function rejectDecisionArticlesCount(Collection $articles)
    {
        if ($articles->isEmpty()) {
            return 0;
        }
        $inner_query = '
                        SELECT MAX(date_decided) as date_decided, submission_id 
                            FROM edit_decisions
                        WHERE
                        submission_id IN (' . $articles->implode(',') . ')
                        GROUP BY submission_id';

        $raw = "
                SELECT COUNT(*) as count FROM ($inner_query) r
                INNER JOIN edit_decisions ed ON 
                    ed.date_decided = r.date_decided AND 
                    ed.submission_id = r.submission_id
                WHERE ed.decision = " . EditDecision::REJECT . " ";

        return DB::select($raw)[0]->count;
    }

    public function rejectedArticles()
    {
        $all_articles = $this->query()->pluck('submission_id');

        $rejected = $this->query()->where('status', ArticleStatus::REJECTED)->pluck('submission_id');

        return $rejected->count() + $this->rejectDecisionArticlesCount($all_articles->diff($rejected));
    }

    private function articleSubmissionFiles()
    {
        if (! $this->article_submission_files) {
            $this->article_submission_files = collect();
            $this->query()->with([
                'submissionFileLogs'
            ])->get()->each(function (Article $article) {
                $this->article_submission_files->push($article->submissionAllFilesIds()->unique());
            });
        }

        return $this->article_submission_files;
    }

    public function articlesWithAntiplagiatReports()
    {
        $antiplagiat_reports = AntiplagiatReport::whereIn('file_id', $this->articleSubmissionFiles()->collapse())->get();
        return $this->articleSubmissionFiles()->filter(function (Collection $file_ids) use ($antiplagiat_reports) {
            return $antiplagiat_reports->whereIn('file_id', $file_ids)->isNotEmpty();
        })->count();
    }

    private function antiplagiatReports()
    {
        return AntiplagiatReport::whereIn('file_id', $this->articleSubmissionFiles()->collapse());
    }

    private function arrayAvg($array)
    {
        return array_sum($array) / count($array);
    }

    private function avg(Collection $collection, Closure $callback = null)
    {
        if ($collection->isEmpty()) {
            return [];
        }
        $array = $collection->toArray();
        sort($array);
        if (count($array) % 2 === 0) {
            $median = $this->arrayAvg([$array[count($array) / 2], $array[count($array) / 2 - 1]]);
        } else {
            $median = $array[count($array) / 2 - 0.5];
        }

        return [
            'meridian' => $callback ? $callback($median) : $median,
            'avg' => $callback ? $callback($this->arrayAvg($array)) : $this->arrayAvg($array),
        ];
    }

    public function antiplagiatReportsSum()
    {
        return $this->antiplagiatReports()->count();
    }

    public function antiplagiatOriginalScoreAvg()
    {
        return $this->avg($this->antiplagiatReports()->get()->pluck('original_score'));
    }

    public function readyArticles()
    {
        return $this->query()->where('status', ArticleStatus::READY)->count();
    }

    public function passedReview()
    {
        return $this
            ->articlesWithDecisionInPeriod([EditDecision::REJECT, EditDecision::ACCEPT])
            ->with('reviewAssignmentsCompleted')
            ->get()
            ->filter(function (Article $article) {
                return $article->reviewAssignmentsCompleted->isNotEmpty();
            })
            ->count();
    }

    public function reviewCount()
    {
        return $this
            ->articlesWithDecisionInPeriod([EditDecision::REJECT, EditDecision::ACCEPT])
            ->with('reviewAssignmentsHasRecommendation')
            ->get()
            ->sum(function (Article $article) {
                return $article->reviewAssignmentsHasRecommendation->count();
            });
    }

    public function firstReviewTime()
    {
        return $this->avg($this->query()->get()->pluck('first_review_time')->filter(function ($timestamp) {
            return $timestamp > 0;
        }));
    }

    public function avgReviewTime()
    {
        return $this->avg($this
            ->articlesWithDecisionInPeriod([EditDecision::REJECT, EditDecision::ACCEPT])
            ->get()
            ->pluck('review_time')
            ->filter(function ($timestamp) {
                return $timestamp > 0;
            }));
    }

    public function avgBeforePrintPrepare()
    {
        $times = $this
            ->articlesWithDecisionInPeriod([EditDecision::ACCEPT])
            ->where('status', ArticleStatus::READY)
            ->get()
            ->pluck('before_print_prepare_time')
            ->filter(function ($timestamp) {
                return $timestamp > 0;
            });
        return $this->avg($times);
    }

    public function articles()
    {
        return $this->query()->with([
            'sectionSettings',
            'submissionFile.antiplagiatReport',
            'submissionFileLogs.oldFile.antiplagiatReport',
            'submissionFileLogs.newFile.antiplagiatReport',
            'reviewAssignmentsConfirmed',
            'reviewAssignmentsHasRecommendation'
        ])->get();
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    function jsonSerialize()
    {
        if ($this->query()->count() == 0) {
            return [];
        }

        return [
            'incoming_articles' => $this->incomingArticles(),
            'rejected_articles' => $this->rejectedArticles(),
            'ready_articles' => $this->readyArticles(),
            'articles_with_antiplagiat_reports' => $this->articlesWithAntiplagiatReports(),
            'antiplagiat_reports_sum' => $this->antiplagiatReportsSum(),
            'antiplagiat_original_score_avg' => $this->antiplagiatOriginalScoreAvg(),
            'passed_review' => $this->passedReview(),
            'review_count' => $this->reviewCount(),
            'first_review_time' => $this->firstReviewTime(),
            'avg_review_time' => $this->avgReviewTime(),
            'avg_before_print_prepare' => $this->avgBeforePrintPrepare(),
        ];
    }
}