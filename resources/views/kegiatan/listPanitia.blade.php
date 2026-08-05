<x-app-layout>
    @php
        $statusFilter = request()->query('status');

        $allPanitia = [
            [
                'nama' => 'HELPME',
                'divisi' => 'Acara',
                'jam_tap' => '07:45',
                'hari' => 'Senin',
                'tanggal' => '20 Juli 2026',
                'status' => 'Hadir',
                'scanned_by' => 'Azmil Ramadhan'
            ],
            [
                'nama' => 'AISYAH',
                'divisi' => 'Humas',
                'jam_tap' => '08:15',
                'hari' => 'Senin',
                'tanggal' => '20 Juli 2026',
                'status' => 'Telat',
                'scanned_by' => 'Admin'
            ],
            [
                'nama' => 'RYANN',
                'divisi' => 'Logistik',
                'jam_tap' => '-',
                'hari' => 'Senin',
                'tanggal' => '20 Juli 2026',
                'status' => 'Tidak Hadir',
                'jenis_izin' => 'Sakit'
            ],
            [
                'nama' => 'JAHDANN',
                'divisi' => 'Konsumsi',
                'jam_tap' => '-',
                'hari' => 'Senin',
                'tanggal' => '20 Juli 2026',
                'status' => 'Alpha'
            ],
            [
                'nama' => 'AZMILL H',
                'divisi' => 'Acara',
                'jam_tap' => '07:50',
                'hari' => 'Senin',
                'tanggal' => '20 Juli 2026',
                'status' => 'Hadir',
                'scanned_by' => 'Azmil Ramadhan'
            ],
        ];

        if ($statusFilter) {
            $panitia = array_filter($allPanitia, function($item) use ($statusFilter) {
                return $item['status'] === $statusFilter;
            });
        } else {
            $panitia = $allPanitia;
        }
    @endphp

    <div class="max-w-5xl mx-auto min-h-screen bg-gray-50 md:bg-white dark:bg-slate-900 md:dark:bg-slate-800 p-4 md:p-6 font-poppins my-4 md:rounded-2xl md:shadow-sm border md:border-gray-100 dark:md:border-slate-700 transition-colors duration-200">
         @include('components.back-header', [
                'href' => url('lihat'),
                'title' => 'Daftar Kehadiran Panitia'
            ])
        
        <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
            
            <div class="flex items-center gap-2 overflow-x-auto pb-2 md:pb-0 hide-scrollbar">
                <span class="text-sm font-semibold text-gray-500 dark:text-slate-400 mr-2 hidden md:flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                    Filter:
                </span>

                <a href="{{ request()->url() }}" 
                   class="px-4 py-1.5 rounded-full text-sm font-semibold whitespace-nowrap transition-all {{ !$statusFilter ? 'bg-primary-500 text-white shadow-md' : 'bg-white md:bg-gray-100 dark:bg-slate-700 dark:md:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-gray-200 dark:hover:bg-slate-600' }}">
                    Semua
                </a>

                @php $filterTabs = ['Hadir', 'Telat', 'Tidak Hadir', 'Alpha']; @endphp
                @foreach($filterTabs as $tab)
                    <a href="{{ request()->fullUrlWithQuery(['status' => $tab]) }}" 
                       class="px-4 py-1.5 rounded-full text-sm font-semibold whitespace-nowrap transition-all {{ $statusFilter === $tab ? 'bg-primary-500 text-white shadow-md' : 'bg-white md:bg-gray-100 dark:bg-slate-700 dark:md:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-gray-200 dark:hover:bg-slate-600' }}">
                        {{ $tab }}
                    </a>
                @endforeach
            </div>
        </div>

        <div class="md:overflow-x-auto md:border md:border-gray-200 dark:md:border-slate-700 md:rounded-xl">
            <table class="w-full text-left border-collapse block md:table md:min-w-[800px]">
                <thead class="bg-gray-50 dark:bg-slate-800/90 hidden md:table-header-group">
                    <tr>
                        <th class="py-3.5 px-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest border-b border-gray-200 dark:border-slate-700">Panitia</th>
                        <th class="py-3.5 px-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest border-b border-gray-200 dark:border-slate-700">Divisi</th>
                        <th class="py-3.5 px-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest border-b border-gray-200 dark:border-slate-700">Jam Tap</th>
                        <th class="py-3.5 px-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest border-b border-gray-200 dark:border-slate-700">Tanggal</th>
                        <th class="py-3.5 px-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest border-b border-gray-200 dark:border-slate-700">Status</th>
                        <th class="py-3.5 px-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest border-b border-gray-200 dark:border-slate-700">Aksi</th>
                    </tr>
                </thead>
                <tbody class="block md:table-row-group">
                    @forelse($panitia as $item)
                        <x-attendance-row :item="$item" />
                    @empty
                        <tr class="block md:table-row bg-white dark:bg-slate-800 rounded-lg p-4 text-center">
                            <td colspan="6" class="py-8 text-center text-gray-500 dark:text-slate-400 font-medium">
                                Tidak ada panitia dengan status "{{ $statusFilter }}"
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>