<x-app-layout>

    @php
        $user = [
            'nama' => 'Azmil Ramadhan',
            'jabatan' => 'Staff Chiper',
        ];
    @endphp

    <div class="py-8">

        <div class="max-w-sm mx-auto">

            <div class="bg-sky-50 rounded-[28px] p-7 min-h-[680px]">

                <h2 class="text-3xl font-bold text-cyan-800 mb-8">
                    Presensi RABES
                </h2>

                @include('dashboard.presensi.components.profile-card',[
                    'user'=>$user
                ])

                @include('dashboard.presensi.components.qr-card')

            </div>

        </div>

    </div>

</x-app-layout>