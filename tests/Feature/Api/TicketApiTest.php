<?php

namespace Tests\Feature\Api;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TicketApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_clients_receive_only_their_tickets(): void
    {
        $client = User::factory()->create();
        Ticket::factory()->create(['user_id' => $client->id]);
        Ticket::factory()->create();
        Sanctum::actingAs($client);

        $this->getJson('/api/v1/tickets')
            ->assertOk()
            ->assertJsonPath('meta.total', 1);
    }

    public function test_clients_cannot_view_another_clients_ticket(): void
    {
        $client = User::factory()->create();
        $ticket = Ticket::factory()->create();
        Sanctum::actingAs($client);

        $this->getJson("/api/v1/tickets/{$ticket->id}")->assertForbidden();
    }

    public function test_token_endpoint_issues_a_sanctum_token(): void
    {
        $user = User::factory()->create(['password' => 'password']);

        $this->postJson('/api/v1/auth/tokens', [
            'email' => $user->email,
            'password' => 'password',
            'device_name' => 'test-suite',
        ])->assertOk()->assertJsonStructure(['token']);
    }
}
