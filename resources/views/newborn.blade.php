<x-app-layout>
    {{-- ========================================================== --}}
    {{--    SEO COMPLETO Y PROFESIONAL PARA FOTOGRAFÍA NEWBORN      --}}
    {{-- ========================================================== --}}
    <x-slot name="head">
        {{-- Meta Tags Básicos --}}
        <title>Fotografía Newborn en Almería | Sesiones de Recién Nacidos Profesionales | Foto Valera</title>
        <meta name="description" content="Foto Valera, especialistas en fotografía newborn en Almería. Sesiones de recién nacido seguras, tiernas y profesionales. Capturamos la magia de los primeros días de tu bebé con más de 23 años de experiencia.">
        <meta name="keywords" content="fotografia newborn almeria, fotografo recien nacido almeria, sesion de fotos bebe almeria, fotos newborn almeria, estudio fotografia bebes almeria, fotovalera newborn, foto valera newborn, fotografo newborn profesional almeria, sesion recien nacido almeria">
        <meta name="author" content="Foto Valera">
        <meta name="publisher" content="Foto Valera">
        <meta name="robots" content="index, follow">
        <meta name="language" content="es">
        <meta name="geo.region" content="ES-AL">
        <meta name="geo.placename" content="Almería">
        <meta name="geo.position" content="36.8381;-2.4597">
        <meta name="ICBM" content="36.8381, -2.4597">

        {{-- URL Canónica --}}
        <link rel="canonical" href="{{ route('newborn.index') }}">

        {{-- Open Graph / Facebook --}}
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ route('newborn.index') }}">
        <meta property="og:title" content="Fotografía Newborn en Almería | Sesiones de Recién Nacidos Profesionales | Foto Valera">
        <meta property="og:description" content="Foto Valera, especialistas en fotografía newborn en Almería. Sesiones de recién nacido seguras, tiernas y profesionales. Capturamos la magia de los primeros días de tu bebé.">
        <meta property="og:image" content="{{ Storage::disk('logos')->url('SuperLogo.png') }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:image:alt" content="Foto Valera - Fotografía Newborn Profesional en Almería">
        <meta property="og:site_name" content="Foto Valera">
        <meta property="og:locale" content="es_ES">

        {{-- Twitter Card --}}
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:site" content="@foto_valera">
        <meta name="twitter:creator" content="@foto_valera">
        <meta name="twitter:title" content="Fotografía Newborn en Almería | Sesiones de Recién Nacidos Profesionales | Foto Valera">
        <meta name="twitter:description" content="Foto Valera, especialistas en fotografía newborn en Almería. Sesiones de recién nacido seguras, tiernas y profesionales.">
        <meta name="twitter:image" content="{{ Storage::disk('logos')->url('SuperLogo.png') }}">
        <meta name="twitter:image:alt" content="Foto Valera - Fotografía Newborn Profesional en Almería">

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
            "name": "Fotografía Newborn en Almería",
            "description": "Servicio profesional de fotografía newborn y recién nacidos en Almería",
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
                "telephone": "+34-660-581-178",
                "email": "info@fotovalera.com",
                "url": "{{ route('home') }}"
            },
            "areaServed": {
                "@type": "City",
                "name": "Almería"
            },
            "serviceType": "Fotografía Newborn",
            "offers": {
                "@type": "Offer",
                "description": "Sesiones fotográficas newborn y de recién nacidos",
                "priceRange": "€€€"
            },
            "aggregateRating": {
                "@type": "AggregateRating",
                "ratingValue": "4.9",
                "reviewCount": "78"
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
                    "name": "¿Cuándo es el mejor momento para hacer fotos newborn?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "El momento ideal para fotos newborn es entre los 5-15 días de vida del bebé, cuando duermen más profundamente y son más flexibles para las poses."
                    }
                },
                {
                    "@type": "Question",
                    "name": "¿Son seguras las sesiones newborn?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Absolutamente. Tenemos más de 23 años de experiencia y seguimos estrictos protocolos de seguridad. El bienestar del bebé es nuestra prioridad absoluta."
                    }
                },
                {
                    "@type": "Question",
                    "name": "¿Qué incluye una sesión newborn?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Incluye sesión fotográfica profesional, todos los accesorios y props, edición de fotos, entrega en alta resolución y álbum digital. También ofrecemos productos personalizados."
                    }
                },
                {
                    "@type": "Question",
                    "name": "¿Pueden participar los padres en la sesión newborn?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "¡Por supuesto! Las fotos con los padres son muy especiales y emotivas. Incluimos poses familiares tiernas y momentos de conexión."
                    }
                }
            ]
        }
        </script>
    </x-slot>
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
