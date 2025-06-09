<x-app-layout> {{-- O tu archivo de layout principal --}}

    {{-- Para SEO y el título en la pestaña del navegador --}}
    {{-- Esta sintaxis @section funciona si tu x-app-layout usa @yield('title') y @yield('metaDescription') --}}
    {{-- Si x-app-layout usa slots (ej. <x-slot name="title">), deberás ajustar la sintaxis --}}
    <x-slot name="title">
        Fotografía Newborn en Almería | Sesiones de Recién Nacido - Fotovalera
    </x-slot>
    <x-slot name="metaDescription">
        Capturamos la ternura y los primeros días de tu bebé con sesiones de fotografía newborn en Almería. Fotógrafos especializados en recién nacidos. Recuerdos para toda la vida.
    </x-slot>
    <x-slot name="metaKeywords">
        fotografia newborn almeria, sesion recien nacido almeria, fotografo bebes almeria, fotos de bebes almeria, newborn photography almeria, TuNombreDeEmpresa newborn
    </x-slot>

        <div>

            @livewire('configurable-page-header', [
                'identifier' => 'Newborn_header',
            ])

        </div>


        @livewire('curated-portrait-gallery', [
            'identifier' => 'Newborn_gallery',
        ])
           <x-self.section-text title="Fotografía Newborn en Almería: Capturando la Magia de los Primeros Días
"
            subtitle="En Fotovalera, nos especializamos en crear recuerdos atemporales de los primeros y preciosos días de vuestro bebé. Nuestras sesiones de fotografía Recien Nacidos en Almería están diseñadas con mimo y profesionalidad para ser una experiencia relajada, segura y mágica tanto para los pequeños como para sus familias.
                                Entendemos la importancia de estos momentos fugaces y nos dedicamos a capturar la inocencia, la ternura y la belleza única de vuestro recién nacido con un toque artístico y delicado. Cada sesión es personalizada, buscando reflejar la personalidad de vuestro bebé y el amor que lo rodea. Confía en nuestros más de 23 años de experiencia para inmortalizar este capítulo tan especial.

" />
        <x-self.superPie></x-self.superPie>
    </x-app-layout>
