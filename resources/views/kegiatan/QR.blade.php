<x-app-layout>

    <div class="py-8 font-poppins min-h-screen bg-gray-100 dark:bg-slate-900 transition-colors duration-200">

        <div class="max-w-md mx-auto px-4">

            <div class="bg-white dark:bg-slate-800 rounded-[28px] p-6 sm:p-7 shadow-sm border border-gray-100 dark:border-slate-700">

                @include('components.back-header', [
                    'href' => route('dashboard'),
                    'title' => 'QR Absensi'
                ])

                @include('components.profile-card',[
                    'user' => $user
                ])

                @include('components.qr-card', [
                    'qrUrl' => $qrUrl
                ])

            </div>

        </div>

    </div>

</x-app-layout>