<x-guest-layout title="Login">
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- NIM  -->
        <div>
            <x-input-label for="nim" :value="__('NIM')" />
            <div class="relative">
                <span class="icon-[tabler--id] text-zinc-400 absolute text-2xl top-1/2 left-2 -translate-y-1/2"></span>
                <x-text-input id="nim" placeholder="Masukkan NIM anda" class="block mt-1 w-full" type="nim"
                    name="nim" :value="old('nim')" required autofocus autocomplete="username" />
            </div>
            <x-input-error :messages="$errors->get('nim')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <div class="relative">
                <span
                    class="icon-[material-symbols--lock-outline] text-zinc-400 absolute text-2xl top-1/2 left-2 -translate-y-1/2"></span>
                <x-text-input id="password" placeholder="Masukkan password anda" class="block mt-1 w-full"
                    type="password" name="password" required autocomplete="current-password" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block my-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                    name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Ingat saya') }}</span>
            </label>
        </div>

        <x-primary-button class="block w-full text-center mb-4">
            {{ __('Log in') }}
        </x-primary-button>

        @if (Route::has('password.request'))
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 block text-center"
                href="{{ route('password.request') }}">
                {{ __('Lupa password?') }}
            </a>
        @endif
    </form>
</x-guest-layout>
