<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page()
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
        \App\Models\Ticket::factory()->create(['user_id' => $client->id]);
        \App\Models\Ticket::factory()->create(['user_id' => $otherClient->id]);

        $this->actingAs($client)->get(route('dashboard'))
            ->assertInertia(fn (Assert $page) => $page
                ->component('dashboard')
                ->where('role', 'client')
                ->where('stats.total', 1)
                ->count('recentTickets', 1),
            );
    }

    public function test_agents_receive_assigned_dashboard_ticket_data(): void
    {
        $agent = User::factory()->agent()->create();
        $otherAgent = User::factory()->agent()->create();
        \App\Models\Ticket::factory()->create(['assigned_to' => $agent->id]);
        \App\Models\Ticket::factory()->create(['assigned_to' => $otherAgent->id]);

        $this->actingAs($agent)->get(route('dashboard'))
            ->assertInertia(fn (Assert $page) => $page
                ->component('dashboard')
                ->where('role', 'agent')
                ->where('stats.total', 1)
                ->count('recentTickets', 1),
            );
    }

    public function test_admins_receive_the_full_dashboard_ticket_data(): void
    {
        $admin = User::factory()->admin()->create();
        \App\Models\Ticket::factory()->count(2)->create();

        $this->actingAs($admin)->get(route('dashboard'))
            ->assertInertia(fn (Assert $page) => $page
                ->component('dashboard')
                ->where('role', 'admin')
                ->where('stats.total', 2)
                ->count('recentTickets', 2),
            );
    }
}
