<x-app-layout :$title>
    <div class="py-8 font-poppins min-h-screen bg-gray-50 dark:bg-slate-900 transition-colors duration-200">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">Edit Pengguna</h1>
                    <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Perbarui informasi profil, hak akses, dan status akun</p>
                </div>
                <a href="{{ route('admin.users.index') }}"
                   class="px-3.5 py-2 bg-gray-200 dark:bg-slate-700 hover:bg-gray-300 dark:hover:bg-slate-600 text-gray-700 dark:text-slate-200 text-xs font-semibold rounded-xl transition">
                    &larr; Kembali
                </a>
            </div>

            {{-- Form Card --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 p-6 sm:p-8">
                <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-5 text-xs">
                    @csrf
                    @method('PUT')

                    {{-- Nama Lengkap --}}
                    <div>
                        <label for="nama" class="block font-bold text-gray-700 dark:text-slate-300 mb-1 uppercase tracking-wider">Nama Lengkap *</label>
                        <input type="text" id="nama" name="nama" value="{{ old('nama', $user->nama) }}" required autofocus
                               class="w-full bg-slate-50 dark:bg-slate-700/60 border border-gray-300 dark:border-slate-600 rounded-xl py-2.5 px-3.5 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-500">
                        <x-input-error :messages="$errors->get('nama')" class="mt-1" />
                    </div>

                    {{-- NIM & Email --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="nim" class="block font-bold text-gray-700 dark:text-slate-300 mb-1 uppercase tracking-wider">NIM *</label>
                            <input type="text" id="nim" name="nim" value="{{ old('nim', $user->nim) }}" required
                                   class="w-full bg-slate-50 dark:bg-slate-700/60 border border-gray-300 dark:border-slate-600 rounded-xl py-2.5 px-3.5 text-xs font-mono text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-500">
                            <x-input-error :messages="$errors->get('nim')" class="mt-1" />
                        </div>

                        <div>
                            <label for="email" class="block font-bold text-gray-700 dark:text-slate-300 mb-1 uppercase tracking-wider">Email</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                                   class="w-full bg-slate-50 dark:bg-slate-700/60 border border-gray-300 dark:border-slate-600 rounded-xl py-2.5 px-3.5 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-500">
                            <x-input-error :messages="$errors->get('email')" class="mt-1" />
                        </div>
                    </div>

                    {{-- Grid Role, Divisi, Jabatan --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-1">
                        {{-- Role --}}
                        <div>
                            <label for="role" class="block font-bold text-gray-700 dark:text-slate-300 mb-1 uppercase tracking-wider">Role *</label>
                            <select id="role" name="role" required class="w-full bg-slate-50 dark:bg-slate-700/60 border border-gray-300 dark:border-slate-600 rounded-xl py-2.5 px-3 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-500">
                                @php $currentRole = $user->getRoleNames()->first(); @endphp
                                @foreach($roles as $r)
                                    <option value="{{ $r->name }}" {{ old('role', $currentRole) == $r->name ? 'selected' : '' }}>
                                        {{ ucfirst($r->name) }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('role')" class="mt-1" />
                        </div>

                        {{-- Divisi --}}
                        <div>
                            <label for="divisi_id" class="block font-bold text-gray-700 dark:text-slate-300 mb-1 uppercase tracking-wider">Divisi</label>
                            <select id="divisi_id" name="divisi_id" class="w-full bg-slate-50 dark:bg-slate-700/60 border border-gray-300 dark:border-slate-600 rounded-xl py-2.5 px-3 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">-- Tanpa Divisi --</option>
                                @foreach($divisis as $d)
                                    <option value="{{ $d->id }}" {{ old('divisi_id', $user->divisi_id) == $d->id ? 'selected' : '' }}>
                                        {{ $d->nama }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('divisi_id')" class="mt-1" />
                        </div>

                        {{-- Jabatan --}}
                        <div>
                            <label for="jabatan_id" class="block font-bold text-gray-700 dark:text-slate-300 mb-1 uppercase tracking-wider">Jabatan</label>
                            <select id="jabatan_id" name="jabatan_id" class="w-full bg-slate-50 dark:bg-slate-700/60 border border-gray-300 dark:border-slate-600 rounded-xl py-2.5 px-3 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">-- Tanpa Jabatan --</option>
                                @foreach($jabatans as $j)
                                    <option value="{{ $j->id }}" {{ old('jabatan_id', $user->jabatan_id) == $j->id ? 'selected' : '' }}>
                                        {{ $j->nama }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('jabatan_id')" class="mt-1" />
                        </div>
                    </div>

                    {{-- Status Akun --}}
                    <div>
                        <label for="status" class="block font-bold text-gray-700 dark:text-slate-300 mb-1 uppercase tracking-wider">Status Akun *</label>
                        <select id="status" name="status" required class="w-full bg-slate-50 dark:bg-slate-700/60 border border-gray-300 dark:border-slate-600 rounded-xl py-2.5 px-3 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="1" {{ old('status', (int) $user->status) === 1 ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ old('status', (int) $user->status) === 0 ? 'selected' : '' }}>Non-Aktif</option>
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-1" />
                    </div>

                    {{-- Actions --}}
                    <div class="pt-4 flex items-center justify-end gap-2.5 border-t border-gray-100 dark:border-slate-700">
                        <a href="{{ route('admin.users.index') }}"
                           class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-700 dark:text-slate-200 font-semibold rounded-xl transition">
                            Batal
                        </a>
                        <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow-sm transition">
                            Simpan Perubahan
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</x-app-layout>