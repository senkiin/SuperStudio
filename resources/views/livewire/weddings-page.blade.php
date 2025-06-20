<div>
 <title>Fotógrafo de Bodas en Almería | Reportajes Únicos | Fotovalera</title>
    <meta name="description" content="Fotógrafo de bodas en Almería especializado en capturar la emoción y la naturalidad de vuestro día. Creamos recuerdos inolvidables con un estilo documental y artístico. ¡Pide presupuesto!">
    <meta name="keywords" content="fotografo de bodas almeria, fotografo para bodas, reportajes de boda almeria, video de boda, bodas en almeria, fotovalera, fotografia matrimonial">

    {{-- Open Graph / Facebook --}}
    <meta property="og:title" content="Fotógrafo de Bodas en Almería | Fotovalera">
    <meta property="og:description" content="Hacemos que los recuerdos de tu boda duren para siempre. Fotografía y vídeo con un enfoque artístico y emocional en Almería y provincia.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ route('weddings') }}">
    <meta property="og:image" content="{{ asset('images/og_bodas.jpg') }}"> {{-- RECOMENDACIÓN: Crear y subir la imagen public/images/og_bodas.jpg --}}

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Fotógrafo de Bodas en Almería | Fotovalera">
    <meta name="twitter:description" content="Hacemos que los recuerdos de tu boda duren para siempre. Fotografía y vídeo con un enfoque artístico y emocional en Almería y provincia.">
    <meta name="twitter:image" content="{{ asset('images/twitter_bodas.jpg') }}"> {{-- RECOMENDACIÓN: Crear y subir la imagen public/images/twitter_bodas.jpg --}}

    {{-- Etiquetas Adicionales --}}
    <link rel="canonical" href="{{ route('weddings') }}" />
    <meta name="author" content="Fotovalera">
    <meta name="publisher" content="Fotovalera">
    <meta name="robots" content="index, follow">
    {{-- @livewire('header-component') --}}
 @livewire('configurable-page-header', [
        'identifier' => 'bodas_header', // ¡Nuevo identificador!
        'defaultTitle' => 'Bodas de Ensueño',
        'defaultSubtitle' => 'Vuestro día más especial, contado a través de imágenes que emocionan.',
        'defaultImage' => null // O '/images/bodas_default_bg.jpg'
    ])


    {{-- Album Section --}}

    @livewire('configurable-album-section', ['identifier' => 'home_albums'])

    @livewire('video-gallery-manager', [
        'identifier' => 'homepage-tour-videos',
        'defaultTitle' => 'TOUR VIDEOS',
        'defaultDescription' => 'Descubre los destinos más increíbles a través de nuestros videos de tours.'
    ])
    {{-- Placeholder for potential admin modal to edit header --}}
    {{-- You would create a separate Livewire component or Alpine modal for this --}}
    {{-- @livewire('admin.edit-section-modal') --}}

  <x-self.section-text
        title="Fotografía y Vídeo de Bodas en Almería: Vuestra Historia, Nuestra Pasión
"
        subtitle="En Fotovalera, entendemos que vuestra boda es uno de los capítulos más importantes y emotivos de vuestra vida. Con más de 23 años de experiencia como fotógrafos y videógrafos especializados en bodas en Almería, nos dedicamos a inmortalizar cada sonrisa, cada lágrima de alegría y cada detalle con un enfoque artístico, natural y profundamente personalizado.
                Nuestro compromiso es capturar la esencia de vuestro amor y la atmósfera única de vuestro gran día, creando un reportaje de boda que no solo recordaréis, sino que reviviréis una y otra vez. Dejad que nuestra experiencia y pasión por la fotografía de bodas cuenten vuestra historia de amor de una manera auténtica y memorable en los maravillosos escenarios que Almería ofrece.


" />
        <x-self.superPie></x-self.superPie>

</div>
