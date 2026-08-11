<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
            {{ __('Persetujuan Pengajuan Role & Jabatan') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 font-poppins">
        {{-- Session Flash Alerts --}}
        @if(session('success'))
            <div class="mb-4 p-4 bg-emerald-500/10 border border-emerald-500/30 rounded-xl text-emerald-600 dark:text-emerald-400 flex items-center gap-3">
                <span class="icon-[akar-icons--circle-check] text-xl"></span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 bg-rose-500/10 border border-rose-500/30 rounded-xl text-rose-600 dark:text-rose-400 flex items-center gap-3">
                <span class="icon-[akar-icons--circle-x] text-xl"></span>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <div class="bg-white dark:bg-slate-800 shadow-xl rounded-2xl p-6 border border-gray-100 dark:border-slate-700">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                <span class="icon-[mdi--shield-account] text-primary-500 text-2xl"></span>
                Daftar Permintaan Role & Jabatan Baru
            </h3>

            @if($roleRequests->isEmpty())
                <div class="text-center py-12 text-slate-500 dark:text-slate-400">
                    <span class="icon-[mdi--text-box-remove-outline] text-5xl mb-2 block mx-auto opacity-50"></span>
                    Belum ada pengajuan role atau jabatan yang perlu ditinjau.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                        <thead class="bg-slate-50 dark:bg-slate-700/50 text-slate-700 dark:text-slate-200 uppercase text-xs">
                            <tr>
                                <th class="py-3 px-4 rounded-l-lg">User</th>
                                <th class="py-3 px-4">NIM</th>
                                <th class="py-3 px-4">Requested Role</th>
                                <th class="py-3 px-4">Requested Divisi</th>
                                <th class="py-3 px-4">Requested Jabatan</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4 text-center rounded-r-lg">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                            @foreach($roleRequests as $req)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/30 transition">
                                    <td class="py-3.5 px-4 font-semibold text-slate-900 dark:text-white">
                                        {{ $req->user->name ?? '-' }}
                                    </td>
                                    <td class="py-3.5 px-4">{{ $req->user->nim ?? '-' }}</td>
                                    <td class="py-3.5 px-4">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-primary-500/10 text-primary-600 dark:text-primary-400 border border-primary-500/20">
                                            {{ $req->requested_role }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4">{{ $req->requestedDivisi->nama_divisi ?? '-' }}</td>
                                    <td class="py-3.5 px-4">{{ $req->requestedJabatan->nama_jabatan ?? '-' }}</td>
                                    <td class="py-3.5 px-4">
                                        @if($req->status === 'Pending')
                                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">
                                                Pending Admin
                                            </span>
                                        @elseif($req->status === 'Approved')
                                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                                Disetujui
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20">
                                                Ditolak
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        @if($req->status === 'Pending')
                                            <div class="flex items-center justify-center gap-2">
                                                <form action="{{ route('admin.role-request.approve', $req->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menyetujui role & jabatan pengguna ini?')" class="px-3 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg text-xs font-semibold transition flex items-center gap-1 shadow-sm">
                                                        <span class="icon-[akar-icons--check]"></span> Approve
                                                    </button>
                                                </form>

                                                <form action="{{ route('admin.role-request.reject', $req->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menolak permintaan role pengguna ini?')" class="px-3 py-1.5 bg-rose-500 hover:bg-rose-600 text-white rounded-lg text-xs font-semibold transition flex items-center gap-1 shadow-sm">
                                                        <span class="icon-[akar-icons--cross]"></span> Reject
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-xs text-slate-400">Selesai</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $roleRequests->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
