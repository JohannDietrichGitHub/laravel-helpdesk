<?php

namespace App\Http\Controllers\Api;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TicketController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $user = request()->user();
        $query = Ticket::query()->with(['user:id,name', 'assignee:id,name', 'category:id,name'])
            ->when($user->role === Role::CLIENT, fn ($tickets) => $tickets->where('user_id', $user->id))
            ->when($user->role === Role::AGENT, fn ($tickets) => $tickets->where('assigned_to', $user->id))
            ->when(request()->filled('status'), fn ($tickets) => $tickets->where('status', request()->string('status')->toString()))
            ->when(request()->filled('priority'), fn ($tickets) => $tickets->where('priority', request()->string('priority')->toString()))
            ->when(request()->filled('search'), fn ($tickets) => $tickets->where('title', 'like', '%'.request()->string('search')->toString().'%'))
            ->latest('last_reply_at');

        return TicketResource::collection($query->paginate(15)->withQueryString());
    }

    public function store(StoreTicketRequest $request): TicketResource
    {
        $ticket = Ticket::create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
            'last_reply_at' => now(),
        ]);

        $ticket->messages()->create([
            'user_id' => $request->user()->id,
            'body' => $ticket->description,
        ]);

        return new TicketResource($ticket->load(['user:id,name', 'category:id,name']));
    }

    public function show(Ticket $ticket): TicketResource
    {
        request()->user()->can('view', $ticket) || abort(403);

        return new TicketResource($ticket->load(['user:id,name', 'assignee:id,name', 'category:id,name']));
    }
}
