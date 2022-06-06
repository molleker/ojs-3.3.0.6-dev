<?php

namespace App\Events;

use App\AntiplagiatReport;
use Event;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class WSReportResolvedEvent extends Event implements ShouldBroadcastNow
{
    use InteractsWithSockets;
    /**
     * @var AntiplagiatReport
     */
    public $antiplagiat_report;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(AntiplagiatReport $antiplagiat_report)
    {
        $this->antiplagiat_report = $antiplagiat_report;
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
}
