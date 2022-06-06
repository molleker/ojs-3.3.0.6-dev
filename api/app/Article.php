<?php

namespace App;

use App\Components\NullEditDecision;
use App\States\ArticleFileState;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

/**
 * App\Article
 *
 * @property integer $submission_id
 * @property string $locale
 * @property integer $user_id
 * @property integer $context_id
 * @property integer $section_id
 * @property string $language
 * @property string $comments_to_ed
 * @property string $citations
 * @property string $date_submitted
 * @property string $last_modified
 * @property string $date_status_modified
 * @property boolean $status
 * @property boolean $submission_progress
 * @property boolean $current_round
 * @property integer $submission_file_id
 * @property integer $status_submission_file
 * @property integer $revised_file_id
 * @property integer $review_file_id
 * @property integer $editor_file_id
 * @property string $pages
 * @property boolean $fast_tracked
 * @property boolean $hide_author
 * @property boolean $comments_status
 * @property-read \App\Journal $journal
 * @method static \Illuminate\Database\Query\Builder|\App\Article whereArticleId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Article whereLocale($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Article whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Article whereJournalId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Article whereSectionId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Article whereLanguage($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Article whereCommentsToEd($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Article whereCitations($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Article whereDateSubmitted($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Article whereLastModified($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Article whereDateStatusModified($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Article whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Article whereSubmissionProgress($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Article whereCurrentRound($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Article whereSubmissionFileId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Article whereRevisedFileId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Article whereReviewFileId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Article whereEditorFileId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Article wherePages($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Article whereFastTracked($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Article whereHideAuthor($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Article whereCommentsStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Article whereStatusSubmissionFile($value)
 * @mixin \Eloquent
 * @property boolean $antiplagiat_skipped
 * @property-read \App\ArticleFile $submissionFile
 * @method static \Illuminate\Database\Query\Builder|\App\Article whereAntiplagiatSkipped($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\EventLog[] $eventLogs
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ArticleFile[] $files
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ArticleSetting[] $settings
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\SubmissionFileLog[] $submissionFileLogs
 * @property boolean $edit_submission_file
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $editors
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ArticleFile[] $reviewFiles
 * @property-read mixed $submission_file_unlock_available
 * @method static \Illuminate\Database\Query\Builder|\App\Article whereEditSubmissionFile($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ReviewAssignment[] $reviewAssignments
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\EditDecision[] $editDecisions
 * @property-read mixed $first_review_time
 * @property-read mixed $review_time
 * @property-read mixed $before_print_prepare_time
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\SectionSetting[] $sectionSettings
 * @property-read mixed $section_name
 * @property-read mixed $first_antiplagiat_report_created_at
 * @property-read mixed $first_antiplagiat_report_original_score
 * @property-read mixed $antiplagiat_count
 * @property-read mixed $first_assignment_confirm
 * @property-read mixed $reviewers_count
 * @property-read mixed $edit_decision
 * @property-read mixed $completed_date
 * @property-read mixed $edit_decision_date
 */

use Illuminate\Support\Facades\DB;

class Article extends Model
{
    const EVENT_LOG_ID = 257;
    const REJECTED_STATUS = 0;

    protected $table = 'submissions';//+
//    protected $primaryKey = 'article_id';
    protected $primaryKey = 'submission_id';
    public $timestamps = false;

    protected $dates = [
        'date_submitted',
        'date_status_modified'
    ];

    private $_edit_decision;

    public function journal()
    {   
        return $this->belongsTo(Journal::class, 'context_id');
    }

    public function editors()
    {
        return $this->belongsToMany(User::class, 'edit_assignments', 'submission_id', 'editor_id');
    }

    public function files()
    {
//	dd($this->hasMany(ArticleFile::class, 'submission_id')->toSql());
        return $this->hasMany(ArticleFile::class, 'submission_id');
    }

    public function submissionFile()
    {   //DB::enableQueryLog(); 
//dd($this->files()->where('file_stage', 2)->toSql());	
//dd(DB::getQueryLog());
//	 return $this->files()->where('file_stage', 2)->get()->first();
	 return $this->hasOne(ArticleFile::class, 'submission_id')->where('file_stage', 2);
    }

    public function reviewFiles()
    {
        return $this->hasMany(ArticleFile::class, 'file_id', 'review_file_id');
    }

    public function submissionFileLogs()
    {
        return $this->hasMany(SubmissionFileLog::class);
    }

    public function eventLogs()
    {
        return $this->hasMany(EventLog::class, 'assoc_id')->where('assoc_type', self::EVENT_LOG_ID);
    }

    public function settings()
    {
        return $this->hasMany(ArticleSetting::class);
    }

    public function reviewAssignments()
    {
        return $this->hasMany(ReviewAssignment::class, 'submission_id');
    }

    public function reviewAssignmentsCompleted()
    {
        return $this->reviewAssignments()->whereNotNull('date_completed');
    }

    public function reviewAssignmentsConfirmed()
    {
        return $this->reviewAssignments()->whereNotNull('date_confirmed');
    }

    public function reviewAssignmentsHasRecommendation()
    {
        return $this->reviewAssignments()->whereNotNull('recommendation');
    }

    public function reviewAssignmentsHasReview()
    {
        return $this->reviewAssignments()->whereNotNull('reviewer_file_id');
    }

    public function editDecisions()
    {
        return $this->hasMany(EditDecision::class);
    }

    public function getFirstReviewTimeAttribute()
    {
        if ($this->status == 0) {
            return $this->date_status_modified->timestamp - $this->date_submitted->timestamp;
        }

        if ($this->reviewAssignmentsConfirmed->isNotEmpty()) {
            return $this->reviewAssignmentsConfirmed->min('date_confirmed')->timestamp - $this->date_submitted->timestamp;
        }

        return null;

    }

    public function getReviewTimeAttribute()
    {
        if ($this->reviewAssignments->isEmpty()) {
            return null;
        }

        $not_complete_reviews = $this->reviewAssignments->filter(function (ReviewAssignment $review_assignment) {
            return ! $review_assignment->date_completed;
        });

        $not_confirmed_reviews = $this->reviewAssignments->filter(function (ReviewAssignment $review_assignment) {
            return ! $review_assignment->date_confirmed;
        });

        if ($not_complete_reviews->isNotEmpty()) {
            return null;
        }

        if ($not_confirmed_reviews->isNotEmpty()) {
            return null;
        }

        $first_reviewer_confirmed = $this->reviewAssignmentsConfirmed->min('date_confirmed');
        $last_reviewer_completed = $this->reviewAssignmentsCompleted->max('date_completed');

        return $last_reviewer_completed->timestamp - $first_reviewer_confirmed->timestamp;
    }

    public function getBeforePrintPrepareTimeAttribute()
    {
        if (! $this->status === ArticleStatus::READY) {
            return null;
        }

        $last_decision = $this->editDecisions->sortBy(function (EditDecision $edit_decision) {
            return $edit_decision->date_decided;
        })->last();

        if (! $last_decision || $last_decision->decision !== EditDecision::ACCEPT) {
            return null;
        }

        return $this->date_status_modified->timestamp - $last_decision->date_decided->timestamp;
    }

    public function setting($setting_name)
    {
        $setting = $this->settings()->whereSettingName($setting_name)->first();
        return $setting ? $setting->setting_value : null;
    }

    public function addEvent($message, User $user = null)
    {
        return $this->eventLogs()->create([
            'assoc_type' => self::EVENT_LOG_ID,
            'message' => $message,
            'user_id' => $user ? $user->user_id : null,
        ]);
    }

    public function submissionAllFilesIds()
    {
        return collect([$this->submission_file_id])
            ->merge($this->submissionFileLogs->pluck('old_file_id'))
            ->merge($this->submissionFileLogs->pluck('new_file_id'));
    }

    public function getSubmissionFileUnlockAvailableAttribute()
    {
        return
            AntiplagiatReport::whereIn('file_id', $this->submissionAllFilesIds())->count() < 2 &&
            ! $this->edit_submission_file &&
            $this->submissionFile &&
            $this->submissionFile->first()->antiplagiatReport &&
            $this->submissionFile->antiplagiatReport->status_id === AntiplagiatReportStatus::READY &&
            $this->editors()->count() === 0;
    }

    public function saveFileInDb($mime_type, $size, $original_name, $type)
    {
        $file = new ArticleFile([
            'file_type' => $mime_type,
            'file_size' => $size,
            'original_file_name' => $original_name,
            'round' => $this->current_round,
        ]);
        $file->setState($type);

        /**
         * @var $file ArticleFile
         */
        $file = $this->files()->save($file);
        $file->freshName();
        $file->state->changeArticle($this);
        $this->save();
        $file->makeDir();
        return $file;
    }

    public function sectionSettings()
    {
        return $this->hasMany(SectionSetting::class, 'section_id', 'section_id');
    }

    public function filesSequence()
    {
        $files = collect([]);
        if ($this->submissionFile) {
            $files->push($this->submissionFile);
        }
        if ($this->submissionFileLogs->isNotEmpty()) {
            $this->submissionFileLogs->each(function (SubmissionFileLog $submission_file_log) use ($files) {
                if ($submission_file_log->oldFile) {
                    $files->push($submission_file_log->oldFile);
                }

                if ($submission_file_log->newFile) {
                    $files->push($submission_file_log->newFile);
                }

            });
        }
        return $files->sortBy(function (ArticleFile $article_file) {
            return $article_file->date_uploaded;
        });
    }

    public function firstAntiplagiatReport()
    {
        foreach ($this->filesSequence() as $article_file) {
            if ($article_file->antiplagiatReport) {
                return $article_file->antiplagiatReport;
            }
        }

        return null;
    }

    public function getFirstAntiplagiatReportCreatedAtAttribute()
    {
        $report = $this->firstAntiplagiatReport();
        return $report ? (string) $report->created_at : null;
    }

    public function getFirstAntiplagiatReportOriginalScoreAttribute()
    {
        $report = $this->firstAntiplagiatReport();
        return $report ? $report->original_score : null;
    }

    public function getAntiplagiatCountAttribute()
    {
        return $this->filesSequence()->reject(function (ArticleFile $article_file) {
            return is_null($article_file->antiplagiatReport);
        })->count();
    }

    public function getFirstAssignmentConfirmAttribute()
    {
        return $this->reviewAssignmentsConfirmed->isNotEmpty() ? (string) $this->reviewAssignmentsConfirmed->min('date_confirmed') : null;
    }

    public function getSectionNameAttribute()
    {
        $section_setting = $this->sectionSettings->where('setting_name', 'title')->first();
        return $section_setting ? $section_setting->setting_value : '';
    }

    public function getReviewersCountAttribute()
    {
        return $this->reviewAssignmentsHasRecommendation->count();
    }

    /**
     * @return EditDecision | false
     */
    private function editDecision()
    {
        if (is_null($this->_edit_decision)) {
            $this->_edit_decision = $this->editDecisions->sortBy('date_decided')->last() ?: app(NullEditDecision::class);
        }

        return $this->_edit_decision;
    }

    private function finallyEditDecision()
    {
        return $this->editDecision()->isFinally() ? $this->editDecision() : app(NullEditDecision::class);
    }

    public function getEditDecisionAttribute()
    {
        if ($this->status === ArticleStatus::REJECTED) {
            return EditDecision::REJECT;
        }

        return $this->finallyEditDecision()->decision;
    }

    public function getEditDecisionDateAttribute()
    {
        if ($this->status === ArticleStatus::REJECTED) {
            return (string) $this->date_status_modified;
        }

        return (string) $this->finallyEditDecision()->date_decided;
    }

    public function getCompletedDateAttribute()
    {
        if ($this->status !== ArticleStatus::READY) {
            return null;
        }

        return (string) $this->date_status_modified;
    }

    /**
     * @param UploadedFile $uploaded_file
     * @return ArticleFile
     */
    public function attachFile(UploadedFile $uploaded_file, $type)
    {
        $mime_type = $uploaded_file->getClientMimeType();
        $size = $uploaded_file->getSize();
        $original_name = $uploaded_file->getClientOriginalName();
        $file = $this->saveFileInDb($mime_type, $size, $original_name, $type);

        if ($type === ArticleFileState::SUBMISSION) {
            $review_file = $this->saveFileInDb($mime_type, $size, $original_name, ArticleFileState::REVIEW);
        }

        $uploaded_file->move($file->dirPath(), $file->file_name);

        if ($type === ArticleFileState::SUBMISSION) {
            \File::copy($file->fullPath(), $review_file->fullPath());
        }

        return $file;
    }
}
