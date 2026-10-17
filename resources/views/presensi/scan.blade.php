<!DOCTYPE html>
<html lang="id" class="h-full bg-black">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ ($title ?? 'Scanner Presensi') }} - {{ config('app.name') }}</title>

    {{-- Anti-FOUC & Asset Bundling --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- jsQR Parser --}}
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
</head>

<body class="h-full bg-black overflow-hidden select-none font-poppins text-slate-100">

    <div x-data="scannerApp()" x-init="initScanner()"
        class="relative w-screen h-screen flex justify-center items-center">

        {{-- Toast Floating Notification --}}
        <div x-show="toast.show" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 -translate-y-6" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-6"
            class="fixed top-6 left-1/2 transform -translate-x-1/2 z-50 w-11/12 max-w-sm px-5 py-3.5 rounded-2xl shadow-2xl flex items-center justify-center gap-2 text-center border"
            :class="toast.type === 'error' ? 'bg-red-600/90 border-red-500 text-white backdrop-blur-md' :
                'bg-emerald-600/90 border-emerald-500 text-white backdrop-blur-md'" style="display: none;">
            <span x-text="toast.type === 'error' ? '⚠️' : '✅'"></span>
            <span x-text="toast.message" class="text-xs sm:text-sm font-semibold tracking-wide"></span>
        </div>

        {{-- Video Element Kamera --}}
        <video x-ref="video" class="absolute inset-0 w-full h-full object-cover" autoplay playsinline muted></video>

        {{-- Overlay Target Scanner --}}
        <div class="absolute inset-0 z-10 flex flex-col items-center justify-center pointer-events-none p-4">
            <div
                class="w-64 h-64 sm:w-72 sm:h-72 border-2 border-brand-500 border-dashed rounded-3xl relative flex flex-col justify-center items-center shadow-[0_0_25px_rgba(254,90,29,0.35)] bg-slate-900/20 backdrop-blur-[1px]">
                <div class="w-full h-0.5 bg-slate-400 absolute top-0 animate-pulse"></div>
                <span
                    class="text-white/80 text-xs font-bold tracking-widest uppercase bg-black/40 px-3 py-1.5 rounded-full border border-white/10">
                    Arahkan QR Code
                </span>
            </div>

            @if (isset($kegiatanAktif) && $kegiatanAktif)
                <div
                    class="mt-6 px-4 py-2 bg-slate-900/80 backdrop-blur-md rounded-xl border border-slate-700 text-center max-w-xs">
                    <p class="text-[10px] text-gray-400 uppercase font-semibold">Sesi Kegiatan Aktif</p>
                    <p class="text-xs font-bold text-white truncate">{{ $kegiatanAktif->nama }}</p>
                </div>
            @endif
        </div>

        {{-- Tombol Navigasi Kembali --}}
        <a href="{{ route('dashboard.index') }}" @click="stopCamera()"
            class="absolute top-6 left-6 z-20 px-4 py-2 bg-slate-900/80 hover:bg-slate-800 text-white text-xs font-semibold rounded-xl border border-slate-700 backdrop-blur-md shadow-lg transition">
            &larr; Dashboard
        </a>

        {{-- Action Buttons: Snapshot & Input Kode Unik --}}
        <div class="absolute bottom-8 z-20 flex items-center justify-center gap-3 w-full max-w-md px-4">
            <button @click="takeSnapshot()" :disabled="isLoading"
                class="flex-1 py-3 px-4 bg-white/90 hover:bg-white text-slate-900 font-bold text-xs sm:text-sm rounded-2xl shadow-lg active:scale-95 transition flex items-center justify-center gap-2 disabled:opacity-60 cursor-pointer backdrop-blur-xs">
                <span>📷 Snapshot</span>
            </button>

            <button @click="openManualModal()" :disabled="isLoading"
                class="flex-1 py-3 px-4 bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs sm:text-sm rounded-2xl shadow-lg active:scale-95 transition flex items-center justify-center gap-2 disabled:opacity-60 cursor-pointer">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                <span>Input Kode Unik</span>
            </button>
        </div>

        {{-- ============================================================= --}}
        {{-- MODAL INPUT MANUAL KODE UNIK (LUPA HP / KAMERA ERROR) --}}
        {{-- ============================================================= --}}
        <div x-show="showManualModal" style="display: none;"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4">
            <div @click.away="closeManualModal()" x-show="showManualModal"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                class="bg-slate-900 border border-slate-700 rounded-3xl shadow-2xl max-w-sm w-full p-6 sm:p-7 text-center relative font-poppins">

                <div
                    class="w-12 h-12 mx-auto mb-3 bg-brand-500/20 text-brand-400 rounded-2xl flex items-center justify-center border border-brand-500/30">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                    </svg>
                </div>

                <h3 class="text-base sm:text-lg font-bold text-white mb-1">Presensi Kode Unik</h3>
                <p class="text-xs text-slate-400 mb-5 leading-relaxed">
                    Masukkan <strong>Kode Unik</strong></p>

                <form @submit.prevent="submitManualCode()">
                    <input type="text" x-model="manualInputCode" x-ref="manualInput" placeholder="Contoh: MTR-7A3B"
                        class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-center text-white font-mono text-base font-bold placeholder-slate-500 focus:outline-hidden focus:border-brand-500 uppercase tracking-widest mb-4 transition"
                        autofocus />

                    <div class="flex gap-2.5">
                        <button type="button" @click="closeManualModal()"
                            class="flex-1 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold rounded-xl transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" :disabled="isLoading || !manualInputCode.trim()"
                            class="flex-1 py-2.5 bg-brand-600 hover:bg-brand-500 text-white text-xs font-semibold rounded-xl shadow-sm transition disabled:opacity-50 flex justify-center items-center cursor-pointer">
                            <span x-show="!isLoading">Cari & Presensi</span>
                            <span x-show="isLoading" class="animate-pulse">Mencari...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ============================================================= --}}
        {{-- MODAL POPUP HASIL SCAN QR --}}
        {{-- ============================================================= --}}
        <div x-show="showPopup" style="display: none;"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4">
            <div @click.away="denyScan()" x-show="showPopup" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                class="bg-slate-900 border border-slate-700 rounded-3xl shadow-2xl max-w-sm w-full p-6 sm:p-7 text-center relative font-poppins">

                {{-- Foto User --}}
                <div class="mx-auto w-24 h-24 mb-4 rounded-full p-1 bg-slate-600 shadow-md">
                    <img :src="scanData.photo" class="w-full h-full rounded-full object-cover border-2 border-slate-900"
                        alt="Foto Profil" />
                </div>

                {{-- Detail Identitas --}}
                <div class="flex flex-col items-center justify-center gap-1.5 mb-2">
                    <template x-if="inputSource === 'manual'">
                        <span
                            class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-amber-500/20 text-amber-300 border border-amber-500/30 rounded-full text-[10px] font-semibold tracking-wide">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Input Manual (Kode Unik)
                        </span>
                    </template>
                    <div class="flex items-center justify-center gap-2">
                        <p class="text-[11px] font-mono text-slate-400 font-semibold tracking-wider uppercase"
                            x-text="'NIM: ' + scanData.nim"></p>
                        <template x-if="scanData.kode_unik">
                            <span
                                class="px-2 py-0.5 bg-brand-500/20 text-brand-400 border border-brand-500/30 rounded-md text-[10px] font-mono font-bold"
                                x-text="scanData.kode_unik"></span>
                        </template>
                    </div>
                </div>

                <h3 class="text-lg font-bold text-white mb-1 truncate" x-text="scanData.nama"></h3>
                <p class="text-xs font-medium text-slate-400 mb-6 truncate" x-text="scanData.divisi"></p>

                {{-- Tombol Aksi --}}
                <div class="flex gap-2.5">
                    <button @click="denyScan()" :disabled="isSaving"
                        class="flex-1 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold rounded-xl transition disabled:opacity-50 cursor-pointer">
                        Batalkan
                    </button>

                    <button @click="acceptScan()" :disabled="isSaving"
                        class="flex-1 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl shadow-sm transition disabled:opacity-50 flex justify-center items-center cursor-pointer">
                        <span x-show="!isSaving">Konfirmasi Hadir</span>
                        <span x-show="isSaving" class="animate-pulse">Menyimpan...</span>
                    </button>
                </div>
            </div>
        </div>

    </div>

    {{-- Logika Scanner Alpine.js --}}
    <script>
        function scannerApp() {
            return {
                showPopup: false,
                showManualModal: false,
                manualInputCode: '',
                inputSource: 'scan',
                isLoading: false,
                isSaving: false,
                isScanning: false,
                stream: null,
                kegiatanId: {{ $kegiatanAktif?->id ?? 'null' }}, // <-- Ubah jadi dinamis seperti ini
                scanData: {
                    id: '',
                    nama: '',
                    nim: '',
                    kode_unik: '',
                    divisi: '',
                    photo: ''
                },
                toast: {
                    show: false,
                    message: '',
                    type: 'error'
                },

                hiddenCanvas: null,
                hiddenContext: null,

                openManualModal() {
                    this.isScanning = false;
                    this.inputSource = 'manual';
                    this.showManualModal = true;
                    this.manualInputCode = '';
                    this.$nextTick(() => {
                        if (this.$refs.manualInput) {
                            this.$refs.manualInput.focus();
                        }
                    });
                },

                closeManualModal() {
                    this.showManualModal = false;
                    this.manualInputCode = '';
                    this.inputSource = 'scan';
                    this.resumeScanning();
                },

                async submitManualCode() {
                    const code = this.manualInputCode.trim();
                    if (!code) return;

                    this.inputSource = 'manual';
                    await this.fetchDataFromBackend(null, code);
                    if (this.scanData.id) {
                        this.showManualModal = false;
                    }
                },

                initScanner() {
                    this.hiddenCanvas = document.createElement('canvas');
                    this.hiddenContext = this.hiddenCanvas.getContext('2d', {
                        willReadFrequently: true
                    });
                    this.startCamera();
                },

                startCamera() {
                    navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: "environment"
                        }
                    })
                        .then(stream => {
                            this.stream = stream;
                            this.$refs.video.srcObject = stream;
                            this.$refs.video.setAttribute("playsinline", true);
                            this.$refs.video.play();

                            this.isScanning = true;
                            requestAnimationFrame(() => this.tick());
                        })
                        .catch(err => {
                            this.showToast("Kamera tidak diizinkan atau tidak ditemukan.", "error");
                        });
                },

                stopCamera() {
                    if (this.stream) {
                        this.stream.getTracks().forEach(track => track.stop());
                    }
                },

                tick() {
                    if (!this.isScanning) return;

                    let video = this.$refs.video;

                    if (video && video.readyState === video.HAVE_ENOUGH_DATA) {
                        this.hiddenCanvas.width = video.videoWidth;
                        this.hiddenCanvas.height = video.videoHeight;

                        this.hiddenContext.drawImage(video, 0, 0, this.hiddenCanvas.width, this.hiddenCanvas.height);
                        let imageData = this.hiddenContext.getImageData(0, 0, this.hiddenCanvas.width, this.hiddenCanvas
                            .height);

                        let code = jsQR(imageData.data, imageData.width, imageData.height, {
                            inversionAttempts: "dontInvert",
                        });

                        if (code) {
                            this.isScanning = false;
                            this.processQrData(code.data);
                        }
                    }

                    if (this.isScanning) {
                        requestAnimationFrame(() => this.tick());
                    }
                },

                takeSnapshot() {
                    if (this.isLoading) return;

                    let video = this.$refs.video;
                    if (!video) return;

                    this.hiddenCanvas.width = video.videoWidth;
                    this.hiddenCanvas.height = video.videoHeight;

                    this.hiddenContext.drawImage(video, 0, 0, this.hiddenCanvas.width, this.hiddenCanvas.height);
                    let imageData = this.hiddenContext.getImageData(0, 0, this.hiddenCanvas.width, this.hiddenCanvas
                        .height);
                    let code = jsQR(imageData.data, imageData.width, imageData.height);

                    if (code) {
                        this.isScanning = false;
                        this.processQrData(code.data);
                    } else {
                        this.showToast("QR Code tidak terbaca. Pastikan QR berada di area fokus.", "error");
                    }
                },

                processQrData(rawData) {
                    let qrPayload;
                    try {
                        qrPayload = JSON.parse(rawData);
                    } catch (e) {
                        this.showToast("Format QR Code tidak dikenali.", "error");
                        this.resumeScanning();
                        return;
                    }

                    if (!qrPayload.token) {
                        this.showToast("Token QR Code tidak valid.", "error");
                        this.resumeScanning();
                        return;
                    }

                    this.inputSource = 'scan';
                    this.fetchDataFromBackend(qrPayload.token);
                },

                async fetchDataFromBackend(token = null, kodeUnik = null) {
                    this.isLoading = true;

                    try {
                        const payload = {};
                        if (token) payload.token = token;
                        if (kodeUnik) payload.kode_unik = kodeUnik;

                        const response = await fetch("{{ route('dashboard.api.scan.lookup') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify(payload)
                        });

                        const result = await response.json();

                        if (!response.ok || !result.success) {
                            this.showToast(result.message || "Data pengguna tidak ditemukan.", "error");
                            if (!this.showManualModal) {
                                this.resumeScanning();
                            }
                            return;
                        }

                        this.scanData = {
                            id: result.data.id,
                            nama: result.data.nama,
                            nim: result.data.nim,
                            kode_unik: result.data.kode_unik,
                            divisi: result.data.divisi,
                            photo: result.data.photo
                        };

                        this.showPopup = true;
                    } catch (error) {
                        this.showToast("Gagal menghubungi server. Periksa koneksi internet.", "error");
                        if (!this.showManualModal) {
                            this.resumeScanning();
                        }
                    } finally {
                        this.isLoading = false;
                    }
                },

                denyScan() {
                    if (this.isSaving) return;
                    this.showToast("Presensi dibatalkan.", "error");
                    this.closePopup();
                },

                async acceptScan() {
                    this.isSaving = true;

                    try {
                        const response = await fetch("{{ route('dashboard.api.scan.store') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                user_id: this.scanData.id,
                                kegiatan_id: this.kegiatanId,
                                sumber: this.inputSource || 'scan'
                            })
                        });

                        const result = await response.json();

                        if (!response.ok || !result.success) {
                            this.showToast(result.message || "Gagal mencatat presensi.", "error");
                        } else {
                            this.showToast(result.message || "Presensi berhasil dicatat!", "success");
                        }
                    } catch (error) {
                        this.showToast("Gagal menghubungi server saat menyimpan.", "error");
                    } finally {
                        this.isSaving = false;
                        this.closePopup();
                    }
                },

                closePopup() {
                    this.showPopup = false;
                    this.scanData = {
                        id: '',
                        nama: '',
                        nim: '',
                        kode_unik: '',
                        divisi: '',
                        photo: ''
                    };
                    this.resumeScanning();
                },

                resumeScanning() {
                    setTimeout(() => {
                        this.isScanning = true;
                        requestAnimationFrame(() => this.tick());
                    }, 1200);
                },

                showToast(message, type = 'error') {
                    this.toast.message = message;
                    this.toast.type = type;
                    this.toast.show = true;

                    setTimeout(() => {
                        this.toast.show = false;
                    }, 3200);
                }
            }
        }
    </script>
</body>

</html>