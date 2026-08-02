<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Scan QR Panitia</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
</head>
<body class="bg-black overflow-hidden select-none">
    
    <div x-data="scannerApp()" x-init="initScanner()" class="relative w-screen h-screen flex justify-center items-center">

        <div x-show="toast.show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-6"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-6"
             class="fixed top-6 left-1/2 transform -translate-x-1/2 z-100 w-11/12 max-w-sm px-5 py-4 rounded-xl shadow-2xl flex items-center justify-center gap-3 text-center"
             :class="toast.type === 'error' ? 'bg-red-500 text-white' : 'bg-[#065E75] text-[#EFF8FF]'"
             style="display: none;">
             <span x-text="toast.message" class="text-sm font-bold tracking-wide"></span>
        </div>

        <video x-ref="video" class="absolute inset-0 w-full h-full object-cover" autoplay playsinline></video>

        <div class="absolute inset-0 z-10 flex items-center justify-center pointer-events-none">
            <div class="w-64 h-64 border-2 border-white/50 border-dashed rounded-xl relative flex justify-center items-center">
                <span class="text-white/50 text-sm font-semibold tracking-widest uppercase">Arahkan QR</span>
            </div>
        </div>

        <a href="{{ route('dashboard') }}" class="absolute top-8 left-6 z-20 px-4 py-2 bg-[#eff8ff] backdrop-blur-md text-[#065E75] font-bold rounded-lg border border-white/20">
            &larr; Kembali
        </a>

        <button @click="takeSnapshot()" :disabled="isLoading" 
                class="absolute bottom-12 z-20 px-10 py-4 bg-[#eff8ff] backdrop-blur-md text-[#065E75] font-extrabold text-lg rounded-full shadow-[0_0_20px_rgba(255,255,255,0.4)] active:scale-95 transition-all flex items-center gap-3 disabled:opacity-70 disabled:scale-100">
            <span x-show="!isLoading">📸 TAKE</span>
            <span x-show="isLoading" class="animate-pulse">⏳ MENCARI DATA...</span>
        </button>

        <div x-show="showPopup" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-6">
            <div @click.away="denyScan()" 
                 x-show="showPopup" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-8"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="bg-[#eff8ff] rounded-3xl shadow-2xl max-w-sm w-full p-8 text-center relative">
                
                <div class="mx-auto w-32 h-32 mb-5 rounded-full p-1 bg-[#065E75]">
                    <img :src="scanData.photo" class="w-full h-full rounded-full object-cover border-4 border-[#eff8ff]" alt="Profile" />
                </div>
                
                <p class="text-xs font-bold text-gray-400 tracking-widest uppercase mb-1">ID: <span x-text="scanData.id"></span></p>
                <h3 class="text-2xl font-bold text-black mb-1" x-text="scanData.nama"></h3>
                <p class="text-md font-semibold text-[#065E75] mb-8" x-text="scanData.divisi"></p>
                
                <div class="flex gap-3 mt-4">
                    <button @click="denyScan()" :disabled="isSaving" class="flex-1 px-4 py-3 bg-red-100 text-red-600 font-bold rounded-xl hover:bg-red-200 transition-colors disabled:opacity-50">
                        ❌ TOLAK
                    </button>
                    
                    <button @click="acceptScan()" :disabled="isSaving" class="flex-1 px-4 py-3 bg-[#065E75] text-[#eff8ff] font-bold rounded-xl hover:bg-[#065E75]/80 transition-colors disabled:opacity-50 flex justify-center items-center">
                        <span x-show="!isSaving">✅ TERIMA</span>
                        <span x-show="isSaving" class="animate-pulse">⌛MENYIMPAN...</span>
                    </button>
                </div>
            </div>
        </div>

    </div>

    <script>
        function scannerApp() {
            return {
                showPopup: false,
                isLoading: false,
                isSaving: false,
                isScanning: false,
                stream: null,
                scanData: { id: '', nama: '', divisi: '', photo: '' },
                
                toast: { show: false, message: '', type: 'error' },

                hiddenCanvas: null,
                hiddenContext: null,

                initScanner() {
                    this.hiddenCanvas = document.createElement('canvas');
                    this.hiddenContext = this.hiddenCanvas.getContext('2d', { willReadFrequently: true });
                    
                    this.startCamera();
                },

                startCamera() {
                    navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } })
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

                tick() {
                    if (!this.isScanning) return;

                    let video = this.$refs.video;

                    if (video.readyState === video.HAVE_ENOUGH_DATA) {
                        this.hiddenCanvas.width = video.videoWidth;
                        this.hiddenCanvas.height = video.videoHeight;
                        
                        this.hiddenContext.drawImage(video, 0, 0, this.hiddenCanvas.width, this.hiddenCanvas.height);
                        let imageData = this.hiddenContext.getImageData(0, 0, this.hiddenCanvas.width, this.hiddenCanvas.height);
                        
                        let code = jsQR(imageData.data, imageData.width, imageData.height, {
                            inversionAttempts: "dontInvert",
                        });

                        if (code) {
                            this.isScanning = false
                            let idPanitia = code.data;
                            this.fetchDataFromBackend(idPanitia);
                        }
                    }

                    if (this.isScanning) {
                        requestAnimationFrame(() => this.tick());
                    }
                },

                takeSnapshot() {
                    if(this.isLoading) return;
                    
                    let video = this.$refs.video;
                    this.hiddenCanvas.width = video.videoWidth;
                    this.hiddenCanvas.height = video.videoHeight;
                    
                    this.hiddenContext.drawImage(video, 0, 0, this.hiddenCanvas.width, this.hiddenCanvas.height);
                    let imageData = this.hiddenContext.getImageData(0, 0, this.hiddenCanvas.width, this.hiddenCanvas.height);
                    
                    let code = jsQR(imageData.data, imageData.width, imageData.height);

                    if (code) {
                        this.isScanning = false;
                        let idPanitia = code.data;
                        this.fetchDataFromBackend(idPanitia);
                    } else {
                        this.showToast("QR Code tidak terbaca. Pastikan fokus dan coba lagi.", "error");
                    }
                },

                async fetchDataFromBackend(id) {
                    this.isLoading = true;

                    //------------------------ buat ngetest aja, nanti diganti pake fetch ke backend ------------------------
                    await new Promise(resolve => setTimeout(resolve, 800)); 
                    
                    this.scanData = {
                        id: id,
                        nama: "Azmil Monitor",
                        divisi: "Divisi Monitor Panitia",
                        photo: "https://ui-avatars.com/api/?size=256&background=0D8ABC&color=fff&name=Azmil+Monitor"
                    };
                    // ---------------------------------------------
                    
                    this.isLoading = false;
                    this.showPopup = true;
                },

                denyScan() {
                    if(this.isSaving) return;
                    this.showToast("Data absen dibatalkan.", "error");
                    this.closePopup();
                },

                async acceptScan() {
                    this.isSaving = true;

                    //------------------------ buat ngetest aja, nanti diganti pake fetch ke backend ------------------------
                    await new Promise(resolve => setTimeout(resolve, 800));
                    // ----------------------------------------------

                    this.isSaving = false;
                    
                    this.showToast("Berhasil! Data absen disimpan.", "success");
                    this.closePopup();
                },

                closePopup() {
                    this.showPopup = false;
                    this.scanData = { id: '', nama: '', divisi: '', photo: '' };
                    
                    setTimeout(() => {
                        this.isScanning = true;
                        requestAnimationFrame(() => this.tick());
                    }, 1500);
                },

                showToast(message, type = 'error') {
                    this.toast.message = message;
                    this.toast.type = type;
                    this.toast.show = true;
                    
                    setTimeout(() => {
                        this.toast.show = false;
                    }, 3000);
                }
            }
        }
    </script>
</body>
</html>