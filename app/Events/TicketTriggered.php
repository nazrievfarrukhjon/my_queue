<?php

namespace App\Events;

use App\Models\MonitorGroups;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TicketTriggered implements ShouldBroadcast
{
    public $tickets;
    private string $monitorName;
    private string $eventName = 'ticket_triggered';

    public function __construct($tickets)
    {
        $this->tickets = $tickets;
        $ticket = $this->tickets->firstOrFail();
        $this->monitorName = MonitorGroups::whereId($ticket->monitor_group_id)->firstOrFail()?->name;

    }

    public function broadcastOn()
    {
        return new Channel('monitor_group_name_' . $this->monitorName);
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
