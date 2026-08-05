@props(['show', 'url'])

<div x-show="{{ $show }}" class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-gray-900/80 backdrop-blur-sm" style="display: none;" x-transition>
    <div @click.outside="{{ $show }} = false" class="bg-white dark:bg-slate-800 rounded-2xl w-full max-w-4xl h-[85vh] flex flex-col relative overflow-hidden shadow-2xl border border-gray-100 dark:border-slate-700">
        <!-- Header Modal -->
        <div class="flex justify-between items-center p-4 border-b border-gray-100 dark:border-slate-700 bg-gray-50 dark:bg-slate-800/90">
            <h3 class="font-bold text-slate-800 dark:text-slate-200 flex items-center gap-2">
                <svg class="w-5 h-5 text-primary-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path></svg>
                Preview Surat Izin
            </h3>
            <button type="button" @click="{{ $show }} = false" class="text-gray-400 dark:text-slate-400 hover:text-red-600 font-bold text-2xl transition-colors cursor-pointer">&times;</button>
        </div>
        <!-- Konten PDF -->
        <div class="flex-1 w-full h-full bg-gray-100 dark:bg-slate-900">
            <iframe src="{{ $url }}" class="w-full h-full border-none"></iframe>
        </div>
    </div>
</div>