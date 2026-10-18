<x-app-layout :$title>
    <div x-data="{
        agreed: false,
        fileName: '',
        fileSize: '',
        darkMode: document.documentElement.classList.contains('dark'),
        toggleTheme() {
            this.darkMode = !this.darkMode;
            if (this.darkMode) {
                document.documentElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            } else {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            }
        },
        handleFileChange(event) {
            const file = event.target.files[0];
            if (file) {
                this.fileName = file.name;
                this.fileSize = file.size > 1024 * 1024 
                    ? (file.size / (1024 * 1024)).toFixed(2) + ' MB' 
                    : (file.size / 1024).toFixed(2) + ' KB';
            } else {
                this.fileName = '';
                this.fileSize = '';
            }
        },
        removeFile() {
            this.fileName = '';
            this.fileSize = '';
            this.$refs.fileInput.value = '';
        }
    }"
        class="bg-slate-50/50 dark:bg-slate-900 min-h-screen pb-12 font-poppins transition-colors duration-300">

        <div class="w-full max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">

            <div class="space-y-6 sm:space-y-8">

                <!-- PAGE HEADER -->
                <div class="flex items-center gap-3 py-3">
                    <a href="{{ route('peserta.tugas-list') }}"
                        class="p-2 -ml-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-200/60 dark:hover:bg-slate-800 transition-colors"
                        title="Kembali ke List Penugasan">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                        </svg>
                    </a>
                    <div>
                        <h2 class="font-bold text-xl sm:text-2xl text-slate-900 dark:text-white tracking-tight">Pengumpulan Tugas</h2>
                        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-medium mt-0.5">
                            Detail instruksi dan pengunggahan berkas PDF
                        </p>
                    </div>
                </div>

                <!-- FLASH ALERTS -->
                @if(session('success'))
                    <div class="p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 rounded-2xl text-xs sm:text-sm flex items-center gap-3 shadow-xs">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="p-4 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 rounded-2xl text-xs sm:text-sm space-y-1.5 shadow-xs">
                        <div class="flex items-center gap-2 font-bold">
                            <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span>Gagal memproses penugasan:</span>
                        </div>
                        <ul class="list-disc pl-6 space-y-0.5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- CONTENT CARD: TASK DETAIL -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 sm:p-7 shadow-sm border border-slate-200/60 dark:border-slate-700/60">
                    <div class="flex items-center justify-between gap-2 mb-3.5 flex-wrap">
                        <div class="flex items-center gap-2">
                            @if($tugas->jenis === 'individu')
                                <span class="bg-rose-500/10 text-rose-600 dark:text-rose-400 text-[11px] font-bold px-2.5 py-0.5 rounded-full border border-rose-500/20">Individu</span>
                            @elseif($tugas->jenis === 'tim')
                                <span class="bg-sky-500/10 text-sky-600 dark:text-sky-400 text-[11px] font-bold px-2.5 py-0.5 rounded-full border border-sky-500/20">Regu / Tim</span>
                            @else
                                <span class="bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[11px] font-bold px-2.5 py-0.5 rounded-full border border-emerald-500/20">Angkatan</span>
                            @endif

                            @if($tugas->day)
                                <span class="bg-amber-500/10 text-amber-600 dark:text-amber-400 text-[10px] font-bold px-2 py-0.5 rounded-md border border-amber-500/20">
                                    {{ $tugas->day }}
                                </span>
                            @endif
                        </div>

                        <div class="text-right text-xs">
                            @if($tugas->tenggat_waktu)
                                <span class="font-medium {{ $isExpired ? 'text-rose-600 dark:text-rose-400 font-bold' : 'text-slate-500 dark:text-slate-400' }}">
                                    Tenggat: {{ $tugas->tenggat_waktu->translatedFormat('d F Y, H:i') }} WIB
                                </span>
                            @else
                                <span class="text-slate-400">Tanpa batas tenggat waktu</span>
                            @endif
                        </div>
                    </div>

                    <h3 class="font-bold text-lg sm:text-xl text-slate-900 dark:text-white mb-3.5">
                        {{ $tugas->judul }}
                    </h3>

                    <!-- NOTICE KHUSUS TIM ATAU ANGKATAN -->
                    @if($tugas->jenis === 'tim')
                        <div class="mb-4 p-3.5 rounded-2xl bg-sky-50 dark:bg-sky-950/40 border border-sky-200 dark:border-sky-800 text-sky-900 dark:text-sky-200 text-xs sm:text-sm flex items-center gap-3">
                            <svg class="w-5 h-5 text-sky-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div>
                                Tugas Regu dikumpulkan oleh <strong>1 perwakilan</strong> atas nama regu:
                                <span class="font-bold underline">{{ $tim?->nama ?? 'Anda belum memiliki regu' }}</span>.
                            </div>
                        </div>
                    @elseif($tugas->jenis === 'angkatan')
                        <div class="mb-4 p-3.5 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-900 dark:text-emerald-200 text-xs sm:text-sm flex items-center gap-3">
                            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div>
                                Tugas Angkatan: Cukup <strong>1 perwakilan angkatan</strong> yang mengunggah tugas ini untuk seluruh peserta.
                            </div>
                        </div>
                    @endif

                    <div class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed space-y-2 border-t border-slate-100 dark:border-slate-700/60 pt-4">
                        <p class="font-semibold text-slate-700 dark:text-slate-200">Deskripsi Tugas:</p>
                        <div class="prose prose-xs sm:prose-sm dark:prose-invert max-w-none text-slate-600 dark:text-slate-300">
                            {!! $tugas->deskripsi ?: '<em>Tidak ada deskripsi penugasan.</em>' !!}
                        </div>
                    </div>
                </div>

                <!-- STATUS SUBMISSION SEBELUMNYA (JIKA SUDAH ADA) -->
                @if($submission)
                    <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 sm:p-7 shadow-sm border border-slate-200/60 dark:border-slate-700/60">
                        <div class="flex items-center justify-between mb-4 border-b border-slate-100 dark:border-slate-700/60 pb-3.5">
                            <h4 class="font-bold text-sm sm:text-base text-slate-900 dark:text-white flex items-center gap-2">
                                <span>Berkas Pengumpulan Anda</span>
                            </h4>
                        </div>

                        <div class="space-y-2.5 text-xs sm:text-sm text-slate-600 dark:text-slate-300 mb-5">
                            <div class="flex items-center justify-between py-1.5 border-b border-slate-50 dark:border-slate-800">
                                <span class="text-slate-400">Pengunggah:</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-100">{{ $submission->original_participant_name ?? $submission->peserta?->nama ?? 'Peserta' }}</span>
                            </div>
                            @if($submission->tim)
                                <div class="flex items-center justify-between py-1.5 border-b border-slate-50 dark:border-slate-800">
                                    <span class="text-slate-400">Asal Regu:</span>
                                    <span class="font-semibold text-slate-800 dark:text-slate-100">{{ $submission->tim->nama }}</span>
                                </div>
                            @endif
                            <div class="flex items-center justify-between py-1.5 border-b border-slate-50 dark:border-slate-800">
                                <span class="text-slate-400">Waktu Pengumpulan:</span>
                                <span class="font-medium text-slate-700 dark:text-slate-300">
                                    {{ $submission->tanggal_pengumpulan ? $submission->tanggal_pengumpulan->translatedFormat('d F Y, H:i') . ' WIB' : '-' }}
                                </span>
                            </div>
                            @if($submission->catatan_peserta)
                                <div class="py-1.5">
                                    <span class="text-slate-400 block mb-1">Catatan Peserta:</span>
                                    <p class="p-3 rounded-xl bg-slate-50 dark:bg-slate-700/50 text-slate-700 dark:text-slate-200 italic">
                                        "{{ $submission->catatan_peserta }}"
                                    </p>
                                </div>
                            @endif
                            @if($submission->catatan_pemeriksa)
                                <div class="p-3.5 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/40 rounded-2xl mt-3">
                                    <span class="font-bold text-amber-800 dark:text-amber-300 block mb-1">Catatan Pemeriksa:</span>
                                    <p class="text-slate-700 dark:text-slate-300 leading-relaxed">{{ $submission->catatan_pemeriksa }}</p>
                                    @if($submission->pemeriksa)
                                        <p class="text-[11px] text-slate-400 mt-1">Oleh: {{ $submission->pemeriksa->nama }}</p>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <!-- ACTION BERKAS SAAT INI -->
                        <div class="flex items-center justify-between gap-3 pt-4 border-t border-slate-100 dark:border-slate-700/60 flex-wrap">
                            <a href="{{ asset('storage/' . ltrim($submission->file_path, '/')) }}" target="_blank"
                                class="inline-flex items-center gap-2 px-4 py-2.5 bg-orange-50 hover:bg-orange-100 dark:bg-orange-950/40 dark:hover:bg-orange-900/50 text-[#FF5B00] rounded-xl text-xs sm:text-sm font-semibold transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>Lihat / Unduh File PDF</span>
                            </a>

                            @if(!$isExpired)
                                <form method="POST" action="{{ route('peserta.tugas-kumpulkan.destroy', $tugas->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus / membatalkan pengumpulan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs sm:text-sm font-semibold text-rose-600 dark:text-rose-400 hover:text-rose-700 hover:underline cursor-pointer">
                                        Batalkan &amp; Hapus File
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- UPLOAD FORM AREA -->
                @if(!$isExpired)
                    <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 sm:p-7 shadow-sm border border-slate-200/60 dark:border-slate-700/60">
                        <h4 class="font-bold text-base sm:text-lg text-slate-900 dark:text-white mb-1.5">
                            {{ $submission ? 'Unggah Ulang / Perbarui Berkas' : 'Form Pengumpulan Berkas' }}
                        </h4>
                        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mb-5">
                            Format berkas harus berupa dokumen <strong>PDF</strong> dengan batas ukuran maksimal <strong>5 MB</strong>.
                        </p>

                        <form method="POST" action="{{ route('peserta.tugas-kumpulkan.store', $tugas->id) }}" enctype="multipart/form-data">
                            @csrf

                            <!-- DRAG & DROP / FILE INPUT -->
                            <div class="mb-5">
                                <label class="block text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                    Pilih File PDF:
                                </label>

                                <!-- Area Klik yang Dipicu lewat Alpine.js ($refs) -->
                                <div @click="$refs.fileInput.click()" class="flex flex-col items-center justify-center p-8 border-2 border-dashed border-slate-300 dark:border-slate-600 hover:border-[#FF5B00] rounded-2xl cursor-pointer bg-slate-50/50 dark:bg-slate-700/30 hover:bg-orange-50/30 transition group">
                                    
                                    <!-- Tampilan Default Sebelum File Dipilih -->
                                    <div class="flex flex-col items-center text-center" x-show="!fileName">
                                        <svg class="w-10 h-10 text-slate-400 group-hover:text-[#FF5B00] transition-colors mb-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                        </svg>
                                        <span class="text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-200 group-hover:text-[#FF5B00]">
                                            Klik untuk memilih berkas PDF
                                        </span>
                                        <span class="text-[11px] text-slate-400 mt-1">Hanya format PDF (Maks. 5MB)</span>
                                    </div>

                                    <!-- Tampilan Preview Setelah File Dipilih -->
                                    <div class="flex items-center justify-between w-full p-3 bg-white dark:bg-slate-800 border border-orange-200 dark:border-orange-900/50 rounded-xl shadow-xs" x-show="fileName" x-cloak>
                                        <div class="flex items-center gap-3 overflow-hidden">
                                            <div class="p-2.5 bg-orange-100 dark:bg-orange-950/60 text-[#FF5B00] rounded-lg shrink-0">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            </div>
                                            <div class="truncate text-left">
                                                <p class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-100 truncate" x-text="fileName"></p>
                                                <p class="text-[11px] text-emerald-600 dark:text-emerald-400 font-medium flex items-center gap-1">
                                                    <span>Berkas sudah terupload</span>
                                                    <span class="text-slate-400">•</span>
                                                    <span x-text="fileSize"></span>
                                                </p>
                                            </div>
                                        </div>
                                        <!-- Tombol Batalkan File (Gunakan @click.stop agar tidak memicu pembukaan file explorer lagi) -->
                                        <button type="button" @click.stop="removeFile()" class="p-1.5 text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 transition" title="Batalkan pilihan file">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>

                                    <!-- Input File Asli (Tersembunyi, dipanggil via $refs.fileInput.click()) -->
                                    <input type="file" name="file_tugas" x-ref="fileInput" accept=".pdf,application/pdf" class="hidden" required
                                        @change="handleFileChange($event)">
                                </div>
                            </div>

                            <!-- CATATAN PESERTA -->
                            <div class="mb-6">
                                <label for="catatan_peserta" class="block text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                    Catatan Pengumpulan (Opsional):
                                </label>
                                <textarea name="catatan_peserta" id="catatan_peserta" rows="3" placeholder="Tuliskan catatan atau keterangan tugas bila ada..."
                                    class="w-full p-3.5 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-2xl text-xs sm:text-sm text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#FF5B00]/40 transition"></textarea>
                            </div>

                            <!-- AGREEMENT CHECKBOX -->
                            <label class="flex items-start gap-3 cursor-pointer mb-6 select-none">
                                <input type="checkbox" x-model="agreed" class="mt-0.5 w-4 h-4 rounded border-orange-400 text-[#FF5B00] focus:ring-[#FF5B00] cursor-pointer">
                                <span class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-snug">
                                    Saya telah memeriksa dan memastikan berkas PDF tugas yang saya unggah sudah benar dan sesuai instruksi.
                                </span>
                            </label>

                            <!-- SUBMIT BUTTON -->
                            <button type="submit" :disabled="!agreed"
                                :class="agreed ? 'bg-[#FF5B00] hover:bg-[#E05000] text-white cursor-pointer shadow-md shadow-orange-500/20 active:scale-95 hover:-translate-y-0.5' : 'bg-slate-200 dark:bg-slate-700 text-slate-400 dark:text-slate-500 cursor-not-allowed'"
                                class="w-full py-3.5 rounded-xl font-semibold text-xs sm:text-sm transition-all duration-200 text-center flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                                <span>{{ $submission ? 'Unggah Ulang Tugas' : 'Kumpulkan Tugas Sekarang' }}</span>
                            </button>
                        </form>
                    </div>
                @else
                    <div class="p-6 rounded-3xl bg-slate-100 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-center shadow-sm">
                        <p class="text-xs sm:text-sm font-semibold text-rose-600 dark:text-rose-400">
                            Tenggat waktu pengumpulan tugas ini telah berakhir.
                        </p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                            Sistem tidak lagi menerima pengunggahan atau perubahan berkas. Hubungi guider regu Anda bila terdapat kendala khusus.
                        </p>
                    </div>
                @endif

            </div>

        </div>
    </div>
</x-app-layout>