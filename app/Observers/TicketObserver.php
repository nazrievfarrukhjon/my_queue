<?php

namespace App\Observers;

use App\Events\TicketTriggered;
use App\Models\Ticket;

class TicketObserver
{
    public function created(Ticket $ticket)
    {
        $this->runEvent($ticket);
    }

    public function updated(Ticket $ticket)
    {
        $this->runEvent($ticket);
    }

    public function deleted(Ticket $ticket)
    {
        $this->runEvent($ticket);
    }

    private function runEvent($ticket)
    {
        $tickets = Ticket::where('monitor_group_id', $ticket->monitor_group_id)->limit(5)->get();
        event(new TicketTriggered($tickets));
    }

}
