<?php

namespace Database\Factories;

use App\Models\Message;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Message> */
class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition(): array
    {
        return [
            'ticket_id' => Ticket::factory(),
            'user_id' => User::factory(),
            'body' => fake()->paragraphs(2, true),
            'is_internal' => false,
        ];
    }

    public function internal(): static
    {
        return $this->state(['is_internal' => true]);
    }
}
