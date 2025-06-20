<div>
   <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Vídeos de Boda | Reportajes Cinematográficos - Fotovalera</title>
    <meta name="description"
        content="Descubre nuestros reportajes de vídeo en Almería. Creamos vídeos cinematográficos para bodas, comuniones y eventos especiales que cuentan tu historia de una manera única y emotiva.">
    <meta name="keywords"
        content="video de boda almeria, videografos almeria, videos de comunion, videos para eventos, reportajes de video, cinematografia de bodas, fotovalera">

    {{-- Open Graph / Facebook --}}
    <meta property="og:title" content="Vídeos de Boda| Fotovalera">
    <meta property="og:description"
        content="Emociónate con nuestros reportajes de vídeo. Capturamos la esencia de tu boda o evento especial en Almería con un estilo cinematográfico.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ route('videos') }}">
    <meta property="og:image" content="{{ asset('images/og_videos.jpg') }}"> {{-- RECUERDA: Crear y subir la imagen public/images/og_videos.jpg --}}

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Vídeos de Boda y Eventos en Almería | Fotovalera">
    <meta name="twitter:description"
        content="Emociónate con nuestros reportajes de vídeo. Capturamos la esencia de tu boda o evento especial en Almería con un estilo cinematográfico.">
    <meta name="twitter:image" content="{{ asset('images/twitter_videos.jpg') }}"> {{-- RECUERDA: Crear y subir la imagen public/images/twitter_videos.jpg --}}

    {{-- Etiquetas Adicionales --}}
    <link rel="canonical" href="{{ route('videos') }}" />
    <meta name="author" content="Fotovalera">
    <meta name="publisher" content="Fotovalera">
    <meta name="robots" content="index, follow">

    {{-- @livewire('header-component') --}}
    @livewire('configurable-page-header', [
        'identifier' => 'Videos_header', // ¡Nuevo identificador!
        'defaultTitle' => 'Videos de Ensueño',
        'defaultSubtitle' => 'Vuestro día más especial, contado a través de imágenes que emocionan.',
        'defaultImage' => null, // O '/images/bodas_default_bg.jpg'
    ])


    @livewire('video-gallery-manager', [
        'identifier' => 'videos-tour-videos',
        'defaultTitle' => 'Videografos VIDEOS',
    ])
    <x-self.section-text title=" Videógrafos en Almería: Capturando la Esencia de Tus Momentos

"
        subtitle=" En Fotovalera, transformamos momentos fugaces en recuerdos cinematográficos duraderos. Como videógrafos en Almería con más de 23 años de experiencia, nos apasiona contar historias a través de la lente, capturando la emoción, la atmósfera y los detalles que hacen único cada evento, ya sea una boda, un evento corporativo o un proyecto personal.
                        Nuestro enfoque combina la técnica cinematográfica con una narrativa sensible y personalizada. Trabajamos de cerca contigo para entender tu visión y producir un vídeo que no solo cumpla tus expectativas, sino que las supere, convirtiéndose en un tesoro visual que revivirás una y otra vez.

" />

    <x-self.superPie></x-self.superPie>

</div>
