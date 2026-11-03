<?php

namespace App\Console\Commands;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use Illuminate\Console\Command;

class CloseResolvedTickets extends Command
{
    protected $signature = 'tickets:close-resolved';

    protected $description = 'Close resolved tickets older than seven days';

    public function handle(): int
    {
        $closed = Ticket::query()
            ->where('status', TicketStatus::RESOLVED)
            ->whereNotNull('resolved_at')
            ->where('resolved_at', '<=', now()->subDays(7))
            ->update(['status' => TicketStatus::CLOSED]);

        $this->info("Closed {$closed} resolved ticket(s).");

        return self::SUCCESS;
    }
}
