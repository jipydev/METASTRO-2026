<div>
    <label for="foto" class="block font-bold text-gray-700 dark:text-slate-300 mb-1 uppercase tracking-wider">
        Foto Profil
    </label>
    <div class="flex items-center gap-4">
        <div class="shrink-0">
            @if (isset($user) && $user->foto)
                <img src="{{ asset('storage/' . $user->foto) }}" alt="{{ $user->nama }}"
                    class="w-16 h-16 rounded-2xl object-cover border border-gray-300 dark:border-slate-600">
            @else
                <div class="w-16 h-16 rounded-2xl bg-brand-600 text-white flex items-center justify-center text-lg font-bold">
                    {{ isset($user) ? mb_strtoupper(mb_substr($user->nama, 0, 1)) : '?' }}
                </div>
            @endif
        </div>
        <div class="flex-1">
            <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/webp"
                class="block w-full text-xs text-gray-600 dark:text-slate-300 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-600 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-white hover:file:bg-brand-700">
            <p class="mt-1 text-[11px] text-gray-500 dark:text-slate-400">JPG, PNG, atau WEBP. Maksimal 2 MB.</p>
        </div>
    </div>
    <x-input-error :messages="$errors->get('foto')" class="mt-1" />
</div>
