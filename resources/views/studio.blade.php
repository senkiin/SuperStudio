<x-app-layout> {{-- O tu archivo de layout principal --}}
    <title>Estudio Fotografico en Almería - Fotovalera</title>
    <meta name="description" content="Descubre nuestro estudio fotográfico en Almería. Realizamos sesiones creativas y temáticas como Halloween, Navidad y proyectos artísticos. ¡Ideas originales para tus fotos!">
    <meta name="keywords" content="estudio fotografia almeria, sesiones tematicas almeria, fotografia creativa almeria, fotografo estudio almeria, sesiones halloween almeria, sesiones navidad almeria, fotovalera estudio, fotografia artistica almeria">

    {{-- Open Graph / Facebook --}}
    <meta property="og:title" content="Estudio Fotográfico Creativo en Almería | Fotovalera">
    <meta property="og:description" content="Explora nuestras sesiones fotográficas de estudio en Almería: desde retratos artísticos hasta divertidas temáticas de Halloween y Navidad.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    {{--<meta property="og:image" content="{{ asset('images/og_estudio.jpg') }}">  Asegúrate que la imagen public/images/og_estudio.jpg exista --}}

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Estudio Fotográfico Creativo en Almería | Fotovalera">
    <meta name="twitter:description" content="Sesiones de estudio únicas en Almería: Halloween, Navidad y fotografía artística. ¡Descubre Fotovalera!">
    {{-- <meta name="twitter:image" content="{{ asset('images/twitter_estudio.jpg') }}">  Asegúrate que la imagen public/images/twitter_estudio.jpg exista --}}

    <link rel="canonical" href="{{ url()->current() }}" />

    <meta name="author" content="Fotovalera">
    <meta name="publisher" content="Fotovalera">
    <meta name="robots" content="@yield('meta_robots', 'index, follow')"> {{-- Permite la indexación por defecto --}}


    @livewire('dynamic-carousel')
    <div>
        <x-self.section-text title="Reportajes de Haloween"
            subtitle="Este es un texto estático que aparece debajo de la cabecera hero." />

        @livewire('curated-portrait-gallery', [
            'identifier' => 'Halloween_gallery',
        ])


    </div>
     <div>
        <x-self.section-text title="Reportajes de Na vidad"
            subtitle="Este es un texto estático que aparece debajo de la cabecera hero." />

        @livewire('curated-portrait-gallery', [
            'identifier' => 'Navidad_gallery',
        ])
    </div>

    <x-self.superPie></x-self.superPie>
</x-app-layout>
