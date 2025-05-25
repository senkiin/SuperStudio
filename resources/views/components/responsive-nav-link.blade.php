@props(['active'])

@php
    $base = 'block w-full text-base font-medium focus:outline-none transition duration-150 ease-in-out';
    if ($active) {
        // Enlace activo: ligeramente más claro que el fondo general
        $classes = $base.' bg-gray-800 text-white';
    } else {
        // Enlace normal: fondo negro, texto blanco; hover en gris oscuro
        $classes = $base.' bg-black text-white hover:bg-gray-700';
    }
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
