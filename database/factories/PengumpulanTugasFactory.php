<?php

namespace Database\Factories;

use App\Models\PengumpulanTugas;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PengumpulanTugas>
 */
class PengumpulanTugasFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tugas_id' => \App\Models\Tugas::factory(),
            'user_id' => \App\Models\User::factory(),
            'tim_id' => \App\Models\Tim::factory(),
            'file_path' => $this->faker->filePath(),
            'catatan_peserta' => $this->faker->sentence(),
            'catatan_pemeriksa' => $this->faker->optional()->sentence(),
            'pemeriksa_id' => \App\Models\User::factory(),
            'status' => $this->faker->randomElement(['pending', 'reviewed', 'rejected']),
            'tanggal_pengumpulan' => $this->faker->dateTimeThisYear(),
        ];
    }
}
