<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Slides>
 */
class SlidesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'slide1' => fake()->imageUrl(),
            'slide2' => fake()->imageUrl(),
            'slide3' => fake()->imageUrl(),
            'slide4' => fake()->imageUrl(),
            'slide5' => fake()->imageUrl(),
            'slide6' => fake()->imageUrl(),
        ];
    }
}
