<x-app-layout>
    {{-- ========================================================== --}}
    {{--    SEO COMPLETO Y PROFESIONAL PARA LA PÁGINA DE STUDIO     --}}
    {{-- ========================================================== --}}
    <x-slot name="head">
        {{-- Meta Tags Básicos --}}
        <title>Estudio Fotográfico en Almería | Sesiones Creativas y Temáticas | Foto Valera</title>
        <meta name="description" content="Foto Valera, estudio fotográfico profesional en Almería. Sesiones creativas, temáticas y artísticas: Halloween, Navidad, retratos y proyectos únicos. Más de 23 años de experiencia en fotografía de estudio.">
        <meta name="keywords" content="estudio fotografia almeria, sesiones tematicas almeria, fotografia creativa almeria, fotografo estudio almeria, sesiones halloween almeria, sesiones navidad almeria, fotovalera estudio, foto valera estudio, fotografia artistica almeria, estudio fotografico profesional almeria">
        <meta name="author" content="Foto Valera">
        <meta name="publisher" content="Foto Valera">
        <meta name="robots" content="index, follow">
        <meta name="language" content="es">
        <meta name="geo.region" content="ES-AL">
        <meta name="geo.placename" content="Almería">
        <meta name="geo.position" content="36.8381;-2.4597">
        <meta name="ICBM" content="36.8381, -2.4597">

        {{-- URL Canónica --}}
        <link rel="canonical" href="{{ route('studio.index') }}">

        {{-- Open Graph / Facebook --}}
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ route('studio.index') }}">
        <meta property="og:title" content="Estudio Fotográfico en Almería | Sesiones Creativas y Temáticas | Foto Valera">
        <meta property="og:description" content="Foto Valera, estudio fotográfico profesional en Almería. Sesiones creativas, temáticas y artísticas: Halloween, Navidad, retratos y proyectos únicos.">
        <meta property="og:image" content="{{ Storage::disk('logos')->url('SuperLogo.png') }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:image:alt" content="Foto Valera - Estudio Fotográfico Profesional en Almería">
        <meta property="og:site_name" content="Foto Valera">
        <meta property="og:locale" content="es_ES">

        {{-- Twitter Card --}}
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:site" content="@foto_valera">
        <meta name="twitter:creator" content="@foto_valera">
        <meta name="twitter:title" content="Estudio Fotográfico en Almería | Sesiones Creativas y Temáticas | Foto Valera">
        <meta name="twitter:description" content="Foto Valera, estudio fotográfico profesional en Almería. Sesiones creativas, temáticas y artísticas: Halloween, Navidad, retratos y proyectos únicos.">
        <meta name="twitter:image" content="{{ Storage::disk('logos')->url('SuperLogo.png') }}">
        <meta name="twitter:image:alt" content="Foto Valera - Estudio Fotográfico Profesional en Almería">

        {{-- Meta Tags Adicionales --}}
        <meta name="theme-color" content="#1f2937">
        <meta name="msapplication-TileColor" content="#1f2937">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="Foto Valera">

        {{-- Schema.org JSON-LD --}}
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "Service",
            "name": "Estudio Fotográfico en Almería",
            "description": "Servicio profesional de fotografía de estudio y sesiones creativas en Almería",
            "provider": {
                "@type": "LocalBusiness",
                "name": "Foto Valera",
                "address": {
                    "@type": "PostalAddress",
                    "streetAddress": "C. Alcalde Muñoz, 13",
                    "addressLocality": "Almería",
                    "addressRegion": "Andalucía",
                    "postalCode": "04004",
                    "addressCountry": "ES"
                },
                "geo": {
                    "@type": "GeoCoordinates",
                    "latitude": "36.8381",
                    "longitude": "-2.4597"
                },
                "telephone": "+34-XXX-XXX-XXX",
                "email": "info@fotovalera.com",
                "url": "{{ route('home') }}"
            },
            "areaServed": {
                "@type": "City",
                "name": "Almería"
            },
            "serviceType": "Fotografía de Estudio",
            "offers": {
                "@type": "Offer",
                "description": "Sesiones fotográficas de estudio, creativas y temáticas",
                "priceRange": "€€€"
            },
            "aggregateRating": {
                "@type": "AggregateRating",
                "ratingValue": "4.7",
                "reviewCount": "45"
            }
        }
        </script>

        {{-- Schema.org para FAQ --}}
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "FAQPage",
            "mainEntity": [
                {
                    "@type": "Question",
                    "name": "¿Qué tipos de sesiones realizan en el estudio?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Realizamos sesiones de retrato, sesiones temáticas (Halloween, Navidad), fotografía artística, sesiones creativas, fotos de perfil profesional y proyectos personalizados según tus ideas."
                    }
                },
                {
                    "@type": "Question",
                    "name": "¿Tienen vestuario y accesorios para las sesiones temáticas?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Sí, disponemos de un amplio vestuario y accesorios para sesiones temáticas como Halloween y Navidad. También puedes traer tu propio vestuario para personalizar aún más la sesión."
                    }
                },
                {
                    "@type": "Question",
                    "name": "¿Cuánto dura una sesión de estudio?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Las sesiones de estudio suelen durar entre 1-2 horas, dependiendo del tipo de sesión y la complejidad. Esto nos permite crear diferentes looks y poses sin prisas."
                    }
                },
                {
                    "@type": "Question",
                    "name": "¿Puedo proponer ideas creativas para mi sesión?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "¡Por supuesto! Nos encanta trabajar con ideas creativas y personalizadas. Antes de la sesión, hablamos contigo para planificar y hacer realidad tu visión."
                    }
                }
            ]
        }
        </script>
    </x-slot>


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
