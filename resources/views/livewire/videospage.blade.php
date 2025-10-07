<div>
    {{-- ========================================================== --}}
    {{--    SEO COMPLETO Y PROFESIONAL PARA LA PÁGINA DE VÍDEOS     --}}
    {{-- ========================================================== --}}
    <x-slot name="head">
        {{-- Meta Tags Básicos --}}
        <title>Videógrafos de Bodas en Almería | Vídeo Cinematográfico Profesional | Foto Valera</title>
        <meta name="description" content="Videógrafos profesionales en Almería especializados en vídeo de bodas cinematográfico. Reportajes en 4K con drones, edición profesional y estilo cinematográfico. Más de 23 años de experiencia creando películas emotivas de bodas y eventos.">
        <meta name="keywords" content="videografos almeria, videografo bodas almeria, video de boda almeria, video cinematografico boda, videografia bodas almeria, video 4k bodas, drones bodas almeria, videografo profesional almeria, reportajes video boda, cinematografia bodas almeria, video eventos almeria, fotovalera videos">
        <meta name="author" content="Foto Valera">
        <meta name="publisher" content="Foto Valera">
        <meta name="robots" content="index, follow">
        <meta name="language" content="es">
        <meta name="geo.region" content="ES-AL">
        <meta name="geo.placename" content="Almería">
        <meta name="geo.position" content="36.8381;-2.4597">
        <meta name="ICBM" content="36.8381, -2.4597">

        {{-- URL Canónica --}}
        <link rel="canonical" href="{{ route('videos') }}">

        {{-- Open Graph / Facebook --}}
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ route('videos') }}">
        <meta property="og:title" content="Videógrafos de Bodas en Almería | Vídeo Cinematográfico Profesional | Foto Valera">
        <meta property="og:description" content="Videógrafos profesionales en Almería. Vídeo de bodas cinematográfico en 4K con drones. Más de 23 años creando películas emotivas de bodas y eventos.">
        <meta property="og:image" content="{{ Storage::disk('logos')->url('SuperLogo.png') }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:image:alt" content="Foto Valera - Videógrafos Profesionales en Almería">
        <meta property="og:site_name" content="Foto Valera">
        <meta property="og:locale" content="es_ES">

        {{-- Twitter Card --}}
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:site" content="@foto_valera">
        <meta name="twitter:creator" content="@foto_valera">
        <meta name="twitter:title" content="Videógrafos de Bodas en Almería | Vídeo Cinematográfico Profesional | Foto Valera">
        <meta name="twitter:description" content="Videógrafos profesionales en Almería. Vídeo de bodas cinematográfico en 4K con drones y edición profesional.">
        <meta property="twitter:image" content="{{ Storage::disk('logos')->url('SuperLogo.png') }}">
        <meta name="twitter:image:alt" content="Foto Valera - Videógrafos Profesionales en Almería">

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
            "name": "Videografía de Bodas en Almería",
            "description": "Servicio profesional de videografía y vídeo cinematográfico para bodas en Almería",
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
            "serviceType": "Videografía de Bodas",
            "offers": {
                "@type": "Offer",
                "description": "Reportajes de vídeo cinematográfico para bodas y eventos",
                "priceRange": "€€€"
            },
            "aggregateRating": {
                "@type": "AggregateRating",
                "ratingValue": "4.9",
                "reviewCount": "76"
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
                    "name": "¿Qué incluye un vídeo de boda cinematográfico?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Incluye cobertura completa del día en 4K, vídeo resumen editado (5-10 min), vídeo completo de la ceremonia, tomas con drone, edición profesional con música personalizada y entrega en formato digital de alta calidad."
                    }
                },
                {
                    "@type": "Question",
                    "name": "¿Utilizan drones para los vídeos de boda?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Sí, utilizamos drones con grabación 4K para capturar tomas aéreas espectaculares de vuestra boda en las localizaciones más bonitas de Almería, siempre cumpliendo con la normativa vigente."
                    }
                },
                {
                    "@type": "Question",
                    "name": "¿Cuánto tiempo tardan en entregar el vídeo de boda?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "El tiempo de entrega es de 4-8 semanas. Cada vídeo se edita cuidadosamente con corrección de color, música personalizada y efectos cinematográficos para garantizar la máxima calidad."
                    }
                },
                {
                    "@type": "Question",
                    "name": "¿Ofrecen vídeo y fotografía juntos?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Sí, ofrecemos paquetes combinados de fotografía y vídeo de bodas. Es la opción más completa para tener un recuerdo integral de vuestro día especial."
                    }
                }
            ]
        }
        </script>
    </x-slot>

    {{-- @livewire('header-component') --}}
    @livewire('configurable-page-header', [
        'identifier' => 'Videos_header',
        'defaultTitle' => 'Vídeos de Bodas Cinematográficos',
        'defaultSubtitle' => 'Películas emotivas que cuentan vuestra historia de amor',
        'defaultImage' => null,
    ])


    @livewire('video-gallery-manager', [
        'identifier' => 'videos-tour-videos',
        'defaultTitle' => 'Nuestros Vídeos de Bodas',
    ])

    <x-self.section-text title="Videógrafos en Almería: Capturando la Esencia de Tus Momentos"
        subtitle="En Fotovalera, transformamos momentos fugaces en recuerdos cinematográficos duraderos. Como videógrafos en Almería con más de 23 años de experiencia, nos apasiona contar historias a través de la lente, capturando la emoción, la atmósfera y los detalles que hacen único cada evento, ya sea una boda, un evento corporativo o un proyecto personal. Nuestro enfoque combina la técnica cinematográfica con una narrativa sensible y personalizada. Trabajamos de cerca contigo para entender tu visión y producir un vídeo que no solo cumpla tus expectativas, sino que las supere, convirtiéndose en un tesoro visual que revivirás una y otra vez." />

    {{-- Sección SEO: Contenido Optimizado para Videografía de Bodas Almería --}}
    <section class="bg-gradient-to-b from-black via-gray-950 to-black py-20 sm:py-28">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Videografía de Bodas en Almería - Contenido Principal --}}
            <div class="mb-20">
                <div class="text-center mb-12">
                    <p class="text-red-400 font-semibold text-sm uppercase tracking-wide mb-3">Videógrafos Profesionales</p>
                    <h2 class="text-4xl sm:text-5xl font-bold text-white mb-6">
                        Vídeo de Bodas Cinematográfico en Almería
                    </h2>
                    <div class="w-24 h-1 bg-gradient-to-r from-red-500 via-orange-500 to-yellow-500 mx-auto rounded-full"></div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-16">
                    <div class="bg-gradient-to-br from-gray-900 to-gray-950 rounded-2xl p-8 border border-gray-800 shadow-xl">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="flex items-center justify-center w-14 h-14 bg-red-600/20 rounded-xl">
                                <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-white">Películas de Boda Emotivas</h3>
                        </div>
                        <p class="text-gray-300 leading-relaxed mb-4">
                            Como <strong class="text-white">videógrafos especializados en bodas en Almería</strong>, creamos películas cinematográficas
                            que cuentan vuestra historia de amor de manera única y emotiva. Nuestros <strong class="text-white">vídeos de boda</strong> no son
                            simples grabaciones; son obras audiovisuales cuidadosamente editadas que capturan la esencia, las emociones y la magia de vuestro día especial.
                        </p>
                        <p class="text-gray-300 leading-relaxed">
                            Con más de <strong class="text-white">23 años de experiencia en videografía de bodas</strong>, hemos perfeccionado el arte de
                            capturar momentos decisivos, votos emotivos, lágrimas de alegría y celebraciones espontáneas. Cada <strong class="text-white">vídeo
                            cinematográfico de boda</strong> es una pieza única que podréis revivir una y otra vez.
                        </p>
                    </div>

                    <div class="bg-gradient-to-br from-gray-900 to-gray-950 rounded-2xl p-8 border border-gray-800 shadow-xl">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="flex items-center justify-center w-14 h-14 bg-orange-600/20 rounded-xl">
                                <svg class="w-8 h-8 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-white">Tecnología 4K y Drones</h3>
                        </div>
                        <p class="text-gray-300 leading-relaxed mb-4">
                            Nuestros <strong class="text-white">vídeos de boda en Almería</strong> se graban en <strong class="text-white">resolución 4K</strong>
                            con cámaras de cine, estabilizadores profesionales y <strong class="text-white">drones con grabación 4K</strong> para tomas aéreas
                            espectaculares. Capturamos vuestro día desde todos los ángulos posibles, creando una narrativa visual completa y cinematográfica.
                        </p>
                        <p class="text-gray-300 leading-relaxed">
                            Como <strong class="text-white">videógrafos profesionales en Almería</strong>, utilizamos equipos de audio inalámbrico de alta calidad
                            para grabar vuestros votos, discursos y momentos emotivos con claridad perfecta. La postproducción incluye corrección de color
                            cinematográfica, música personalizada y efectos que transforman vuestro vídeo en una verdadera obra maestra.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Servicios de Videografía --}}
            <div class="mb-20">
                <div class="text-center mb-12">
                    <h2 class="text-3xl sm:text-4xl font-bold text-white mb-4">Nuestros Servicios de Videografía</h2>
                    <p class="text-gray-400 text-lg max-w-3xl mx-auto">
                        Como <strong class="text-white">videógrafos en Almería</strong>, ofrecemos diferentes tipos de vídeos
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="bg-gray-900/50 backdrop-blur-sm rounded-xl p-6 border border-gray-800 hover:border-red-500/50 transition-all duration-300 transform hover:scale-105">
                        <div class="text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-red-600 to-orange-600 rounded-2xl mb-4 shadow-lg shadow-red-500/20">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-3">Vídeo de Bodas</h3>
                            <p class="text-gray-400">
                                <strong class="text-white">Reportajes cinematográficos completos</strong> de vuestra boda.
                                Desde los preparativos hasta el final de la celebración en 4K.
                            </p>
                        </div>
                    </div>

                    <div class="bg-gray-900/50 backdrop-blur-sm rounded-xl p-6 border border-gray-800 hover:border-orange-500/50 transition-all duration-300 transform hover:scale-105">
                        <div class="text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-orange-600 to-yellow-600 rounded-2xl mb-4 shadow-lg shadow-orange-500/20">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-3">Vídeo Resumen</h3>
                            <p class="text-gray-400">
                                <strong class="text-white">Película resumen de 5-10 minutos</strong> con los mejores momentos,
                                música emotiva y edición cinematográfica.
                            </p>
                        </div>
                    </div>

                    <div class="bg-gray-900/50 backdrop-blur-sm rounded-xl p-6 border border-gray-800 hover:border-yellow-500/50 transition-all duration-300 transform hover:scale-105">
                        <div class="text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-yellow-600 to-amber-600 rounded-2xl mb-4 shadow-lg shadow-yellow-500/20">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-3">Vídeo con Drones</h3>
                            <p class="text-gray-400">
                                Tomas aéreas espectaculares con <strong class="text-white">drones 4K</strong>.
                                Perfectas para bodas en playas, cortijos y exteriores de Almería.
                            </p>
                        </div>
                    </div>

                    <div class="bg-gray-900/50 backdrop-blur-sm rounded-xl p-6 border border-gray-800 hover:border-purple-500/50 transition-all duration-300 transform hover:scale-105">
                        <div class="text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-purple-600 to-pink-600 rounded-2xl mb-4 shadow-lg shadow-purple-500/20">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-3">Vídeo de Ceremonia</h3>
                            <p class="text-gray-400">
                                <strong class="text-white">Grabación completa de la ceremonia</strong> religiosa o civil.
                                Audio limpio de votos y discursos.
                            </p>
                        </div>
                    </div>

                    <div class="bg-gray-900/50 backdrop-blur-sm rounded-xl p-6 border border-gray-800 hover:border-blue-500/50 transition-all duration-300 transform hover:scale-105">
                        <div class="text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-blue-600 to-cyan-600 rounded-2xl mb-4 shadow-lg shadow-blue-500/20">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-3">Vídeo de Eventos</h3>
                            <p class="text-gray-400">
                                <strong class="text-white">Vídeos para eventos corporativos</strong>, comuniones,
                                bautizos y celebraciones especiales.
                            </p>
                        </div>
                    </div>

                    <div class="bg-gray-900/50 backdrop-blur-sm rounded-xl p-6 border border-gray-800 hover:border-green-500/50 transition-all duration-300 transform hover:scale-105">
                        <div class="text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-green-600 to-emerald-600 rounded-2xl mb-4 shadow-lg shadow-green-500/20">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-3">Edición Profesional</h3>
                            <p class="text-gray-400">
                                <strong class="text-white">Postproducción cinematográfica</strong>: corrección de color,
                                efectos, música personalizada y transiciones suaves.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Qué Incluyen Nuestros Vídeos de Boda --}}
            <div class="mb-20">
                <div class="text-center mb-12">
                    <h2 class="text-3xl sm:text-4xl font-bold text-white mb-4">Qué Incluyen Nuestros Vídeos de Boda</h2>
                    <p class="text-gray-400 text-lg max-w-3xl mx-auto">
                        Paquetes completos de videografía de bodas en Almería
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="bg-gradient-to-br from-gray-900 to-gray-950 rounded-xl p-6 border border-gray-800">
                        <h3 class="text-xl font-bold text-white mb-4 flex items-center gap-3">
                            <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Grabación y Cobertura
                        </h3>
                        <ul class="space-y-3 text-gray-300">
                            <li class="flex items-start gap-2">
                                <span class="text-red-400 mt-1">•</span>
                                <span>Grabación completa del día en 4K</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-red-400 mt-1">•</span>
                                <span>Tomas con drones (si la ubicación lo permite)</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-red-400 mt-1">•</span>
                                <span>Cámaras múltiples para diferentes ángulos</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-red-400 mt-1">•</span>
                                <span>Estabilizadores para tomas fluidas</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-red-400 mt-1">•</span>
                                <span>Audio profesional de votos y discursos</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-red-400 mt-1">•</span>
                                <span>Cobertura desde preparativos hasta final</span>
                            </li>
                        </ul>
                    </div>

                    <div class="bg-gradient-to-br from-gray-900 to-gray-950 rounded-xl p-6 border border-gray-800">
                        <h3 class="text-xl font-bold text-white mb-4 flex items-center gap-3">
                            <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Edición y Entrega
                        </h3>
                        <ul class="space-y-3 text-gray-300">
                            <li class="flex items-start gap-2">
                                <span class="text-orange-400 mt-1">•</span>
                                <span>Vídeo resumen cinematográfico (5-10 min)</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-orange-400 mt-1">•</span>
                                <span>Vídeo completo de ceremonia editado</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-orange-400 mt-1">•</span>
                                <span>Corrección de color cinematográfica</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-orange-400 mt-1">•</span>
                                <span>Música personalizada según vuestro estilo</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-orange-400 mt-1">•</span>
                                <span>Entrega en formato digital HD y 4K</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-orange-400 mt-1">•</span>
                                <span>Entrega en 4-8 semanas</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="mt-8 bg-gray-900/50 border border-gray-800 rounded-xl p-6">
                    <p class="text-gray-300 leading-relaxed">
                        Nuestros <strong class="text-white">vídeos de boda en Almería</strong> están diseñados para ser obras cinematográficas que emocionan.
                        Como <strong class="text-white">videógrafos profesionales</strong>, utilizamos cámaras de cine, drones DJI con grabación 4K,
                        estabilizadores gimbal, iluminación LED profesional y micrófonos inalámbricos de solapa para capturar audio cristalino.
                        Cada vídeo se edita durante semanas con software profesional de postproducción cinematográfica.
                    </p>
                </div>
            </div>

            {{-- Nuestro Proceso de Videografía --}}
            <div class="mb-20 bg-gradient-to-r from-red-950/50 to-orange-950/50 rounded-2xl p-8 lg:p-12 border border-red-800/30 backdrop-blur-sm">
                <div class="text-center mb-10">
                    <h2 class="text-3xl font-bold text-white mb-4">Nuestro Proceso de Videografía de Bodas</h2>
                    <p class="text-gray-300 text-lg max-w-3xl mx-auto">
                        Cómo creamos <strong class="text-white">vídeos cinematográficos de bodas</strong> en Almería
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="bg-gray-900/60 rounded-lg p-5 text-center border border-gray-700">
                        <div class="inline-flex items-center justify-center w-12 h-12 bg-red-600 rounded-full mb-3 text-white font-bold">1</div>
                        <h4 class="text-white font-bold mb-2">Reunión Previa</h4>
                        <p class="text-gray-400 text-sm">Conocemos vuestra historia, estilo musical y planificamos el día.</p>
                    </div>
                    <div class="bg-gray-900/60 rounded-lg p-5 text-center border border-gray-700">
                        <div class="inline-flex items-center justify-center w-12 h-12 bg-orange-600 rounded-full mb-3 text-white font-bold">2</div>
                        <h4 class="text-white font-bold mb-2">Grabación 4K</h4>
                        <p class="text-gray-400 text-sm">Cobertura completa con múltiples cámaras y drones profesionales.</p>
                    </div>
                    <div class="bg-gray-900/60 rounded-lg p-5 text-center border border-gray-700">
                        <div class="inline-flex items-center justify-center w-12 h-12 bg-yellow-600 rounded-full mb-3 text-white font-bold">3</div>
                        <h4 class="text-white font-bold mb-2">Edición Cinematográfica</h4>
                        <p class="text-gray-400 text-sm">Selección, edición y postproducción profesional con efectos.</p>
                    </div>
                    <div class="bg-gray-900/60 rounded-lg p-5 text-center border border-gray-700">
                        <div class="inline-flex items-center justify-center w-12 h-12 bg-green-600 rounded-full mb-3 text-white font-bold">4</div>
                        <h4 class="text-white font-bold mb-2">Entrega Final</h4>
                        <p class="text-gray-400 text-sm">Vídeo resumen y ceremonia completa en formato digital 4K.</p>
                    </div>
                </div>
            </div>

            {{-- Por Qué Elegirnos como Videógrafos --}}
            <div class="mb-20 bg-gradient-to-br from-gray-900 to-gray-950 rounded-2xl p-8 lg:p-12 border border-gray-800">
                <div class="text-center mb-10">
                    <h2 class="text-3xl font-bold text-white mb-4">
                        ¿Por Qué Elegir a Foto Valera como Videógrafos en Almería?
                    </h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-red-600 to-orange-600 rounded-2xl mb-4 shadow-lg shadow-red-500/20">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3">+23 Años de Experiencia</h3>
                        <p class="text-gray-300">
                            Más de dos décadas como <strong class="text-white">videógrafos profesionales de bodas</strong> en Almería,
                            creando películas que emocionan.
                        </p>
                    </div>

                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-orange-600 to-yellow-600 rounded-2xl mb-4 shadow-lg shadow-orange-500/20">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3">Tecnología 4K y Drones</h3>
                        <p class="text-gray-300">
                            Equipos profesionales de última generación: cámaras 4K, drones, estabilizadores y audio inalámbrico.
                        </p>
                    </div>

                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-yellow-600 to-green-600 rounded-2xl mb-4 shadow-lg shadow-yellow-500/20">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3">Estilo Cinematográfico</h3>
                        <p class="text-gray-300">
                            Cada vídeo es una <strong class="text-white">película emotiva</strong> con narrativa, música y edición de nivel profesional.
                        </p>
                    </div>
                </div>
            </div>

            {{-- CTA Final --}}
            <div class="text-center bg-gradient-to-r from-red-600/10 to-orange-600/10 rounded-2xl p-10 border border-red-500/30">
                <h2 class="text-3xl font-bold text-white mb-4">¿Listos para Vuestro Vídeo de Boda Cinematográfico?</h2>
                <p class="text-gray-300 text-lg mb-8 max-w-2xl mx-auto">
                    Captura la magia de vuestro día especial con un vídeo cinematográfico profesional.
                    Contáctanos y hablemos sobre tu película de boda perfecta.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('weddings') }}"
                        class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-gradient-to-r from-red-600 to-orange-600 hover:from-red-500 hover:to-orange-500 text-white font-bold rounded-xl shadow-lg shadow-red-500/20 transition-all duration-300 transform hover:scale-105">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Ver Bodas Fotografiadas
                    </a>
                    <button onclick="Livewire.dispatch('openContactModal')"
                        class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-gradient-to-r from-orange-600 to-yellow-600 hover:from-orange-500 hover:to-yellow-500 text-white font-bold rounded-xl shadow-lg shadow-orange-500/20 transition-all duration-300 transform hover:scale-105 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Solicitar Presupuesto
                    </button>
                </div>
            </div>

            {{-- Enlaces a Otros Servicios --}}
            <div class="mt-16">
                <h3 class="text-xl font-bold text-white text-center mb-6">Descubre Nuestros Otros Servicios</h3>
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="{{ route('weddings') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-indigo-600/20 hover:bg-indigo-600/30 border border-indigo-500/30 rounded-lg text-indigo-300 hover:text-indigo-200 transition-all duration-300 transform hover:scale-105">
                        Reportajes de Bodas
                    </a>
                    <a href="{{ route('comuniones') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-purple-600/20 hover:bg-purple-600/30 border border-purple-500/30 rounded-lg text-purple-300 hover:text-purple-200 transition-all duration-300 transform hover:scale-105">
                        Fotografía de Comuniones
                    </a>
                    <a href="{{ route('gallery') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-green-600/20 hover:bg-green-600/30 border border-green-500/30 rounded-lg text-green-300 hover:text-green-200 transition-all duration-300 transform hover:scale-105">
                        Galería de Fotos
                    </a>
                    <a href="{{ route('studio.index') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-pink-600/20 hover:bg-pink-600/30 border border-pink-500/30 rounded-lg text-pink-300 hover:text-pink-200 transition-all duration-300 transform hover:scale-105">
                        Sesiones de Estudio
                    </a>
                </div>
            </div>
        </div>
    </section>

    <x-self.superPie></x-self.superPie>

</div>
