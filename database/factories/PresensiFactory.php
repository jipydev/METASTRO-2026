<?php

namespace Database\Factories;

use App\Models\Presensi;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Timeline;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Presensi>
 */
class PresensiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statuses = ['Hadir', 'Izin', 'Sakit', 'Alpha'];

        $timelines = Timeline::all();
        $nims = User::pluck('nim')->toArray();

        if ($timelines->isEmpty() || empty($nims)) {
            return [
                'nim_panitia' => $this->faker->numerify('00000'),
                'timeline_id' => null,
                'waktu_presensi' => Carbon::now(),
                'status' => 'Alpha',
            ];
        }

        $timeline = $this->faker->randomElement($timelines->all());
        $nim = $this->faker->randomElement($nims);
        $status = $this->faker->randomElement($statuses);
        $base = Carbon::parse($timeline->tanggal_mulai ?? now());
        $waktu = (clone $base)->addMinutes($this->faker->numberBetween(-15, 60));

        $attrs = [
            'nim_panitia' => $nim,
            'timeline_id' => $timeline->id,
            'waktu_presensi' => $waktu,
            'status' => $status,
        ];

        if (in_array($status, ['Izin', 'Sakit'])) {
            $attrs['surat_izin'] = 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf';
            $attrs['bukti_foto'] = "https://placehold.co/800x1200/png?text=Foto+" . $this->faker->unique()->numberBetween(1000, 9999);
            $attrs['jenis_izin'] = $status === 'Izin' ? 'Izin Keterangan' : 'Sakit';

        return $attrs;
    }
}
