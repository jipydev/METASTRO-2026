<x-app-layout>
    <div x-data="{
        openEditTimeline: false,
        openJadwalAbsen: false,
        selectedTimeline: {
            id: null,
            judul: '',
            tanggal: '',
            jam: '',
            tempat: ''
        },
        selectedAbsen: {
            id: null,
            judul: '',
            status_absen: 'Tutup',
            waktu_buka: '',
            waktu_telat: '',
            waktu_tutup: ''
        }
    }" class="min-h-screen bg-gray-100 dark:bg-slate-900 transition-colors duration-200">

        {{-- ===== MOBILE LAYOUT (< md) ===== --}}
        <div class="md:hidden py-6 px-4">
            <div class="bg-white dark:bg-slate-800 rounded-[28px] p-6 min-h-[calc(100vh-6rem)] shadow-sm border border-gray-100 dark:border-slate-700">

                {{-- Header --}}
                <div class="flex justify-between items-start mb-5">
                    <div>
                        <h1 class="text-[22px] font-bold text-primary-600 dark:text-primary-400 leading-tight">
                            Timeline & Absensi
                        </h1>
                        <a href="{{ route('dashboard') }}"
                           class="mt-1.5 inline-flex items-center gap-0.5 text-gray-500 dark:text-slate-400 hover:text-primary-600 dark:hover:text-primary-400 transition font-medium text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                            </svg>
                            <span>kembali</span>
                        </a>
                    </div>

                    @role('Admin|Sekretaris')
                        <button @click="
                            selectedTimeline = { id: null, judul: '', tanggal: '', jam: '', tempat: '' };
                            openEditTimeline = true;
                        " class="bg-primary-500 hover:bg-primary-600 text-white font-semibold text-xs px-4 py-2 rounded-xl transition shadow-sm flex items-center gap-1 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                            </svg>
                            <span>Tambah</span>
                        </button>
                    @endrole
                </div>

                @if(session('success'))
                    <div class="mb-4 p-3 bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 rounded-xl text-xs font-medium">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Timeline Cards --}}
                <div class="space-y-4">
                    @forelse($timelines as $item)
                        @php
                            $formattedDate = \Carbon\Carbon::parse($item->tanggal)->locale('id')->isoFormat('dddd, D MMMM YYYY');
                            $formattedTime = \Carbon\Carbon::parse($item->jam)->format('H.i');
                            $timeInput = \Carbon\Carbon::parse($item->jam)->format('H:i');
                            $dateInput = \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d');
                            $itemData = [
                                'id' => $item->id,
                                'judul' => $item->judul,
                                'tanggal' => $dateInput,
                                'jam' => $timeInput,
                                'tempat' => $item->tempat
                            ];
                            $absenData = [
                                'id' => $item->id,
                                'judul' => $item->judul,
                                'status_absen' => $item->status_absen ?? 'Tutup',
                                'waktu_buka' => $item->waktu_buka ? substr($item->waktu_buka, 0, 5) : '',
                                'waktu_telat' => $item->waktu_telat ? substr($item->waktu_telat, 0, 5) : '',
                                'waktu_tutup' => $item->waktu_tutup ? substr($item->waktu_tutup, 0, 5) : ''
                            ];
                        @endphp

                        <div class="bg-primary-50/50 dark:bg-slate-700/60 rounded-2xl p-4 relative border border-primary-100/50 dark:border-slate-600 space-y-3">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-primary-600 dark:text-primary-400 font-bold text-[15px] mb-0.5">
                                        {{ $item->judul }}
                                    </h3>
                                    <p class="text-gray-700 dark:text-slate-300 text-[13px]">{{ $formattedDate }}</p>
                                    <p class="text-gray-700 dark:text-slate-300 text-[13px]">pukul {{ $formattedTime }} WIB</p>
                                    <p class="text-gray-700 dark:text-slate-300 text-[13px]">{{ $item->tempat }}</p>
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold border {{ ($item->status_absen ?? 'Tutup') === 'Buka' ? 'bg-emerald-500/10 text-emerald-600 border-emerald-500/30' : (($item->status_absen ?? 'Tutup') === 'Dijadwalkan' ? 'bg-blue-500/10 text-blue-600 border-blue-500/30' : 'bg-rose-500/10 text-rose-600 border-rose-500/30') }}">
                                    Absen: {{ $item->status_absen ?? 'Tutup' }}
                                </span>
                            </div>

                            @role('Admin|Sekretaris')
                                <!-- Sekretaris Control Buttons -->
                                <div class="pt-2 border-t border-primary-100/50 dark:border-slate-600 space-y-2">
                                    <div class="flex gap-2">
                                        <form action="{{ route('absen.toggle', $item->id) }}" method="POST" class="flex-1">
                                            @csrf
                                            @if(($item->status_absen ?? 'Tutup') === 'Buka')
                                                <input type="hidden" name="status_absen" value="Tutup">
                                                <button type="submit" class="w-full bg-rose-500 hover:bg-rose-600 text-white text-xs font-bold py-1.5 px-3 rounded-lg transition cursor-pointer">
                                                    🔒 Tutup Absen
                                                </button>
                                            @else
                                                <input type="hidden" name="status_absen" value="Buka">
                                                <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold py-1.5 px-3 rounded-lg transition cursor-pointer">
                                                    🔓 Buka Absen
                                                </button>
                                            @endif
                                        </form>

                                        <button
                                            data-absen='@json($absenData)'
                                            @click="
                                                selectedAbsen = JSON.parse($el.dataset.absen);
                                                openJadwalAbsen = true;
                                            "
                                            class="bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold py-1.5 px-3 rounded-lg transition flex items-center gap-1 cursor-pointer">
                                            ⏱️ Jadwal
                                        </button>
                                    </div>

                                    <div class="flex gap-2">
                                        <button
                                            data-item='@json($itemData)'
                                            @click.stop="
                                                selectedTimeline = JSON.parse($el.dataset.item);
                                                openEditTimeline = true;
                                            "
                                            class="flex-1 bg-slate-200 dark:bg-slate-600 hover:bg-slate-300 text-slate-800 dark:text-slate-100 text-xs font-semibold py-1.5 rounded-lg transition flex items-center justify-center gap-1 cursor-pointer">
                                            Edit Timeline
                                        </button>
                                        <form action="{{ route('timeline.destroy', $item->id) }}" method="POST"
                                              onsubmit="return confirm('Hapus timeline {{ $item->judul }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="bg-red-500/20 hover:bg-red-500/30 text-red-600 dark:text-red-400 text-xs font-semibold py-1.5 px-3 rounded-lg transition cursor-pointer">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endrole
                        </div>
                    @empty
                        <div class="text-center py-16">
                            <div class="text-5xl mb-3">📅</div>
                            <p class="font-semibold text-sm text-gray-500 dark:text-slate-400">Belum ada timeline.</p>
                            @role('Admin|Sekretaris')
                                <p class="text-xs text-gray-400 dark:text-slate-500 mt-1">Klik tombol <strong>+ Tambah</strong> untuk menambahkan.</p>
                            @endrole
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- ===== DESKTOP LAYOUT (md+) ===== --}}
        <div class="hidden md:block py-10 px-6 lg:px-8">
            <div class="max-w-5xl mx-auto">

                {{-- Header --}}
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h1 class="text-3xl lg:text-4xl font-bold text-primary-600 dark:text-primary-400">
                            Timeline & Kelola Absensi
                        </h1>
                        <a href="{{ route('dashboard') }}"
                           class="mt-2 inline-flex items-center gap-1 text-gray-500 dark:text-slate-400 hover:text-primary-600 dark:hover:text-primary-400 transition font-medium text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                            </svg>
                            <span>kembali</span>
                        </a>
                    </div>

                    @role('Admin|Sekretaris')
                        <button @click="
                            selectedTimeline = { id: null, judul: '', tanggal: '', jam: '', tempat: '' };
                            openEditTimeline = true;
                        " class="bg-primary-500 hover:bg-primary-600 text-white font-semibold text-sm px-6 py-2.5 rounded-xl transition shadow-sm flex items-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                            </svg>
                            <span>Tambah Timeline</span>
                        </button>
                    @endrole
                </div>

                @if(session('success'))
                    <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 rounded-xl text-sm font-medium">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Grid Cards --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @forelse($timelines as $item)
                        @php
                            $formattedDate = \Carbon\Carbon::parse($item->tanggal)->locale('id')->isoFormat('dddd, D MMMM YYYY');
                            $formattedTime = \Carbon\Carbon::parse($item->jam)->format('H.i');
                            $timeInput = \Carbon\Carbon::parse($item->jam)->format('H:i');
                            $dateInput = \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d');
                            $itemData = [
                                'id' => $item->id,
                                'judul' => $item->judul,
                                'tanggal' => $dateInput,
                                'jam' => $timeInput,
                                'tempat' => $item->tempat
                            ];
                            $absenData = [
                                'id' => $item->id,
                                'judul' => $item->judul,
                                'status_absen' => $item->status_absen ?? 'Tutup',
                                'waktu_buka' => $item->waktu_buka ? substr($item->waktu_buka, 0, 5) : '',
                                'waktu_telat' => $item->waktu_telat ? substr($item->waktu_telat, 0, 5) : '',
                                'waktu_tutup' => $item->waktu_tutup ? substr($item->waktu_tutup, 0, 5) : ''
                            ];
                        @endphp

                        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-100 dark:border-slate-700 shadow-sm hover:shadow-md transition-all p-6 relative group overflow-hidden flex flex-col justify-between">
                            {{-- Accent bar --}}
                            <div class="absolute top-0 left-0 w-1.5 h-full bg-primary-500"></div>

                            <div class="pl-3 space-y-3">
                                <div class="flex justify-between items-start">
                                    <h3 class="text-primary-600 dark:text-primary-400 font-bold text-lg">
                                        {{ $item->judul }}
                                    </h3>
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold border {{ ($item->status_absen ?? 'Tutup') === 'Buka' ? 'bg-emerald-500/10 text-emerald-600 border-emerald-500/30' : (($item->status_absen ?? 'Tutup') === 'Dijadwalkan' ? 'bg-blue-500/10 text-blue-600 border-blue-500/30' : 'bg-rose-500/10 text-rose-600 border-rose-500/30') }}">
                                        Absen: {{ $item->status_absen ?? 'Tutup' }}
                                    </span>
                                </div>

                                <div class="space-y-1 text-gray-600 dark:text-slate-300 text-sm">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-primary-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <span>{{ $formattedDate }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-primary-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span>Pukul {{ $formattedTime }} WIB</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-primary-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <span>{{ $item->tempat }}</span>
                                    </div>
                                </div>

                                {{-- Schedule details summary if set --}}
                                @if($item->waktu_buka || $item->waktu_telat || $item->waktu_tutup)
                                    <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-700/50 border border-slate-100 dark:border-slate-700 text-xs space-y-1 text-slate-500 dark:text-slate-400">
                                        @if($item->waktu_buka) <div>🔓 Buka: <span class="font-semibold text-slate-800 dark:text-slate-200">{{ substr($item->waktu_buka, 0, 5) }}</span></div> @endif
                                        @if($item->waktu_telat) <div>⚠️ Batas Tepat Waktu: <span class="font-semibold text-amber-600 dark:text-amber-400">{{ substr($item->waktu_telat, 0, 5) }}</span></div> @endif
                                        @if($item->waktu_tutup) <div>🔒 Tutup: <span class="font-semibold text-rose-600 dark:text-rose-400">{{ substr($item->waktu_tutup, 0, 5) }}</span></div> @endif
                                    </div>
                                @endif
                            </div>

                            @role('Admin|Sekretaris')
                                <div class="pl-3 mt-4 pt-3 border-t border-gray-100 dark:border-slate-700 space-y-2">
                                    <div class="flex gap-2">
                                        <form action="{{ route('absen.toggle', $item->id) }}" method="POST" class="flex-1">
                                            @csrf
                                            @if(($item->status_absen ?? 'Tutup') === 'Buka')
                                                <input type="hidden" name="status_absen" value="Tutup">
                                                <button type="submit" class="w-full bg-rose-500 hover:bg-rose-600 text-white text-xs font-bold py-2 rounded-lg transition shadow-sm cursor-pointer flex items-center justify-center gap-1">
                                                    🔒 Tutup Absen
                                                </button>
                                            @else
                                                <input type="hidden" name="status_absen" value="Buka">
                                                <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold py-2 rounded-lg transition shadow-sm cursor-pointer flex items-center justify-center gap-1">
                                                    🔓 Buka Absen
                                                </button>
                                            @endif
                                        </form>

                                        <button
                                            data-absen='@json($absenData)'
                                            @click="
                                                selectedAbsen = JSON.parse($el.dataset.absen);
                                                openJadwalAbsen = true;
                                            "
                                            class="bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold py-2 px-3 rounded-lg transition shadow-sm flex items-center gap-1 cursor-pointer">
                                            ⏱️ Atur Jadwal
                                        </button>
                                    </div>

                                    <div class="flex gap-2">
                                        <button
                                            data-item='@json($itemData)'
                                            @click.stop="
                                                selectedTimeline = JSON.parse($el.dataset.item);
                                                openEditTimeline = true;
                                            "
                                            class="flex-1 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 text-xs font-semibold py-2 rounded-lg transition flex items-center justify-center gap-1.5 cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                            </svg>
                                            Edit
                                        </button>
                                        <form action="{{ route('timeline.destroy', $item->id) }}" method="POST"
                                              onsubmit="return confirm('Hapus timeline {{ $item->judul }}?')"
                                              class="flex-1">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="w-full bg-red-500/10 hover:bg-red-500 text-red-600 hover:text-white dark:text-red-400 text-xs font-semibold py-2 rounded-lg transition flex items-center justify-center gap-1.5 cursor-pointer">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endrole
                        </div>
                    @empty
                        <div class="col-span-full text-center py-20">
                            <div class="text-6xl mb-4">📅</div>
                            <h3 class="text-xl font-bold text-gray-400 dark:text-slate-500">Belum ada timeline</h3>
                            @role('Admin|Sekretaris')
                                <p class="text-gray-400 dark:text-slate-500 mt-2 text-sm">Klik tombol <strong>+ Tambah Timeline</strong> untuk memulai.</p>
                            @endrole
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <x-modal-edit-timeline />
        <x-modal-jadwal-absen />
    </div>
</x-app-layout>
