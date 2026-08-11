<x-guest-layout>
    <div x-data="{
        photoPreview: null,
        previewPhoto(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.photoPreview = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }
    }" class="w-full max-w-md mx-auto font-poppins">

        <div class="bg-white dark:bg-slate-800 shadow-2xl rounded-3xl p-8 border border-gray-100 dark:border-slate-700">
            
            {{-- Header --}}
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-14 h-14 bg-primary-500/10 text-primary-500 rounded-2xl mb-3">
                    <span class="icon-[akar-icons--user-check] text-3xl"></span>
                </div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Lengkapi Profil Anda</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Selamat datang! Karena ini adalah login pertama Anda, harap lengkapi foto profil, nama, email, dan ganti password baru.
                </p>
            </div>

            {{-- Form --}}
            <form method="POST" action="{{ route('initial-setup.store') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf

                {{-- Photo Avatar Upload --}}
                <div class="flex flex-col items-center">
                    <div class="relative group">
                        <template x-if="photoPreview">
                            <img :src="photoPreview" class="w-24 h-24 rounded-full object-cover border-4 border-primary-500 shadow-md">
                        </template>
                        <template x-if="!photoPreview">
                            <div class="w-24 h-24 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center border-4 border-dashed border-slate-300 dark:border-slate-600 text-slate-400">
                                <span class="icon-[akar-icons--camera] text-3xl"></span>
                            </div>
                        </template>
                        <label for="foto" class="absolute inset-0 flex items-center justify-center bg-black/40 text-white rounded-full opacity-0 group-hover:opacity-100 transition cursor-pointer text-xs font-bold">
                            Ubah
                        </label>
                    </div>
                    <input type="file" id="foto" name="foto" accept="image/*" required class="hidden" @change="previewPhoto($event)">
                    <span class="text-xs font-semibold text-primary-500 mt-2 cursor-pointer" onclick="document.getElementById('foto').click()">
                        + Unggah Foto Profil (Wajib)
                    </span>
                    <x-input-error :messages="$errors->get('foto')" class="mt-1" />
                </div>

                {{-- NIM Readonly --}}
                <div>
                    <x-input-label for="nim" :value="__('NIM')" class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300" />
                    <x-text-input id="nim" class="block mt-1 w-full bg-slate-100 dark:bg-slate-700/50 cursor-not-allowed" type="text" :value="$user->nim" readonly />
                </div>

                {{-- Name --}}
                <div>
                    <x-input-label for="name" :value="__('Nama Lengkap')" class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $user->name)" required autofocus autocomplete="name" placeholder="Nama Lengkap Anda" />
                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                </div>

                {{-- Email --}}
                <div>
                    <x-input-label for="email" :value="__('Alamat Email')" class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $user->email)" required autocomplete="email" placeholder="contoh@email.com" />
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>

                {{-- Password --}}
                <div>
                    <x-input-label for="password" :value="__('Password Baru')" class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300" />
                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter" />
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>

                {{-- Password Confirmation --}}
                <div>
                    <x-input-label for="password_confirmation" :value="__('Konfirmasi Password Baru')" class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300" />
                    <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Ketik ulang password baru" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
                </div>

                {{-- Submit --}}
                <div class="pt-2">
                    <button type="submit" class="w-full py-3.5 bg-primary-500 hover:bg-primary-600 text-white font-bold rounded-2xl shadow-lg shadow-primary-500/20 transition cursor-pointer text-sm">
                        Simpan & Masuk Aplikasi
                    </button>
                </div>

            </form>
        </div>
    </div>
</x-guest-layout>
