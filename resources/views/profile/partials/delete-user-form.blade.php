<section class="space-y-4 font-poppins">
    <header class="border-b border-red-200 dark:border-red-900/60 pb-3">
        <h2 class="text-xl font-bold text-red-600 dark:text-red-400 flex items-center gap-2">
            <span class="p-2 rounded-xl bg-red-100 dark:bg-red-950/60 text-red-600 dark:text-red-400">⚠️</span>
            {{ __('Zona Bahaya — Hapus Akun') }}
        </h2>

        <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
            {{ __('Setelah akun Anda dihapus, semua data dan informasi yang terkait akan dihapus secara permanen.') }}
        </p>
    </header>

    <div class="pt-2">
        <button type="button"
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            class="bg-red-600 hover:bg-red-700 text-white font-bold text-sm px-6 py-2.5 rounded-xl shadow-sm transition cursor-pointer">
            {{ __('Hapus Akun Permanen') }}
        </button>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 sm:p-8 font-poppins bg-white dark:bg-slate-800 border border-gray-100 dark:border-slate-700 rounded-3xl">
            @csrf
            @method('delete')

            <h2 class="text-xl font-bold text-slate-900 dark:text-white">
                {{ __('Apakah Anda yakin ingin menghapus akun Anda?') }}
            </h2>

            <p class="mt-2 text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                {{ __('Setelah akun Anda dihapus, semua data dan informasi yang terkait akan dihapus secara permanen. Harap masukkan password Anda untuk mengonfirmasi.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                <input id="password" name="password" type="password"
                    class="w-full rounded-xl border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:border-red-500 focus:ring-red-500 text-sm py-2.5 px-3.5"
                    placeholder="{{ __('Masukkan Password Anda') }}" />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')"
                    class="px-5 py-2.5 rounded-xl border border-gray-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 font-semibold text-sm hover:bg-gray-100 dark:hover:bg-slate-700 transition cursor-pointer">
                    {{ __('Batal') }}
                </button>

                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold text-sm px-6 py-2.5 rounded-xl shadow-sm transition cursor-pointer">
                    {{ __('Ya, Hapus Akun') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>
