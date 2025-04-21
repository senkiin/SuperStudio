@props(['active'])

@php
// Clases base para layout, fuente, padding, borde izquierdo, etc.
$baseClasses = 'block w-full ps-3 pe-4 py-3 border-l-4 text-start text-base font-medium focus:outline-none transition duration-150 ease-in-out';

$classes = ($active ?? false)
            // --- ESTADO ACTIVO (para fondo oscuro) ---
            // Borde índigo visible, fondo índigo oscuro, texto blanco.
            ? $baseClasses . ' border-indigo-500 bg-indigo-900 text-white focus:bg-indigo-800 focus:border-indigo-400'

            // --- ESTADO INACTIVO (para fondo oscuro) ---
            // Fondo gris muy oscuro por defecto, borde transparente, texto gris claro.
            // Hover/Focus: fondo gris más claro, texto blanco, borde gris visible.
            : $baseClasses . ' bg-gray-900 border-transparent text-gray-300 hover:bg-gray-700 hover:text-white hover:border-gray-500 focus:bg-gray-700 focus:text-white focus:border-gray-500';
            // ^-- CAMBIO AQUÍ: Añadido 'bg-gray-900' --^
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
