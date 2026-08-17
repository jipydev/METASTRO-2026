<x-app-layout :$title>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-bold text-xl text-gray-900 dark:text-white leading-tight">
                    {{ $title }}
                </h2>
            </div>

            <a href="{{ route('hukuman.kelola', $mode) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold rounded-xl text-xs transition self-start sm:self-auto">
                ← Kembali
            </a>
        </div>
    </x-slot>

    @php
        $targetOptions = $targets->map(fn ($user) => [
            'id' => $user->id,
            'nama' => $user->nama,
            'nim' => (string) ($user->nim ?? ''),
            'divisi' => $user->divisi?->nama ?? 'Umum',
            'jabatan' => $user->formatted_divisi_jabatan,
        ])->values();
    @endphp

    <div class="py-8 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 font-poppins">
        <div class="bg-white dark:bg-slate-800 shadow-sm rounded-3xl border border-gray-100 dark:border-slate-700 overflow-hidden">
            {{-- Form header --}}
            <div class="px-6 sm:px-8 pt-6 sm:pt-8 pb-5 border-b border-gray-100 dark:border-slate-700 bg-gradient-to-r from-brand-50/80 to-transparent dark:from-brand-950/20">
                <div class="flex items-start gap-3">
                    <div class="shrink-0 w-10 h-10 rounded-2xl bg-brand-100 dark:bg-brand-950/50 flex items-center justify-center text-brand-600 dark:text-brand-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M5.07 19h13.86a2 2 0 001.74-3L13.74 4a2 2 0 00-3.48 0L3.33 16a2 2 0 001.74 3z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Form Hukuman</h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                            Lengkapi target, kategori, dan alasan sebelum menerbitkan hukuman.
                        </p>
                    </div>
                </div>
            </div>

            <form
                method="POST"
                action="{{ route('hukuman.store', $mode) }}"
                class="p-6 sm:p-8 space-y-6 text-xs"
                x-data="{
                    targets: {{ \Illuminate\Support\Js::from($targetOptions) }},
                    selectedId: @js((string) old('user_id', '')),
                    targetQuery: '',
                    targetOpen: false,
                    targetLabel(item) {
                        return item.nama + ' — ' + item.jabatan;
                    },
                    get filteredTargets() {
                        const q = this.targetQuery.trim().toLowerCase();
                        if (!q) return this.targets;
                        return this.targets.filter((item) => (
                            item.nama.toLowerCase().includes(q)
                            || String(item.nim).toLowerCase().includes(q)
                            || item.divisi.toLowerCase().includes(q)
                            || item.jabatan.toLowerCase().includes(q)
                        ));
                    },
                    init() {
                        const selected = this.targets.find((item) => String(item.id) === String(this.selectedId));
                        if (selected) this.targetQuery = this.targetLabel(selected);
                    },
                    selectTarget(item) {
                        this.selectedId = String(item.id);
                        this.targetQuery = this.targetLabel(item);
                        this.targetOpen = false;
                    },
                    onTargetInput() {
                        this.selectedId = '';
                        this.targetOpen = true;
                    },
                    validateTarget(event) {
                        if (this.selectedId) return;
                        const exact = this.filteredTargets.find((item) => this.targetLabel(item).toLowerCase() === this.targetQuery.trim().toLowerCase());
                        const match = exact || (this.filteredTargets.length === 1 ? this.filteredTargets[0] : null);
                        if (match) {
                            this.selectTarget(match);
                            return;
                        }
                        event.preventDefault();
                        this.targetOpen = true;
                    }
                }"
                @submit="validateTarget($event)"
            >
                @csrf

                {{-- Target dengan search --}}
                <div class="space-y-1.5">
                    <label class="block font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider text-[11px]">
                        Target Hukuman <span class="text-brand-600">*</span>
                    </label>

                    <input type="hidden" name="user_id" x-model="selectedId">

                    <div class="relative" @click.outside="targetOpen = false">
                        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z" />
                            </svg>
                        </span>
                        <input
                            type="text"
                            x-model="targetQuery"
                            @input="onTargetInput()"
                            @focus="targetOpen = true"
                            @keydown.escape.prevent="targetOpen = false"
                            @keydown.enter.prevent="filteredTargets[0] && selectTarget(filteredTargets[0])"
                            autocomplete="off"
                            placeholder="Cari nama, NIM, atau divisi..."
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 py-2.5 pl-11 pr-10 text-xs text-slate-900 dark:text-slate-100 outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500"
                        >
                        <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-slate-400">
                            <svg class="h-4 w-4 shrink-0 transition-transform" :class="targetOpen && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </span>

                        <div
                            x-show="targetOpen"
                            x-cloak
                            x-transition
                            class="absolute z-30 mt-1.5 w-full max-h-56 overflow-y-auto rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 shadow-xl"
                        >
                            <template x-for="item in filteredTargets" :key="item.id">
                                <button
                                    type="button"
                                    @click="selectTarget(item)"
                                    class="w-full text-left px-3.5 py-2.5 border-b border-slate-100 dark:border-slate-700/80 last:border-0 hover:bg-brand-50 dark:hover:bg-slate-700/80 transition"
                                    :class="String(item.id) === selectedId && 'bg-brand-50 dark:bg-slate-700/80'"
                                >
                                    <div class="font-semibold text-slate-900 dark:text-white truncate" x-text="item.nama"></div>
                                    <div class="mt-0.5 text-[10px] text-slate-400">
                                        <span class="font-mono" x-text="item.nim || '—'"></span>
                                        <span class="mx-1">·</span>
                                        <span x-text="item.jabatan"></span>
                                    </div>
                                </button>
                            </template>
                            <div x-show="filteredTargets.length === 0" class="px-3.5 py-3 text-center text-slate-400">
                                Panitia tidak ditemukan.
                            </div>
                        </div>
                    </div>

                    <p class="text-[11px] text-slate-400">
                        {{ $targets->count() }} panitia tersedia. Ketik untuk memfilter daftar.
                    </p>
                    @error('user_id')
                        <p class="text-xs text-red-600 dark:text-red-400 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Kategori --}}
                <fieldset class="space-y-2">
                    <legend class="block font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider text-[11px] mb-2">
                        Kategori Hukuman <span class="text-brand-600">*</span>
                    </legend>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                        @foreach ($kategoriOptions as $kategori)
                            @php
                                $checkedClass = match ($kategori) {
                                    'ringan' => 'peer-checked:bg-emerald-100 peer-checked:text-emerald-700 peer-checked:border-emerald-200 dark:peer-checked:bg-emerald-950/50 dark:peer-checked:text-emerald-300 dark:peer-checked:border-emerald-800',
                                    'sedang' => 'peer-checked:bg-amber-100 peer-checked:text-amber-700 peer-checked:border-amber-200 dark:peer-checked:bg-amber-950/50 dark:peer-checked:text-amber-300 dark:peer-checked:border-amber-800',
                                    'berat' => 'peer-checked:bg-orange-100 peer-checked:text-orange-700 peer-checked:border-orange-200 dark:peer-checked:bg-orange-950/50 dark:peer-checked:text-orange-300 dark:peer-checked:border-orange-800',
                                    'khusus' => 'peer-checked:bg-rose-100 peer-checked:text-rose-700 peer-checked:border-rose-200 dark:peer-checked:bg-rose-950/50 dark:peer-checked:text-rose-300 dark:peer-checked:border-rose-800',
                                    default => 'peer-checked:bg-brand-100 peer-checked:text-brand-700 peer-checked:border-brand-200',
                                };
                            @endphp
                            <label class="cursor-pointer">
                                <input
                                    type="radio"
                                    name="kategori"
                                    value="{{ $kategori }}"
                                    class="peer sr-only"
                                    @checked(old('kategori') === $kategori)
                                    @if ($loop->first) required @endif
                                >
                                <span class="flex items-center justify-center min-h-[2.75rem] px-3 py-2 rounded-xl text-[11px] font-bold border text-center transition
                                    bg-slate-50 text-slate-500 border-slate-200
                                    dark:bg-slate-700/40 dark:text-slate-400 dark:border-slate-600
                                    peer-checked:ring-2 peer-checked:ring-offset-1 peer-checked:ring-brand-400/40
                                    hover:border-brand-300 dark:hover:border-brand-500 {{ $checkedClass }}">
                                    {{ ucfirst($kategori) }}
                                </span>
                            </label>
                        @endforeach
                    </div>

                    <p class="text-[11px] text-slate-400 flex items-start gap-1.5 pt-0.5">
                        <svg class="w-3.5 h-3.5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Detail tugas per kategori tidak ditampilkan di web. Panitia mengerjakannya offline sesuai arahan Ranger.
                    </p>

                    @error('kategori')
                        <p class="text-xs text-red-600 dark:text-red-400 font-semibold">{{ $message }}</p>
                    @enderror
                </fieldset>

                {{-- Alasan --}}
                <div class="space-y-1.5">
                    <label for="alasan" class="block font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider text-[11px]">
                        Alasan Hukuman <span class="text-brand-600">*</span>
                    </label>
                    <textarea
                        name="alasan"
                        id="alasan"
                        rows="5"
                        required
                        maxlength="2000"
                        placeholder="Jelaskan pelanggaran atau alasan hukuman secara jelas..."
                        class="form-control-app w-full resize-y min-h-[120px]"
                    >{{ old('alasan') }}</textarea>
                    @error('alasan')
                        <p class="text-xs text-red-600 dark:text-red-400 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Info deadline --}}
                <div class="flex gap-3 p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800">
                    <svg class="w-5 h-5 shrink-0 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-[11px] leading-relaxed text-amber-900 dark:text-amber-100">
                        Target memiliki waktu <strong>2×24 jam (2 hari)</strong> sejak hukuman diterbitkan untuk mengajukan pembelaan dan menyelesaikan tugas.
                    </p>
                </div>

                {{-- Actions --}}
                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2.5 pt-4 border-t border-gray-100 dark:border-slate-700">
                    <a href="{{ route('hukuman.kelola', $mode) }}"
                       class="inline-flex justify-center px-5 py-2.5 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-300 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 transition">
                        Batal
                    </a>
                    <button type="submit"
                            class="inline-flex justify-center items-center gap-2 px-6 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl text-xs shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        Terbitkan Hukuman
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
