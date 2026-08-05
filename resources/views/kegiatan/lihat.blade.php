@php
$presensi = [
    [
        'id' => 1,
        'judul' => 'RABES 1',
        'tanggal' => 'Selasa, 20 Juli 2026',
        'jam' => '08.00',
        'ruangan' => 'PGSD 4',
        'url' => url('/lihat/list') 
    ],
    [
        'id' => 2,
        'judul' => 'RABES 2',
        'tanggal' => 'Kamis, 6 Agustus 2026',
        'jam' => '08.00',
        'ruangan' => 'PGSD 4',
        'url' => url('/lihat/list') 
    ],
];
@endphp

<x-app-layout>
    <div class="py-8 font-poppins min-h-screen bg-gray-100 dark:bg-slate-900 transition-colors duration-200">
        <div class="max-w-md mx-auto px-4">
            <div class="bg-white dark:bg-slate-800 rounded-[28px] p-6 sm:p-7 shadow-sm border border-gray-100 dark:border-slate-700">

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