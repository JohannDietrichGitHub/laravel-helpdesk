<?php

namespace App\Listeners;

use App\Events\TicketStatusChanged;
use App\Notifications\TicketStatusChangedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyTicketStatusChanged implements ShouldQueue
{
    public function handle(TicketStatusChanged $event): void
    {
        $recipients = collect([$event->ticket->user, $event->ticket->assignee])
            ->filter()
            ->reject(fn ($user): bool => $user->is($event->actor))
            ->unique('id');

        $recipients->each(fn ($user) => $user->notify(new TicketStatusChangedNotification($event)));
    }
}
