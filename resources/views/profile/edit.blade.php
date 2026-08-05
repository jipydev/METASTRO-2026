<x-app-layout>
    <div class="py-8 font-poppins min-h-screen bg-gray-100 dark:bg-slate-900 transition-colors duration-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Page Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-primary-600 dark:text-primary-400">Pengaturan Profil</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Kelola informasi pribadi, keamanan akun, dan preferensi Anda</p>
                </div>
                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center gap-1.5 text-slate-600 dark:text-slate-400 hover:text-primary-600 dark:hover:text-primary-400 font-semibold text-sm transition">
                    &larr; Kembali ke Dashboard
                </a>
            </div>

            <!-- Main Content: 2-Column Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Left Column: User Summary Card -->
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-slate-700 text-center relative overflow-hidden">
                        <!-- Top Accent Banner -->
                        <div class="h-20 bg-gradient-to-r from-primary-500 to-amber-500 -mx-6 -mt-6 mb-12 relative">
                        </div>

                        <!-- User Avatar -->
                        <div class="relative -mt-20 mb-4 inline-block">
                            @php
                                $photoUrl = $user->foto
                                    ? asset('storage/' . $user->foto)
                                    : 'https://ui-avatars.com/api/?size=256&background=fe5a1d&color=fff&name=' . urlencode($user->name);
                            @endphp
                            <img src="{{ $photoUrl }}" alt="{{ $user->name }}" class="w-28 h-28 rounded-full border-4 border-white dark:border-slate-800 object-cover shadow-md mx-auto">
                            <span class="absolute bottom-1 right-1 w-4 h-4 bg-emerald-500 border-2 border-white dark:border-slate-800 rounded-full" title="Aktif"></span>
                        </div>

                        <h2 class="text-xl font-bold text-slate-900 dark:text-white">{{ $user->name }}</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $user->email }}</p>

                        <!-- Badges -->
                        <div class="flex flex-wrap justify-center gap-2 mt-4">
                            <span class="px-3 py-1 bg-primary-50 dark:bg-primary-950/60 text-primary-600 dark:text-primary-400 rounded-full text-xs font-bold border border-primary-200 dark:border-primary-900">
                                {{ $user->divisi?->nama_divisi ?? 'Panitia' }}
                            </span>
                            @if($user->roles->count() > 0)
                                <span class="px-3 py-1 bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 rounded-full text-xs font-bold border border-amber-200 dark:border-amber-900">
                                    {{ $user->roles->pluck('name')->implode(', ') }}
                                </span>
                            @endif
                        </div>

                        <!-- Meta Info List -->
                        <div class="mt-6 pt-6 border-t border-gray-100 dark:border-slate-700 text-left space-y-3 text-xs sm:text-sm">
                            <div class="flex justify-between items-center text-slate-600 dark:text-slate-400">
                                <span>NIM / NIP</span>
                                <span class="font-bold text-slate-800 dark:text-slate-200 font-mono">{{ $user->nim ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center text-slate-600 dark:text-slate-400">
                                <span>Bergabung</span>
                                <span class="font-medium text-slate-800 dark:text-slate-200">{{ $user->created_at ? $user->created_at->translatedFormat('d M Y') : '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Settings Forms -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Update Profile Information -->
                    <div class="p-6 sm:p-8 bg-white dark:bg-slate-800 shadow-sm border border-gray-100 dark:border-slate-700 rounded-3xl transition-colors duration-200">
                        @include('profile.partials.update-profile-information-form')
                    </div>

                    <!-- Update Password -->
                    <div class="p-6 sm:p-8 bg-white dark:bg-slate-800 shadow-sm border border-gray-100 dark:border-slate-700 rounded-3xl transition-colors duration-200">
                        @include('profile.partials.update-password-form')
                    </div>

                    <!-- Delete Account / Danger Zone -->
                    <div class="p-6 sm:p-8 bg-red-50/40 dark:bg-red-950/20 border border-red-200 dark:border-red-900/60 shadow-sm rounded-3xl transition-colors duration-200">
                        @include('profile.partials.delete-user-form')
                    </div>

                </div>

            </div>

        </div>
    </div>
</x-app-layout>
