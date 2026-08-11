<?php

namespace Database\Seeders;

use App\Models\Divisi;
use App\Models\Jabatan;
use Illuminate\Database\Seeder;

class DivisiJabatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $divisis = ['Archivist', 'Scribe', 'Gearmaster', 'Chef', 'Chiper', 'Rescuer', 'Informer', 'Ranger', 'Guider', 'Documenter', 'Guardian', 'Pathfinder', 'Fundkeeper', 'Stakeholder'];
        
        foreach ($divisis as $nama) {
            Divisi::firstOrCreate(['nama_divisi' => $nama]);
        }

        $jabatans = ['Koordinator', 'Staff'];

        foreach ($jabatans as $nama) {
            Jabatan::firstOrCreate(['nama_jabatan' => $nama]);
        }
    }
}
