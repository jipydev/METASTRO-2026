<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            
            <!-- Logo -->
            <div class="flex items-center shrink-0">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 sm:gap-3">
                    <img src="{{ asset('images/logo.webp') }}" alt="Logo" class="h-7 w-7 sm:h-8 sm:w-8 object-contain" />
                    <span class="font-oswald text-lg sm:text-xl font-semibold uppercase tracking-tight text-black truncate">
                        METASTRO 2026
                    </span>
                </a>
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-4">
                
                <!-- Notifikasi Desktop -->
                <div x-data="{ showNotif: false }" class="relative">
                    <button @click="showNotif = !showNotif" @click.outside="showNotif = false" class="relative p-1 rounded-full text-black hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-black transition-colors">
                        <span class="sr-only">Lihat notifikasi</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                        </svg>
                        <span class="absolute top-1 right-1 block h-2 w-2 rounded-full bg-red-500 ring-2 ring-white"></span>
                    </button>

                   <!-- Pop-up Dropdown Notifikasi -->
                   <div x-show="showNotif" 
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        style="display: none;" 
                        class="absolute right-0 mt-3 w-64 bg-gradient-to-br from-[#1c6989] to-[#3ba1c4] text-white text-sm rounded-2xl p-4 shadow-xl z-50">
                        
    <!-- Panah Segitiga CSS (Warnanya disamakan dengan bagian atas gradasi) -->
    <div class="absolute -top-2 right-3 w-0 h-0 border-l-[8px] border-l-transparent border-b-[8px] border-b-[#1c6989] border-r-[8px] border-r-transparent"></div>
    
    <p class="font-semibold leading-snug">Izin Rabes 2 Kamu diterima oleh Ranger</p>
    <p class="text-xs text-white/80 mt-1">Senin, 20 Juli 2026 18.00</p>
</div>
                </div>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-black bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div class="font-poppins">{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4 text-black" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Mobile Navigation -->
            <div class="-me-2 flex items-center sm:hidden gap-2">
                
                <!-- Notifikasi Mobile -->
                <div x-data="{ showNotif: false }" class="relative">
                    <button @click="showNotif = !showNotif" @click.outside="showNotif = false" class="relative p-1 rounded-full text-black hover:text-gray-700 focus:outline-none">
                        <span class="sr-only">Lihat notifikasi</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                        </svg>
                        <span class="absolute top-1 right-1 block h-2 w-2 rounded-full bg-red-500 ring-2 ring-white"></span>
                    </button>

                    <!-- Pop-up Dropdown Notifikasi -->
                        <div x-show="showNotif" 
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            style="display: none;" 
                            class="absolute right-0 mt-3 w-64 bg-gradient-to-br from-[#1c6989] to-[#3ba1c4] text-white text-sm rounded-2xl p-4 shadow-xl z-50">
                            
                            <!-- Panah Segitiga CSS (Warnanya disamakan dengan bagian atas gradasi) -->
                            <div class="absolute -top-2 right-3 w-0 h-0 border-l-[8px] border-l-transparent border-b-[8px] border-b-[#1c6989] border-r-[8px] border-r-transparent"></div>
                            
                            <p class="font-semibold leading-snug">Izin Rabes 2 Kamu diterima oleh Ranger</p>
                            <p class="text-xs text-white/80 mt-1">Senin, 20 Juli 2026 18.00</p>
                        </div>
                </div>

                <!-- Hamburger Button -->
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-black hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white">
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium font-poppins text-base text-black">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-600">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>