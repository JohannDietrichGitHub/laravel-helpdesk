<?php

namespace Database\Seeders;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::factory()->admin()->create([
            'name' => config('demo.admin.name'),
            'email' => config('demo.admin.email'),
            'password' => config('demo.admin.password'),
        ]);

        $agents = User::factory(2)->agent()->create();
        $clients = User::factory(5)->create();

        $categories = collect([
            ['name' => 'Account access', 'slug' => 'account-access'],
            ['name' => 'Billing', 'slug' => 'billing'],
            ['name' => 'Technical issue', 'slug' => 'technical-issue'],
            ['name' => 'Feature request', 'slug' => 'feature-request'],
        ])->map(fn (array $attributes): Category => Category::create($attributes));

        $tags = Tag::factory(6)->create();

        $clients->each(function (User $client, int $index) use ($agents, $categories, $tags): void {
            $ticket = Ticket::factory()->create([
                'user_id' => $client->id,
                'assigned_to' => $agents->random()->id,
                'category_id' => $categories->random()->id,
                'status' => $index === 0 ? TicketStatus::RESOLVED->value : TicketStatus::OPEN->value,
                'priority' => $index === 1 ? TicketPriority::URGENT->value : TicketPriority::MEDIUM->value,
            ]);

            $ticket->tags()->attach($tags->random(2)->modelKeys());
            $ticket->messages()->createMany([
                ['user_id' => $client->id, 'body' => 'I need help with this request.'],
                ['user_id' => $ticket->assigned_to, 'body' => 'We are reviewing your request.'],
            ]);
        });

        Ticket::factory(5)->create([
            'user_id' => $clients->random()->id,
            'assigned_to' => $agents->random()->id,
            'category_id' => $categories->random()->id,
        ])->each(function (Ticket $ticket) use ($tags, $admin): void {
            $ticket->tags()->attach($tags->random(2)->modelKeys());
            $ticket->messages()->create([
                'user_id' => $admin->id,
                'body' => 'Internal triage note.',
                'is_internal' => true,
            ]);
        });
    }
}
