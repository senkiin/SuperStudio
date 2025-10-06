<div>
    {{-- ========================================================== --}}
    {{--    SEO COMPLETO Y PROFESIONAL PARA LA PÁGINA DE BODAS      --}}
    {{-- ========================================================== --}}
    <x-slot name="head">
        {{-- Meta Tags Básicos --}}
        <title>Fotógrafo de Bodas en Almería | Reportajes de Boda Profesionales | Foto Valera</title>
        <meta name="description" content="Foto Valera, fotógrafo de bodas profesional en Almería con más de 23 años de experiencia. Reportajes de boda únicos, creativos y emotivos. Capturamos vuestro día más especial con pasión y profesionalidad.">
        <meta name="keywords" content="fotografo bodas almeria, fotografo de boda almeria, reportaje boda almeria, fotografia boda almeria, video boda almeria, fotografo matrimonio almeria, fotovalera bodas, foto valera bodas, videografo boda almeria, reportaje fotografico boda almeria">
        <meta name="author" content="Foto Valera">
        <meta name="publisher" content="Foto Valera">
        <meta name="robots" content="index, follow">
        <meta name="language" content="es">
        <meta name="geo.region" content="ES-AL">
        <meta name="geo.placename" content="Almería">
        <meta name="geo.position" content="36.8381;-2.4597">
        <meta name="ICBM" content="36.8381, -2.4597">

        {{-- URL Canónica --}}
        <link rel="canonical" href="{{ route('weddings') }}">

        {{-- Open Graph / Facebook --}}
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ route('weddings') }}">
        <meta property="og:title" content="Fotógrafo de Bodas en Almería | Reportajes de Boda Profesionales | Foto Valera">
        <meta property="og:description" content="Foto Valera, fotógrafo de bodas profesional en Almería. Más de 23 años capturando momentos únicos de vuestro día más especial. Reportajes creativos, emotivos y de calidad profesional.">
        <meta property="og:image" content="{{ Storage::disk('logos')->url('SuperLogo.png') }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:image:alt" content="Foto Valera - Fotógrafo de Bodas Profesional en Almería">
        <meta property="og:site_name" content="Foto Valera">
        <meta property="og:locale" content="es_ES">

        {{-- Twitter Card --}}
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:site" content="@foto_valera">
        <meta name="twitter:creator" content="@foto_valera">
        <meta name="twitter:title" content="Fotógrafo de Bodas en Almería | Reportajes de Boda Profesionales | Foto Valera">
        <meta name="twitter:description" content="Foto Valera, fotógrafo de bodas profesional en Almería. Más de 23 años capturando momentos únicos de vuestro día más especial.">
        <meta name="twitter:image" content="{{ Storage::disk('logos')->url('SuperLogo.png') }}">
        <meta name="twitter:image:alt" content="Foto Valera - Fotógrafo de Bodas Profesional en Almería">

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
            "name": "Fotografía de Bodas en Almería",
            "description": "Servicio profesional de fotografía y videografía para bodas en Almería y provincia",
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
            "serviceType": "Fotografía de Bodas",
            "offers": {
                "@type": "Offer",
                "description": "Reportajes fotográficos y de vídeo para bodas",
                "priceRange": "€€€"
            },
            "aggregateRating": {
                "@type": "AggregateRating",
                "ratingValue": "4.9",
                "reviewCount": "89"
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
                    "name": "¿Qué incluye el reportaje de boda en Foto Valera?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Nuestro reportaje de boda incluye fotografía y videografía profesional, sesión de preparación, ceremonia, banquete y sesión de pareja. Entregamos todas las fotos editadas en alta resolución y un vídeo resumen del día."
                    }
                },
                {
                    "@type": "Question",
                    "name": "¿Cuánto tiempo antes debo reservar el fotógrafo de boda?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Recomendamos reservar con al menos 6-12 meses de antelación, especialmente en temporada alta (primavera y verano). Esto nos permite planificar mejor vuestro día especial."
                    }
                },
                {
                    "@type": "Question",
                    "name": "¿Trabajáis en toda la provincia de Almería?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Sí, realizamos reportajes de boda en toda la provincia de Almería. Nos desplazamos a cualquier ubicación: playas, cortijos, hoteles, iglesias o espacios al aire libre."
                    }
                },
                {
                    "@type": "Question",
                    "name": "¿Ofrecen sesión de pareja antes de la boda?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Sí, ofrecemos sesiones de pareja (engagement) antes de la boda. Es una excelente oportunidad para conocernos y crear fotos románticas en los paisajes únicos de Almería."
                    }
                }
            ]
        }
        </script>
    </x-slot>
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
