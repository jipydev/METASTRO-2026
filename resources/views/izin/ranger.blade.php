<x-app-layout>
    <div class="min-h-screen bg-gray-100 dark:bg-slate-900 py-10 font-poppins">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="mb-4 bg-emerald-50 text-emerald-600 p-4 rounded-xl border border-emerald-200">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                <h2 class="text-2xl font-bold text-primary-600 dark:text-primary-400 mb-6">
                    Validasi Pengajuan Izin (Ranger)
                </h2>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-slate-700 text-sm text-slate-500 dark:text-slate-400">
                                <th class="py-3 px-4">Nama / NIM</th>
                                <th class="py-3 px-4">Jadwal</th>
                                <th class="py-3 px-4">Alasan</th>
                                <th class="py-3 px-4">Keterangan</th>
                                <th class="py-3 px-4">Lampiran</th>
                                <th class="py-3 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @forelse($izinList as $izin)
                                @php
                                    $bukti = json_decode($izin->bukti, true);
                                @endphp
                                <tr class="border-b border-gray-100 dark:border-slate-700/50 hover:bg-gray-50 dark:hover:bg-slate-800/50">
                                    <td class="py-3 px-4">
                                        <div class="font-bold text-slate-800 dark:text-slate-200">{{ $izin->user->name }}</div>
                                        <div class="text-xs text-slate-500">{{ $izin->user->nim }}</div>
                                    </td>
                                    <td class="py-3 px-4 text-slate-700 dark:text-slate-300">
                                        {{ $izin->jadwal->judul ?? '-' }}
                                    </td>
                                    <td class="py-3 px-4 text-slate-700 dark:text-slate-300">
                                        <span class="bg-primary-50 text-primary-600 px-2 py-1 rounded-md font-semibold text-xs">{{ $izin->status }}</span>
                                    </td>
                                    <td class="py-3 px-4 text-slate-700 dark:text-slate-300">
                                        {{ $izin->keterangan ?? '-' }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex flex-col gap-1">
                                            @if(!empty($bukti['surat']))
                                                <a href="{{ asset('storage/' . $bukti['surat']) }}" target="_blank" class="text-primary-500 text-xs hover:underline">Surat PDF</a>
                                            @endif
                                            @if(!empty($bukti['dokumentasi']))
                                                <a href="{{ asset('storage/' . $bukti['dokumentasi']) }}" target="_blank" class="text-primary-500 text-xs hover:underline">Dokumentasi</a>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <div class="flex justify-center gap-2">
                                            <form action="{{ route('izin.ranger.validasi', $izin->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="action" value="acc">
                                                <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition">ACC</button>
                                            </form>
                                            <form action="{{ route('izin.ranger.validasi', $izin->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="action" value="reject">
                                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition">TOLAK</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-8 text-slate-500">Belum ada pengajuan izin yang perlu divalidasi oleh Ranger.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $izinList->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
