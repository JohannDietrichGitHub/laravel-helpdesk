<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\Ticket;
use App\Services\DashboardMetrics;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request, DashboardMetrics $metrics): Response
    {
        $user = $request->user();
        $tickets = Ticket::query()
            ->with(['user:id,name', 'assignee:id,name', 'category:id,name'])
            ->when($user->role === Role::CLIENT, fn ($query) => $query->where('user_id', $user->id))
            ->when($user->role === Role::AGENT, fn ($query) => $query->where('assigned_to', $user->id));

        return Inertia::render('dashboard', [
            'role' => $user->role->value,
            'metrics' => $metrics->for($user),
            'recentTickets' => (clone $tickets)->latest('last_reply_at')->limit(5)->get(),
        ]);
    }
}
