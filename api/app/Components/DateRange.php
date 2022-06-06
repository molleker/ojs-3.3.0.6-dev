<?php


namespace App\Components;


use Carbon\Carbon;

class DateRange
{
    private $from;
    private $to;

    public function __construct(Carbon $from, Carbon $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    public function queryRange()
    {
        return [
            $this->from,
            $this->to
        ];
    }
}