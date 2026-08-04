<x-app-layout>

    <div class="py-8">

        <div class="max-w-sm mx-auto">

            <div class="bg-sky-50 rounded-[28px] p-7 min-h-[680px]">

                @include('components.back-header', [
                'href' => route('dashboard'),
                'title' => $title
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