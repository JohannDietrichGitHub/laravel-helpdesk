<?php

namespace Tests\Feature;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_users_can_visit_the_dashboard(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('dashboard'));
        $response->assertOk();
    }

    public function test_clients_receive_only_their_dashboard_ticket_data(): void
    {
        $client = User::factory()->create();
        $otherClient = User::factory()->create();
        Ticket::factory()->create(['user_id' => $client->id]);
        Ticket::factory()->create(['user_id' => $otherClient->id]);

        $this->actingAs($client)->get(route('dashboard'))
            ->assertInertia(fn (Assert $page) => $page
                ->component('dashboard')
                ->where('role', 'client')
                ->where('metrics.total', 1)
                ->count('recentTickets', 1),
            );
    }

    public function test_agents_receive_assigned_dashboard_ticket_data(): void
    {
        $agent = User::factory()->agent()->create();
        $otherAgent = User::factory()->agent()->create();
        Ticket::factory()->create(['assigned_to' => $agent->id]);
        Ticket::factory()->create(['assigned_to' => $otherAgent->id]);

        $this->actingAs($agent)->get(route('dashboard'))
            ->assertInertia(fn (Assert $page) => $page
                ->component('dashboard')
                ->where('role', 'agent')
                ->where('metrics.total', 1)
                ->count('recentTickets', 1),
            );
    }

    public function test_admins_receive_the_full_dashboard_ticket_data(): void
    {
        $admin = User::factory()->admin()->create();
        Ticket::factory()->count(2)->create();

        $this->actingAs($admin)->get(route('dashboard'))
            ->assertInertia(fn (Assert $page) => $page
                ->component('dashboard')
                ->where('role', 'admin')
                ->where('metrics.total', 2)
                ->count('recentTickets', 2),
            );
    }

    public function test_dashboard_metrics_include_breakdowns_and_average_resolution_time(): void
    {
        Cache::flush();
        $admin = User::factory()->admin()->create();
        $createdAt = now()->subHours(6);

        Ticket::factory()->create([
            'status' => TicketStatus::RESOLVED,
            'priority' => TicketPriority::URGENT,
            'created_at' => $createdAt,
            'resolved_at' => $createdAt->copy()->addHours(6),
        ]);
        Ticket::factory()->create([
            'status' => TicketStatus::OPEN,
            'priority' => TicketPriority::LOW,
        ]);

        $this->actingAs($admin)->get(route('dashboard'))
            ->assertInertia(fn (Assert $page) => $page
                ->where('metrics.total', 2)
                ->where('metrics.resolved', 1)
                ->where('metrics.urgent', 1)
                ->where('metrics.average_resolution_hours', 6)
                ->where('metrics.status_breakdown.3.value', 1)
                ->where('metrics.priority_breakdown.3.value', 1),
            );
    }

    public function test_ticket_changes_invalidate_cached_dashboard_metrics(): void
    {
        Cache::flush();
        $admin = User::factory()->admin()->create();
        $ticket = Ticket::factory()->create();

        $this->actingAs($admin)->get(route('dashboard'))
            ->assertInertia(fn (Assert $page) => $page->where('metrics.total', 1));

        $this->actingAs($admin)->patch(route('tickets.status.update', $ticket), [
            'status' => TicketStatus::RESOLVED->value,
        ])->assertRedirect();

        $this->actingAs($admin)->get(route('dashboard'))
            ->assertInertia(fn (Assert $page) => $page->where('metrics.resolved', 1));
    }
}
