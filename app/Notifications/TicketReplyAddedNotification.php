<?php

namespace App\Notifications;

use App\Events\TicketReplyAdded;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketReplyAddedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly TicketReplyAdded $event) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New reply on your ticket')
            ->line("{$this->event->actor->name} replied to \"{$this->event->ticket->title}\".")
            ->line($this->event->message->body)
            ->action('View ticket', route('tickets.show', $this->event->ticket));
    }
}
