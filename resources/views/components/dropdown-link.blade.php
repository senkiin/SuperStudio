<a {{ $attributes->merge(['class' => 'block w-full px-4 py-2 text-start text-sm leading-5 focus:outline-none transition duration-150 ease-in-out']) }}>
    <span class="inline-block px-3 py-0.5 rounded-full bg-white text-black hover:text-blue-500 focus:text-blue-500">
        {{ $slot }}
    </span>
</a>
