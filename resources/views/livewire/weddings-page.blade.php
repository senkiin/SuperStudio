<div>
    {{-- ========================================================== --}}
    {{--    SEO COMPLETO Y PROFESIONAL PARA LA PÁGINA DE BODAS      --}}
    {{-- ========================================================== --}}
    <x-slot name="head">
        @php
            // Obtener imagen del header de bodas
            $headerSettings = \App\Models\PageHeaderSetting::where('identifier', 'bodas_header')->first();
            if ($headerSettings && $headerSettings->background_image_url) {
                $pageImageUrl = Storage::disk('page-headers')->url($headerSettings->background_image_url);
            } else {
                $pageImageUrl = Storage::disk('logos')->url('SuperLogo.png');
            }
            $pageImageWidth = 1200;
            $pageImageHeight = 630;
        @endphp
        {{-- Meta Tags Básicos Optimizados para SEO 2025 --}}
        <title>Fotógrafo de Bodas en Almería | Reportajes de Boda Profesionales | Foto Valera</title>
        <meta name="description" content="Foto Valera, fotógrafo de bodas profesional en Almería con más de 23 años de experiencia. Reportajes de boda únicos, creativos y emotivos. Capturamos vuestro día más especial con pasión y profesionalidad.">
        <meta name="keywords" content="fotografo bodas almeria, fotografos de bodas almeria, reportajes de bodas almeria, fotografia boda almeria, video boda almeria, videografos bodas almeria, fotografo matrimonio almeria, fotovalera bodas, bodas en almeria, reportaje fotografico boda almeria, fotografo profesional bodas, videografia bodas almeria, fotografos bodas en almeria, bodas almeria fotografo">
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
        <meta name="classification" content="Fotografía de Bodas, Videografía de Bodas, Reportajes Matrimoniales">

        {{-- URL Canónica --}}
        <link rel="canonical" href="{{ route('weddings') }}">

        {{-- Open Graph / Facebook --}}
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ route('weddings') }}">
        <meta property="og:title" content="Fotógrafo de Bodas en Almería | Reportajes de Boda Profesionales | Foto Valera">
        <meta property="og:description" content="Foto Valera, fotógrafo de bodas profesional en Almería. Más de 23 años capturando momentos únicos de vuestro día más especial. Reportajes creativos, emotivos y de calidad profesional.">
        <meta property="og:image" content="{{ $pageImageUrl }}">
        <meta property="og:image:width" content="{{ $pageImageWidth }}">
        <meta property="og:image:height" content="{{ $pageImageHeight }}">
        <meta property="og:image:alt" content="Foto Valera - Fotógrafo de Bodas Profesional en Almería">
        <meta property="og:site_name" content="Foto Valera">
        <meta property="og:locale" content="es_ES">

        {{-- Twitter Card --}}
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:site" content="@foto_valera">
        <meta name="twitter:creator" content="@foto_valera">
        <meta name="twitter:title" content="Fotógrafo de Bodas en Almería | Reportajes de Boda Profesionales | Foto Valera">
        <meta name="twitter:description" content="Foto Valera, fotógrafo de bodas profesional en Almería. Más de 23 años capturando momentos únicos de vuestro día más especial.">
        <meta name="twitter:image" content="{{ $pageImageUrl }}">
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
        
        {{-- Schema.org para LocalBusiness (para valoraciones) --}}
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "LocalBusiness",
            "name": "Foto Valera",
            "image": {
                "@type": "ImageObject",
                "url": "{{ $pageImageUrl }}",
                "width": "{{ $pageImageWidth }}",
                "height": "{{ $pageImageHeight }}"
            },
            "logo": {
                "@type": "ImageObject",
                "url": "{{ Storage::disk('logos')->url('SuperLogo.png') }}",
                "width": "600",
                "height": "600"
            },
            "@id": "{{ route('home') }}#organization",
            "url": "{{ route('home') }}",
            "telephone": "+34-660-581-178",
            "email": "info@fotovalera.com",
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
            "openingHoursSpecification": [
                {
                    "@type": "OpeningHoursSpecification",
                    "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"],
                    "opens": "09:00",
                    "closes": "19:00"
                },
                {
                    "@type": "OpeningHoursSpecification",
                    "dayOfWeek": "Saturday",
                    "opens": "09:00",
                    "closes": "14:00"
                }
            ],
            "priceRange": "€€€",
            "aggregateRating": {
                "@type": "AggregateRating",
                "ratingValue": "4.9",
                "reviewCount": "89",
                "bestRating": "5",
                "worstRating": "1"
            },
            "sameAs": [
                "https://www.facebook.com/fotovalera",
                "https://www.instagram.com/fotovalera",
                "https://www.twitter.com/foto_valera"
            ]
        }
        </script>

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
            "description": "Foto Valera es un estudio fotográfico profesional en Almería con más de 23 años de experiencia especializado en fotografía y videografía de bodas. Más de 1,000 bodas fotografiadas con equipo profesional de última generación, drones y edición cinematográfica.",
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
                "reviewCount": "89",
                "bestRating": "5",
                "worstRating": "1"
            },
            "sameAs": [
                "https://www.facebook.com/fotovalera",
                "https://www.instagram.com/fotovalera",
                "https://www.twitter.com/foto_valera"
            ],
            "knowsAbout": [
                "Fotografía de Bodas",
                "Videografía de Bodas",
                "Fotografía Cinematográfica",
                "Reportajes Matrimoniales"
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

    {{-- Sección SEO: Contenido Optimizado para Fotógrafos de Bodas Almería --}}
    <section class="bg-gradient-to-b from-black via-gray-950 to-black py-20 sm:py-28">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Fotógrafos de Bodas en Almería - Contenido Principal --}}
            <div class="mb-20">
                <div class="text-center mb-12">
                    <p class="text-indigo-400 font-semibold text-sm uppercase tracking-wide mb-3">Fotógrafos Profesionales</p>
                    <h2 class="text-4xl sm:text-5xl font-bold text-white mb-6">
                        Los Mejores Fotógrafos de Bodas en Almería
                    </h2>
                    <div class="w-24 h-1 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 mx-auto rounded-full"></div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-16">
                    <div class="bg-gradient-to-br from-gray-900 to-gray-950 rounded-2xl p-8 border border-gray-800 shadow-xl">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="flex items-center justify-center w-14 h-14 bg-indigo-600/20 rounded-xl">
                                <svg class="w-8 h-8 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-white">Reportajes de Bodas Únicos</h3>
                        </div>
                        <p class="text-gray-300 leading-relaxed mb-4">
                            Como <strong class="text-white">fotógrafos de bodas en Almería</strong>, entendemos que cada pareja es única.
                            Nuestros <strong class="text-white">reportajes de bodas</strong> no siguen un formato estándar; nos adaptamos a vuestro
                            estilo, personalidad y visión. Desde bodas íntimas en cortijos hasta grandes celebraciones en <strong class="text-white">playas de Almería</strong>,
                            capturamos la magia de vuestro amor de manera auténtica y emotiva.
                        </p>
                        <p class="text-gray-300 leading-relaxed">
                            Más de <strong class="text-white">1,000 bodas fotografiadas</strong> en toda la provincia nos convierten en uno de los
                            <strong class="text-white">fotógrafos de bodas más experimentados de Almería</strong>. Conocemos los mejores rincones,
                            la luz perfecta en cada momento del día y cómo capturar la emoción genuina de vuestros invitados.
                        </p>
                    </div>

                    <div class="bg-gradient-to-br from-gray-900 to-gray-950 rounded-2xl p-8 border border-gray-800 shadow-xl">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="flex items-center justify-center w-14 h-14 bg-purple-600/20 rounded-xl">
                                <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-white">Videografía de Bodas Cinematográfica</h3>
                        </div>
                        <p class="text-gray-300 leading-relaxed mb-4">
                            Como <strong class="text-white">videógrafos profesionales de bodas en Almería</strong>, creamos películas que cuentan vuestra
                            historia de amor de forma cinematográfica. Utilizamos <strong class="text-white">drones, cámaras 4K y estabilizadores</strong> para
                            capturar cada ángulo perfecto, cada sonrisa sincera y cada lágrima de emoción.
                        </p>
                        <p class="text-gray-300 leading-relaxed">
                            Nuestro servicio de <strong class="text-white">vídeo de bodas en Almería</strong> incluye edición profesional con música
                            personalizada, corrección de color avanzada y efectos cinematográficos. El resultado es una película emotiva que podréis
                            revivir una y otra vez, capturando no solo imágenes, sino también el sonido de vuestras voces, las risas y la atmósfera única
                            de vuestro día especial.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Estilos de Fotografía de Bodas --}}
            <div class="mb-20">
                <div class="text-center mb-12">
                    <h2 class="text-3xl sm:text-4xl font-bold text-white mb-4">Nuestro Estilo de Fotografía de Bodas</h2>
                    <p class="text-gray-400 text-lg max-w-3xl mx-auto">
                        Como <strong class="text-white">fotógrafos especializados en bodas</strong>, combinamos diferentes estilos para crear
                        reportajes únicos y memorables
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="bg-gray-900/50 backdrop-blur-sm rounded-xl p-6 border border-gray-800 hover:border-indigo-500/50 transition-all duration-300 transform hover:scale-105">
                        <div class="text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-2xl mb-4 shadow-lg shadow-indigo-500/20">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-3">Fotografía Natural</h3>
                            <p class="text-gray-400">
                                Capturamos momentos espontáneos y genuinos. Nuestro estilo <strong class="text-white">natural y documental</strong>
                                refleja emociones reales sin poses forzadas.
                            </p>
                        </div>
                    </div>

                    <div class="bg-gray-900/50 backdrop-blur-sm rounded-xl p-6 border border-gray-800 hover:border-purple-500/50 transition-all duration-300 transform hover:scale-105">
                        <div class="text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-purple-600 to-pink-600 rounded-2xl mb-4 shadow-lg shadow-purple-500/20">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-3">Fotografía Artística</h3>
                            <p class="text-gray-400">
                                Creamos <strong class="text-white">imágenes artísticas y creativas</strong> utilizando la luz, composición y los
                                paisajes únicos de Almería como lienzo.
                            </p>
                        </div>
                    </div>

                    <div class="bg-gray-900/50 backdrop-blur-sm rounded-xl p-6 border border-gray-800 hover:border-pink-500/50 transition-all duration-300 transform hover:scale-105">
                        <div class="text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-pink-600 to-red-600 rounded-2xl mb-4 shadow-lg shadow-pink-500/20">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-3">Fotografía Romántica</h3>
                            <p class="text-gray-400">
                                Capturamos la <strong class="text-white">conexión y el amor</strong> entre vosotros con imágenes románticas,
                                íntimas y llenas de emoción.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Localizaciones de Bodas en Almería --}}
            <div class="mb-20 bg-gradient-to-r from-indigo-950/50 to-purple-950/50 rounded-2xl p-8 lg:p-12 border border-indigo-800/30 backdrop-blur-sm">
                <div class="text-center mb-10">
                    <h2 class="text-3xl font-bold text-white mb-4">Bodas en Toda la Provincia de Almería</h2>
                    <p class="text-gray-300 text-lg max-w-3xl mx-auto">
                        Como <strong class="text-white">fotógrafos de bodas en Almería</strong>, conocemos los mejores lugares para
                        vuestro reportaje fotográfico
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-gray-900/60 rounded-lg p-5 text-center border border-gray-700 hover:border-indigo-500/50 transition-all">
                        <div class="text-2xl mb-2">🏖️</div>
                        <h4 class="text-white font-bold mb-2">Bodas en Playa</h4>
                        <p class="text-gray-400 text-sm">Cabo de Gata, Mojácar, San José, Las Negras</p>
                    </div>
                    <div class="bg-gray-900/60 rounded-lg p-5 text-center border border-gray-700 hover:border-purple-500/50 transition-all">
                        <div class="text-2xl mb-2">🏰</div>
                        <h4 class="text-white font-bold mb-2">Cortijos y Haciendas</h4>
                        <p class="text-gray-400 text-sm">Níjar, Tabernas, Sorbas, Vera</p>
                    </div>
                    <div class="bg-gray-900/60 rounded-lg p-5 text-center border border-gray-700 hover:border-pink-500/50 transition-all">
                        <div class="text-2xl mb-2">⛪</div>
                        <h4 class="text-white font-bold mb-2">Iglesias y Ermitas</h4>
                        <p class="text-gray-400 text-sm">Catedral de Almería, iglesias históricas</p>
                    </div>
                    <div class="bg-gray-900/60 rounded-lg p-5 text-center border border-gray-700 hover:border-red-500/50 transition-all">
                        <div class="text-2xl mb-2">🏨</div>
                        <h4 class="text-white font-bold mb-2">Hoteles y Salones</h4>
                        <p class="text-gray-400 text-sm">Roquetas, El Ejido, ciudad de Almería</p>
                    </div>
                </div>

                <p class="text-gray-300 text-center leading-relaxed">
                    Nuestros <strong class="text-white">fotógrafos profesionales de bodas</strong> nos desplazamos a cualquier punto de
                    <strong class="text-white">Almería y provincia</strong> para capturar vuestro día especial. Ya sea una boda en las
                    <strong class="text-white">playas paradisíacas del Cabo de Gata</strong>, un cortijo rústico en el desierto de Tabernas,
                    o una elegante celebración en la ciudad, adaptamos nuestro servicio a vuestras necesidades.
                </p>
            </div>

            {{-- Qué Incluyen Nuestros Reportajes --}}
            <div class="mb-20">
                <div class="text-center mb-12">
                    <h2 class="text-3xl sm:text-4xl font-bold text-white mb-4">Qué Incluyen Nuestros Reportajes de Bodas</h2>
                    <p class="text-gray-400 text-lg max-w-3xl mx-auto">
                        Paquetes completos de fotografía y vídeo de bodas en Almería
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="bg-gradient-to-br from-gray-900 to-gray-950 rounded-xl p-6 border border-gray-800">
                        <h3 class="text-xl font-bold text-white mb-4 flex items-center gap-3">
                            <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Cobertura Completa del Día
                        </h3>
                        <ul class="space-y-3 text-gray-300">
                            <li class="flex items-start gap-2">
                                <span class="text-indigo-400 mt-1">•</span>
                                <span>Preparativos de novia y novio</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-indigo-400 mt-1">•</span>
                                <span>Ceremonia religiosa o civil completa</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-indigo-400 mt-1">•</span>
                                <span>Cóctel y recepción de invitados</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-indigo-400 mt-1">•</span>
                                <span>Sesión de pareja en exteriores</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-indigo-400 mt-1">•</span>
                                <span>Banquete y celebración completa</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-indigo-400 mt-1">•</span>
                                <span>Primer baile y momentos especiales</span>
                            </li>
                        </ul>
                    </div>

                    <div class="bg-gradient-to-br from-gray-900 to-gray-950 rounded-xl p-6 border border-gray-800">
                        <h3 class="text-xl font-bold text-white mb-4 flex items-center gap-3">
                            <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Entrega y Postproducción
                        </h3>
                        <ul class="space-y-3 text-gray-300">
                            <li class="flex items-start gap-2">
                                <span class="text-purple-400 mt-1">•</span>
                                <span>Todas las fotos editadas en alta resolución</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-purple-400 mt-1">•</span>
                                <span>Vídeo resumen cinematográfico (5-10 min)</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-purple-400 mt-1">•</span>
                                <span>Vídeo completo de la ceremonia</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-purple-400 mt-1">•</span>
                                <span>Galería online privada para compartir</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-purple-400 mt-1">•</span>
                                <span>USB personalizado con todo el contenido</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-purple-400 mt-1">•</span>
                                <span>Entrega en 4-6 semanas</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="mt-8 bg-gray-900/50 border border-gray-800 rounded-xl p-6">
                    <p class="text-gray-300 leading-relaxed">
                        <strong class="text-white">Nuestro servicio de fotografía y vídeo de bodas en Almería</strong> incluye equipo profesional de última generación:
                        cámaras Full Frame, lentes de alta calidad, drones con grabación 4K, estabilizadores, iluminación profesional y sistema de audio inalámbrico
                        para capturar los votos y discursos con claridad perfecta. Como <strong class="text-white">fotógrafos y videógrafos experimentados</strong>,
                        contamos con equipos de respaldo para garantizar que ningún momento se pierda.
                    </p>
                </div>
            </div>

            {{-- Por Qué Elegirnos --}}
            <div class="mb-20 bg-gradient-to-br from-gray-900 to-gray-950 rounded-2xl p-8 lg:p-12 border border-gray-800">
                <div class="text-center mb-10">
                    <h2 class="text-3xl font-bold text-white mb-4">
                        ¿Por Qué Elegir a Foto Valera para Vuestra Boda en Almería?
                    </h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-2xl mb-4 shadow-lg shadow-indigo-500/20">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3">+23 Años de Experiencia</h3>
                        <p class="text-gray-300">
                            Más de dos décadas como <strong class="text-white">fotógrafos profesionales de bodas</strong>, con más de
                            1,000 bodas documentadas en toda Almería.
                        </p>
                    </div>

                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-purple-600 to-pink-600 rounded-2xl mb-4 shadow-lg shadow-purple-500/20">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3">Calidad Garantizada</h3>
                        <p class="text-gray-300">
                            Utilizamos equipo profesional de última generación y técnicas avanzadas de edición para resultados excepcionales.
                        </p>
                    </div>

                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-pink-600 to-red-600 rounded-2xl mb-4 shadow-lg shadow-pink-500/20">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3">Atención Personalizada</h3>
                        <p class="text-gray-300">
                            Cada <strong class="text-white">reportaje de boda</strong> es único. Nos reunimos con vosotros para conocer vuestros
                            deseos y crear un plan personalizado.
                        </p>
                    </div>
                </div>
            </div>

            {{-- CTA Final --}}
            <div class="text-center bg-gradient-to-r from-indigo-600/10 to-purple-600/10 rounded-2xl p-10 border border-indigo-500/30">
                <h2 class="text-3xl font-bold text-white mb-4">¿Listos para Reservar Vuestro Fotógrafo de Bodas en Almería?</h2>
                <p class="text-gray-300 text-lg mb-8 max-w-2xl mx-auto">
                    Contáctanos hoy mismo y hablemos sobre cómo podemos capturar la magia de vuestro día especial.
                    Plazas limitadas para cada temporada.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('gallery') }}"
                        class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-bold rounded-xl shadow-lg shadow-indigo-500/20 transition-all duration-300 transform hover:scale-105">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Ver Más Bodas
                    </a>
                    <button onclick="Livewire.dispatch('openContactModal')"
                        class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-gradient-to-r from-pink-600 to-red-600 hover:from-pink-500 hover:to-red-500 text-white font-bold rounded-xl shadow-lg shadow-pink-500/20 transition-all duration-300 transform hover:scale-105 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Solicitar Información
                    </button>
                </div>
            </div>

            {{-- Enlaces a Otros Servicios --}}
            <div class="mt-16">
                <h3 class="text-xl font-bold text-white text-center mb-6">Descubre Nuestros Otros Servicios</h3>
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="{{ route('comuniones') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-purple-600/20 hover:bg-purple-600/30 border border-purple-500/30 rounded-lg text-purple-300 hover:text-purple-200 transition-all duration-300 transform hover:scale-105">
                        Reportajes de Comuniones
                    </a>
                    <a href="{{ route('videos') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-indigo-600/20 hover:bg-indigo-600/30 border border-indigo-500/30 rounded-lg text-indigo-300 hover:text-indigo-200 transition-all duration-300 transform hover:scale-105">
                        Vídeos Profesionales
                    </a>
                    <a href="{{ route('embarazo.index') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-pink-600/20 hover:bg-pink-600/30 border border-pink-500/30 rounded-lg text-pink-300 hover:text-pink-200 transition-all duration-300 transform hover:scale-105">
                        Fotografía de Embarazo
                    </a>
                    <a href="{{ route('newborn.index') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-green-600/20 hover:bg-green-600/30 border border-green-500/30 rounded-lg text-green-300 hover:text-green-200 transition-all duration-300 transform hover:scale-105">
                        Fotografía Newborn
                    </a>
                </div>
            </div>
        </div>
    </section>

    <x-self.superPie></x-self.superPie>

</div>
