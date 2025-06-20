<x-app-layout> {{-- O tu archivo de layout principal --}}
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- ========================================================== --}}
    {{-- INICIO: ETIQUETAS META ESPECÍFICAS PARA RECIÉN NACIDOS (NEWBORN) --}}
    {{-- ========================================================== --}}

    <title>Fotografía Newborn en Almería | Sesiones Recién Nacido</title>
    <meta name="description" content="Especialistas en fotografía newborn en Almería. Sesiones de recién nacido seguras y tiernas para capturar los primeros días de tu bebé. Un recuerdo para toda la vida.">
    <meta name="keywords" content="fotografia newborn almeria, fotografo recien nacido almeria, sesion de fotos bebe, fotos newborn, estudio fotografia bebes, fotovalera newborn">

    {{-- Open Graph / Facebook --}}
    <meta property="og:title" content="Fotografía Newborn en Almería | Sesiones para Recién Nacidos">
    <meta property="og:description" content="Capturamos la magia de los primeros días de tu bebé con sesiones newborn artísticas y llenas de ternura en nuestro estudio de Almería.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ route('newborn.index') }}">
    <meta property="og:image" content="{{ asset('images/og_newborn.jpg') }}"> {{-- RECOMENDACIÓN: Crear y subir la imagen public/images/og_newborn.jpg --}}

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Fotografía Newborn en Almería | Sesiones para Recién Nacidos">
    <meta name="twitter:description" content="Capturamos la magia de los primeros días de tu bebé con sesiones newborn artísticas y llenas de ternura en nuestro estudio de Almería.">
    <meta name="twitter:image" content="{{ asset('images/twitter_newborn.jpg') }}"> {{-- RECOMENDACIÓN: Crear y subir la imagen public/images/twitter_newborn.jpg --}}

    {{-- Etiquetas Adicionales --}}
    <link rel="canonical" href="{{ route('newborn.index') }}" />
    <meta name="author" content="Fotovalera">
    <meta name="publisher" content="Fotovalera">
    <meta name="robots" content="index, follow">
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
