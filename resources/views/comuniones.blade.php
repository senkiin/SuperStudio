<x-app-layout> {{-- O tu archivo de layout principal --}}

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- ========================================================== --}}
    {{--    INICIO: ETIQUETAS META ESPECÍFICAS PARA COMUNIONES      --}}
    {{-- ========================================================== --}}

    <title>Fotógrafo de Comuniones en Almería | Reportajes de Comunión</title>
    <meta name="description" content="Reportajes de comunión en Almería, en estudio y exteriores. Capturamos la inocencia y alegría de su primera comunión con un recuerdo único y especial.">
    <meta name="keywords" content="fotografo comunion almeria, reportaje comunion almeria, fotos de comunion, album de comunion, fotovalera, fotografo infantil almeria">

    {{-- Open Graph / Facebook --}}
    <meta property="og:title" content="Fotógrafo de Comuniones en Almería | Fotovalera">
    <meta property="og:description" content="Inmortalizamos la primera comunión de tus hijos con reportajes creativos y llenos de encanto en Almería. Sesiones en estudio y exteriores.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ route('comuniones') }}">
    <meta property="og:image" content="{{ asset('images/og_comuniones.jpg') }}"> {{-- RECOMENDACIÓN: Crear y subir la imagen public/images/og_comuniones.jpg --}}

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Fotógrafo de Comuniones en Almería | Fotovalera">
    <meta name="twitter:description" content="Inmortalizamos la primera comunión de tus hijos con reportajes creativos y llenos de encanto en Almería. Sesiones en estudio y exteriores.">
    <meta name="twitter:image" content="{{ asset('images/twitter_comuniones.jpg') }}"> {{-- RECOMENDACIÓN: Crear y subir la imagen public/images/twitter_comuniones.jpg --}}

    {{-- Etiquetas Adicionales --}}
    <link rel="canonical" href="{{ route('comuniones') }}" />
    <meta name="author" content="Fotovalera">
    <meta name="publisher" content="Fotovalera">
    <meta name="robots" content="index, follow">

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
