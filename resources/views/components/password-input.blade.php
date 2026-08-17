@props([
    'id',
    'name',
    'label',
    'autocomplete' => 'current-password',
    'errorBag' => null,
])

@php
    $messages = $errorBag ?? $errors->get($name);
@endphp

<div x-data="{ show: false }">
    <x-input-label :for="$id" :value="$label" class="font-semibold text-slate-700 dark:text-slate-300" />
    <div class="relative mt-1">
        <input id="{{ $id }}" name="{{ $name }}" :type="show ? 'text' : 'password'"
            {{ $attributes->merge([
                'class' => 'w-full min-h-10 bg-slate-50 dark:bg-slate-700/60 border border-gray-300 dark:border-slate-600 rounded-xl py-2.5 pl-3.5 pr-11 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-brand-500',
                'autocomplete' => $autocomplete,
            ]) }} />
        <button type="button" @click="show = !show"
            class="absolute inset-y-0 right-0 px-3 flex items-center text-slate-400 hover:text-slate-700 dark:hover:text-slate-200"
            :aria-label="show ? 'Sembunyikan password' : 'Lihat password'">
            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <svg x-show="show" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
            </svg>
        </button>
    </div>
    <x-input-error :messages="$messages" class="mt-2" />
</div>
