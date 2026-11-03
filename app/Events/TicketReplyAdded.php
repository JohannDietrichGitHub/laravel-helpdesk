<?php

namespace App\Events;

use App\Models\Message;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;

class TicketReplyAdded
{
    use Dispatchable;

    public function __construct(
        public Ticket $ticket,
        public Message $message,
        public User $actor,
    ) {}
}
