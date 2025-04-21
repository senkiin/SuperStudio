{{-- resources/views/components/nav-link.blade.php --}}
@props(['active'])

@php
// --- CLASES PARA EL CONTENEDOR <a> EXTERIOR ---
// Layout, borde, interacción general
$outerBaseClasses = 'inline-flex items-center border-b-2 text-sm font-bold uppercase tracking-wider leading-5 focus:outline-none transition duration-150 ease-in-out';
$outerBorderClasses = '';
$outerHoverFocusClasses = '';

if ($active ?? false) {
    // Activo: Borde morado en <a>
    $outerBorderClasses = 'border-purple-500';
} else {
    // Inactivo: Borde transparente en <a>, cambia a gris en hover/focus
    $outerBorderClasses = 'border-transparent';
    $outerHoverFocusClasses = 'hover:border-gray-500 focus:border-gray-500 hover:animate-shake'; // Borde gris + animación
}
// Clases combinadas para <a>
$outerClasses = $outerBaseClasses . ' ' . $outerBorderClasses . ' ' . $outerHoverFocusClasses;


// --- CLASES PARA EL <span> INTERIOR ---
// Fondo (BLANCO), padding, redondeo, color/sombra de texto
$innerBaseClasses = 'inline-block px-3 py-0.5 rounded-full bg-white'; // <<--- CAMBIO AQUÍ: Fondo ahora blanco

$innerTextClasses = '';         // Color del texto
$innerTextShadowClasses = ''; // Sombra del texto

if ($active ?? false) {
    // Activo: Texto Neón + Sombra. Fondo blanco de $innerBaseClasses.
    $innerTextClasses = 'text-blue-500';
    $innerTextShadowClasses = '[text-shadow:0_0_8px_#BF00FF]';
} else {
    // Inactivo: Texto negro por defecto, azul en hover/focus. Fondo blanco de $innerBaseClasses.
    $innerTextClasses = 'text-black hover:text-blue-500 focus:text-blue-500'; // Texto negro -> Azul
    // Sin sombra si está inactivo
}
// Clases combinadas para <span>
$innerClasses = $innerBaseClasses . ' ' . $innerTextClasses . ' ' . $innerTextShadowClasses;

@endphp

{{-- Contenedor <a> exterior --}}
<a {{ $attributes->merge(['class' => $outerClasses]) }}>

    {{-- Contenedor <span> interior --}}
    <span class="{{ $innerClasses }}">
        {{ $slot }}
    </span>

</a>
