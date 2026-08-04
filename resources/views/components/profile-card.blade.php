<div class="flex items-center gap-4 mb-8">

    @php
        $photoUrl = $user->foto
            ? asset('storage/' . $user->foto)
            : 'https://ui-avatars.com/api/?size=256&background=065E75&color=fff&name=' . urlencode($user->name);
    @endphp

    <img
        src="{{ $photoUrl }}"
        alt="Foto {{ $user->name }}"
        class="w-24 h-24 rounded-full border object-cover">

    <div>

        <h3 class="text-2xl font-semibold">

            {{ $user->name }}

        </h3>

        <p class="text-gray-600">

            {{ $user->divisi?->nama_divisi ?? 'Belum ada divisi' }}

        </p>

    </div>

</div>