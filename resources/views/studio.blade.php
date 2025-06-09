<x-app-layout> {{-- O tu archivo de layout principal --}}
    {{-- SEO Meta Tags para la Página de Studio --}}
    @section('title', 'Estudio Fotográfico Creativo en Almería | Sesiones Temáticas - Fotovalera')
    @section('meta_description', 'Descubre nuestro estudio fotográfico en Almería. Realizamos sesiones creativas y temáticas como Halloween, Navidad y proyectos artísticos. ¡Ideas originales para tus fotos!')
    @section('meta_keywords', 'estudio fotografia almeria, sesiones tematicas almeria, fotografia creativa almeria, fotografo estudio almeria, sesiones halloween almeria, sesiones navidad almeria, fotovalera estudio, fotografia artistica almeria')

    {{-- Open Graph / Facebook --}}
    @section('og_title', 'Estudio Fotográfico Creativo en Almería | Fotovalera')
    @section('og_description', 'Explora nuestras sesiones fotográficas de estudio en Almería: desde retratos artísticos hasta divertidas temáticas de Halloween y Navidad.')
    @section('og_image', asset('images/default-og-image.jpg')) {{-- Asegúrate que esta imagen exista o usa una específica para el estudio --}}

   {{-- Twitter --}}
    @section('twitter_title', 'Estudio Fotográfico Creativo en Almería | Fotovalera')
    @section('twitter_description', 'Sesiones de estudio únicas en Almería: Halloween, Navidad y fotografía artística. ¡Descubre Fotovalera!')
    @section('twitter_image', asset('images/default-twitter-card-image.jpg')) {{-- Asegúrate que esta imagen exista o usa una específica para el estudio --}}



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
