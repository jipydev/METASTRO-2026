@props(['disabled' => false])

<input @disabled($disabled)
    {{ $attributes->merge(['class' => 'p-2 ps-10  placeholder-zinc-400 rounded-md border  focus:border-primary-500 focus:ring-2 outline-none focus:ring-primary-500']) }}>
