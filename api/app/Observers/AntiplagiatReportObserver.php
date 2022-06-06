<?php


namespace App\Observers;


use App\AntiplagiatReport;

class AntiplagiatReportObserver
{
    public function created(AntiplagiatReport $antiplagiat_report)
    {
        $antiplagiat_report->file->article->edit_submission_file = false;
        $antiplagiat_report->file->article->save();
    }

}