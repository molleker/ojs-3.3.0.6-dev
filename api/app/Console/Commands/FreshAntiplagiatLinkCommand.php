<?php

namespace App\Console\Commands;

use App\AntiplagiatReport;
use App\AntiplagiatReportStatus;
use App\Components\AntiplagiatClient;
use App\Components\ProgressCommand;
use Illuminate\Console\Command;

class FreshAntiplagiatLinkCommand extends Command
{
    use ProgressCommand;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'antiplagiat:fresh-links';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновляет ссылки на отчет по всем полученным отчетам по антиплагиату';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $client = new AntiplagiatClient();
        $reports = AntiplagiatReport::whereStatusId(AntiplagiatReportStatus::READY)->get();
        $this->init($reports->count());   
        $reports->each(function (AntiplagiatReport $report) use ($client) {
            if (! $report->uploadedFile) {
                $this->bar->advance(1);
                return;
            }
            $api_report = $client->getReport($report->uploadedFile);
            $report->link = $api_report->link();
            $report->linkForEditors = $api_report->linkForEditors();
            $report->save();
            $this->bar->advance(1);
        });
    }
}
