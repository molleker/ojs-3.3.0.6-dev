<?php

namespace App\Providers;

use App\AntiplagiatReport;
use App\AntiplagiatReportStatus;
use App\EmailLog;
use App\EventLog;
use App\Jobs\AntiplagiatReportJob;
use App\Listeners\MailLog;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        AntiplagiatReport::created(function (AntiplagiatReport $antiplagiat_report) {
            dispatch(new AntiplagiatReportJob($antiplagiat_report, Auth::user()));
        });

        AntiplagiatReport::updated(function (AntiplagiatReport $antiplagiat_report) {
            $can_retry = app(AntiplagiatReportStatus::class, [
                ['id' => $antiplagiat_report->getOriginal('status_id')]
            ])->canRetry();
            if ($can_retry) {
		if($antiplagiat_report->getOriginal('status_id') == AntiplagiatReportStatus::READY) {
                	dispatch(new AntiplagiatReportJob($antiplagiat_report, Auth::user(), $antiplagiat_report->uploadedFile));
		}
		else {
                	dispatch(new AntiplagiatReportJob($antiplagiat_report, Auth::user()));
		}
            }
        });

        EventLog::creating(function (EventLog $event_log) {
            $event_log->date_logged = Carbon::now();
            $event_log->is_translated = true;
            if (! $event_log->user_id) {
                $event_log->user_id = Auth::user()->user_id;
            }
        });

        EmailLog::creating(function (EmailLog $email_log) {
            $email_log->date_sent = Carbon::now();
        });
    }
}
