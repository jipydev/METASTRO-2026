<?php

namespace Database\Factories;

use App\Models\AnggotaTim;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AnggotaTim>
 */
class AnggotaTimFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'anggota_id' => \App\Models\User::factory(),
            'tim_id' => \App\Models\Tim::factory(),
        ];
    }
}
