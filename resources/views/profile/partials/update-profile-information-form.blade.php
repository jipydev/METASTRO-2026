<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Informasi Profil') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Ubah informasi profil dan alamat email akun Anda.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div x-data="{ previewUrl: '{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : '' }}' }">
            <div class="mt-4 flex justify-center">
                <label for="profile-picture"
                    class="mt-2 flex h-32 w-32 cursor-pointer items-center justify-center overflow-hidden rounded-full border-2 border-dashed border-primary-700 text-primary-700">
                    <img x-show="previewUrl" :src="previewUrl" alt="Profile preview"
                        class="h-full w-full rounded-full object-cover" />
                    <span x-show="!previewUrl" class="icon-[material-symbols--camera] text-4xl"></span>
                </label>
            </div>
            <x-text-input id="profile-picture" name="profile-picture" type="file" accept="image/*" class="hidden"
                required autofocus
                x-on:change="const input = $event.target; if (input.files && input.files[0]) { const reader = new FileReader(); reader.onload = e => previewUrl = e.target.result; reader.readAsDataURL(input.files[0]); } else { previewUrl = ''; }" />
            <x-input-error class="mt-2" :messages="$errors->get('profile-picture')" />
        </div>

        <div>
            <x-input-label for="name" :value="__('Nama')" />
            <div class="relative">
                <span class="icon-[mdi--user] absolute left-2 top-2 text-2xl text-primary-700"></span>
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)"
                    required autofocus autocomplete="name" />
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <div class="relative">
                <span class="icon-[mdi--email] absolute left-2 top-2 text-2xl text-primary-700"></span>
                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)"
                    required autocomplete="username" />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                    <div>
                        <p class="text-sm mt-2 text-gray-800">
                            {{ __('Your email address is unverified.') }}

                            <button form="send-verification"
                                class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                {{ __('Click here to re-send the verification email.') }}
                            </button>
                        </p>

                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-2 font-medium text-sm text-green-600">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Simpan') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
