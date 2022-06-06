<?php


namespace App\Http\Controllers;


use App\Components\JournalReport;
use App\Components\DateRange;
use App\Components\ReportArticlesPresenter;
use App\Journal;
use Carbon\Carbon;
use Illuminate\Http\Request;

class JournalReportController
{
    private function getReport(Request $request)
    {
        $from_date = Carbon::parse($request->input('date_range.from'));
        $to_date = Carbon::parse($request->input('date_range.to'));

        return new JournalReport(new DateRange($from_date, $to_date), $request->input('journals'));
    }
    public function sum(Request $request)
    {
        return $this->getReport($request);
    }

    public function articles(Request $request)
    {
        return new ReportArticlesPresenter($this->getReport($request)->articles());
    }

}