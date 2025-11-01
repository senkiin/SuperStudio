<x-app-layout>
    {{-- ========================================================== --}}
    {{--    SEO COMPLETO Y PROFESIONAL PARA FOTOGRAFÍA DE EMBARAZO    --}}
    {{-- ========================================================== --}}
    <x-slot name="head">
        @php
            // Obtener imagen del header de embarazo
            $headerSettings = \App\Models\PageHeaderSetting::where('identifier', 'Embarazo_header')->first();
            if ($headerSettings && $headerSettings->background_image_url) {
                $pageImageUrl = Storage::disk('page-headers')->url($headerSettings->background_image_url);
            } else {
                $pageImageUrl = Storage::disk('logos')->url('SuperLogo.png');
            }
            $pageImageWidth = 1200;
            $pageImageHeight = 630;
        @endphp
        {{-- Meta Tags Básicos Optimizados para SEO 2025 --}}
        <title>Fotografía de Embarazo en Almería | Sesiones de Maternidad Profesionales | Foto Valera</title>
        <meta name="description" content="Foto Valera, especialistas en fotografía de embarazo en Almería. Sesiones de maternidad artísticas y emotivas. Capturamos la belleza de la dulce espera con más de 23 años de experiencia profesional.">
        <meta name="keywords" content="fotografia embarazo almeria, fotografo embarazo almeria, sesion fotos embarazo almeria, sesiones de embarazo almeria, fotos embarazada almeria, fotografo maternidad almeria, reportaje embarazo almeria, fotovalera embarazo, sesion maternidad almeria, fotografo embarazo profesional almeria, fotos de embarazada en almeria, sesiones fotograficas embarazo, estudio fotografico embarazo almeria">
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
        <meta name="classification" content="Fotografía de Embarazo, Maternidad, Sesiones Prenatales">

        {{-- URL Canónica --}}
        <link rel="canonical" href="{{ route('embarazo.index') }}">

        {{-- Open Graph / Facebook --}}
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ route('embarazo.index') }}">
        <meta property="og:title" content="Fotografía de Embarazo en Almería | Sesiones de Maternidad Profesionales | Foto Valera">
        <meta property="og:description" content="Foto Valera, especialistas en fotografía de embarazo en Almería. Sesiones de maternidad artísticas y emotivas. Capturamos la belleza de la dulce espera con profesionalidad.">
        <meta property="og:image" content="{{ $pageImageUrl }}">
        <meta property="og:image:width" content="{{ $pageImageWidth }}">
        <meta property="og:image:height" content="{{ $pageImageHeight }}">
        <meta property="og:image:alt" content="Foto Valera - Fotografía de Embarazo Profesional en Almería">
        <meta property="og:site_name" content="Foto Valera">
        <meta property="og:locale" content="es_ES">

        {{-- Twitter Card --}}
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:site" content="@foto_valera">
        <meta name="twitter:creator" content="@foto_valera">
        <meta name="twitter:title" content="Fotografía de Embarazo en Almería | Sesiones de Maternidad Profesionales | Foto Valera">
        <meta name="twitter:description" content="Foto Valera, especialistas en fotografía de embarazo en Almería. Sesiones de maternidad artísticas y emotivas.">
        <meta name="twitter:image" content="{{ $pageImageUrl }}">
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
                "ratingValue": "4.8",
                "reviewCount": "67",
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
            "description": "Foto Valera es un estudio fotográfico profesional en Almería con más de 23 años de experiencia especializado en fotografía de embarazo. Sesiones de maternidad artísticas y emotivas que capturan la belleza única de la dulce espera en estudio, exteriores o en la intimidad del hogar.",
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
                "ratingValue": "4.8",
                "reviewCount": "67",
                "bestRating": "5",
                "worstRating": "1"
            },
            "sameAs": [
                "https://www.facebook.com/fotovalera",
                "https://www.instagram.com/fotovalera",
                "https://www.twitter.com/foto_valera"
            ],
            "knowsAbout": [
                "Fotografía de Embarazo",
                "Sesiones de Maternidad",
                "Fotografía Prenatal",
                "Fotografía Artística"
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

    {{-- Sección SEO: Contenido Optimizado para Fotografía de Embarazo Almería --}}
    <section class="bg-gradient-to-b from-black via-gray-950 to-black py-20 sm:py-28">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Fotografía de Embarazo en Almería - Contenido Principal --}}
            <div class="mb-20">
                <div class="text-center mb-12">
                    <p class="text-pink-400 font-semibold text-sm uppercase tracking-wide mb-3">Fotógrafos Especializados</p>
                    <h2 class="text-4xl sm:text-5xl font-bold text-white mb-6">
                        Fotografía de Embarazo Profesional en Almería
                    </h2>
                    <div class="w-24 h-1 bg-gradient-to-r from-pink-500 via-rose-500 to-red-500 mx-auto rounded-full"></div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-16">
                    <div class="bg-gradient-to-br from-gray-900 to-gray-950 rounded-2xl p-8 border border-gray-800 shadow-xl">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="flex items-center justify-center w-14 h-14 bg-pink-600/20 rounded-xl">
                                <svg class="w-8 h-8 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-white">Sesiones de Embarazo Artísticas</h3>
                        </div>
                        <p class="text-gray-300 leading-relaxed mb-4">
                            Como <strong class="text-white">fotógrafos especializados en embarazo en Almería</strong>, capturamos la belleza única de la maternidad
                            con un enfoque artístico y sensible. Nuestras <strong class="text-white">sesiones de fotografía de embarazo</strong> están diseñadas
                            para que te sientas cómoda, hermosa y radiante, celebrando este momento mágico de tu vida.
                        </p>
                        <p class="text-gray-300 leading-relaxed">
                            Con más de <strong class="text-white">23 años de experiencia fotografiando embarazos</strong>, sabemos cómo resaltar tu belleza natural,
                            capturar la conexión con tu bebé y crear imágenes que atesorarás para siempre. Cada <strong class="text-white">sesión de fotos de embarazo</strong>
                            es única y personalizada según tu estilo y preferencias.
                        </p>
                    </div>

                    <div class="bg-gradient-to-br from-gray-900 to-gray-950 rounded-2xl p-8 border border-gray-800 shadow-xl">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="flex items-center justify-center w-14 h-14 bg-rose-600/20 rounded-xl">
                                <svg class="w-8 h-8 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-white">Reportajes de Maternidad Completos</h3>
                        </div>
                        <p class="text-gray-300 leading-relaxed mb-4">
                            Nuestros <strong class="text-white">reportajes de embarazo en Almería</strong> incluyen diferentes opciones: sesiones en
                            <strong class="text-white">estudio fotográfico profesional</strong> con iluminación controlada, en exteriores aprovechando los
                            paisajes únicos de Almería, o en la intimidad de tu hogar. También ofrecemos <strong class="text-white">sesiones en pareja</strong>
                            para capturar este momento especial junto a tu compañero.
                        </p>
                        <p class="text-gray-300 leading-relaxed">
                            Como <strong class="text-white">fotógrafos de maternidad en Almería</strong>, utilizamos técnicas avanzadas de iluminación y
                            postproducción para crear imágenes suaves, elegantes y atemporales. Cada fotografía refleja la luz especial que irradias durante
                            tu embarazo.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Tipos de Sesiones de Embarazo --}}
            <div class="mb-20">
                <div class="text-center mb-12">
                    <h2 class="text-3xl sm:text-4xl font-bold text-white mb-4">Tipos de Sesiones de Fotografía de Embarazo</h2>
                    <p class="text-gray-400 text-lg max-w-3xl mx-auto">
                        Opciones personalizadas para cada estilo y preferencia
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="bg-gray-900/50 backdrop-blur-sm rounded-xl p-6 border border-gray-800 hover:border-pink-500/50 transition-all duration-300 transform hover:scale-105">
                        <div class="text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-pink-600 to-rose-600 rounded-2xl mb-4 shadow-lg shadow-pink-500/20">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-3">Sesión en Estudio</h3>
                            <p class="text-gray-400">
                                En nuestro <strong class="text-white">estudio fotográfico en Almería</strong>, controlamos la iluminación perfecta
                                para crear imágenes elegantes y profesionales.
                            </p>
                        </div>
                    </div>

                    <div class="bg-gray-900/50 backdrop-blur-sm rounded-xl p-6 border border-gray-800 hover:border-rose-500/50 transition-all duration-300 transform hover:scale-105">
                        <div class="text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-rose-600 to-red-600 rounded-2xl mb-4 shadow-lg shadow-rose-500/20">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-3">Sesión en Exteriores</h3>
                            <p class="text-gray-400">
                                Aprovechamos los <strong class="text-white">paisajes únicos de Almería</strong>: playas, campo, atardeceres.
                                Naturaleza y luz natural para fotos memorables.
                            </p>
                        </div>
                    </div>

                    <div class="bg-gray-900/50 backdrop-blur-sm rounded-xl p-6 border border-gray-800 hover:border-red-500/50 transition-all duration-300 transform hover:scale-105">
                        <div class="text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-red-600 to-pink-600 rounded-2xl mb-4 shadow-lg shadow-red-500/20">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-3">Sesión en Casa</h3>
                            <p class="text-gray-400">
                                En la <strong class="text-white">intimidad de tu hogar</strong>, creamos fotos naturales y relajadas
                                en el ambiente donde pronto llegará tu bebé.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Momento Ideal para la Sesión --}}
            <div class="mb-20 bg-gradient-to-r from-pink-950/50 to-rose-950/50 rounded-2xl p-8 lg:p-12 border border-pink-800/30 backdrop-blur-sm">
                <div class="text-center mb-10">
                    <h2 class="text-3xl font-bold text-white mb-4">¿Cuándo Hacer tu Sesión de Fotos de Embarazo?</h2>
                    <p class="text-gray-300 text-lg max-w-3xl mx-auto">
                        El momento perfecto para tu <strong class="text-white">sesión fotográfica de embarazo</strong>
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                    <div class="bg-gray-900/60 rounded-lg p-6 text-center border border-gray-700">
                        <div class="text-4xl mb-3">📅</div>
                        <h4 class="text-white font-bold mb-2 text-lg">Semana 28-32</h4>
                        <p class="text-gray-400 text-sm">El momento más popular. La barriga está bien redondeada y aún te sientes cómoda.</p>
                    </div>
                    <div class="bg-gray-900/60 rounded-lg p-6 text-center border border-gray-700">
                        <div class="text-4xl mb-3">✨</div>
                        <h4 class="text-white font-bold mb-2 text-lg">Semana 32-36</h4>
                        <p class="text-gray-400 text-sm">Para quienes desean mostrar la barriga al máximo. Perfecto para fotos dramáticas.</p>
                    </div>
                    <div class="bg-gray-900/60 rounded-lg p-6 text-center border border-gray-700">
                        <div class="text-4xl mb-3">🌸</div>
                        <h4 class="text-white font-bold mb-2 text-lg">Flexibilidad</h4>
                        <p class="text-gray-400 text-sm">Cada embarazo es único. Te asesoramos sobre el mejor momento según tu caso.</p>
                    </div>
                </div>

                <p class="text-gray-300 text-center leading-relaxed">
                    Como <strong class="text-white">fotógrafos especializados en embarazo</strong>, recomendamos reservar tu sesión con antelación,
                    especialmente si deseas una <strong class="text-white">sesión en exteriores en Almería</strong> para aprovechar la mejor luz
                    y las condiciones meteorológicas ideales. Nuestras <strong class="text-white">sesiones fotográficas de maternidad</strong>
                    son relajadas y sin prisas, adaptándonos completamente a tu comodidad.
                </p>
            </div>

            {{-- Qué Incluyen Nuestras Sesiones --}}
            <div class="mb-20">
                <div class="text-center mb-12">
                    <h2 class="text-3xl sm:text-4xl font-bold text-white mb-4">Qué Incluyen Nuestras Sesiones de Embarazo</h2>
                    <p class="text-gray-400 text-lg max-w-3xl mx-auto">
                        Paquetes completos de fotografía de embarazo en Almería
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="bg-gradient-to-br from-gray-900 to-gray-950 rounded-xl p-6 border border-gray-800">
                        <h3 class="text-xl font-bold text-white mb-4 flex items-center gap-3">
                            <svg class="w-6 h-6 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            La Sesión Fotográfica
                        </h3>
                        <ul class="space-y-3 text-gray-300">
                            <li class="flex items-start gap-2">
                                <span class="text-pink-400 mt-1">•</span>
                                <span>Sesión de 60-90 minutos sin prisas</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-pink-400 mt-1">•</span>
                                <span>Diferentes poses y escenarios</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-pink-400 mt-1">•</span>
                                <span>Fotos individuales y en pareja</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-pink-400 mt-1">•</span>
                                <span>Vestuario y accesorios disponibles</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-pink-400 mt-1">•</span>
                                <span>Asesoramiento de poses y estilismo</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-pink-400 mt-1">•</span>
                                <span>Ambiente relajado y cómodo</span>
                            </li>
                        </ul>
                    </div>

                    <div class="bg-gradient-to-br from-gray-900 to-gray-950 rounded-xl p-6 border border-gray-800">
                        <h3 class="text-xl font-bold text-white mb-4 flex items-center gap-3">
                            <svg class="w-6 h-6 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Entrega y Productos
                        </h3>
                        <ul class="space-y-3 text-gray-300">
                            <li class="flex items-start gap-2">
                                <span class="text-rose-400 mt-1">•</span>
                                <span>Todas las fotos editadas profesionalmente</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-rose-400 mt-1">•</span>
                                <span>Entrega en alta resolución</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-rose-400 mt-1">•</span>
                                <span>Galería online privada</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-rose-400 mt-1">•</span>
                                <span>USB personalizado con todas las fotos</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-rose-400 mt-1">•</span>
                                <span>Opciones de álbumes y productos impresos</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-rose-400 mt-1">•</span>
                                <span>Entrega en 2-3 semanas</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="mt-8 bg-gray-900/50 border border-gray-800 rounded-xl p-6">
                    <p class="text-gray-300 leading-relaxed">
                        Nuestras <strong class="text-white">sesiones de fotografía de embarazo en Almería</strong> están diseñadas para ser una experiencia
                        relajante y memorable. Como <strong class="text-white">fotógrafos profesionales de maternidad</strong>, nos aseguramos de que te sientas
                        cómoda, hermosa y celebrada. Utilizamos iluminación suave que resalta tu belleza natural, técnicas de postproducción profesionales y
                        editamos cada imagen cuidadosamente para crear recuerdos atemporales de este momento especial.
                    </p>
                </div>
            </div>

            {{-- Consejos y Preparación --}}
            <div class="mb-20 bg-gradient-to-br from-gray-900 to-gray-950 rounded-2xl p-8 lg:p-12 border border-gray-800">
                <div class="text-center mb-10">
                    <h2 class="text-3xl font-bold text-white mb-4">Consejos para tu Sesión de Fotos de Embarazo</h2>
                    <p class="text-gray-300 text-lg">
                        Cómo prepararte para tu sesión fotográfica de embarazo
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-pink-600 to-rose-600 rounded-2xl mb-4 shadow-lg shadow-pink-500/20">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3">Siéntete Cómoda</h3>
                        <p class="text-gray-300">
                            Elige ropa que te haga sentir hermosa y resalte tu barriga. Ofrecemos opciones de vestuario si lo necesitas.
                        </p>
                    </div>

                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-rose-600 to-red-600 rounded-2xl mb-4 shadow-lg shadow-rose-500/20">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3">Sin Prisas</h3>
                        <p class="text-gray-300">
                            Las sesiones son relajadas. Puedes tomar descansos cuando lo necesites. Tu comodidad es nuestra prioridad.
                        </p>
                    </div>

                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-red-600 to-pink-600 rounded-2xl mb-4 shadow-lg shadow-red-500/20">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3">Incluye a la Familia</h3>
                        <p class="text-gray-300">
                            Puedes incluir a tu pareja, otros hijos o familiares. Creamos recuerdos familiares únicos.
                        </p>
                    </div>
                </div>
            </div>

            {{-- CTA Final --}}
            <div class="text-center bg-gradient-to-r from-pink-600/10 to-rose-600/10 rounded-2xl p-10 border border-pink-500/30">
                <h2 class="text-3xl font-bold text-white mb-4">¿Lista para Reservar tu Sesión de Embarazo en Almería?</h2>
                <p class="text-gray-300 text-lg mb-8 max-w-2xl mx-auto">
                    Celebra este momento mágico con una sesión fotográfica profesional. Contáctanos y crearemos juntos
                    recuerdos atemporales de tu embarazo.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('gallery') }}"
                        class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-gradient-to-r from-pink-600 to-rose-600 hover:from-pink-500 hover:to-rose-500 text-white font-bold rounded-xl shadow-lg shadow-pink-500/20 transition-all duration-300 transform hover:scale-105">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Ver Más Sesiones de Embarazo
                    </a>
                    <button onclick="Livewire.dispatch('openContactModal')"
                        class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-500 hover:to-red-500 text-white font-bold rounded-xl shadow-lg shadow-rose-500/20 transition-all duration-300 transform hover:scale-105 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Reservar Mi Sesión
                    </button>
                </div>
            </div>

            {{-- Enlaces a Otros Servicios --}}
            <div class="mt-16">
                <h3 class="text-xl font-bold text-white text-center mb-6">Descubre Nuestros Otros Servicios</h3>
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="{{ route('newborn.index') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-indigo-600/20 hover:bg-indigo-600/30 border border-indigo-500/30 rounded-lg text-indigo-300 hover:text-indigo-200 transition-all duration-300 transform hover:scale-105">
                        Fotografía Newborn
                    </a>
                    <a href="{{ route('weddings') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-purple-600/20 hover:bg-purple-600/30 border border-purple-500/30 rounded-lg text-purple-300 hover:text-purple-200 transition-all duration-300 transform hover:scale-105">
                        Reportajes de Bodas
                    </a>
                    <a href="{{ route('comuniones') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-pink-600/20 hover:bg-pink-600/30 border border-pink-500/30 rounded-lg text-pink-300 hover:text-pink-200 transition-all duration-300 transform hover:scale-105">
                        Fotografía de Comuniones
                    </a>
                    <a href="{{ route('studio.index') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-green-600/20 hover:bg-green-600/30 border border-green-500/30 rounded-lg text-green-300 hover:text-green-200 transition-all duration-300 transform hover:scale-105">
                        Sesiones de Estudio
                    </a>
                </div>
            </div>
        </div>
    </section>

    <x-self.superPie></x-self.superPie>
</x-app-layout>
