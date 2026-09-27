<?php

namespace Database\Factories;

use App\Models\Guider;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Guider>
 */
class GuiderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pembimbing_id' => \App\Models\User::factory(),
            'tim_id' => \App\Models\Tim::factory(),
        ];
    }
}
