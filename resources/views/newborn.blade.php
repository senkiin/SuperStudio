<x-app-layout> {{-- O tu archivo de layout principal --}}

    {{-- Para SEO y el título en la pestaña del navegador --}}
    {{-- Esta sintaxis @section funciona si tu x-app-layout usa @yield('title') y @yield('metaDescription') --}}
    {{-- Si x-app-layout usa slots (ej. <x-slot name="title">), deberás ajustar la sintaxis --}}
    @section('title', $pageTitle ?? 'Fotografía de Recién Nacidos')
    @section('metaDescription',
        $metaDescription ??
        'Sesiones fotográficas artísticas para recién nacidos. Los primeros
        días de tu bebé, un recuerdo para siempre.')

        <div>

            @livewire('configurable-page-header', [
                'identifier' => 'Newborn_header',
            ])

        </div>
        <x-self.section-text title="Título Estático de Ejemplo"
            subtitle="Este es un texto estático que aparece debajo de la cabecera hero." />

        @livewire('curated-portrait-gallery', [
            'identifier' => 'Newborn_gallery',
        ])
        <x-self.superPie></x-self.superPie>
    </x-app-layout>
