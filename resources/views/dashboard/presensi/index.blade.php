<x-app-layout :title="$title">
    <div class="w-full min-h-screen bg-gray-50 py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-2xl p-6">

                @include('components.back-header', [
                    'href' => route('dashboard'),
                    'title' => $title,
                ])

                <div class="mt-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($timelines as $timeline)
                            <div class="w-full">
                                @include('components.attendance-card', [
                                    'timeline' => $timeline,
                                ])
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
