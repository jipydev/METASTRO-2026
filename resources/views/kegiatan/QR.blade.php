<x-app-layout>

    @php
        $user = [
            'nama' => 'Azmil Ramadhan',
            'jabatan' => 'Staff Chiper',
        ];
    @endphp

    <div class="py-8">

        <div class="max-w-sm mx-auto">

            <div class="bg-sky-50 rounded-[28px] p-7 min-h-[680px] change color to white">

                @include('components.back-header', [
                'href' => route('dashboard'),
                'title' => 'Lihat Presensi'
            ])

                @include('components.profile-card',[
                    'user'=>$user
                ])

                @include('components.qr-card')

            </div>

        </div>

    </div>

</x-app-layout>