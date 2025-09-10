<button {{ $attributes->merge(['type' => 'submit', 'class' => 'w-full text-center items-center px-4 py-3 tracking-widest rounded-md font-bold text-xs rounded-2x1']) }}>
    {{ $slot }}
</button>
