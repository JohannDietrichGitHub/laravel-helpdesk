<?php

namespace Tests\Feature\Tickets;

use App\Enums\TicketStatus;
use App\Models\Attachment;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

    public function test_admins_can_view_any_ticket(): void
    {
        $admin = User::factory()->admin()->create();
        $ticket = Ticket::factory()->create();

        $this->actingAs($admin)
            ->get(route('tickets.show', $ticket))
            ->assertOk();
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

    public function test_admins_cannot_create_tickets(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('tickets.create'))
            ->assertForbidden();

        $this->actingAs($admin)
            ->post(route('tickets.store'), [
                'title' => 'Admin ticket',
                'description' => 'This request should be rejected.',
            ])
            ->assertForbidden();
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

    public function test_clients_cannot_change_ticket_status(): void
    {
        $client = User::factory()->create();
        $ticket = Ticket::factory()->create(['user_id' => $client->id]);

        $this->actingAs($client)
            ->patch(route('tickets.status.update', $ticket), ['status' => TicketStatus::RESOLVED->value])
            ->assertForbidden();
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

    public function test_clients_cannot_assign_tickets(): void
    {
        $client = User::factory()->create();
        $agent = User::factory()->agent()->create();
        $ticket = Ticket::factory()->create(['user_id' => $client->id]);

        $this->actingAs($client)
            ->patch(route('tickets.assignment.update', $ticket), ['assigned_to' => $agent->id])
            ->assertForbidden();
    }

    public function test_ticket_replies_can_store_private_attachments(): void
    {
        Storage::fake('local');
        $client = User::factory()->create();
        $ticket = Ticket::factory()->create(['user_id' => $client->id]);

        $this->actingAs($client)->post(route('tickets.messages.store', $ticket), [
            'body' => 'Here is the requested screenshot.',
            'attachments' => [UploadedFile::fake()->create('screenshot.txt', 10, 'text/plain')],
        ])->assertRedirect();

        $attachment = Attachment::query()->firstOrFail();
        $this->assertSame('local', $attachment->disk);
        Storage::disk('local')->assertExists($attachment->path);
    }

    public function test_only_ticket_participants_can_download_attachments(): void
    {
        Storage::fake('local');
        $owner = User::factory()->create();
        $otherClient = User::factory()->create();
        $ticket = Ticket::factory()->create(['user_id' => $owner->id]);
        $message = $ticket->messages()->create(['user_id' => $owner->id, 'body' => 'Attachment']);
        $path = UploadedFile::fake()->create('manual.pdf', 10, 'application/pdf')
            ->store('attachments', 'local');
        $attachment = $message->attachments()->create([
            'user_id' => $owner->id,
            'disk' => 'local',
            'path' => $path,
            'original_name' => 'manual.pdf',
            'mime_type' => 'application/pdf',
            'size' => 10240,
        ]);

        $this->actingAs($otherClient)
            ->get(route('attachments.download', $attachment))
            ->assertForbidden();

        $this->actingAs($owner)
            ->get(route('attachments.download', $attachment))
            ->assertOk()
            ->assertHeader('Content-Disposition');
    }
}
