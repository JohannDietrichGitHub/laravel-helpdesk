<?php

namespace Tests\Feature;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Category;
use App\Models\Message;
use App\Models\Tag;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DomainModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_ticket_factory_builds_the_relationship_graph_and_casts_domain_values(): void
    {
        $ticket = Ticket::factory()->create();
        $message = Message::factory()->create(['ticket_id' => $ticket->id]);
        $tag = Tag::factory()->create();
        $ticket->tags()->attach($tag);

        $ticket->refresh();

        $this->assertInstanceOf(User::class, $ticket->user);
        $this->assertInstanceOf(Category::class, $ticket->category);
        $this->assertInstanceOf(TicketStatus::class, $ticket->status);
        $this->assertInstanceOf(TicketPriority::class, $ticket->priority);
        $this->assertTrue($ticket->messages->contains($message));
        $this->assertTrue($ticket->tags->contains($tag));
    }

    public function test_resolved_factory_state_sets_a_resolved_timestamp(): void
    {
        $ticket = Ticket::factory()->resolved()->create();

        $this->assertSame(TicketStatus::RESOLVED, $ticket->status);
        $this->assertNotNull($ticket->resolved_at);
        $this->assertTrue($ticket->resolved_at->lessThanOrEqualTo(now()));
    }

    public function test_tickets_are_soft_deleted(): void
    {
        $ticket = Ticket::factory()->create();
        $ticket->delete();

        $this->assertSoftDeleted('tickets', ['id' => $ticket->id]);
        $this->assertNull(Ticket::query()->find($ticket->id));
        $this->assertNotNull(Ticket::withTrashed()->find($ticket->id));
    }

    public function test_domain_tables_have_required_keys_and_filter_indexes(): void
    {
        $this->assertTrue(Schema::hasColumns('tickets', [
            'user_id', 'assigned_to', 'category_id', 'status', 'priority', 'resolved_at', 'deleted_at',
        ]));
        $this->assertTrue(Schema::hasColumns('messages', ['ticket_id', 'user_id', 'body']));
        $this->assertTrue(Schema::hasColumns('attachments', [
            'message_id', 'user_id', 'disk', 'path', 'mime_type', 'size',
        ]));
        $this->assertTrue(Schema::hasIndex('tickets', ['status', 'priority']));
        $this->assertTrue(Schema::hasIndex('tickets', ['assigned_to', 'status']));
        $this->assertTrue(Schema::hasIndex('tickets', ['user_id', 'status']));
    }
}
