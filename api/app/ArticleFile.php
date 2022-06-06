<?php

namespace App;

use App\Article;
use App\States\ArticleFileReviewState;
use App\States\ArticleFileState;
use App\States\ArticleFileSubmissionState;
use File;
use Illuminate\Database\Eloquent\Model;

/**
 * App\ArticleFile
 *
 * @property integer $file_id
 * @property integer $revision
 * @property integer $source_file_id
 * @property integer $source_revision
 * @property integer $article_id
 * @property string $file_name
 * @property string $file_type
 * @property integer $file_size
 * @property string $original_file_name
 * @property integer $file_stage
 * @property boolean $viewable
 * @property string $date_uploaded
 * @property string $date_modified
 * @property boolean $round
 * @property integer $assoc_id
 * @property-read \App\AntiplagiatReport $antiplagiatReport
 * @property-read \App\Article $article
 * @method static \Illuminate\Database\Query\Builder|\App\ArticleFile whereFileId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ArticleFile whereRevision($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ArticleFile whereSourceFileId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ArticleFile whereSourceRevision($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ArticleFile whereArticleId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ArticleFile whereFileName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ArticleFile whereFileType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ArticleFile whereFileSize($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ArticleFile whereOriginalFileName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ArticleFile whereFileStage($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ArticleFile whereViewable($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ArticleFile whereDateUploaded($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ArticleFile whereDateModified($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ArticleFile whereRound($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ArticleFile whereAssocId($value)
 * @mixin \Eloquent
 */
class ArticleFile extends Model
{
    protected $table = 'submission_files';//+
    protected $primaryKey = 'submission_file_id';
    public $timestamps = false;

    protected $fillable = [
        'file_type',
        'file_size',
        'original_file_name',
        'revision',
        'round',
    ];

    protected $dates = [
        'date_uploaded',
        'date_modified'
    ];

    /**
     * @var ArticleFileState
     */
    public $state;

    public function setState($type)
    {
        switch ($type){
            case ArticleFileState::SUBMISSION:
                $this->state = new ArticleFileSubmissionState($this);
                break;
            case ArticleFileState::REVIEW;
                $this->state = new ArticleFileReviewState($this);
                break;
        }

    }

    public function antiplagiatReport()
    {
        return $this->hasOne(AntiplagiatReport::class, 'file_id');
    }

    public function article()
    {
        return $this->belongsTo(Article::class, 'submission_id');
    }

    public function fullPath()
    {
        return $this->dirPath() . '/' . $this->file_name;
    }

    public function dirPath()
    {
        return base_path('../files/journals/' . $this->article->journal->journal_id . '/articles/' . $this->article->article_id . '/' . $this->state->path());
    }

    public function freshRevision()
    {
        $this->revision = $this->state->revision($this->article);
    }

    public function freshStage()
    {
        $this->file_stage = $this->state->stage();
    }

    public function freshId()
    {
        $this->file_id = $this->state->id($this->article);
    }

    public function freshName()
    {
        $this->file_name = $this->article_id . '-' . $this->file_id . '-' . $this->revision . '-';
        $this->file_name .= $this->state->nameSuffix();
        $extension = collect(explode('.', $this->original_file_name))->last();
        $this->file_name .= '.' . $extension;
        $this->save();
    }

    public function makeDir()
    {
        if (! file_exists($this->dirPath())) {
            File::makeDirectory($this->dirPath(), 0777, true, true);
        }
    }

}
