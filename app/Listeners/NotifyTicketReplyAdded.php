<?php

namespace App\Listeners;

use App\Events\TicketReplyAdded;
use App\Notifications\TicketReplyAddedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyTicketReplyAdded implements ShouldQueue
{
    public function handle(TicketReplyAdded $event): void
    {
        $recipients = collect([$event->ticket->user, $event->ticket->assignee])
            ->filter()
            ->reject(fn ($user): bool => $user->is($event->actor))
            ->unique('id');

        $recipients->each(fn ($user) => $user->notify(new TicketReplyAddedNotification($event)));
    }
}
