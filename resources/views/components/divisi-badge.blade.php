@props([
    'divisi' => null,
    'label' => null,
    'size' => 'sm',
])

@php
    $text = $label ?? $divisi?->nama ?? '—';
    $classes = $divisi instanceof \App\Models\Divisi
        ? $divisi->badgeClasses()
        : \App\Models\Divisi::badgeClassesFor(is_string($divisi) ? $divisi : $divisi?->nama);
    $sizeClass = match ($size) {
        'xs' => 'px-2 py-0.5 text-[10px]',
        'md' => 'px-3 py-1 text-xs',
        default => 'px-2.5 py-0.5 text-[11px]',
    };
@endphp

<span {{ $attributes->class("inline-flex items-center rounded-full font-semibold border {$sizeClass} {$classes}") }}>
    {{ $text }}
</span>
