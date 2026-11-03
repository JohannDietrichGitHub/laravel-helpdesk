<?php

namespace App\Notifications;

use App\Events\TicketStatusChanged;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketStatusChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly TicketStatusChanged $event) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Ticket status updated')
            ->line("Ticket \"{$this->event->ticket->title}\" changed from {$this->event->from->value} to {$this->event->to->value}.")
            ->action('View ticket', route('tickets.show', $this->event->ticket));
    }
}
