<?php


namespace App\Components;


class NullEditDecision
{
    public $date_decided = null;
    public $decision = null;
    public function isFinally()
    {
        return false;
    }
}