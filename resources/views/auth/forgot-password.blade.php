<x-guest-layout :$title>
    <div class="w-full max-w-md mx-auto px-4 sm:px-6">
        <div class="text-center mb-6">
            <p class="font-oswald text-xl font-semibold uppercase tracking-tight text-brand-500">
                METASTRO 2026
            </p>
        </div>

        <div
            class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700 shadow-sm p-6 sm:p-8">
            <div class="mb-6">
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-brand-50 text-brand-500 dark:bg-brand-950/40 dark:text-brand-300 mb-4">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V7a4.5 4.5 0 10-9 0v3.5m-1.5 0h12a1.5 1.5 0 011.5 1.5v7A1.5 1.5 0 0118 20.5H6A1.5 1.5 0 014.5 19v-7A1.5 1.5 0 016 10.5z" />
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                    Lupa password?
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed">
                    Masukkan email Anda. Kami akan mengirimkan tautan untuk membuat password baru.
                </p>
            </div>

            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                @csrf

                <div>
                    <x-input-label for="email" :value="__('Email')"
                        class="font-semibold text-slate-700 dark:text-slate-300 text-xs" />
                    <div class="relative mt-1">
                        <span class="icon-[material-symbols--mail-outline] text-slate-400 dark:text-slate-500 absolute text-xl top-1/2 left-3 -translate-y-1/2 pointer-events-none"></span>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                            autocomplete="email" placeholder="nama@email.com"
                            class="w-full bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl py-2.5 pl-10 pr-3.5 text-xs text-slate-900 dark:text-slate-100 outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 placeholder:text-slate-400 dark:placeholder:text-slate-500" />
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <button type="submit"
                    class="w-full px-4 py-2.5 bg-brand-500 hover:bg-brand-600 text-white text-sm font-bold rounded-xl shadow-sm transition cursor-pointer">
                    Kirim tautan reset password
                </button>
            </form>

            <a class="mt-5 block text-center text-xs text-slate-500 dark:text-slate-400 hover:text-brand-500 dark:hover:text-brand-400 transition"
                href="{{ route('login') }}">
                Kembali ke halaman login
            </a>
        </div>
    </div>
</x-guest-layout>
