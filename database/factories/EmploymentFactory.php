<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\EmploymentStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employment>
 */
final class EmploymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'client_id' => \App\Models\Client::factory(),
            'position' => $this->faker->jobTitle(),
            'hire_date' => $this->faker->date(),
            'status' => collect(EmploymentStatus::cases())->random()->value,
            'salary' => $this->faker->numberBetween(30000, 150000),
            'work_location' => $this->faker->city(),
            'effective_date' => $this->faker->date(),
            'end_date' => $this->faker->optional(0.3)->date(),
        ];
    }
}
