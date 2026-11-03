<?php

namespace App\Http\Resources;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Ticket */
class TicketResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        /** @var Ticket $ticket */
        $ticket = $this->resource;

        return [
            'id' => $ticket->id,
            'title' => $ticket->title,
            'description' => $ticket->description,
            'status' => TicketStatus::from($ticket->getRawOriginal('status'))->value,
            'priority' => TicketPriority::from($ticket->getRawOriginal('priority'))->value,
            'resolved_at' => $ticket->resolved_at,
            'last_reply_at' => $ticket->last_reply_at,
            'user' => $this->whenLoaded('user', fn () => [
                'id' => $ticket->user->id,
                'name' => $ticket->user->name,
            ]),
            'assignee' => $this->whenLoaded('assignee', fn () => $ticket->assignee ? [
                'id' => $ticket->assignee->id,
                'name' => $ticket->assignee->name,
            ] : null),
            'category' => $this->whenLoaded('category', fn () => $ticket->category ? [
                'id' => $ticket->category->id,
                'name' => $ticket->category->name,
            ] : null),
            'created_at' => $ticket->created_at,
            'updated_at' => $ticket->updated_at,
        ];
    }
}
