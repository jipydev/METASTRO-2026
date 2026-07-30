<button {{ $attributes->merge(['type' => 'submit', 'class' => 'px-4 py-2 bg-primary-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dtransition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
