<?php

namespace App\Http\Controllers;

use App\Article;
use App\ArticleFile;
use App\Journal;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;


class JournalController extends Controller
{
    public function dates(Request $request)
    {
        /**
         * @var $dates Collection
         */
        $dates = Article::select(
            DB::raw('DATE_FORMAT(date_submitted, "%Y") as group_value'), DB::raw('MAX(date_submitted) as date')
        )
            ->whereIn('journal_id', $request->input('journals'))
            ->whereNotNull('date_submitted')
            ->groupBy('group_value')
            ->get()
            ->pluck('date')
            ->map(function ($date) {
                return Carbon::parse($date);
            });
        return [
            'dates' => $dates->map(function (Carbon $date) {
                return (string) $date;
            })
            ,
            'max' => (string) max($dates->toArray()),
            'min' => (string) min($dates->toArray()),
        ];
    }
}
