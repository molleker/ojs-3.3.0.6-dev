<?php

namespace App\Jobs;

use App\AntiplagiatReport;
use App\AntiplagiatReportStatus;
use App\AntiplagiatUploadedFile;
use App\Components\AntiplagiatClient;
use App\Exceptions\AntiplagiatFileUploadException;
use App\States\ArticleFileState;
use App\User;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AntiplagiatReportJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var AntiplagiatReport
     */
    protected $antiplagiat_report;
    protected $antiplagiat_client;
    protected $auth_user;
    /**
     * @var AntiplagiatUploadedFile
     */
    protected $uploaded_file;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(AntiplagiatReport $antiplagiat_report, User $auth_user, AntiplagiatUploadedFile $uploaded_file = null)
    {   
        $this->antiplagiat_report = $antiplagiat_report;
        $this->auth_user = $auth_user;
        $this->uploaded_file = $uploaded_file;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {	
        try {

            $this->antiplagiat_client = new AntiplagiatClient();
            $this->antiplagiat_report->file->setState(ArticleFileState::SUBMISSION);
            if (! $this->uploaded_file) {
                $this->uploaded_file = $this->antiplagiat_client->uploadFile($this->antiplagiat_report->file, $this->auth_user->user_id);
		$this->antiplagiat_client->requestReport($this->uploaded_file); 
            }
            
            dispatch((new AntiplagiatUploadedFileJob($this->uploaded_file, $this->auth_user))->delay(10));
        } catch (Exception $e) {
            $this->failed($e);
        }

    }

    public function repeat()
    {
        dispatch((new AntiplagiatReportJob($this->antiplagiat_report, $this->auth_user, $this->uploaded_file))->delay(10));
    }

    public function failed(Exception $e)
    {
        $this->antiplagiat_report->status_id = AntiplagiatReportStatus::UNKNOWN_FAILED;
        if ($e instanceof AntiplagiatFileUploadException) {
            if (! $e->repeatable()) {
                $this->antiplagiat_report->status_id = $e->getFailStatus();
                $this->antiplagiat_report->file->article->edit_submission_file = true;
                $this->antiplagiat_report->file->article->save();
            } else {
                $this->repeat();
            }
        } else {
            $this->repeat();
        }

        $this->antiplagiat_report->exceptionLogs()->create([
            'exception' => get_class($e),
            'message' =>
                $e->getMessage() . PHP_EOL .
                $e->getFile() . PHP_EOL .
                $e->getLine() . PHP_EOL .
                $e->getCode()
            ,
        ]);

        if (! $e instanceof AntiplagiatFileUploadException || $e->repeatable()) {
            return;
        }

        $this->antiplagiat_report->save();
        $this->antiplagiat_report->broadcastResolved();
    }

}
