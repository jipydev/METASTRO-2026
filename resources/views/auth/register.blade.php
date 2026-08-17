<x-guest-layout :$title>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Nama Lengkap')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- NIM -->
        <div class="mt-4">
            <x-input-label for="nim" :value="__('NIM')" />
            <x-text-input id="nim" class="block mt-1 w-full" type="number" name="nim" :value="old('nim')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('nim')" class="mt-2" />
        </div>

        <!-- Role -->
        <div class="mt-4">
            <x-input-label for="role" :value="__('Role')" />
            <select id="role" name="role" required class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-brand-500 dark:focus:border-indigo-600 focus:ring-brand-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                <option value="" disabled selected>-- Pilih Role --</option>
                @foreach($roles as $r)
                    <option value="{{ $r->name }}" {{ old('role') == $r->name ? 'selected' : '' }}>{{ $r->name }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <!-- Divisi -->
        <div class="mt-4">
            <x-input-label for="divisi_id" :value="__('Divisi')" />
            <select id="divisi_id" name="divisi_id" required class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-brand-500 dark:focus:border-indigo-600 focus:ring-brand-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                <option value="" disabled selected>-- Pilih Divisi --</option>
                @foreach($divisis as $d)
                    <option value="{{ $d->id }}" {{ old('divisi_id') == $d->id ? 'selected' : '' }}>{{ $d->nama_divisi }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('divisi_id')" class="mt-2" />
        </div>

        <!-- Jabatan -->
        <div class="mt-4">
            <x-input-label for="jabatan_id" :value="__('Jabatan')" />
            <select id="jabatan_id" name="jabatan_id" required class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-brand-500 dark:focus:border-indigo-600 focus:ring-brand-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                <option value="" disabled selected>-- Pilih Jabatan --</option>
                @foreach($jabatans as $j)
                    <option value="{{ $j->id }}" {{ old('jabatan_id') == $j->id ? 'selected' : '' }}>{{ $j->nama_jabatan }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('jabatan_id')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-6">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
