@props(['active'])

@php
    $base = 'block w-full text-base font-medium focus:outline-none transition duration-150 ease-in-out';
    if ($active) {
        $classes = $base.' bg-indigo-900 text-white';
    } else {
        $classes = $base.' bg-gray-900 text-gray-300 hover:bg-gray-700 hover:text-white';
    }
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
