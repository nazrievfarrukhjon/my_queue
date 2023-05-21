<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TicketTriggered implements ShouldBroadcast
{
    public $tickets;
    private $monitor_group_id;
    private string $eventName = 'ticket_triggered';

    public function __construct($tickets)
    {
        $this->tickets = $tickets;
        $this->monitor_group_id = $this->tickets->first()?->monitor_group_id;
    }

    public function broadcastOn()
    {
        return new Channel('monitor_group_id_' . $this->monitor_group_id);
    }

    public function broadcastAs()
    {
        return $this->eventName;
    }

    public function broadcastWith()
    {
        return [
            'tickets' => $this->tickets,
        ];
    }
}
