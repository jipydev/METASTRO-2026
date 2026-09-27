<?php

namespace Database\Factories;

use App\Models\Tugas;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tugas>
 */
class TugasFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'judul' => fake()->sentence(),
            'deskripsi' => fake()->paragraph(),
            'jenis' => fake()->randomElement(['angkatan', 'tim', 'individu']),
            'tenggat_waktu' => fake()->dateTimeBetween('now', '+1 month'),
            'pembuat_id' => \App\Models\User::factory(),
        ];
    }
}
