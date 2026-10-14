<?php

namespace Tests\Feature\Tickets;

use App\Enums\TicketStatus;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Tests\TestCase;

class TicketAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(PreventRequestForgery::class);
    }

    public function test_clients_cannot_view_another_clients_ticket(): void
    {
        $client = User::factory()->create();
        $ticket = Ticket::factory()->create();

        $this->actingAs($client)
            ->get(route('tickets.show', $ticket))
            ->assertForbidden();
    }

    public function test_assigned_agents_can_view_tickets(): void
    {
        $agent = User::factory()->agent()->create();
        $ticket = Ticket::factory()->create(['assigned_to' => $agent->id]);

        $this->actingAs($agent)
            ->get(route('tickets.show', $ticket))
            ->assertOk();
    }

    public function test_unassigned_agents_cannot_view_tickets(): void
    {
        $agent = User::factory()->agent()->create();
        $ticket = Ticket::factory()->create();

        $this->actingAs($agent)
            ->get(route('tickets.show', $ticket))
            ->assertForbidden();
    }

    public function test_clients_can_create_tickets(): void
    {
        $client = User::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($client)->post(route('tickets.store'), [
            'title' => 'Cannot export report',
            'description' => 'The export completes without a file.',
            'category_id' => $category->id,
            'priority' => 'high',
        ]);

        $ticket = Ticket::query()->where('title', 'Cannot export report')->firstOrFail();

        $response->assertRedirect(route('tickets.show', $ticket));
        $this->assertSame($client->id, $ticket->user_id);
        $this->assertDatabaseHas('messages', [
            'ticket_id' => $ticket->id,
            'user_id' => $client->id,
        ]);
    }

    public function test_only_assigned_agents_can_change_ticket_status(): void
    {
        $assignedAgent = User::factory()->agent()->create();
        $otherAgent = User::factory()->agent()->create();
        $ticket = Ticket::factory()->create(['assigned_to' => $assignedAgent->id]);

        $this->actingAs($otherAgent)
            ->patch(route('tickets.status.update', $ticket), ['status' => TicketStatus::RESOLVED->value])
            ->assertForbidden();

        $this->actingAs($assignedAgent)
            ->patch(route('tickets.status.update', $ticket), ['status' => TicketStatus::RESOLVED->value])
            ->assertRedirect();

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'status' => TicketStatus::RESOLVED->value,
        ]);
    }

    public function test_only_admins_can_assign_tickets(): void
    {
        $admin = User::factory()->admin()->create();
        $agent = User::factory()->agent()->create();
        $ticket = Ticket::factory()->create();

        $this->actingAs($agent)
            ->patch(route('tickets.assignment.update', $ticket), ['assigned_to' => $agent->id])
            ->assertForbidden();

        $this->actingAs($admin)
            ->patch(route('tickets.assignment.update', $ticket), ['assigned_to' => $agent->id])
            ->assertRedirect();

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'assigned_to' => $agent->id,
        ]);
    }
}
