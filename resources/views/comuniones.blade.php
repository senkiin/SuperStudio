<x-app-layout> {{-- O tu archivo de layout principal --}}

    {{-- Para SEO y el título en la pestaña del navegador --}}
    {{-- Esta sintaxis @section funciona si tu x-app-layout usa @yield('title') y @yield('metaDescription') --}}
    {{-- Si x-app-layout usa slots (ej. <x-slot name="title">), deberás ajustar la sintaxis --}}
    <x-slot name="title">
        Fotografía de Comuniones en Almería | Reportajes Primera Comunión - Fotovalera
    </x-slot>
    <x-slot name="metaDescription">
        Capturamos la ilusión y los momentos especiales de la Primera Comunión en Almería. Fotógrafos con experiencia en reportajes de comunión creativos y emotivos.
    </x-slot>
    <x-slot name="metaKeywords">
        fotografo comunion almeria, reportaje comunion almeria, fotografia primera comunion almeria, fotos comunion almeria, fotovalera comuniones
    </x-slot>


        <div>

            @livewire('configurable-page-header', [
                'identifier' => 'Comuniones_header',
            ])
            @livewire('curated-portrait-gallery', [
                'identifier' => 'Comuniones_gallery',
            ])
        </div>
         <x-self.section-text
        title="Reportajes de Primera Comunión en Almería: Recuerdos Inolvidables

"
        subtitle=" La Primera Comunión es un hito lleno de significado e ilusión en la vida de vuestros hijos. En Fotovalera, nos dedicamos a crear reportajes de comunión en Almería que reflejen la alegría, la inocencia y la personalidad única de cada niño y niña en este día tan especial.
                Con un enfoque creativo y cercano, buscamos capturar no solo las fotografías tradicionales, sino también esos momentos espontáneos y emotivos que hacen que cada comunión sea diferente. Ofrecemos sesiones en estudio, exteriores con encanto en Almería, o en la iglesia, adaptándonos a vuestras preferencias para crear un recuerdo que atesoraréis para siempre.


" />
        <x-self.superPie></x-self.superPie>
</x-app-layout>
