<x-layouts::auth :title="__('Register')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Create an account')" :description="__('Enter your details below to create your account')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-6">
            @csrf
            <!-- Name -->
            <flux:input
                name="name"
                :label="__('Name')"
                :value="old('name')"
                type="text"
                required
                autofocus
                autocomplete="name"
                :placeholder="__('Full name')"
            />

            <!-- Email Address -->
            <!-- <flux:input
                name="email"
                :label="__('Email address')"
                :value="old('email')"
                type="email"
                required
                autocomplete="email"
                placeholder="email@example.com"
            /> -->

            <!-- NIM -->
            <flux:input
                name="nim"
                :label="__('NIM')"
                :value="old('nim')"
                type="number"
                required
                autocomplete="nim"
                placeholder="4444444"
            />

            <!-- Divisi -->
            <flux:select name="divisi_id" :label="__('Divisi')" :placeholder="__('Pilih Divisi')" required>
                @foreach(\App\Models\Divisi::orderBy('nama_divisi')->get() as $divisi)
                    <flux:select.option value="{{ $divisi->id }}">{{ $divisi->nama_divisi }}</flux:select.option>
                @endforeach
            </flux:select>

            <!-- Jabatan -->
            <flux:select name="jabatan_id" :label="__('Jabatan')" :placeholder="__('Pilih Jabatan')" required>
                @foreach(\App\Models\Jabatan::orderBy('nama_jabatan')->get() as $jabatan)
                    <flux:select.option value="{{ $jabatan->id }}">{{ $jabatan->nama_jabatan }}</flux:select.option>
                @endforeach
            </flux:select>

            <!-- Password -->
            <flux:input
                name="password"
                :label="__('Password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Password')"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                viewable
            />

            <!-- Confirm Password -->
            <flux:input
                name="password_confirmation"
                :label="__('Confirm password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Confirm password')"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                viewable
            />

            <div class="flex items-center justify-end">
                <flux:button type="submit" variant="primary" class="w-full" data-test="register-user-button">
                    {{ __('Create account') }}
                </flux:button>
            </div>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            <span>{{ __('Already have an account?') }}</span>
            <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
        </div>
    </div>
</x-layouts::auth>
