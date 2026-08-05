<div class="flex items-center gap-4 mb-8 font-poppins">

    @php
        $photoUrl = $user->foto
            ? asset('storage/' . $user->foto)
            : 'https://ui-avatars.com/api/?size=256&background=fe5a1d&color=fff&name=' . urlencode($user->name);
    @endphp

    <img
        src="{{ $photoUrl }}"
        alt="Foto {{ $user->name }}"
        class="w-20 h-20 sm:w-24 sm:h-24 rounded-full border-2 border-primary-500 object-cover shadow-sm">

    <div>

        <h3 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white">

            {{ $user->name }}

        </h3>

        <p class="text-slate-500 dark:text-slate-400 text-sm">

            {{ $user->divisi?->nama_divisi ?? 'Belum ada divisi' }}

        </p>

    </div>

</div>