<?php

namespace Database\Factories;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Ticket> */
class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'assigned_to' => null,
            'category_id' => Category::factory(),
            'title' => fake()->sentence(6),
            'description' => fake()->paragraphs(2, true),
            'status' => TicketStatus::OPEN->value,
            'priority' => fake()->randomElement(TicketPriority::cases())->value,
            'resolved_at' => null,
            'last_reply_at' => now(),
        ];
    }

    public function resolved(): static
    {
        return $this->state([
            'status' => TicketStatus::RESOLVED->value,
            'resolved_at' => now()->subDays(fake()->numberBetween(0, 6)),
        ]);
    }
}
