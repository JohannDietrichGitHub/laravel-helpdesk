<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Enums\TicketStatus;
use App\Http\Requests\AssignTicketRequest;
use App\Http\Requests\StoreMessageRequest;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketStatusRequest;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class TicketController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        $query = Ticket::query()
            ->with(['user:id,name,email', 'assignee:id,name', 'category:id,name'])
            ->when(
                $user->role === Role::CLIENT,
                fn ($tickets) => $tickets->where('user_id', $user->id),
            )
            ->when(
                $user->role === Role::AGENT,
                fn ($tickets) => $tickets->where('assigned_to', $user->id),
            )
            ->when($request->filled('status'), fn ($tickets) => $tickets->where('status', $request->string('status')->toString()))
            ->when($request->filled('priority'), fn ($tickets) => $tickets->where('priority', $request->string('priority')->toString()))
            ->when($request->filled('search'), function ($tickets) use ($request): void {
                $search = $request->string('search')->toString();
                $tickets->where(fn ($query) => $query
                    ->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%"));
            })
            ->latest('last_reply_at');

        return Inertia::render('tickets/index', [
            'tickets' => $query->paginate(15)->withQueryString(),
            'filters' => [
                'search' => $request->string('search')->toString(),
                'status' => $request->string('status')->toString(),
                'priority' => $request->string('priority')->toString(),
            ],
            'categories' => Category::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('tickets/create', [
            'categories' => Category::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(StoreTicketRequest $request): RedirectResponse
    {
        $ticket = DB::transaction(function () use ($request): Ticket {
            $data = $request->validated();
            $ticket = Ticket::create([
                ...$data,
                'user_id' => $request->user()->id,
                'last_reply_at' => now(),
            ]);

            $ticket->messages()->create([
                'user_id' => $request->user()->id,
                'body' => $data['description'],
            ]);

            return $ticket;
        });

        return to_route('tickets.show', $ticket)->with('success', 'Ticket created.');
    }

    public function show(Ticket $ticket): Response
    {
        request()->user()->can('view', $ticket) || abort(403);

        return Inertia::render('tickets/show', [
            'ticket' => $ticket->load([
                'user:id,name,email',
                'assignee:id,name',
                'category:id,name',
                'tags:id,name,slug',
                'messages.user:id,name,role',
                'messages.attachments',
            ]),
            'agents' => request()->user()->role === Role::ADMIN
                ? User::query()->where('role', Role::AGENT->value)->orderBy('name')->get(['id', 'name'])
                : [],
        ]);
    }

    public function storeMessage(StoreMessageRequest $request, Ticket $ticket): RedirectResponse
    {
        DB::transaction(function () use ($request, $ticket): void {
            $ticket->messages()->create([
                ...$request->validated(),
                'user_id' => $request->user()->id,
            ]);
            $ticket->update(['last_reply_at' => now()]);
        });

        return back()->with('success', 'Reply added.');
    }

    public function updateStatus(UpdateTicketStatusRequest $request, Ticket $ticket): RedirectResponse
    {
        $status = TicketStatus::from($request->string('status')->toString());
        $ticket->update([
            'status' => $status,
            'resolved_at' => $status === TicketStatus::RESOLVED ? now() : null,
        ]);

        return back()->with('success', 'Ticket status updated.');
    }

    public function assign(AssignTicketRequest $request, Ticket $ticket): RedirectResponse
    {
        $ticket->update(['assigned_to' => $request->integer('assigned_to')]);

        return back()->with('success', 'Ticket assigned.');
    }
}
