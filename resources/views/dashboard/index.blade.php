<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-black leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-black">
                    {{ __("You're logged in!") }}
                </div>

                {{-- button to scanner, nanti dirapihin ya^^ --}}
                <a href="{{ route('scan') }}" class="inline-flex items-center px-4 py-2 bg-[#065e75] text-[#EFF8FF] border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-[#054555] focus:bg-[#065e75] active:bg-[#065e75] focus:outline-none focus:ring-2 focus:ring-[#065e75] focus:ring-offset-2 transition ease-in-out duration-150">
                    Buka Scanner </a>
                
                <a href="{{ route('kegiatan.lihat') }}" class="inline-flex items-center px-4 py-2 bg-[#065e75] text-[#EFF8FF] border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-[#054555] focus:bg-[#065e75] active:bg-[#065e75] focus:outline-none focus:ring-2 focus:ring-[#065e75] focus:ring-offset-2 transition ease-in-out duration-150">
                    Lihat </a>
                
                <a href="{{ route('kegiatan.QR') }}" class="inline-flex items-center px-4 py-2 bg-[#065e75] text-[#EFF8FF] border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-[#054555] focus:bg-[#065e75] active:bg-[#065e75] focus:outline-none focus:ring-2 focus:ring-[#065e75] focus:ring-offset-2 transition ease-in-out duration-150">
                    QR </a>
            </div>
        </div>
</x-app-layout>
