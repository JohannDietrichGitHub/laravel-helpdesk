<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Ticket $ticket): bool
    {
        return $this->isAdmin($user)
            || $ticket->user_id === $user->id
            || $ticket->assigned_to === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->role !== Role::ADMIN;
    }

    public function reply(User $user, Ticket $ticket): bool
    {
        return $this->view($user, $ticket);
    }

    public function changeStatus(User $user, Ticket $ticket): bool
    {
        return $this->isAdmin($user) || $ticket->assigned_to === $user->id;
    }

    public function assign(User $user, Ticket $ticket): bool
    {
        return $this->isAdmin($user);
    }

    private function isAdmin(User $user): bool
    {
        return $user->role === Role::ADMIN;
    }
}
