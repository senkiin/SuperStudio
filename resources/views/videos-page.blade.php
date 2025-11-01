<x-app-layout>
    {{-- ========================================================== --}}
    {{--    SEO COMPLETO Y PROFESIONAL PARA LA PÁGINA DE VÍDEOS     --}}
    {{-- ========================================================== --}}
    <x-slot name="head">
        @php
            // Obtener imagen del header de videos
            $headerSettings = \App\Models\PageHeaderSetting::where('identifier', 'videos_header')->first();
            if ($headerSettings && $headerSettings->background_image_url) {
                $pageImageUrl = Storage::disk('page-headers')->url($headerSettings->background_image_url);
            } else {
                $pageImageUrl = Storage::disk('logos')->url('SuperLogo.png');
            }
            $pageImageWidth = 1200;
            $pageImageHeight = 630;
        @endphp
        {{-- Meta Tags Básicos Optimizados para SEO 2025 --}}
        <title>Videógrafos de Bodas en Almería | Vídeo Cinematográfico Profesional | Foto Valera</title>
        <meta name="description" content="Videógrafos profesionales en Almería especializados en vídeo de bodas cinematográfico. Reportajes en 4K con drones, edición profesional y estilo cinematográfico. Más de 23 años de experiencia creando películas emotivas de bodas y eventos.">
        <meta name="keywords" content="videografos almeria, videografo bodas almeria, video de boda almeria, video cinematografico boda, videografia bodas almeria, video 4k bodas, drones bodas almeria, videografo profesional almeria, reportajes video boda, cinematografia bodas almeria, video eventos almeria, fotovalera videos">
        <meta name="author" content="Foto Valera">
        <meta name="publisher" content="Foto Valera">
        <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
        <meta name="language" content="es">
        <meta name="geo.region" content="ES-AL">
        <meta name="geo.placename" content="Almería">
        <meta name="geo.position" content="36.8381;-2.4597">
        <meta name="ICBM" content="36.8381, -2.4597">
        
        {{-- Meta Tags para Búsqueda por Voz y E-E-A-T 2025 --}}
        <meta name="rating" content="General">
        <meta name="distribution" content="Global">
        <meta name="coverage" content="Worldwide">
        <meta name="audience" content="All">
        <meta name="classification" content="Videografía de Bodas, Vídeo Cinematográfico, Videógrafos Profesionales">
        
        {{-- URL Canónica --}}
        <link rel="canonical" href="{{ route('videos') }}">
        
        {{-- Open Graph / Facebook --}}
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ route('videos') }}">
        <meta property="og:title" content="Videógrafos de Bodas en Almería | Vídeo Cinematográfico Profesional | Foto Valera">
        <meta property="og:description" content="Videógrafos profesionales en Almería. Vídeo de bodas cinematográfico en 4K con drones. Más de 23 años creando películas emotivas de bodas y eventos.">
        <meta property="og:image" content="{{ $pageImageUrl }}">
        <meta property="og:image:width" content="{{ $pageImageWidth }}">
        <meta property="og:image:height" content="{{ $pageImageHeight }}">
        <meta property="og:image:alt" content="Foto Valera - Videógrafos Profesionales en Almería">
        <meta property="og:site_name" content="Foto Valera">
        <meta property="og:locale" content="es_ES">
        
        {{-- Twitter Card --}}
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:site" content="@foto_valera">
        <meta name="twitter:creator" content="@foto_valera">
        <meta name="twitter:title" content="Videógrafos de Bodas en Almería | Vídeo Cinematográfico Profesional | Foto Valera">
        <meta name="twitter:description" content="Videógrafos profesionales en Almería. Vídeo de bodas cinematográfico en 4K con drones y edición profesional.">
        <meta property="twitter:image" content="{{ $pageImageUrl }}">
        <meta name="twitter:image:alt" content="Foto Valera - Videógrafos Profesionales en Almería">
        
        {{-- Schema.org para Organization Mejorado 2025 - Para imagen en buscador --}}
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "Organization",
            "name": "Foto Valera",
            "legalName": "Foto Valera",
            "url": "{{ route('home') }}",
            "logo": {
                "@type": "ImageObject",
                "url": "{{ Storage::disk('logos')->url('SuperLogo.png') }}",
                "width": "600",
                "height": "600"
            },
            "image": {
                "@type": "ImageObject",
                "url": "{{ $pageImageUrl }}",
                "width": "{{ $pageImageWidth }}",
                "height": "{{ $pageImageHeight }}"
            },
            "description": "Foto Valera es un estudio fotográfico profesional en Almería con más de 23 años de experiencia especializado en videografía de bodas. Videógrafos profesionales que crean películas cinematográficas en 4K con drones, estabilizadores y edición profesional de alta calidad.",
            "address": {
                "@type": "PostalAddress",
                "streetAddress": "C. Alcalde Muñoz, 13",
                "addressLocality": "Almería",
                "addressRegion": "Andalucía",
                "postalCode": "04004",
                "addressCountry": "ES"
            },
            "contactPoint": [
                {
                    "@type": "ContactPoint",
                    "telephone": "+34-660-581-178",
                    "contactType": "customer service",
                    "availableLanguage": ["Spanish", "es"],
                    "areaServed": "ES"
                }
            ],
            "foundingDate": "2001",
            "aggregateRating": {
                "@type": "AggregateRating",
                "ratingValue": "4.9",
                "reviewCount": "76",
                "bestRating": "5",
                "worstRating": "1"
            },
            "sameAs": [
                "https://www.facebook.com/fotovalera",
                "https://www.instagram.com/fotovalera",
                "https://www.twitter.com/foto_valera"
            ],
            "knowsAbout": [
                "Videografía de Bodas",
                "Vídeo Cinematográfico",
                "Vídeo 4K",
                "Drones Profesionales",
                "Edición Cinematográfica"
            ]
        }
        </script>
    </x-slot>

    {{-- @livewire('header-component') --}}
    @livewire('configurable-page-header', [
        'identifier' => 'videos_header', // ¡Nuevo identificador!
    ])

    @livewire('video-gallery-manager', [
        'identifier' => 'homepage-tour-videos',
        'defaultTitle' => 'TOUR VIDEOS',
        'defaultDescription' => 'Descubre los destinos más increíbles a través de nuestros videos de tours.',
    ])
    {{-- Placeholder for potential admin modal to edit header --}}
    {{-- You would create a separate Livewire component or Alpine modal for this --}}
    {{-- @livewire('admin.edit-section-modal') --}}


    <x-self.superPie></x-self.superPie>

</x-app-layout>
