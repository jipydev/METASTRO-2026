<x-app-layout>
    <div class="py-8 font-poppins min-h-screen bg-gray-100 dark:bg-slate-900 transition-colors duration-200">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-primary-600 dark:text-primary-400">Tambah User Baru</h1>
                    <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Registrasi pengguna/panitia baru oleh Admin</p>
                </div>
                <a href="{{ route('admin.manage-users.index') }}"
                   class="px-4 py-2.5 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 text-slate-700 dark:text-slate-200 text-xs font-bold rounded-xl transition">
                    &larr; Kembali ke Kelola User
                </a>
            </div>

            {{-- Card Form --}}
            <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-xl border border-gray-100 dark:border-slate-700 p-8">
                <form method="POST" action="{{ route('admin.manage-users.store') }}" class="space-y-6">
                    @csrf

                    {{-- Nama Lengkap --}}
                    <div>
                        <label for="name" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">Nama Lengkap *</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                               placeholder="Masukkan Nama Lengkap"
                               class="w-full bg-slate-50 dark:bg-slate-700/60 border border-gray-200 dark:border-slate-600 rounded-xl py-3 px-4 text-sm text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-primary-500">
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>

                    {{-- NIM --}}
                    <div>
                        <label for="nim" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">NIM *</label>
                        <input type="text" id="nim" name="nim" value="{{ old('nim') }}" required
                               placeholder="Masukkan NIM Pengguna"
                               class="w-full bg-slate-50 dark:bg-slate-700/60 border border-gray-200 dark:border-slate-600 rounded-xl py-3 px-4 text-sm text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-primary-500">
                        <x-input-error :messages="$errors->get('nim')" class="mt-1" />
                    </div>

                    {{-- Temporary Password --}}
                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">Password Sementara *</label>
                        <input type="password" id="password" name="password" required
                               placeholder="Minimal 8 karakter (User wajib mengganti saat login pertama)"
                               class="w-full bg-slate-50 dark:bg-slate-700/60 border border-gray-200 dark:border-slate-600 rounded-xl py-3 px-4 text-sm text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-primary-500">
                        <p class="text-xs text-slate-400 mt-1">User wajib mengganti password ini saat pertama kali login.</p>
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>

                    {{-- Grid 3 kolom untuk Role, Divisi, Jabatan --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        {{-- Role Utama --}}
                        <div>
                            <label for="role" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">Role Utama *</label>
                            <select id="role" name="role" required class="w-full bg-slate-50 dark:bg-slate-700/60 border border-gray-200 dark:border-slate-600 rounded-xl py-3 px-3 text-sm text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-primary-500">
                                <option value="" disabled selected>-- Pilih Role --</option>
                                @foreach($allRoles as $r)
                                    <option value="{{ $r->name }}" {{ old('role') == $r->name ? 'selected' : '' }}>{{ $r->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('role')" class="mt-1" />
                        </div>

                        {{-- Divisi --}}
                        <div>
                            <label for="divisi_id" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">Divisi</label>
                            <select id="divisi_id" name="divisi_id" class="w-full bg-slate-50 dark:bg-slate-700/60 border border-gray-200 dark:border-slate-600 rounded-xl py-3 px-3 text-sm text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-primary-500">
                                <option value="">-- Tanpa Divisi --</option>
                                @foreach($allDivisis as $d)
                                    <option value="{{ $d->id }}" {{ old('divisi_id') == $d->id ? 'selected' : '' }}>{{ $d->nama_divisi }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('divisi_id')" class="mt-1" />
                        </div>

                        {{-- Jabatan --}}
                        <div>
                            <label for="jabatan_id" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">Jabatan</label>
                            <select id="jabatan_id" name="jabatan_id" class="w-full bg-slate-50 dark:bg-slate-700/60 border border-gray-200 dark:border-slate-600 rounded-xl py-3 px-3 text-sm text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-primary-500">
                                <option value="">-- Tanpa Jabatan --</option>
                                @foreach($allJabatans as $j)
                                    <option value="{{ $j->id }}" {{ old('jabatan_id') == $j->id ? 'selected' : '' }}>{{ $j->nama_jabatan }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('jabatan_id')" class="mt-1" />
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="pt-4 flex items-center justify-end gap-3 border-t border-gray-100 dark:border-slate-700">
                        <a href="{{ route('admin.manage-users.index') }}"
                           class="px-5 py-3 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 text-slate-700 dark:text-slate-200 font-bold rounded-2xl text-xs transition">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-3 bg-primary-500 hover:bg-primary-600 text-white font-bold rounded-2xl shadow-lg shadow-primary-500/20 text-xs transition cursor-pointer">
                            + Tambah & Buat User
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</x-app-layout>
