<x-app-layout> {{-- O tu archivo de layout principal --}}

    {{-- Para SEO y el título en la pestaña del navegador --}}
    {{-- Esta sintaxis @section funciona si tu x-app-layout usa @yield('title') y @yield('metaDescription') --}}
    {{-- Si x-app-layout usa slots (ej. <x-slot name="title">), deberás ajustar la sintaxis --}}
    @section('title', $pageTitle ?? 'Fotografía de Embarazo')
    @section('metaDescription', $metaDescription ?? 'Sesiones fotográficas profesionales para embarazadas. Captura la
        magia de tu maternidad.')

        <div>

            @livewire('configurable-page-header', [
                'identifier' => 'Embarazo_header', 
            ])

            @livewire('curated-portrait-gallery', [
                'identifier' => 'Embarazo_gallery',
            ])
        </div>
        <x-self.superPie></x-self.superPie>
    </x-app-layout>
