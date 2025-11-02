<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
final class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'email' => fake()->unique()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'status' => fake()->randomElement([
                'active',
                'inactive',
                'prospect',
            ]),
            'industry' => fake()->randomElement([
                'Technology',
                'Healthcare',
                'Finance',
                'Manufacturing',
                'Retail',
                'Education',
                'Consulting',
                'Real Estate',
            ]),
            'contact_person' => fake()->name(),
            'website' => fake()->url(),
        ];
    }
}
