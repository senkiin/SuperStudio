<x-app-layout> {{-- O tu archivo de layout principal --}}

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- ========================================================== --}}
    {{--    INICIO: ETIQUETAS META ESPECÍFICAS PARA EMBARAZO          --}}
    {{-- ========================================================== --}}

    <title>Fotografía de Embarazo en Almería | Sesiones de Maternidad</title>
    <meta name="description" content="Captura la magia de tu embarazo con una sesión de fotos profesional en Almería. Creamos recuerdos artísticos y emotivos de tu maternidad. ¡Infórmate ahora!">
    <meta name="keywords" content="fotografia embarazo almeria, sesion fotos embarazada almeria, fotografo maternidad almeria, fotos de embarazo, reportaje embarazo, fotovalera">

    {{-- Open Graph / Facebook --}}
    <meta property="og:title" content="Fotografía de Embarazo en Almería | Fotovalera">
    <meta property="og:description" content="Celebra la dulce espera con una sesión de fotos de maternidad única en Almería. Capturamos la belleza de este momento tan especial para ti.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ route('embarazo.index') }}">
    <meta property="og:image" content="{{ asset('images/og_embarazo.jpg') }}"> {{-- RECOMENDACIÓN: Crear y subir la imagen public/images/og_embarazo.jpg --}}

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Fotografía de Embarazo en Almería | Fotovalera">
    <meta name="twitter:description" content="Celebra la dulce espera con una sesión de fotos de maternidad única en Almería. Capturamos la belleza de este momento tan especial para ti.">
    <meta name="twitter:image" content="{{ asset('images/twitter_embarazo.jpg') }}"> {{-- RECOMENDACIÓN: Crear y subir la imagen public/images/twitter_embarazo.jpg --}}

    {{-- Etiquetas Adicionales --}}
    <link rel="canonical" href="{{ route('embarazo.index') }}" />
    <meta name="author" content="Fotovalera">
    <meta name="publisher" content="Fotovalera">
    <meta name="robots" content="index, follow">

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
