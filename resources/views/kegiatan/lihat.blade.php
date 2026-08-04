@php
$presensi = [
    [
        'id' => 1,
        'judul' => 'RABES 1',
        'tanggal' => 'Selasa, 20 Juli 2026',
        'jam' => '08.00',
        'ruangan' => 'PGSD 4',
        // Tambahkan URL dinamis, bisa menggunakan url() atau route()
        'url' => url('/lihat/list/1') 
    ],
    [
        'id' => 2,
        'judul' => 'RABES 2',
        'tanggal' => 'Kamis, 6 Agustus 2026',
        'jam' => '08.00',
        'ruangan' => 'PGSD 4',
        'url' => url('/lihat/list/2') 
    ],
];
@endphp

<x-app-layout>
    <div class="py-8">
        <div class="max-w-md mx-auto">
            <div class="bg-white rounded-[28px] p-7 min-h-[680px]">

            @include('components.back-header', [
                'href' => route('dashboard'),
                'title' => 'Lihat Presensi'
            ])

            @foreach ($presensi as $item)
                @include('components.attendance-card', [
                    'item' => $item
                ])
            @endforeach

            </div>
        </div>
    </div>
</x-app-layout>