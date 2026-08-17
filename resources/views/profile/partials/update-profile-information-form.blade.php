<section class="font-poppins">
    <header class="mb-6 border-b border-gray-100 dark:border-slate-700 pb-4">
        <h2 class="text-xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
            <span class="p-2 rounded-xl bg-primary-50 dark:bg-primary-950/60 text-primary-600 dark:text-primary-400">👤</span>
            {{ __('Informasi Profil') }}
        </h2>

        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
            {{ __("Ubah informasi profil dan alamat email akun Anda.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6" x-data="{ photoPreview: null }">
        @csrf
        @method('patch')

        <!-- Foto Profil -->
        <div>
            <x-input-label for="foto" :value="__('Foto Profil')" class="font-semibold text-slate-700 dark:text-slate-300" />
            <div class="mt-2 flex items-center gap-4">
                <template x-if="photoPreview">
                    <img :src="photoPreview" class="w-16 h-16 rounded-full object-cover border-2 border-primary-500 shadow">
                </template>
                <template x-if="!photoPreview">
                    @php
                        $currentPhoto = $user->foto
                            ? asset('storage/' . $user->foto)
                            : 'https://ui-avatars.com/api/?size=256&background=fe5a1d&color=fff&name=' . urlencode($user->nama);
                    @endphp
                    <img src="{{ $currentPhoto }}" alt="{{ $user->nama }}" class="w-16 h-16 rounded-full object-cover border-2 border-slate-200 dark:border-slate-700 shadow-sm">
                </template>
                <div class="flex-1">
                    <input id="foto" name="foto" type="file" accept="image/*"
                           @change="
                               const file = $event.target.files[0];
                               if (file) {
                                   const reader = new FileReader();
                                   reader.onload = (e) => { photoPreview = e.target.result; };
                                   reader.readAsDataURL(file);
                               }
                           "
                           class="w-full text-sm text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary-50 dark:file:bg-primary-950/60 file:text-primary-600 dark:file:text-primary-400 hover:file:bg-primary-100 cursor-pointer" />
                    <p class="text-xs text-slate-400 mt-1">Format: JPG, JPEG, PNG, WEBP. Maksimal 2MB.</p>
                </div>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('foto')" />
        </div>

        <div>
            <x-input-label for="nama" :value="__('Nama Lengkap')" class="font-semibold text-slate-700 dark:text-slate-300" />
            <div class="relative mt-1">
                <input id="nama" name="nama" type="text"
                       class="form-control-app w-full"
                       value="{{ old('nama', $user->nama) }}" required maxlength="255" autocomplete="name" />
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('nama')" />
        </div>

        <div>
            <x-input-label for="nim" :value="__('NIM')" class="font-semibold text-slate-700 dark:text-slate-300" />
            <div class="relative mt-1">
                <input id="nim" name="nim" type="text"
                       class="form-control-app w-full font-mono"
                       value="{{ old('nim', $user->nim) }}" required maxlength="20" autocomplete="username" />
            </div>
            <p class="mt-1 text-xs text-slate-400">NIM dipakai untuk login. Pastikan sesuai data resmi.</p>
            <x-input-error class="mt-2" :messages="$errors->get('nim')" />
        </div>

        <div>
            <x-input-label :value="__('Divisi & Jabatan')" class="font-semibold text-slate-700 dark:text-slate-300" />
            <div class="relative mt-1">
                <input type="text"
                       class="form-control-app w-full bg-slate-100 dark:bg-slate-700/60 text-slate-500 dark:text-slate-400 cursor-not-allowed"
                       value="{{ $user->divisi ? $user->formatted_divisi_jabatan : ($user->getRoleNames()->first() ?? 'Peserta') }}"
                       readonly disabled />
            </div>
            <p class="mt-1 text-xs text-slate-400">Hubungi admin jika divisi atau jabatan perlu diubah.</p>
        </div>

        <div>
            <x-input-label for="email" :value="__('Alamat Email')" class="font-semibold text-slate-700 dark:text-slate-300" />
            <div class="relative mt-1">
                <input id="email" name="email" type="email"
                       class="form-control-app w-full"
                       value="{{ old('email', $user->email) }}" required maxlength="255" autocomplete="username" />
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                <div class="mt-3 p-3 bg-amber-50 dark:bg-amber-950/50 border border-amber-200 dark:border-amber-900/60 rounded-xl text-xs text-amber-800 dark:text-amber-300">
                    <p>
                        {{ __('Email Anda belum terverifikasi.') }}

                        <button form="send-verification" class="underline font-bold hover:text-amber-900 dark:hover:text-amber-200 cursor-pointer">
                            {{ __('Klik di sini untuk mengirim ulang email verifikasi.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-semibold text-green-600 dark:text-green-400">
                            {{ __('Link verifikasi baru telah dikirimkan ke alamat email Anda.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="bg-primary-500 hover:bg-primary-600 text-white font-bold text-sm px-6 py-2.5 rounded-xl shadow-sm transition cursor-pointer">
                {{ __('Simpan Perubahan') }}
            </button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2500)"
                    class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                    ✓ {{ __('Tersimpan.') }}
                </p>
            @endif
        </div>
    </form>
</section>
