<?php

namespace App\Enums;

enum TicketStatus: string
{
    case OPEN = 'open';
    case IN_PROGRESS = 'in_progress';
    case WAITING_ON_CLIENT = 'waiting_on_client';
    case RESOLVED = 'resolved';
    case CLOSED = 'closed';
}
