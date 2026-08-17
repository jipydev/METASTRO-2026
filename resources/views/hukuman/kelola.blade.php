<x-app-layout :$title>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-bold text-xl text-gray-900 dark:text-white leading-tight">
                    {{ $mode === 'pengawas' ? __('Kelola Hukuman Pengawas') : __('Kelola Hukuman') }}
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    @if ($mode === 'pengawas')
                        Hukuman antar pengawas — target hanya jabatan pengawas
                    @elseif ($isAdminIssuer ?? false)
                        Admin dapat menghukum seluruh panitia, termasuk pengawas
                    @else
                        Hukuman panitia & admin — pengawas tidak termasuk target
                    @endif
                </p>
            </div>

            <a href="{{ route('hukuman.create', $mode) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-xl text-xs shadow-sm transition self-start sm:self-auto">
                + Berikan Hukuman
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 font-poppins">
        @if (session('success'))
            <div class="mb-5 p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-2xl text-emerald-700 dark:text-emerald-300 text-xs font-semibold">
                {{ session('success') }}
            </div>
        @endif

        @if ($mode === 'pengawas' && auth()->user()->canIssueHukumanRanger())
            <div class="mb-5 flex flex-wrap gap-2">
                <a href="{{ route('hukuman.kelola', 'ranger') }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600 transition">
                    Mode Ranger
                </a>
                <span class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-brand-100 dark:bg-brand-950/50 text-brand-700 dark:text-brand-300">
                    Mode Pengawas
                </span>
            </div>
        @elseif ($mode === 'ranger' && auth()->user()->canIssueHukumanPengawas())
            <div class="mb-5 flex flex-wrap gap-2">
                <span class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-brand-100 dark:bg-brand-950/50 text-brand-700 dark:text-brand-300">
                    Mode Ranger
                </span>
                <a href="{{ route('hukuman.kelola', 'pengawas') }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600 transition">
                    Mode Pengawas
                </a>
            </div>
        @endif

        <div class="bg-white dark:bg-slate-800 shadow-sm rounded-3xl p-6 sm:p-7 border border-gray-100 dark:border-slate-700">
            @if ($hukumans->isEmpty())
                <div class="text-center py-12 text-slate-400 dark:text-slate-500">
                    <p class="text-xs font-medium">Belum ada hukuman yang diterbitkan.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300 divide-y divide-gray-100 dark:divide-slate-700">
                        <thead class="bg-slate-50 dark:bg-slate-700/50 text-gray-500 dark:text-slate-400 uppercase text-[11px] font-semibold tracking-wider">
                            <tr>
                                <th class="py-3 px-4 rounded-l-xl">Tanggal</th>
                                <th class="py-3 px-4">Target</th>
                                <th class="py-3 px-4">Kategori</th>
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
                                        <div class="font-semibold text-gray-900 dark:text-white">{{ $h->user?->nama }}</div>
                                        <div class="text-[11px] text-slate-400">{{ $h->user?->formatted_divisi_jabatan }}</div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="inline-flex px-2.5 py-1 rounded-lg text-[11px] font-bold {{ $h->kategoriBadgeClasses() }}">
                                            {{ $h->kategoriLabel() }}
                                        </span>
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
