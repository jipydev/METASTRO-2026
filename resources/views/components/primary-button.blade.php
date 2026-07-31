<button {{ $attributes->merge([
    'type' => 'submit', 
    'class' => 'inline-flex items-center justify-center px-4 py-2 text-xs md:text-sm lg:text-lg border-2 border-t-4 border-s-4 border-primary-300 hover:border-2 focus:border-2 bg-primary-500 hover:bg-primary-600 active:bg-primary-700 text-white font-semibold tracking-widest cursor-pointer rounded-md outline-none transition ease-in-out duration-150'
]) }}>
    {{ $slot }}
</button>