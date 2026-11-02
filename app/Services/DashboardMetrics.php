<?php

namespace App\Services;

use App\Enums\Role;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class DashboardMetrics
{
    /**
     * @return array<string, int|float|array<int, array{name: string, value: int}>>
     */
    public function for(User $user): array
    {
        return Cache::remember(
            $this->key($user->role, $user->role === Role::ADMIN ? null : $user->id),
            now()->addMinutes(5),
            fn (): array => $this->calculate($user),
        );
    }

    public function forgetForTicket(Ticket $ticket): void
    {
        Cache::forget($this->key(Role::ADMIN));
        Cache::forget($this->key(Role::CLIENT, $ticket->user_id));

        if ($ticket->assigned_to !== null) {
            Cache::forget($this->key(Role::AGENT, $ticket->assigned_to));
        }
    }

    public function forgetForUser(User $user): void
    {
        Cache::forget($this->key($user->role, $user->role === Role::ADMIN ? null : $user->id));
        Cache::forget($this->key(Role::ADMIN));
    }

    /**
     * @return array<string, int|float|array<int, array{name: string, value: int}>>
     */
    private function calculate(User $user): array
    {
        $tickets = Ticket::query()
            ->when($user->role === Role::CLIENT, fn ($query) => $query->where('user_id', $user->id))
            ->when($user->role === Role::AGENT, fn ($query) => $query->where('assigned_to', $user->id));

        return [
            'total' => (clone $tickets)->count(),
            'open' => (clone $tickets)->where('status', TicketStatus::OPEN)->count(),
            'in_progress' => (clone $tickets)->where('status', TicketStatus::IN_PROGRESS)->count(),
            'waiting_on_client' => (clone $tickets)->where('status', TicketStatus::WAITING_ON_CLIENT)->count(),
            'resolved' => (clone $tickets)->where('status', TicketStatus::RESOLVED)->count(),
            'urgent' => (clone $tickets)->where('priority', TicketPriority::URGENT)->count(),
            'unassigned' => (clone $tickets)->whereNull('assigned_to')->count(),
            'average_resolution_hours' => (float) ((clone $tickets)
                ->whereNotNull('resolved_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as average_hours')
                ->value('average_hours') ?? 0),
            'status_breakdown' => $this->breakdown($tickets, 'status', TicketStatus::cases()),
            'priority_breakdown' => $this->breakdown($tickets, 'priority', TicketPriority::cases()),
        ];
    }

    /**
     * @param  Builder<Ticket>  $tickets
     * @param  list<TicketStatus|TicketPriority>  $values
     * @return array<int, array{name: string, value: int}>
     */
    private function breakdown(Builder $tickets, string $column, array $values): array
    {
        return collect($values)->map(fn ($value): array => [
            'name' => str($value->value)->replace('_', ' ')->title()->toString(),
            'value' => (clone $tickets)->where($column, $value)->count(),
        ])->values()->all();
    }

    private function key(Role $role, ?int $userId = null): string
    {
        return 'dashboard:metrics:'.$role->value.':'.($userId ?? 'all');
    }
}
