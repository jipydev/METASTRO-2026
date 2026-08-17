<section class="font-poppins">
    <header class="mb-6 border-b border-gray-100 dark:border-slate-700 pb-4">
        <h2 class="text-xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
            {{ __('Ubah Password') }}
        </h2>

        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
            {{ __('Pastikan akun Anda menggunakan password yang aman dan sulit ditebak.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-6">
        @csrf
        @method('put')

        <x-password-input
            id="update_password_current_password"
            name="current_password"
            :label="__('Password Saat Ini')"
            autocomplete="current-password"
            required
            :error-bag="$errors->updatePassword->get('current_password')" />

        <x-password-input
            id="update_password_password"
            name="password"
            :label="__('Password Baru')"
            autocomplete="new-password"
            required
            minlength="8"
            :error-bag="$errors->updatePassword->get('password')" />

        <x-password-input
            id="update_password_password_confirmation"
            name="password_confirmation"
            :label="__('Konfirmasi Password')"
            autocomplete="new-password"
            required
            minlength="8"
            :error-bag="$errors->updatePassword->get('password_confirmation')" />

        <div class="flex items-center gap-4 pt-2">
            <button type="submit"
                class="bg-amber-500 hover:bg-amber-600 text-white font-bold text-sm px-6 py-2.5 rounded-xl shadow-sm transition cursor-pointer">
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
