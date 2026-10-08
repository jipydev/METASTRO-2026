<x-app-layout :$title>
    @if (session('success') || session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                @if (session('success'))
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: @json(session('success')),
                        confirmButtonColor: window.appBrandColor()
                    });
                @else
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: @json(session('error')),
                        confirmButtonColor: '#dc2626'
                    });
                @endif
            });
        </script>
    @endif

    <div class="py-8 font-poppins min-h-screen bg-gray-50 dark:bg-slate-900 transition-colors duration-200">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">Edit Pengguna</h1>
                    <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Perbarui informasi profil, hak akses, dan
                        status akun</p>
                </div>
                <a href="{{ route('admin.users.index') }}"
                    class="px-3.5 py-2 bg-gray-200 dark:bg-slate-700 hover:bg-gray-300 dark:hover:bg-slate-600 text-gray-700 dark:text-slate-200 text-xs font-semibold rounded-xl transition">
                    &larr; Kembali
                </a>
            </div>

            {{-- Form Card --}}
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-700 p-6 sm:p-8">
                <form method="POST" action="{{ route('admin.users.update', $user) }}" enctype="multipart/form-data"
                    class="space-y-5 text-xs">
                    @csrf
                    @method('PUT')

                    {{-- Nama Lengkap --}}
                    <div>
                        <label for="nama"
                            class="block font-bold text-gray-700 dark:text-slate-300 mb-1 uppercase tracking-wider">Nama
                            Lengkap *</label>
                        <input type="text" id="nama" name="nama" value="{{ old('nama', $user->nama) }}"
                            required maxlength="255" autofocus
                            class="w-full bg-slate-50 dark:bg-slate-700/60 border border-gray-300 dark:border-slate-600 rounded-xl py-2.5 px-3.5 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-brand-500">
                        <x-input-error :messages="$errors->get('nama')" class="mt-1" />
                    </div>

                    {{-- NIM & Email --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="nim"
                                class="block font-bold text-gray-700 dark:text-slate-300 mb-1 uppercase tracking-wider">NIM
                                *</label>
                            <input type="text" id="nim" name="nim" value="{{ old('nim', $user->nim) }}"
                                required maxlength="20"
                                class="w-full bg-slate-50 dark:bg-slate-700/60 border border-gray-300 dark:border-slate-600 rounded-xl py-2.5 px-3.5 text-xs font-mono text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-brand-500">
                            <x-input-error :messages="$errors->get('nim')" class="mt-1" />
                        </div>

                        <div>
                            <label for="email"
                                class="block font-bold text-gray-700 dark:text-slate-300 mb-1 uppercase tracking-wider">Email</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                                maxlength="255"
                                class="w-full bg-slate-50 dark:bg-slate-700/60 border border-gray-300 dark:border-slate-600 rounded-xl py-2.5 px-3.5 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-brand-500">
                            <x-input-error :messages="$errors->get('email')" class="mt-1" />
                        </div>
                    </div>

                    {{-- Data Pribadi --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label for="jenis_kelamin"
                                class="block font-bold text-gray-700 dark:text-slate-300 mb-1 uppercase tracking-wider">Jenis
                                Kelamin *</label>
                            <select id="jenis_kelamin" name="jenis_kelamin" required
                                class="w-full bg-slate-50 dark:bg-slate-700/60 border border-gray-300 dark:border-slate-600 rounded-xl py-2.5 px-3 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-brand-500">
                                <option value="">-- Pilih --</option>
                                <option value="laki-laki"
                                    {{ old('jenis_kelamin', $user->jenis_kelamin) === 'laki-laki' ? 'selected' : '' }}>
                                    Laki-laki</option>
                                <option value="perempuan"
                                    {{ old('jenis_kelamin', $user->jenis_kelamin) === 'perempuan' ? 'selected' : '' }}>
                                    Perempuan</option>
                            </select>
                            <x-input-error :messages="$errors->get('jenis_kelamin')" class="mt-1" />
                        </div>

                        <div>
                            <label for="tanggal_lahir"
                                class="block font-bold text-gray-700 dark:text-slate-300 mb-1 uppercase tracking-wider">Tanggal
                                Lahir</label>
                            <input type="date" id="tanggal_lahir" name="tanggal_lahir"
                                value="{{ old('tanggal_lahir', optional($user->tanggal_lahir)->format('Y-m-d')) }}"
                                max="{{ now()->toDateString() }}"
                                class="w-full bg-slate-50 dark:bg-slate-700/60 border border-gray-300 dark:border-slate-600 rounded-xl py-2.5 px-3 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-brand-500">
                            <x-input-error :messages="$errors->get('tanggal_lahir')" class="mt-1" />
                        </div>

                        <div>
                            <label for="nomor_hp"
                                class="block font-bold text-gray-700 dark:text-slate-300 mb-1 uppercase tracking-wider">No.
                                HP</label>
                            <input type="tel" id="nomor_hp" name="nomor_hp"
                                value="{{ old('nomor_hp', $user->nomor_hp) }}" maxlength="20"
                                placeholder="Contoh: 081234567890"
                                class="w-full bg-slate-50 dark:bg-slate-700/60 border border-gray-300 dark:border-slate-600 rounded-xl py-2.5 px-3 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-brand-500">
                            <x-input-error :messages="$errors->get('nomor_hp')" class="mt-1" />
                        </div>
                    </div>

                    @include('admin.users.partials.photo-input', ['user' => $user])

                    {{-- Grid Role, Divisi, Jabatan --}}
                    <div x-data="{
                        stakeholderDivisiId: {{ $stakeholderDivisiId ?? 'null' }},
                        role: @js(old('role', $user->getRoleNames()->first() ?? '')),
                        divisiId: @js((string) old('divisi_id', $user->divisi_id ?? '')),
                        jabatanId: @js((string) old('jabatan_id', $user->jabatan_id ?? '')),
                        timId: @js((string) old('tim_id', $user->tims->first()?->tim_id ?? '')),
                        operational: @js($operationalJabatan),
                        stakeholder: @js($stakeholderJabatan),
                        get jabatanOptions() {
                            return String(this.divisiId) === String(this.stakeholderDivisiId) ?
                                this.stakeholder :
                                this.operational;
                        },
                        syncJabatan() {
                            if (!this.jabatanOptions.some((item) => String(item.id) === String(this.jabatanId))) {
                                this.jabatanId = '';
                            }
                        },
                        syncRole() {
                            if (this.isPeserta) {
                                this.divisiId = '';
                                this.jabatanId = '';
                            } else {
                                this.timId = '';
                            }
                        },
                        get isPeserta() {
                            return this.role.toLowerCase() === 'peserta';
                        }
                    }" x-init="$watch('divisiId', () => syncJabatan());
                    $watch('role', () => syncRole())"
                        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pt-1">
                        {{-- Role --}}
                        <div>
                            <label for="role"
                                class="block font-bold text-gray-700 dark:text-slate-300 mb-1 uppercase tracking-wider">Role
                                *</label>
                            <select id="role" name="role" x-model="role" required
                                class="w-full bg-slate-50 dark:bg-slate-700/60 border border-gray-300 dark:border-slate-600 rounded-xl py-2.5 px-3 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-brand-500">
                                @php $selectedRole = old('role', $user->getRoleNames()->first()); @endphp
                                @foreach ($roles as $r)
                                    <option value="{{ $r->name }}" @selected((string) $selectedRole === (string) $r->name)>
                                        {{ ucfirst($r->name) }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('role')" class="mt-1" />
                        </div>

                        {{-- Divisi --}}
                        <div>
                            <label for="divisi_id"
                                class="block font-bold text-gray-700 dark:text-slate-300 mb-1 uppercase tracking-wider">Divisi</label>
                            <select id="divisi_id" name="divisi_id" x-model="divisiId" x-bind:disabled="isPeserta"
                                class="w-full bg-slate-50 dark:bg-slate-700/60 border border-gray-300 dark:border-slate-600 rounded-xl py-2.5 px-3 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-brand-500">
                                <option value="">-- Tanpa Divisi --</option>
                                @foreach ($divisis as $d)
                                    <option value="{{ $d->id }}"
                                        {{ old('divisi_id', $user->divisi_id) == $d->id ? 'selected' : '' }}>
                                        {{ $d->nama }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('divisi_id')" class="mt-1" />
                        </div>

                        <div>
                            @include('admin.users.partials.jabatan-select')
                        </div>

                        {{-- Tim Peserta --}}
                        <div>
                            <label for="tim_id"
                                class="block font-bold text-gray-700 dark:text-slate-300 mb-1 uppercase tracking-wider">Tim</label>
                            <select id="tim_id" name="tim_id" x-model="timId" x-bind:disabled="!isPeserta"
                                class="w-full bg-slate-50 dark:bg-slate-700/60 border border-gray-300 dark:border-slate-600 rounded-xl py-2.5 px-3 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-brand-500">
                                <option value="">-- Tanpa Tim --</option>
                                @foreach ($tims as $tim)
                                    <option value="{{ $tim->id }}">{{ $tim->nama }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('tim_id')" class="mt-1" />
                        </div>
                    </div>

                    {{-- Status Akun --}}
                    <div>
                        <label for="status"
                            class="block font-bold text-gray-700 dark:text-slate-300 mb-1 uppercase tracking-wider">Status
                            Akun *</label>
                        <select id="status" name="status" required
                            class="w-full bg-slate-50 dark:bg-slate-700/60 border border-gray-300 dark:border-slate-600 rounded-xl py-2.5 px-3 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-brand-500">
                            <option value="1" {{ old('status', (int) $user->status) === 1 ? 'selected' : '' }}>
                                Aktif</option>
                            <option value="0" {{ old('status', (int) $user->status) === 0 ? 'selected' : '' }}>
                                Non-Aktif</option>
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-1" />
                    </div>

                    {{-- Reset Password --}}
                    <div class="pt-4 border-t border-gray-100 dark:border-slate-700">
                        <h2 class="font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider">Reset Password
                        </h2>
                        <p class="mt-1 text-[11px] text-gray-500 dark:text-slate-400">
                            Password akun akan dikembalikan ke <span
                                class="font-mono font-semibold">metastro2026</span>.
                        </p>
                        <button type="submit" form="reset-password-form"
                            onclick="return confirm('Reset password akun ini ke metastro2026?')"
                            class="mt-3 cursor-pointer px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded-xl shadow-sm transition">
                            Reset ke Password Default
                        </button>
                    </div>

                    {{-- Actions --}}
                    <div
                        class="pt-4 flex items-center justify-end gap-2.5 border-t border-gray-100 dark:border-slate-700">
                        <a href="{{ route('admin.users.index') }}"
                            class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-700 dark:text-slate-200 font-semibold rounded-xl transition">
                            Batal
                        </a>
                        <button type="submit"
                            class="cursor-pointer px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-xl shadow-sm transition">
                            Simpan Perubahan
                        </button>
                    </div>

                </form>

                <form id="reset-password-form" method="POST"
                    action="{{ route('admin.users.reset-password', $user) }}" class="hidden">
                    @csrf
                    @method('PATCH')
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
