<x-app-layout>
    {{-- ========================================================== --}}
    {{--    SEO COMPLETO Y PROFESIONAL PARA FOTOGRAFÍA DE EMBARAZO    --}}
    {{-- ========================================================== --}}
    <x-slot name="head">
        {{-- Meta Tags Básicos --}}
        <title>Fotografía de Embarazo en Almería | Sesiones de Maternidad Profesionales | Foto Valera</title>
        <meta name="description" content="Foto Valera, especialistas en fotografía de embarazo en Almería. Sesiones de maternidad artísticas y emotivas. Capturamos la belleza de la dulce espera con más de 23 años de experiencia profesional.">
        <meta name="keywords" content="fotografia embarazo almeria, sesion fotos embarazada almeria, fotografo maternidad almeria, fotos de embarazo almeria, reportaje embarazo almeria, fotovalera embarazo, foto valera embarazo, sesion maternidad almeria, fotografo embarazo profesional almeria">
        <meta name="author" content="Foto Valera">
        <meta name="publisher" content="Foto Valera">
        <meta name="robots" content="index, follow">
        <meta name="language" content="es">
        <meta name="geo.region" content="ES-AL">
        <meta name="geo.placename" content="Almería">
        <meta name="geo.position" content="36.8381;-2.4597">
        <meta name="ICBM" content="36.8381, -2.4597">

        {{-- URL Canónica --}}
        <link rel="canonical" href="{{ route('embarazo.index') }}">

        {{-- Open Graph / Facebook --}}
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ route('embarazo.index') }}">
        <meta property="og:title" content="Fotografía de Embarazo en Almería | Sesiones de Maternidad Profesionales | Foto Valera">
        <meta property="og:description" content="Foto Valera, especialistas en fotografía de embarazo en Almería. Sesiones de maternidad artísticas y emotivas. Capturamos la belleza de la dulce espera con profesionalidad.">
        <meta property="og:image" content="{{ Storage::disk('logos')->url('SuperLogo.png') }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:image:alt" content="Foto Valera - Fotografía de Embarazo Profesional en Almería">
        <meta property="og:site_name" content="Foto Valera">
        <meta property="og:locale" content="es_ES">

        {{-- Twitter Card --}}
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:site" content="@foto_valera">
        <meta name="twitter:creator" content="@foto_valera">
        <meta name="twitter:title" content="Fotografía de Embarazo en Almería | Sesiones de Maternidad Profesionales | Foto Valera">
        <meta name="twitter:description" content="Foto Valera, especialistas en fotografía de embarazo en Almería. Sesiones de maternidad artísticas y emotivas.">
        <meta name="twitter:image" content="{{ Storage::disk('logos')->url('SuperLogo.png') }}">
        <meta name="twitter:image:alt" content="Foto Valera - Fotografía de Embarazo Profesional en Almería">

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
            "name": "Fotografía de Embarazo en Almería",
            "description": "Servicio profesional de fotografía de maternidad y embarazo en Almería",
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
            "serviceType": "Fotografía de Embarazo",
            "offers": {
                "@type": "Offer",
                "description": "Sesiones fotográficas de maternidad y embarazo",
                "priceRange": "€€€"
            },
            "aggregateRating": {
                "@type": "AggregateRating",
                "ratingValue": "4.8",
                "reviewCount": "67"
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
                    "name": "¿Cuándo es el mejor momento para hacer una sesión de fotos de embarazo?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "El momento ideal para una sesión de fotos de embarazo es entre las semanas 28-36, cuando la barriga está bien redondeada pero aún te sientes cómoda para posar."
                    }
                },
                {
                    "@type": "Question",
                    "name": "¿Dónde realizan las sesiones de fotografía de embarazo?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Realizamos sesiones en nuestro estudio profesional, en exteriores con los paisajes únicos de Almería, o en la intimidad de tu hogar. Nos adaptamos a tus preferencias."
                    }
                },
                {
                    "@type": "Question",
                    "name": "¿Qué incluye una sesión de fotos de embarazo?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Incluye sesión fotográfica profesional, edición de todas las fotos, entrega en alta resolución y álbum digital. También ofrecemos álbumes físicos y productos personalizados."
                    }
                },
                {
                    "@type": "Question",
                    "name": "¿Puede participar la pareja en la sesión de embarazo?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "¡Por supuesto! Las sesiones de embarazo son perfectas para incluir a la pareja y crear recuerdos familiares únicos antes de la llegada del bebé."
                    }
                }
            ]
        }
        </script>
    </x-slot>

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
