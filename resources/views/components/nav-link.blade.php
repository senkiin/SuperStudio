{{-- resources/views/components/nav-link.blade.php --}}
@props(['active'])

@php
// --- CLASES PARA EL CONTENEDOR <a> EXTERIOR ---
// Layout, borde, interacción general
$outerBaseClasses = 'inline-flex items-center border-b-2 text-sm font-bold tracking-wider leading-5 focus:outline-none transition duration-150 ease-in-out';
$outerBorderClasses = '';
$outerHoverFocusClasses = '';

if ($active ?? false) {
    // Activo: Borde transparente en <a> (sin subrayado azul)
    $outerBorderClasses = 'border-transparent';
    // Nada en hover/focus: debe seguir igual que cuando está activo
    $outerHoverFocusClasses = '';
} else {
    // Inactivo: Borde transparente siempre (no cambiar en hover)
    $outerBorderClasses = 'border-transparent';
    // Aquí dejamos vacío para que no “subraye” en hover/focus
    $outerHoverFocusClasses = '';
}
// Clases combinadas para <a>
$outerClasses = $outerBaseClasses . ' ' . $outerBorderClasses . ' ' . $outerHoverFocusClasses;


// --- CLASES PARA EL <span> INTERIOR ---
// Estilos base del span (padding, redondeo)
$innerCoreStyling = 'inline-block px-3 py-0.5 rounded-full';
$innerBgClass = '';      // Clase para el fondo, se definirá condicionalmente
$innerTextClasses = '';  // Color del texto

if ($active ?? false) {
    // Activo: Fondo blanco y texto negro
    $innerBgClass = 'bg-white';
    $innerTextClasses = 'text-black';
} else {
    // Inactivo: Fondo semitransparente por defecto,
    // pero en hover/focus pasa a fondo blanco y texto negro.
    $innerBgClass = 'bg-white/50 hover:bg-white focus:bg-white';
    $innerTextClasses = 'text-black hover:text-black focus:text-black';
}
// Clases combinadas para <span>
$innerClasses = $innerCoreStyling . ' ' . $innerBgClass . ' ' . $innerTextClasses;
@endphp

{{-- Contenedor <a> exterior --}}
<a {{ $attributes->merge(['class' => $outerClasses]) }}>
    {{-- Contenedor <span> interior --}}
    <span class="{{ $innerClasses }}">
        {{ $slot }}
    </span>
</a>
