<?php

namespace App\Events;

use App\AntiplagiatReport;
use Event;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;


class WSEstimatedTimeChanged extends Event implements ShouldBroadcastNow
{
    use InteractsWithSockets;
    /**
     * @var AntiplagiatReport
     */
    private $antiplagiat_report;

    /**
     * @var
     */
    private $time;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(AntiplagiatReport $antiplagiat_report, $time)
    {
        $this->antiplagiat_report = $antiplagiat_report;
        $this->time = $time;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('antiplagiat-report.' . $this->antiplagiat_report->id);
    }

    public function broadcastWith()
    {
        return ['wait_time' => $this->time];
    }
}
