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

    <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Nama Lengkap')" class="font-semibold text-slate-700 dark:text-slate-300" />
            <div class="relative mt-1">
                <input id="name" name="name" type="text" 
                       class="w-full rounded-xl border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:border-primary-500 focus:ring-primary-500 text-sm py-2.5 px-3.5"
                       value="{{ old('name', $user->name) }}" required autocomplete="name" />
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Alamat Email')" class="font-semibold text-slate-700 dark:text-slate-300" />
            <div class="relative mt-1">
                <input id="email" name="email" type="email" 
                       class="w-full rounded-xl border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:border-primary-500 focus:ring-primary-500 text-sm py-2.5 px-3.5"
                       value="{{ old('email', $user->email) }}" required autocomplete="username" />
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
