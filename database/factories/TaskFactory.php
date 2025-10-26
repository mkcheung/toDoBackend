<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'task' => fake()->sentence(4),
            'priority' => fake()->randomElement(['low','medium','high']),
            'due_date' => fake()->optional()->date(),
            'done' => fake()->boolean(20),
        ];
    }

    // Add a helper for explicit user assignment
    public function forUser($userId): static
    {
        return $this->state(fn() => ['user_id' => $userId]);
    }
}
