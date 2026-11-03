<?php

namespace Tests\Feature;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Events\CallQueuedListener;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class AsyncWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_status_changes_queue_notifications_for_ticket_participants(): void
    {
        Queue::fake();
        $client = User::factory()->create();
        $agent = User::factory()->agent()->create();
        $ticket = Ticket::factory()->create(['user_id' => $client->id, 'assigned_to' => $agent->id]);

        $this->actingAs($agent)->patch(route('tickets.status.update', $ticket), [
            'status' => TicketStatus::RESOLVED->value,
        ])->assertRedirect();

        Queue::assertPushed(CallQueuedListener::class);
    }

    public function test_replies_notify_the_other_ticket_participant(): void
    {
        Queue::fake();
        $client = User::factory()->create();
        $agent = User::factory()->agent()->create();
        $ticket = Ticket::factory()->create(['user_id' => $client->id, 'assigned_to' => $agent->id]);

        $this->actingAs($agent)->post(route('tickets.messages.store', $ticket), [
            'body' => 'The fix is deployed.',
        ])->assertRedirect();

        Queue::assertPushed(CallQueuedListener::class);
    }

    public function test_scheduled_command_closes_only_resolved_tickets_older_than_seven_days(): void
    {
        $old = Ticket::factory()->resolved()->create(['resolved_at' => now()->subDays(8)]);
        $recent = Ticket::factory()->resolved()->create(['resolved_at' => now()->subDays(2)]);
        $open = Ticket::factory()->create();

        $this->artisan('tickets:close-resolved')->assertSuccessful();

        $this->assertDatabaseHas('tickets', ['id' => $old->id, 'status' => TicketStatus::CLOSED->value]);
        $this->assertDatabaseHas('tickets', ['id' => $recent->id, 'status' => TicketStatus::RESOLVED->value]);
        $this->assertDatabaseHas('tickets', ['id' => $open->id, 'status' => TicketStatus::OPEN->value]);
    }
}
