<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class TicketTriggered implements ShouldBroadcastNow
{
    public $tickets;

    public function __construct($tickets)
    {
        $this->tickets = $tickets;
    }

    public function broadcastOn(): array
    {
        return new Channel('channel-name');
    }

    public function broadcastWith()
    {
        return [
            'tickets' => $this->tickets,
        ];
    }
}
