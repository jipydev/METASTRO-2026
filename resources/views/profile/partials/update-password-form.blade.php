<section class="font-poppins">
    <header class="mb-6 border-b border-gray-100 dark:border-slate-700 pb-4">
        <h2 class="text-xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
            <span class="p-2 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400">🔒</span>
            {{ __('Ubah Password') }}
        </h2>

        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
            {{ __('Pastikan akun Anda menggunakan password yang aman dan sulit ditebak.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-6">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" :value="__('Password Saat Ini')" class="font-semibold text-slate-700 dark:text-slate-300" />
            <input id="update_password_current_password" name="current_password" type="password" 
                   class="mt-1 w-full rounded-xl border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:border-primary-500 focus:ring-primary-500 text-sm py-2.5 px-3.5" 
                   autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" :value="__('Password Baru')" class="font-semibold text-slate-700 dark:text-slate-300" />
            <input id="update_password_password" name="password" type="password" 
                   class="mt-1 w-full rounded-xl border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:border-primary-500 focus:ring-primary-500 text-sm py-2.5 px-3.5" 
                   autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Konfirmasi Password')" class="font-semibold text-slate-700 dark:text-slate-300" />
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" 
                   class="mt-1 w-full rounded-xl border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:border-primary-500 focus:ring-primary-500 text-sm py-2.5 px-3.5" 
                   autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white font-bold text-sm px-6 py-2.5 rounded-xl shadow-sm transition cursor-pointer">
                {{ __('Perbarui Password') }}
            </button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2500)"
                    class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                    ✓ {{ __('Password berhasil diubah.') }}
                </p>
            @endif
        </div>
    </form>
</section>
