<?php

namespace App;

use App\Events\WSReportPreviousResolvingEvent;
use App\Events\WSReportResolvedEvent;
use App\Jobs\AntiplagiatReportJob;
use App\Notifications\AntiplagiatJobDone;


/**
 * App\AntiplagiatReport
 *
 * @property integer $id
 * @property integer $file_id
 * @property integer $status_id
 * @property string $text
 * @property-read \App\ArticleFile $file
 * @method static \Illuminate\Database\Query\Builder|\App\AntiplagiatReport whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\AntiplagiatReport whereFileId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\AntiplagiatReport whereStatusId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\AntiplagiatReport whereText($value)
 * @mixin \Eloquent
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\AntiplagiatReportService[] $services
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\AntiplagiatReportBlock[] $blocks
 * @property-read mixed $wait_time
 * @method static \Illuminate\Database\Query\Builder|\App\AntiplagiatReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\AntiplagiatReport whereUpdatedAt($value)
 * @property string $link
 * @property string $linkForEditors
 * @property float $score
 * @property-read mixed $white_score
 * @method static \Illuminate\Database\Query\Builder|\App\AntiplagiatReport whereLink($value)
 * @method static \Illuminate\Database\Query\Builder|\App\AntiplagiatReport whereLinkForEditors($value)
 * @method static \Illuminate\Database\Query\Builder|\App\AntiplagiatReport whereScore($value)
 * @property-read mixed $original_score
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\AntiplagiatExceptionLog[] $exceptionLogs
 * @property-read \App\AntiplagiatUploadedFile $uploadedFile
 * @property-read mixed $status
 */
class AntiplagiatReport extends \Eloquent
{
    const JOB_EXECUTION_TIME = 10;

    protected $fillable = [
        'status_id',
        'text',
        'link',
        'linkForEditors'
    ];

    protected $appends = [
        'wait_time',
        'original_score',
        'white_score'
    ];

    public function file()
    {
        return $this->belongsTo(ArticleFile::class, 'submission_file_id');
    }

    public function services()
    {
        return $this->hasMany(AntiplagiatReportService::class);
    }

    public function blocks()
    {
        return $this->hasMany(AntiplagiatReportBlock::class);
    }

    public function exceptionLogs()
    {
        return $this->hasMany(AntiplagiatExceptionLog::class);
    }

    public function uploadedFile()
    {
        return $this->hasOne(AntiplagiatUploadedFile::class);
    }

    public function getWaitTimeAttribute()
    {
        return null;
    }

    public function getWhiteScoreAttribute()
    {
        return $this->services()->sum('score_white');
    }

    public function getStatusAttribute()
    {
        return app(AntiplagiatReportStatus::class, [
            ['id' => $this->status_id]
        ]);
    }

    public function statusFailed()
    {
        return in_array($this->status_id, [
            AntiplagiatReportStatus::UNKNOWN_FAILED,
            AntiplagiatReportStatus::NO_TEXT_IN_FILE_FAILED,
            AntiplagiatReportStatus::DOUBLE_UPLOAD_FAILED_FILE,
            AntiplagiatReportStatus::UNSUPPORTED_FILE_FORMAT,
            AntiplagiatReportStatus::SEVER_UNAVAILABLE,
        ]);
    }

    public function canRetry()
    {
        return $this->status->canRetry();
    }

    public function broadcastResolved()
    {   
        self::where('id', '>', $this->id)->get()->each(function (AntiplagiatReport $antiplagiat_report) {
            broadcast(new WSReportPreviousResolvingEvent($antiplagiat_report));
        });

        broadcast(new WSReportResolvedEvent($this));

        if ($this->fresh()->statusFailed()) {
            return;
        }
	
        $email_setting = $this->file->article->journal->setting('antiplagiat_email');

        if ($email_setting && $email_setting->setting_value) {
            $message = new AntiplagiatJobDone($this);
            $email_setting->notify($message);
            EmailLog::create([
                'assoc_type' => Article::EVENT_LOG_ID,
                'assoc_id' => $this->file->article->article_id,
                'from_address' => config('mail.from.address'),
                'recipients' => $email_setting->setting_value,
                'subject' => config('antiplagiat.email.subject'),
                'body' => $message->toText(),
            ]);
        } 
    }

    public function getOriginalScoreAttribute()
    {
        return 100 - $this->score;
    }

}
