@php

$presensi = [

    [
        'judul' => 'RABES 1',
        'tanggal' => 'Selasa, 20 Juli 2026',
        'jam' => '08.00',
        'ruangan' => 'PGSD 4'
    ],

    [
        'judul' => 'RABES 2',
        'tanggal' => 'Kamis, 6 Agustus 2026',
        'jam' => '08.00',
        'ruangan' => 'PGSD 4'
    ],

];

@endphp

<x-app-layout>

    <div class="py-8">

        <div class="max-w-md mx-auto">

            @include('components.back-header', [
                'href' => route('dashboard.presensi'),
                'title' => 'Lihat Presensi'
            ])

            @foreach ($presensi as $item)

                @include('dashboard.presensi.components.attendance-card', [
                    'item' => $item
                ])

            @endforeach

        </div>

    </div>

</x-app-layout>