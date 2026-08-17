@php
    $value = strtolower((string) $status);
    $labels = [
        'pending' => 'Pending',
        'approved' => 'Disetujui',
        'rejected' => 'Ditolak',
        'diproses' => 'Proses Ranger',
    ];
    $label = $labels[$value] ?? ucfirst($value);

    if (! empty($final)) {
        $classes = match ($value) {
            'pending' => 'bg-amber-500 text-white',
            'diproses' => 'bg-sky-500 text-white',
            'approved' => 'bg-emerald-600 text-white',
            default => 'bg-red-600 text-white',
        };
        $label = match ($value) {
            'pending' => 'Menunggu Review',
            'diproses' => 'Proses Ranger',
            'approved' => 'Disetujui',
            default => 'Ditolak',
        };
    } else {
        $classes = match ($value) {
            'pending' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300 border border-amber-200 dark:border-amber-800',
            'approved' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800',
            'diproses' => 'bg-sky-50 text-sky-700 dark:bg-sky-950/40 dark:text-sky-300 border border-sky-200 dark:border-sky-800',
            default => 'bg-red-50 text-red-700 dark:bg-red-950/40 dark:text-red-300 border border-red-200 dark:border-red-800',
        };
    }
@endphp
<span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold {{ $classes }}">{{ $label }}</span>
