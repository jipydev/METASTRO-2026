<x-app-layout :$title>
    <div class="py-8 font-poppins min-h-screen bg-gray-50 dark:bg-slate-900 transition-colors duration-200">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">Tambah Pengguna Baru</h1>
                    <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Registrasi akun panitia atau peserta baru</p>
                </div>
                <a href="{{ route('admin.users.index') }}"
                   class="px-3.5 py-2 bg-gray-200 dark:bg-slate-700 hover:bg-gray-300 dark:hover:bg-slate-600 text-gray-700 dark:text-slate-200 text-xs font-semibold rounded-xl transition">
                    &larr; Kembali
                </a>
            </div>

            {{-- Form Card --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 p-6 sm:p-8">
                <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-5 text-xs">
                    @csrf

                    {{-- Nama Lengkap --}}
                    <div>
                        <label for="nama" class="block font-bold text-gray-700 dark:text-slate-300 mb-1 uppercase tracking-wider">Nama Lengkap *</label>
                        <input type="text" id="nama" name="nama" value="{{ old('nama') }}" required maxlength="255" autofocus
                               placeholder="Contoh: Ahmad Dahlan"
                               class="w-full bg-slate-50 dark:bg-slate-700/60 border border-gray-300 dark:border-slate-600 rounded-xl py-2.5 px-3.5 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-brand-500">
                        <x-input-error :messages="$errors->get('nama')" class="mt-1" />
                    </div>

                    {{-- NIM & Email --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="nim" class="block font-bold text-gray-700 dark:text-slate-300 mb-1 uppercase tracking-wider">NIM *</label>
                            <input type="text" id="nim" name="nim" value="{{ old('nim') }}" required maxlength="20"
                                   placeholder="Contoh: 2201234"
                                   class="w-full bg-slate-50 dark:bg-slate-700/60 border border-gray-300 dark:border-slate-600 rounded-xl py-2.5 px-3.5 text-xs font-mono text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-brand-500">
                            <x-input-error :messages="$errors->get('nim')" class="mt-1" />
                        </div>

                        <div>
                            <label for="email" class="block font-bold text-gray-700 dark:text-slate-300 mb-1 uppercase tracking-wider">Email (Opsional)</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" maxlength="255"
                                   placeholder="user@example.com"
                                   class="w-full bg-slate-50 dark:bg-slate-700/60 border border-gray-300 dark:border-slate-600 rounded-xl py-2.5 px-3.5 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-brand-500">
                            <x-input-error :messages="$errors->get('email')" class="mt-1" />
                        </div>
                    </div>

                    {{-- Password Default (Otomatis) --}}
                    <div>
                        <label class="block font-bold text-gray-700 dark:text-slate-300 mb-1 uppercase tracking-wider">Password Awal Akun</label>
                        <div class="relative">
                            <input type="text" name="password" value="metastro2026" readonly
                                   class="w-full bg-slate-100 dark:bg-slate-700/40 rounded-xl border border-gray-300 dark:border-slate-600 px-3.5 py-2.5 text-xs font-mono text-gray-600 dark:text-slate-300 select-all cursor-not-allowed outline-none">
                        </div>
                        <p class="text-[11px] text-gray-400 dark:text-slate-500 mt-1.5 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Password secara otomatis diatur default menjadi <strong class="text-gray-700 dark:text-slate-200 font-mono">metastro2026</strong>. Pengguna dapat mengubahnya nanti.</span>
                        </p>
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>

                    {{-- Grid Role, Divisi, Jabatan --}}
                    <div
                        x-data="{
                            stakeholderDivisiId: {{ $stakeholderDivisiId ?? 'null' }},
                            divisiId: @js((string) old('divisi_id', '')),
                            jabatanId: @js((string) old('jabatan_id', '')),
                            operational: @js($operationalJabatan),
                            stakeholder: @js($stakeholderJabatan),
                            get jabatanOptions() {
                                return String(this.divisiId) === String(this.stakeholderDivisiId)
                                    ? this.stakeholder
                                    : this.operational;
                            },
                            syncJabatan() {
                                if (!this.jabatanOptions.some((item) => String(item.id) === String(this.jabatanId))) {
                                    this.jabatanId = '';
                                }
                            }
                        }"
                        x-init="$watch('divisiId', () => syncJabatan())"
                        class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-1">
                        {{-- Role Utama --}}
                        <div>
                            <label for="role" class="block font-bold text-gray-700 dark:text-slate-300 mb-1 uppercase tracking-wider">Role *</label>
                            <select id="role" name="role" required class="w-full bg-slate-50 dark:bg-slate-700/60 border border-gray-300 dark:border-slate-600 rounded-xl py-2.5 px-3 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-brand-500">
                                <option value="" disabled selected>-- Pilih Role --</option>
                                @foreach($roles as $r)
                                    <option value="{{ $r->name }}" {{ old('role') == $r->name ? 'selected' : '' }}>{{ ucfirst($r->name) }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('role')" class="mt-1" />
                           
                        </div>

                        {{-- Divisi --}}
                        <div>
                            <label for="divisi_id" class="block font-bold text-gray-700 dark:text-slate-300 mb-1 uppercase tracking-wider">Divisi</label>
                            <select id="divisi_id" name="divisi_id" x-model="divisiId" class="w-full bg-slate-50 dark:bg-slate-700/60 border border-gray-300 dark:border-slate-600 rounded-xl py-2.5 px-3 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-brand-500">
                                <option value="">-- Tanpa Divisi --</option>
                                @foreach($divisis as $d)
                                    <option value="{{ $d->id }}" {{ old('divisi_id') == $d->id ? 'selected' : '' }}>{{ $d->nama }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('divisi_id')" class="mt-1" />
                        </div>

                        <div>
                            @include('admin.users.partials.jabatan-select')
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="pt-4 flex items-center justify-end gap-2.5 border-t border-gray-100 dark:border-slate-700">
                        <a href="{{ route('admin.users.index') }}"
                           class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-700 dark:text-slate-200 font-semibold rounded-xl transition">
                            Batal
                        </a>
                        <button type="submit" class="cursor-pointer px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-xl shadow-sm transition">
                            Simpan Pengguna
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</x-app-layout>