<?php

namespace App\Jobs;

use App\AntiplagiatReportService;
use App\AntiplagiatReportStatus;
use App\AntiplagiatUploadedFile;
use App\Components\AntiplagiatApiReport;
use App\Components\AntiplagiatClient;
use App\Events\WSEstimatedTimeChanged;
use App\Exceptions\AntiplagiatReportStatusFailedException;
use App\Exceptions\AntiplagiatRepeatException;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AntiplagiatUploadedFileJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var \App\AntiplagiatUploadedFile
     */
    private $antiplagiat_uploaded_file;
    /**
     * @var User
     */
    private $auth_user;

    /**
     * @var AntiplagiatClient
     */
    private $antiplagiat_client;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(AntiplagiatUploadedFile $antiplagiat_uploaded_file, User $auth_user)
    {
        $this->antiplagiat_uploaded_file = $antiplagiat_uploaded_file;
        $this->auth_user = $auth_user;
    }

    private function repeat()
    {
        dispatch((new AntiplagiatUploadedFileJob($this->antiplagiat_uploaded_file, $this->auth_user))->delay(10));
    }

    private function getReport()
    {   
        $this->antiplagiat_client = new AntiplagiatClient();
        $status = $this->antiplagiat_client->getStatus($this->antiplagiat_uploaded_file);
        if ($status->inProgress()) {
            broadcast(new WSEstimatedTimeChanged($this->antiplagiat_uploaded_file->antiplagiatReport, $status->waitTime()));
            throw new AntiplagiatRepeatException('status in progress');
        }

        if ($status->isFailed()) {
            throw new AntiplagiatReportStatusFailedException('status failed id: ' . $this->antiplagiat_uploaded_file->id);
        }

        return $this->antiplagiat_client->getReport($this->antiplagiat_uploaded_file);
    }

    private function saveReport(AntiplagiatApiReport $report)
    {
        $this->antiplagiat_uploaded_file->antiplagiatReport->text = $report->text();
        $this->antiplagiat_uploaded_file->antiplagiatReport->link = $report->link();
        $this->antiplagiat_uploaded_file->antiplagiatReport->linkForEditors = $report->linkForEditors();
        $this->antiplagiat_uploaded_file->antiplagiatReport->score = $report->score();
        $this->antiplagiat_uploaded_file->antiplagiatReport->status_id = AntiplagiatReportStatus::READY;

	$this->antiplagiat_uploaded_file->antiplagiatReport->services()->delete();

        foreach ($report->services() as $service) {

            /**
             * @var $db_service AntiplagiatReportService
             */
            $db_service = $this->antiplagiat_uploaded_file->antiplagiatReport->services()->create([
                'name' => $service->name(),
                'score_white' => $service->whiteScore(),
                'score_black' => $service->blackScore(),
            ]);

	    $db_service->sources()->delete();
            foreach ($service->sources() as $source) {
		$source_name = $this->clear_emoji($source->name());
                $db_service->sources()->create([
                    'source_hash' => $source->hash(),
                    'score_by_report' => $source->scoreByReport(),
                    'score_by_source' => $source->scoreBySource(),
                    'author' => $source->author(),
                    'url' => $source->url(),
                    'name' => strlen($source_name) < 1000 ? $source_name : (substr($source_name, 0, 100) . '...'),
                ]);
            }
   	    $this->antiplagiat_uploaded_file->antiplagiatReport->blocks()->delete();
            foreach ($report->blocks() as $block) {
                $this->antiplagiat_uploaded_file->antiplagiatReport->blocks()->create([
                    'offset' => $block->offset(),
                    'length' => $block->length(),
                ]);
            }

        }
        $this->antiplagiat_uploaded_file->antiplagiatReport->save();
        $this->antiplagiat_uploaded_file->antiplagiatReport->fresh()->broadcastResolved();
        $this->antiplagiat_uploaded_file->antiplagiatReport->fresh()->file->article->addEvent('Получен отчет на антиплагиате', $this->auth_user);

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        try {

            $report = $this->getReport();

        } catch (\Exception $e) {

            if ($e instanceof AntiplagiatReportStatusFailedException) {

                $this->failed($e);
                return;
            }
            $this->repeat();
            return;
        }


        try {

            $this->saveReport($report);

        } catch (\Exception $e) {

            $this->failed($e);
        }

    }

    public function failed(\Exception $e)
    {
        $this->antiplagiat_uploaded_file->antiplagiatReport->status_id = AntiplagiatReportStatus::UNKNOWN_FAILED;
        $this->antiplagiat_uploaded_file->antiplagiatReport->exceptionLogs()->create([
            'exception' => get_class($e),
            'message' => $e->getMessage() . PHP_EOL . $e->getFile() . PHP_EOL . $e->getLine(),
        ]);
        $this->antiplagiat_uploaded_file->antiplagiatReport->save();
        $this->antiplagiat_uploaded_file->antiplagiatReport->broadcastResolved();
    }
    public function clear_emoji($content) {
	    $regex = '/(
			 \x23\xE2\x83\xA3 # Digits
			 [\x30-\x39]\xE2\x83\xA3
			 | \xF0\x9F[\x85-\x88][\xA6-\xBF] # Enclosed characters
			 | \xF0\x9F[\x8C-\x97][\x80-\xBF] # Misc
			 | \xF0\x9F\x98[\x80-\xBF] # Smilies
			 | \xF0\x9F\x99[\x80-\x8F]
			 | \xF0\x9F\x9A[\x80-\xBF] # Transport and map symbols
			 )/x';
 
	    $matches = array();
	    if ( preg_match_all( $regex, $content, $matches ) ) {
	        if ( ! empty( $matches[1] ) ) {
		    foreach ( $matches[1] as $emoji ) {
                        $content = str_replace( $emoji, '', $content );
		    }	
		}
	    }
 	return $content;
    }
}
