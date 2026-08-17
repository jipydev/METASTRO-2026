@php
    $appNotifications = $appNotifications ?? collect();
    $appUnreadNotificationCount = $appUnreadNotificationCount ?? 0;
@endphp

<div class="relative" x-data="{ openNotif: false }" @keydown.escape.window="openNotif = false">
    <button type="button" @click="openNotif = !openNotif"
        class="relative p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/60 transition"
        :aria-expanded="openNotif" aria-label="Notifikasi">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2c0 .53-.21 1.04-.59 1.41L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        @if ($appUnreadNotificationCount > 0)
            <span class="absolute top-1.5 right-1.5 min-w-4 h-4 px-1 rounded-full bg-brand-500 text-white text-[9px] font-bold leading-4 text-center">
                {{ $appUnreadNotificationCount > 9 ? '9+' : $appUnreadNotificationCount }}
            </span>
        @endif
    </button>

    <div x-show="openNotif" x-cloak @click.outside="openNotif = false"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-1"
        class="absolute right-0 top-full mt-2 w-[min(22rem,calc(100vw-2rem))] bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700 shadow-xl z-50 overflow-hidden">
        <div class="flex items-center justify-between gap-2 px-4 py-3 border-b border-slate-100 dark:border-slate-700">
            <p class="text-xs font-bold text-slate-900 dark:text-white">Notifikasi</p>
            @if ($appUnreadNotificationCount > 0)
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    <button type="submit" class="text-[11px] font-semibold text-brand-600 dark:text-brand-400 hover:underline">
                        Tandai semua dibaca
                    </button>
                </form>
            @endif
        </div>

        <div class="max-h-80 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-700">
            @forelse ($appNotifications as $notification)
                @php
                    $data = $notification->data;
                    $unread = is_null($notification->read_at);
                @endphp
                <a href="{{ route('notifications.show', $notification->id) }}"
                    class="block px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition {{ $unread ? 'bg-brand-50/60 dark:bg-brand-950/30' : '' }}">
                    <div class="flex items-start gap-2.5">
                        @if ($unread)
                            <span class="mt-1.5 h-1.5 w-1.5 rounded-full bg-brand-500 shrink-0"></span>
                        @else
                            <span class="mt-1.5 h-1.5 w-1.5 rounded-full bg-transparent shrink-0"></span>
                        @endif
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-slate-900 dark:text-white leading-snug">
                                {{ $data['title'] ?? 'Notifikasi' }}
                            </p>
                            <p class="text-[11px] text-slate-600 dark:text-slate-300 mt-0.5 leading-snug">
                                {{ $data['message'] ?? '' }}
                            </p>
                            <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1">
                                {{ $notification->created_at?->locale('id')->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                </a>
            @empty
                <div class="px-4 py-8 text-center">
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Belum ada notifikasi</p>
                    <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">Update presensi, izin, pengumuman, dan kegiatan akan muncul di sini.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
