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
                'bg-emerald-600/90 border-emerald-500 text-white backdrop-blur-md'"
            style="display: none;">
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
        <a href="{{ route('dashboard') }}" @click="stopCamera()"
            class="absolute top-6 left-6 z-20 px-4 py-2 bg-slate-900/80 hover:bg-slate-800 text-white text-xs font-semibold rounded-xl border border-slate-700 backdrop-blur-md shadow-lg transition">
            &larr; Dashboard
        </a>

        {{-- Tombol Manual Snapshot --}}
        <button @click="takeSnapshot()" :disabled="isLoading"
            class="absolute bottom-10 z-20 px-8 py-3.5 bg-white hover:bg-slate-100 text-slate-900 font-bold text-sm rounded-full shadow-lg active:scale-95 transition flex items-center gap-2 disabled:opacity-60 cursor-pointer">
            <span x-show="!isLoading">📷 Scan Manual</span>
            <span x-show="isLoading" class="animate-pulse">⏳ Memproses Data...</span>
        </button>

        {{-- ============================================================= --}}
        {{-- MODAL POPUP HASIL SCAN QR                                     --}}
        {{-- ============================================================= --}}
        <div x-show="showPopup" style="display: none;"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4">
            <div @click.away="denyScan()" x-show="showPopup" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                class="bg-slate-900 border border-slate-700 rounded-3xl shadow-2xl max-w-sm w-full p-6 sm:p-7 text-center relative font-poppins">

                {{-- Foto User --}}
                <div class="mx-auto w-24 h-24 mb-4 rounded-full p-1 bg-slate-600 shadow-md">
                    <img :src="scanData.photo"
                        class="w-full h-full rounded-full object-cover border-2 border-slate-900" alt="Foto Profil" />
                </div>

                {{-- Detail Identitas --}}
                <p class="text-[11px] font-mono text-slate-400 font-semibold tracking-wider uppercase mb-0.5"
                    x-text="'NIM: ' + scanData.nim"></p>
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
                isLoading: false,
                isSaving: false,
                isScanning: false,
                stream: null,
                kegiatanId: {{ $kegiatanAktif?->id ?? 'null' }}, // <-- Ubah jadi dinamis seperti ini
                scanData: {
                    id: '',
                    nama: '',
                    nim: '',
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

                    this.fetchDataFromBackend(qrPayload.token);
                },

                async fetchDataFromBackend(token) {
                    this.isLoading = true;

                    try {
                        const response = await fetch("{{ route('api.scan.lookup') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                token: token
                            })
                        });

                        const result = await response.json();

                        if (!response.ok || !result.success) {
                            this.showToast(result.message || "Data pengguna tidak ditemukan.", "error");
                            this.resumeScanning();
                            return;
                        }

                        this.scanData = {
                            id: result.data.id,
                            nama: result.data.nama,
                            nim: result.data.nim,
                            divisi: result.data.divisi,
                            photo: result.data.photo
                        };

                        this.showPopup = true;
                    } catch (error) {
                        this.showToast("Gagal menghubungi server. Periksa koneksi internet.", "error");
                        this.resumeScanning();
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
                        const response = await fetch("{{ route('api.scan.store') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                user_id: this.scanData.id,
                                kegiatan_id: this.kegiatanId
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
