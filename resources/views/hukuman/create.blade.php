<x-app-layout :$title>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-bold text-xl text-gray-900 dark:text-white leading-tight">
                    {{ $title }}
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    @if ($mode === 'pengawas')
                        Pilih pengawas yang akan dihukum
                    @else
                        Pilih panitia atau admin yang akan dihukum (pengawas dikecualikan)
                    @endif
                </p>
            </div>

            <a href="{{ route('hukuman.kelola', $mode) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold rounded-xl text-xs transition self-start sm:self-auto">
                ← Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 font-poppins">
        <div class="bg-white dark:bg-slate-800 shadow-sm rounded-3xl p-6 sm:p-7 border border-gray-100 dark:border-slate-700">
            <form method="POST" action="{{ route('hukuman.store', $mode) }}" class="space-y-5">
                @csrf

                <div>
                    <label for="user_id" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">
                        Target Hukuman
                    </label>
                    <select name="user_id" id="user_id" required
                            class="w-full rounded-xl border-gray-200 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm focus:border-brand-500 focus:ring-brand-500">
                        <option value="">— Pilih panitia —</option>
                        @foreach ($targets as $target)
                            <option value="{{ $target->id }}" @selected(old('user_id') == $target->id)>
                                {{ $target->nama }} — {{ $target->formatted_divisi_jabatan }}
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="kategori" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">
                        Kategori Hukuman
                    </label>
                    <select name="kategori" id="kategori" required
                            class="w-full rounded-xl border-gray-200 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm focus:border-brand-500 focus:ring-brand-500">
                        <option value="">— Pilih kategori —</option>
                        @foreach ($kategoriOptions as $kategori)
                            <option value="{{ $kategori }}" @selected(old('kategori') === $kategori)>
                                {{ ucfirst($kategori) }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1.5 text-[11px] text-slate-400">
                        Detail tugas per kategori tidak ditampilkan di web. Panitia akan mengerjakannya offline sesuai arahan Ranger.
                    </p>
                    @error('kategori')
                        <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="alasan" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">
                        Alasan Hukuman
                    </label>
                    <textarea name="alasan" id="alasan" rows="4" required maxlength="2000"
                              placeholder="Jelaskan pelanggaran atau alasan hukuman..."
                              class="w-full rounded-xl border-gray-200 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm focus:border-brand-500 focus:ring-brand-500">{{ old('alasan') }}</textarea>
                    @error('alasan')
                        <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800 text-xs text-amber-800 dark:text-amber-200">
                    Target memiliki waktu <strong>2×24 jam (2 hari)</strong> sejak hukuman diterbitkan untuk mengajukan pembelaan dan menyelesaikan tugas.
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <a href="{{ route('hukuman.kelola', $mode) }}"
                       class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-5 py-2 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-xl text-xs shadow-sm transition">
                        Terbitkan Hukuman
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
