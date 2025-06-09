<x-app-layout> {{-- O tu archivo de layout principal --}}

    {{-- Para SEO y el título en la pestaña del navegador --}}
    {{-- Esta sintaxis @section funciona si tu x-app-layout usa @yield('title') y @yield('metaDescription') --}}
    {{-- Si x-app-layout usa slots (ej. <x-slot name="title">), deberás ajustar la sintaxis --}}
    <x-slot name="title">
        Fotografía de Embarazo en Almería | Sesiones Premamá - Fotovalera
    </x-slot>
    <x-slot name="metaDescription">
        Inmortaliza la belleza de tu embarazo con una sesión de fotos premamá en Almería. Fotógrafos especializados en
        capturar la dulzura y emoción de esta etapa única.
    </x-slot>
    <x-slot name="metaKeywords">
        fotografia embarazo almeria, sesion premama almeria, fotografo embarazo almeria, fotos embarazada almeria,
        reportaje embarazo almeria, fotovalera embarazo
    </x-slot>

    <div>

        @livewire('configurable-page-header', [
            'identifier' => 'Embarazo_header',
        ])

        @livewire('curated-portrait-gallery', [
            'identifier' => 'Embarazo_gallery',
        ])
    </div>
    <x-self.section-text
        title="Sesiones de Fotografía de Embarazo en Almería: Celebrando la Maternidad"
        subtitle="En Fotovalera, creemos que el embarazo es una de las etapas más bellas y transformadoras en la vida de una mujer y su familia. Nuestras sesiones de fotografía de embarazo en Almería están diseñadas para celebrar tu feminidad, la conexión con tu bebé y la emoción de la dulce espera.                    Con un enfoque artístico y sensible, capturamos la luz especial que irradias durante estos meses. Ya sea en nuestro estudio, en exteriores con los paisajes únicos de Almería o en la intimidad de tu hogar, creamos un ambiente relajado y confortable para que te sientas radiante. Permítenos crear imágenes atemporales que atesorarás para siempre, un recuerdo imborrable de este milagro de la vida.


" />
    <x-self.superPie></x-self.superPie>
</x-app-layout>
