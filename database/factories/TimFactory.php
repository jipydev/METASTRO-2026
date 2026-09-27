<?php

namespace Database\Factories;

use App\Models\Tim;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tim>
 */
class TimFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => $this->faker->company(),
            'slug' => $this->faker->slug(),
        ];
    }
}
