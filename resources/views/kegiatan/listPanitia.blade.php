<x-app-layout>
    @php
        $statusFilter = request()->query('status');

        // 1. Definisikan Data Dummy
        $allPanitia = [
            [
                'nama' => 'Helmy',
                'divisi' => 'Acara',
                'jam_tap' => '07:45',
                'tanggal' => '20 Juli 2026',
                'status' => 'Hadir'
            ],
            [
                'nama' => 'Ersa',
                'divisi' => 'Humas',
                'jam_tap' => '08:15',
                'tanggal' => '20 Juli 2026',
                'status' => 'Telat'
            ],
            [
                'nama' => 'ryan',
                'divisi' => 'Logistik',
                'jam_tap' => '-',
                'tanggal' => '20 Juli 2026',
                'status' => 'Tidak Hadir'
            ],
            [
                'nama' => 'Jahdan',
                'divisi' => 'Chipper',
                'jam_tap' => '-',
                'tanggal' => '20 Juli 2026',
                'status' => 'Alpha'
            ],
            [
                'nama' => 'azmil',
                'divisi' => 'Acara',
                'jam_tap' => '07:50',
                'tanggal' => '20 Juli 2026',
                'status' => 'Hadir'
            ],
            [
                'nama' => 'aisyah',
                'divisi' => 'PDD',
                'jam_tap' => '07:58',
                'tanggal' => '20 Juli 2026',
                'status' => 'Hadir'
            ],
        ];

        // 2. Logika Filter
        // Jika ada filter status, saring array-nya. Jika tidak, tampilkan semua.
        if ($statusFilter) {
            $panitia = array_filter($allPanitia, function($item) use ($statusFilter) {
                return $item['status'] === $statusFilter;
            });
        } else {
            $panitia = $allPanitia;
        }
    @endphp

    <div class="max-w-5xl mx-auto min-h-screen bg-white p-6 font-sans mt-4 rounded-xl shadow-sm">
        
        <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h1 class="text-xl font-bold text-gray-800">Daftar Kehadiran Panitia</h1>
            
            <div class="flex items-center gap-2 overflow-x-auto pb-2 md:pb-0">
                <span class="text-sm font-semibold text-gray-500 mr-2 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                    Filter:
                </span>

                <a href="{{ request()->url() }}" 
                   class="px-4 py-1.5 rounded-full text-sm font-semibold whitespace-nowrap transition-colors {{ !$statusFilter ? 'bg-[#0c5970] text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Semua
                </a>

                @php $filterTabs = ['Hadir', 'Telat', 'Tidak Hadir', 'Alpha']; @endphp
                @foreach($filterTabs as $tab)
                    <a href="{{ request()->fullUrlWithQuery(['status' => $tab]) }}" 
                       class="px-4 py-1.5 rounded-full text-sm font-semibold whitespace-nowrap transition-colors {{ $statusFilter === $tab ? 'bg-[#0c5970] text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        {{ $tab }}
                    </a>
                @endforeach
            </div>
        </div>

        <div class="overflow-x-auto border border-gray-200 rounded-lg">
            <table class="w-full text-left border-collapse min-w-[800px]">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-widest border-b border-gray-200">Panitia</th>
                        <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-widest border-b border-gray-200">Divisi</th>
                        <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-widest border-b border-gray-200">Jam Tap</th>
                        <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-widest border-b border-gray-200">Tanggal</th>
                        <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-widest border-b border-gray-200">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($panitia as $item)
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                            <td class="py-3 px-4 flex items-center gap-4">
                                @php $inisial = substr($item['nama'], 0, 1); @endphp
                                <div class="w-10 h-10 rounded-full bg-[#e6f0ff] text-blue-600 flex items-center justify-center font-semibold text-lg shrink-0">
                                    {{ $inisial }}
                                </div>
                                <span class="font-semibold text-gray-900 text-[15px]">{{ $item['nama'] }}</span>
                            </td>
                            <td class="py-3 px-4 text-slate-500 text-[15px]">{{ $item['divisi'] }}</td>
                            <td class="py-3 px-4 text-gray-900 font-medium text-[15px]">{{ $item['jam_tap'] }}</td>
                            <td class="py-3 px-4 text-slate-500 text-[15px]">{{ $item['tanggal'] }}</td>
                            <td class="py-3 px-4">
                                @php
                                    $statusStyle = match($item['status']) {
                                        'Hadir' => ['bg' => 'bg-[#e2fae8]', 'text' => 'text-[#0e702c]', 'dot' => 'text-[#0ea53b]'],
                                        'Telat' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-700', 'dot' => 'text-orange-500'],
                                        'Tidak Hadir', 'Alpha' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'dot' => 'text-red-500'],
                                        default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'dot' => 'text-gray-500'],
                                    };
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[13px] font-semibold {{ $statusStyle['bg'] }} {{ $statusStyle['text'] }}">
                                    <span class="{{ $statusStyle['dot'] }} text-lg leading-none">&bull;</span> 
                                    {{ $item['status'] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-500 font-medium">
                                Tidak ada panitia dengan status "{{ $statusFilter }}"
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>