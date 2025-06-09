<div>
    {{-- SEO Meta Tags para la Página de Videos --}}
    @section('title', 'Videógrafos en Almería | Vídeos de Boda y Eventos - Fotovalera')
    @section('metaDescription', 'Servicios profesionales de videografía en Almería. Creamos vídeos emotivos y
        cinematográficos para bodas, eventos y proyectos corporativos. ¡Cuenta tu historia en movimiento!')
    @section('metaKeywords', 'videografo almeria, video boda almeria, video eventos almeria, produccion video almeria,
        videos corporativos almeria, fotovalera videos')

        {{-- Open Graph / Facebook --}}
    @section('og_title', 'Videógrafos Profesionales en Almería | Fotovalera')
    @section('og_description', 'Descubre nuestros trabajos de videografía en Almería. Vídeos de boda, eventos y más, con
        un estilo cinematográfico y emotivo.')
    @section('og_image', asset('images/default-og-image.jpg')) {{-- Reemplaza con una imagen representativa de tus videos o tu marca --}}

    {{-- Twitter --}}
    @section('twitter_title', 'Videógrafos en Almería | Vídeos que Cuentan Historias | Fotovalera')
    @section('twitter_description', 'Vídeos de boda, eventos y corporativos en Almería. Calidad cinematográfica y
        narrativa emotiva. ¡Contacta con Fotovalera!')
    @section('twitter_image', asset('images/default-twitter-card-image.jpg')) {{-- Reemplaza con una imagen para Twitter Card --}}


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
