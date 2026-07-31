@props(['disabled' => false])

<input @disabled($disabled)
    {{ $attributes->merge(['class' => 'p-2 lg:py-4 lg:ps-12 ps-10 placeholder-zinc-400 rounded-md outline-none ring ring-zinc-400 focus:ring-2 focus:ring-primary-500']) }}>
