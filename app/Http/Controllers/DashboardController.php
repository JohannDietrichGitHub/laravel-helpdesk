<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Enums\TicketStatus;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();
        $tickets = Ticket::query()
            ->with(['user:id,name', 'assignee:id,name', 'category:id,name'])
            ->when($user->role === Role::CLIENT, fn ($query) => $query->where('user_id', $user->id))
            ->when($user->role === Role::AGENT, fn ($query) => $query->where('assigned_to', $user->id));

        return Inertia::render('dashboard', [
            'role' => $user->role->value,
            'stats' => [
                'total' => (clone $tickets)->count(),
                'open' => (clone $tickets)->where('status', TicketStatus::OPEN)->count(),
                'in_progress' => (clone $tickets)->where('status', TicketStatus::IN_PROGRESS)->count(),
                'waiting_on_client' => (clone $tickets)->where('status', TicketStatus::WAITING_ON_CLIENT)->count(),
                'resolved' => (clone $tickets)->where('status', TicketStatus::RESOLVED)->count(),
                'urgent' => (clone $tickets)->where('priority', 'urgent')->count(),
                'unassigned' => (clone $tickets)->whereNull('assigned_to')->count(),
            ],
            'recentTickets' => (clone $tickets)->latest('last_reply_at')->limit(5)->get(),
        ]);
    }
}
