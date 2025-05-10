<x-app-layout> {{-- O tu archivo de layout principal --}}

    {{-- Para SEO y el título en la pestaña del navegador --}}
    {{-- Esta sintaxis @section funciona si tu x-app-layout usa @yield('title') y @yield('metaDescription') --}}
    {{-- Si x-app-layout usa slots (ej. <x-slot name="title">), deberás ajustar la sintaxis --}}
    @section('title', $pageTitle ?? 'Reportajes de Comunión')
    @section('metaDescription', $metaDescription ?? 'Fotografía y vídeo profesional para Primeras Comuniones. Momentos especiales capturados para siempre.')

    <div>

        @livewire('hero-section-comuniones', [
               ])
    </div>
</x-app-layout>
