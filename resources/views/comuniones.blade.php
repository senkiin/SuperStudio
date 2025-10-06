<x-app-layout>
    {{-- ========================================================== --}}
    {{--    SEO COMPLETO Y PROFESIONAL PARA FOTOGRAFÍA DE COMUNIONES --}}
    {{-- ========================================================== --}}
    <x-slot name="head">
        {{-- Meta Tags Básicos --}}
        <title>Fotógrafo de Comuniones en Almería | Reportajes de Primera Comunión Profesionales | Foto Valera</title>
        <meta name="description" content="Foto Valera, especialistas en fotografía de comuniones en Almería. Reportajes de primera comunión creativos, emotivos y profesionales. Capturamos la inocencia y alegría de este día tan especial con más de 23 años de experiencia.">
        <meta name="keywords" content="fotografo comunion almeria, reportaje comunion almeria, fotos de comunion almeria, album de comunion almeria, fotovalera comuniones, foto valera comuniones, fotografo infantil almeria, primera comunion almeria, fotografo comunion profesional almeria">
        <meta name="author" content="Foto Valera">
        <meta name="publisher" content="Foto Valera">
        <meta name="robots" content="index, follow">
        <meta name="language" content="es">
        <meta name="geo.region" content="ES-AL">
        <meta name="geo.placename" content="Almería">
        <meta name="geo.position" content="36.8381;-2.4597">
        <meta name="ICBM" content="36.8381, -2.4597">

        {{-- URL Canónica --}}
        <link rel="canonical" href="{{ route('comuniones') }}">

        {{-- Open Graph / Facebook --}}
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ route('comuniones') }}">
        <meta property="og:title" content="Fotógrafo de Comuniones en Almería | Reportajes de Primera Comunión Profesionales | Foto Valera">
        <meta property="og:description" content="Foto Valera, especialistas en fotografía de comuniones en Almería. Reportajes de primera comunión creativos, emotivos y profesionales. Capturamos la inocencia y alegría de este día tan especial.">
        <meta property="og:image" content="{{ Storage::disk('logos')->url('SuperLogo.png') }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:image:alt" content="Foto Valera - Fotógrafo de Comuniones Profesional en Almería">
        <meta property="og:site_name" content="Foto Valera">
        <meta property="og:locale" content="es_ES">

        {{-- Twitter Card --}}
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:site" content="@foto_valera">
        <meta name="twitter:creator" content="@foto_valera">
        <meta name="twitter:title" content="Fotógrafo de Comuniones en Almería | Reportajes de Primera Comunión Profesionales | Foto Valera">
        <meta name="twitter:description" content="Foto Valera, especialistas en fotografía de comuniones en Almería. Reportajes de primera comunión creativos, emotivos y profesionales.">
        <meta name="twitter:image" content="{{ Storage::disk('logos')->url('SuperLogo.png') }}">
        <meta name="twitter:image:alt" content="Foto Valera - Fotógrafo de Comuniones Profesional en Almería">

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
            "name": "Fotografía de Comuniones en Almería",
            "description": "Servicio profesional de fotografía de primera comunión en Almería",
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
            "serviceType": "Fotografía de Comuniones",
            "offers": {
                "@type": "Offer",
                "description": "Reportajes fotográficos de primera comunión",
                "priceRange": "€€€"
            },
            "aggregateRating": {
                "@type": "AggregateRating",
                "ratingValue": "4.8",
                "reviewCount": "92"
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
                    "name": "¿Qué incluye un reportaje de comunión?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Incluye sesión fotográfica en estudio y/o exteriores, fotos de la ceremonia (si se desea), edición profesional de todas las fotos, entrega en alta resolución y álbum digital. También ofrecemos álbumes físicos personalizados."
                    }
                },
                {
                    "@type": "Question",
                    "name": "¿Cuándo debo reservar la sesión de comunión?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Recomendamos reservar con al menos 2-3 meses de antelación, especialmente en temporada de comuniones (mayo-junio). Esto nos permite planificar mejor la sesión y asegurar disponibilidad."
                    }
                },
                {
                    "@type": "Question",
                    "name": "¿Dónde realizan las sesiones de comunión?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Realizamos sesiones en nuestro estudio profesional, en exteriores con los paisajes únicos de Almería, o en la iglesia donde se celebrará la comunión. Nos adaptamos a vuestras preferencias."
                    }
                },
                {
                    "@type": "Question",
                    "name": "¿Pueden participar los familiares en la sesión?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "¡Por supuesto! Las fotos familiares son muy importantes en este día especial. Incluimos poses con padres, abuelos, hermanos y toda la familia para crear recuerdos únicos."
                    }
                }
            ]
        }
        </script>
    </x-slot>

        <div>

            @livewire('configurable-page-header', [
                'identifier' => 'Comuniones_header',
            ])
            @livewire('curated-portrait-gallery', [
                'identifier' => 'Comuniones_gallery',
            ])
        </div>
         <x-self.section-text
        title="Reportajes de Primera Comunión en Almería: Recuerdos Inolvidables

"
        subtitle=" La Primera Comunión es un hito lleno de significado e ilusión en la vida de vuestros hijos. En Fotovalera, nos dedicamos a crear reportajes de comunión en Almería que reflejen la alegría, la inocencia y la personalidad única de cada niño y niña en este día tan especial.
                Con un enfoque creativo y cercano, buscamos capturar no solo las fotografías tradicionales, sino también esos momentos espontáneos y emotivos que hacen que cada comunión sea diferente. Ofrecemos sesiones en estudio, exteriores con encanto en Almería, o en la iglesia, adaptándonos a vuestras preferencias para crear un recuerdo que atesoraréis para siempre.


" />
        <x-self.superPie></x-self.superPie>
</x-app-layout>
