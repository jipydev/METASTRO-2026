<x-guest-layout :$title>
    <div class="text-center mb-6">
        <p class="font-oswald text-xl font-semibold uppercase tracking-tight text-brand-500">
            METASTRO 2026
        </p>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700 shadow-sm p-6 sm:p-8">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
            Selamat datang, <span class="text-brand-500">HIROES</span>.
        </h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 mb-6">
            Silakan masuk untuk mengisi presensi dan mengerjakan tugas.
        </p>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <x-input-label for="nim" :value="__('NIM')" class="font-semibold text-slate-700 dark:text-slate-300 text-xs" />
                <div class="relative mt-1">
                    <span class="icon-[tabler--id] text-slate-400 dark:text-slate-500 absolute text-xl top-1/2 left-3 -translate-y-1/2 pointer-events-none"></span>
                    <input id="nim" name="nim" type="text" value="{{ old('nim') }}" required maxlength="20"
                        autofocus autocomplete="username" placeholder="Masukkan NIM anda"
                        class="w-full bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl py-2.5 pl-10 pr-3.5 text-xs text-slate-900 dark:text-slate-100 outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 placeholder:text-slate-400 dark:placeholder:text-slate-500" />
                </div>
                <x-input-error :messages="$errors->get('nim')" class="mt-2" />
            </div>

            <div x-data="{ show: false }">
                <x-input-label for="password" :value="__('Password')" class="font-semibold text-slate-700 dark:text-slate-300 text-xs" />
                <div class="relative mt-1">
                    <span class="icon-[material-symbols--lock-outline] text-slate-400 dark:text-slate-500 absolute text-xl top-1/2 left-3 -translate-y-1/2 pointer-events-none"></span>
                    <input id="password" name="password" type="password" :type="show ? 'text' : 'password'" required
                        autocomplete="current-password" placeholder="Masukkan password anda"
                        class="w-full bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl py-2.5 pl-10 pr-11 text-xs text-slate-900 dark:text-slate-100 outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 placeholder:text-slate-400 dark:placeholder:text-slate-500" />
                    <button type="button" @click="show = !show"
                        class="absolute inset-y-0 right-0 px-3 flex items-center text-slate-400 hover:text-slate-700 dark:text-slate-500 dark:hover:text-slate-200"
                        :aria-label="show ? 'Sembunyikan password' : 'Lihat password'">
                        <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <svg x-show="show" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" name="remember"
                    class="rounded border-slate-300 dark:border-slate-600 text-brand-500 bg-white dark:bg-slate-700 shadow-sm focus:ring-brand-500 cursor-pointer">
                <span class="ms-2 text-xs text-slate-600 dark:text-slate-400">{{ __('Ingat saya') }}</span>
            </label>

            <button type="submit"
                class="w-full px-4 py-2.5 bg-brand-500 hover:bg-brand-600 text-white text-sm font-bold rounded-xl shadow-sm transition cursor-pointer">
                {{ __('Masuk') }}
            </button>
        </form>

        @if (Route::has('password.request'))
            <a class="mt-5 block text-center text-xs text-slate-500 dark:text-slate-400 hover:text-brand-500 dark:hover:text-brand-400 transition"
                href="{{ route('password.request') }}">
                {{ __('Lupa password?') }}
            </a>
        @endif
    </div>
</x-guest-layout>
