<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class TicketTriggered implements ShouldBroadcastNow
{
    public $tickets;
    private $monitor_group_id;

    public function __construct($tickets)
    {
        $this->tickets = $tickets;
        $this->monitor_group_id = $this->tickets->first()?->monitor_group_id;
    }

    public function broadcastOn()
    {
        return new Channel('monitor_group_id_' . $this->monitor_group_id);
    }

    public function broadcastWith()
    {
        return [
            'tickets' => $this->tickets,
        ];
    }
}
