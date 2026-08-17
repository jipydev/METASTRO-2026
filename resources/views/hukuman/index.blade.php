<x-app-layout :$title>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-bold text-xl text-gray-900 dark:text-white leading-tight">
                    {{ __('Hukuman Saya') }}
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Riwayat hukuman yang diterbitkan kepada Anda
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 font-poppins">
        @if (session('success'))
            <div class="mb-5 p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-2xl text-emerald-700 dark:text-emerald-300 text-xs font-semibold">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white dark:bg-slate-800 shadow-sm rounded-3xl p-6 sm:p-7 border border-gray-100 dark:border-slate-700">
            @if ($hukumans->isEmpty())
                <div class="text-center py-12 text-slate-400 dark:text-slate-500">
                    <p class="text-xs font-medium">Anda belum pernah menerima hukuman.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300 divide-y divide-gray-100 dark:divide-slate-700">
                        <thead class="bg-slate-50 dark:bg-slate-700/50 text-gray-500 dark:text-slate-400 uppercase text-[11px] font-semibold tracking-wider">
                            <tr>
                                <th class="py-3 px-4 rounded-l-xl">Tanggal</th>
                                <th class="py-3 px-4">Kategori</th>
                                <th class="py-3 px-4">Diterbitkan Oleh</th>
                                <th class="py-3 px-4">Deadline</th>
                                <th class="py-3 px-4 text-center">Status</th>
                                <th class="py-3 px-4 text-center rounded-r-xl">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                            @foreach ($hukumans as $h)
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-700/30 transition">
                                    <td class="py-3.5 px-4 whitespace-nowrap font-medium text-gray-900 dark:text-white">
                                        {{ $h->created_at?->translatedFormat('d M Y H:i') }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="inline-flex px-2.5 py-1 rounded-lg text-[11px] font-bold {{ $h->kategoriBadgeClasses() }}">
                                            {{ $h->kategoriLabel() }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-semibold text-gray-900 dark:text-white">{{ $h->issuer?->nama }}</div>
                                        <div class="text-[11px] text-slate-400">{{ $h->issuer?->formatted_divisi_jabatan }}</div>
                                    </td>
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        {{ $h->deadline_at?->translatedFormat('d M Y H:i') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="inline-flex px-2.5 py-1 rounded-lg text-[11px] font-bold {{ $h->statusBadgeClasses() }}">
                                            {{ $h->statusLabel() }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <a href="{{ route('hukuman.show', $h) }}"
                                           class="inline-flex items-center px-3 py-1.5 bg-brand-50 hover:bg-brand-100 dark:bg-brand-950/40 dark:hover:bg-brand-950/60 text-brand-600 dark:text-brand-300 font-semibold rounded-lg transition">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $hukumans->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
