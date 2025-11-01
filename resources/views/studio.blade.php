<x-app-layout>
    {{-- ========================================================== --}}
    {{--    SEO COMPLETO Y PROFESIONAL PARA LA PÁGINA DE STUDIO     --}}
    {{-- ========================================================== --}}
    <x-slot name="head">
        @php
            // Para studio, obtenemos la primera imagen del carrusel dinámico
            $firstCarouselSlide = \App\Models\CarouselSlide::where('is_active', true)
                ->orderBy('order')
                ->first();
            
            if ($firstCarouselSlide && $firstCarouselSlide->background_image_path) {
                $pageImageUrl = Storage::disk('s3')->url($firstCarouselSlide->background_image_path);
            } else {
                // Fallback al logo si no hay slides en el carrusel
                $pageImageUrl = Storage::disk('logos')->url('SuperLogo.png');
            }
            $pageImageWidth = 1200;
            $pageImageHeight = 630;
        @endphp
        {{-- Meta Tags Básicos Optimizados para SEO 2025 --}}
        <title>Estudio Fotográfico en Almería | Sesiones Creativas y Temáticas | Foto Valera</title>
        <meta name="description" content="Foto Valera, estudio fotográfico profesional en Almería. Sesiones creativas, temáticas y artísticas: Halloween, Navidad, retratos y proyectos únicos. Más de 23 años de experiencia en fotografía de estudio.">
        <meta name="keywords" content="estudio fotografia almeria, estudio fotografico almeria, sesiones tematicas almeria, reportajes tematicos almeria, fotografia creativa almeria, fotografo estudio almeria, sesiones navidad almeria, sesiones halloween almeria, reportajes navidad almeria, sesion fotos san valentin, smash cake almeria, retratos profesionales almeria, sesiones personalizadas almeria, fotovalera estudio, fotografia artistica almeria">
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
        <meta name="classification" content="Fotografía de Estudio, Sesiones Creativas, Fotografía Temática">

        {{-- URL Canónica --}}
        <link rel="canonical" href="{{ route('studio.index') }}">

        {{-- Open Graph / Facebook --}}
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ route('studio.index') }}">
        <meta property="og:title" content="Estudio Fotográfico en Almería | Sesiones Creativas y Temáticas | Foto Valera">
        <meta property="og:description" content="Foto Valera, estudio fotográfico profesional en Almería. Sesiones creativas, temáticas y artísticas: Halloween, Navidad, retratos y proyectos únicos.">
        <meta property="og:image" content="{{ $pageImageUrl }}">
        <meta property="og:image:width" content="{{ $pageImageWidth }}">
        <meta property="og:image:height" content="{{ $pageImageHeight }}">
        <meta property="og:image:alt" content="Foto Valera - Estudio Fotográfico Profesional en Almería">
        <meta property="og:site_name" content="Foto Valera">
        <meta property="og:locale" content="es_ES">

        {{-- Twitter Card --}}
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:site" content="@foto_valera">
        <meta name="twitter:creator" content="@foto_valera">
        <meta name="twitter:title" content="Estudio Fotográfico en Almería | Sesiones Creativas y Temáticas | Foto Valera">
        <meta name="twitter:description" content="Foto Valera, estudio fotográfico profesional en Almería. Sesiones creativas, temáticas y artísticas: Halloween, Navidad, retratos y proyectos únicos.">
        <meta name="twitter:image" content="{{ $pageImageUrl }}">
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
                "telephone": "+34-660-581-178",
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
            "description": "Foto Valera es un estudio fotográfico profesional en Almería con más de 23 años de experiencia. Especializados en sesiones de estudio creativas, temáticas (Halloween, Navidad), retratos profesionales, smash cake, sesiones artísticas y proyectos personalizados.",
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
                    "areaServed": "ES",
                    "hoursAvailable": {
                        "@type": "OpeningHoursSpecification",
                        "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"],
                        "opens": "09:00",
                        "closes": "19:00"
                    }
                }
            ],
            "foundingDate": "2001",
            "aggregateRating": {
                "@type": "AggregateRating",
                "ratingValue": "4.7",
                "reviewCount": "45",
                "bestRating": "5",
                "worstRating": "1"
            },
            "sameAs": [
                "https://www.facebook.com/fotovalera",
                "https://www.instagram.com/fotovalera",
                "https://www.twitter.com/foto_valera"
            ],
            "knowsAbout": [
                "Fotografía de Estudio",
                "Sesiones Temáticas",
                "Fotografía Creativa",
                "Retratos Profesionales",
                "Sesiones de Navidad",
                "Sesiones de Halloween",
                "Smash Cake"
            ]
        }
        </script>
    </x-slot>


    @livewire('dynamic-carousel')

    {{-- Introducción al Estudio Fotográfico --}}
    <section class="bg-gradient-to-b from-black via-gray-950 to-black py-20 sm:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <p class="text-indigo-400 font-semibold text-sm uppercase tracking-wide mb-3">Estudio Fotográfico Profesional</p>
                <h1 class="text-4xl sm:text-5xl font-bold text-white mb-6">
                    Sesiones Creativas y Temáticas en Almería
                </h1>
                <p class="text-gray-300 text-lg max-w-3xl mx-auto leading-relaxed">
                    En nuestro <strong class="text-white">estudio fotográfico en Almería</strong>, damos vida a tus ideas más creativas.
                    Desde <strong class="text-white">reportajes temáticos de Navidad</strong> hasta sesiones personalizadas únicas,
                    creamos experiencias fotográficas memorables con más de 23 años de experiencia profesional.
                </p>
            </div>
        </div>
    </section>

    {{-- Reportajes de Navidad --}}
    <div>
        <x-self.section-text title="🎄 Reportajes de Navidad en Almería"
            subtitle="Captura la magia navideña con nuestras sesiones temáticas de Navidad en estudio. Decoración completa con árbol de Navidad, luces, regalos y toda la ambientación festiva. Perfectas para familias, niños y parejas que desean crear recuerdos únicos de estas fechas especiales. Incluye vestuario navideño opcional, gorros de Santa Claus, bufandas y accesorios temáticos para fotos inolvidables." />

        @livewire('curated-portrait-gallery', [
            'identifier' => 'Navidad_gallery',
        ])
    </div>

    {{-- Reportajes de Halloween --}}
    <div>
        <x-self.section-text title="🎃 Reportajes de Halloween en Almería"
            subtitle="Sesiones fotográficas temáticas de Halloween llenas de creatividad y diversión. Nuestro estudio fotográfico se transforma con decoración terrorífica, calabazas, telarañas y fondos oscuros. Ideal para familias, niños y amantes del terror que quieren fotos únicas. Disponemos de disfraces, maquillaje artístico opcional y props espeluznantes para crear imágenes impactantes y divertidas." />

        @livewire('curated-portrait-gallery', [
            'identifier' => 'Halloween_gallery',
        ])
    </div>

    {{-- Reportajes de Cumpleaños y Smash Cake --}}
    <div>
        <x-self.section-text title="🎂 Reportajes de Cumpleaños y Smash Cake"
            subtitle="Celebra el primer cumpleaños de tu bebé con una divertida sesión Smash Cake. Preparamos un set decorado especialmente para la ocasión, con una tarta personalizada que tu bebé puede destronar libremente mientras capturamos cada momento de diversión y sorpresa. También realizamos sesiones de cumpleaños para todas las edades, con decoración temática y ambiente festivo en nuestro estudio fotográfico de Almería." />

        @livewire('curated-portrait-gallery', [
            'identifier' => 'Cumpleanos_gallery',
        ])
    </div>

    {{-- Sección SEO: Más Reportajes Temáticos --}}
    <section class="bg-gradient-to-b from-black via-gray-950 to-black py-20 sm:py-28">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="text-center mb-16">
                <h2 class="text-4xl sm:text-5xl font-bold text-white mb-6">
                    Más Reportajes Temáticos en Nuestro Estudio
                </h2>
                <div class="w-24 h-1 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 mx-auto rounded-full"></div>
            </div>

            {{-- Grid de Reportajes Temáticos --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-20">

                {{-- San Valentín --}}
                <div class="bg-gradient-to-br from-gray-900 to-gray-950 rounded-2xl p-8 border border-gray-800 shadow-xl hover:shadow-2xl hover:shadow-pink-500/10 transition-all duration-500 transform hover:scale-105">
                    <div class="text-center mb-4">
                        <div class="text-5xl mb-3">💕</div>
                        <h3 class="text-2xl font-bold text-white mb-3">Sesiones San Valentín</h3>
                    </div>
                    <p class="text-gray-300 leading-relaxed mb-4">
                        <strong class="text-white">Reportajes de San Valentín</strong> para parejas en nuestro estudio fotográfico.
                        Decoración romántica con corazones, flores, luces cálidas y fondos especiales. Perfectas para regalar o
                        celebrar vuestro amor.
                    </p>
                    <div class="flex flex-wrap gap-2">
                        <span class="inline-block px-3 py-1 bg-pink-500/10 text-pink-300 text-sm rounded-full border border-pink-500/20">Parejas</span>
                        <span class="inline-block px-3 py-1 bg-rose-500/10 text-rose-300 text-sm rounded-full border border-rose-500/20">Romántico</span>
                    </div>
                </div>

                {{-- Retratos Profesionales --}}
                <div class="bg-gradient-to-br from-gray-900 to-gray-950 rounded-2xl p-8 border border-gray-800 shadow-xl hover:shadow-2xl hover:shadow-indigo-500/10 transition-all duration-500 transform hover:scale-105">
                    <div class="text-center mb-4">
                        <div class="text-5xl mb-3">👔</div>
                        <h3 class="text-2xl font-bold text-white mb-3">Retratos Profesionales</h3>
                    </div>
                    <p class="text-gray-300 leading-relaxed mb-4">
                        <strong class="text-white">Fotografía de retrato profesional</strong> para perfiles corporativos, LinkedIn,
                        actores, modelos y profesionales. Sesiones en estudio con iluminación perfecta para destacar tu mejor versión.
                        Ideal para CV, redes profesionales y portfolios.
                    </p>
                    <div class="flex flex-wrap gap-2">
                        <span class="inline-block px-3 py-1 bg-indigo-500/10 text-indigo-300 text-sm rounded-full border border-indigo-500/20">Corporate</span>
                        <span class="inline-block px-3 py-1 bg-purple-500/10 text-purple-300 text-sm rounded-full border border-purple-500/20">LinkedIn</span>
                    </div>
                </div>

                {{-- Sesiones Creativas --}}
                <div class="bg-gradient-to-br from-gray-900 to-gray-950 rounded-2xl p-8 border border-gray-800 shadow-xl hover:shadow-2xl hover:shadow-purple-500/10 transition-all duration-500 transform hover:scale-105">
                    <div class="text-center mb-4">
                        <div class="text-5xl mb-3">🎨</div>
                        <h3 class="text-2xl font-bold text-white mb-3">Sesiones Creativas</h3>
                    </div>
                    <p class="text-gray-300 leading-relaxed mb-4">
                        <strong class="text-white">Fotografía artística y creativa</strong> sin límites. Trae tus ideas más
                        innovadoras y las hacemos realidad en nuestro estudio. Conceptos artísticos, fotografía de moda,
                        editorial y proyectos personalizados únicos.
                    </p>
                    <div class="flex flex-wrap gap-2">
                        <span class="inline-block px-3 py-1 bg-purple-500/10 text-purple-300 text-sm rounded-full border border-purple-500/20">Artístico</span>
                        <span class="inline-block px-3 py-1 bg-pink-500/10 text-pink-300 text-sm rounded-full border border-pink-500/20">Personalizado</span>
                    </div>
                </div>

                {{-- Book Fotográfico --}}
                <div class="bg-gradient-to-br from-gray-900 to-gray-950 rounded-2xl p-8 border border-gray-800 shadow-xl hover:shadow-2xl hover:shadow-cyan-500/10 transition-all duration-500 transform hover:scale-105">
                    <div class="text-center mb-4">
                        <div class="text-5xl mb-3">📸</div>
                        <h3 class="text-2xl font-bold text-white mb-3">Book Fotográfico</h3>
                    </div>
                    <p class="text-gray-300 leading-relaxed mb-4">
                        <strong class="text-white">Books fotográficos profesionales</strong> para modelos, actores y artistas.
                        Múltiples looks, cambios de vestuario y estilos variados en una misma sesión. Perfecto para portfolios
                        y casting profesionales.
                    </p>
                    <div class="flex flex-wrap gap-2">
                        <span class="inline-block px-3 py-1 bg-cyan-500/10 text-cyan-300 text-sm rounded-full border border-cyan-500/20">Modelos</span>
                        <span class="inline-block px-3 py-1 bg-teal-500/10 text-teal-300 text-sm rounded-full border border-teal-500/20">Portfolio</span>
                    </div>
                </div>

                {{-- Sesiones Familiares --}}
                <div class="bg-gradient-to-br from-gray-900 to-gray-950 rounded-2xl p-8 border border-gray-800 shadow-xl hover:shadow-2xl hover:shadow-green-500/10 transition-all duration-500 transform hover:scale-105">
                    <div class="text-center mb-4">
                        <div class="text-5xl mb-3">👨‍👩‍👧‍👦</div>
                        <h3 class="text-2xl font-bold text-white mb-3">Sesiones Familiares</h3>
                    </div>
                    <p class="text-gray-300 leading-relaxed mb-4">
                        <strong class="text-white">Fotografía familiar en estudio</strong> para crear recuerdos atemporales.
                        Ideal para familias que desean fotos elegantes y profesionales. Incluye diferentes poses y composiciones
                        con todos los miembros de la familia.
                    </p>
                    <div class="flex flex-wrap gap-2">
                        <span class="inline-block px-3 py-1 bg-green-500/10 text-green-300 text-sm rounded-full border border-green-500/20">Familia</span>
                        <span class="inline-block px-3 py-1 bg-emerald-500/10 text-emerald-300 text-sm rounded-full border border-emerald-500/20">Retratos</span>
                    </div>
                </div>

                {{-- Sesiones de Primavera/Pascua --}}
                <div class="bg-gradient-to-br from-gray-900 to-gray-950 rounded-2xl p-8 border border-gray-800 shadow-xl hover:shadow-2xl hover:shadow-yellow-500/10 transition-all duration-500 transform hover:scale-105">
                    <div class="text-center mb-4">
                        <div class="text-5xl mb-3">🌸</div>
                        <h3 class="text-2xl font-bold text-white mb-3">Sesiones Primavera & Pascua</h3>
                    </div>
                    <p class="text-gray-300 leading-relaxed mb-4">
                        <strong class="text-white">Reportajes de Primavera y Pascua</strong> con decoración floral, conejitos,
                        cestas y colores pasteles. Perfectas para niños y familias que quieren celebrar la primavera con fotos
                        frescas y alegres.
                    </p>
                    <div class="flex flex-wrap gap-2">
                        <span class="inline-block px-3 py-1 bg-yellow-500/10 text-yellow-300 text-sm rounded-full border border-yellow-500/20">Primavera</span>
                        <span class="inline-block px-3 py-1 bg-amber-500/10 text-amber-300 text-sm rounded-full border border-amber-500/20">Pascua</span>
                    </div>
                </div>
            </div>

            {{-- Por Qué Elegir Nuestro Estudio --}}
            <div class="mb-20 bg-gradient-to-r from-indigo-950/50 to-purple-950/50 rounded-2xl p-8 lg:p-12 border border-indigo-800/30 backdrop-blur-sm">
                <div class="text-center mb-10">
                    <h2 class="text-3xl font-bold text-white mb-4">
                        ¿Por Qué Elegir Nuestro Estudio Fotográfico en Almería?
                    </h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-2xl mb-4 shadow-lg shadow-indigo-500/20">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3">Equipo Profesional</h3>
                        <p class="text-gray-300">
                            Iluminación de estudio profesional, fondos variados y equipos de última generación para resultados excepcionales.
                        </p>
                    </div>

                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-purple-600 to-pink-600 rounded-2xl mb-4 shadow-lg shadow-purple-500/20">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3">Decoración Temática</h3>
                        <p class="text-gray-300">
                            Props, vestuario y decoración completa para cada temática. Renovamos constantemente nuestros sets.
                        </p>
                    </div>

                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-pink-600 to-red-600 rounded-2xl mb-4 shadow-lg shadow-pink-500/20">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3">Ideas Personalizadas</h3>
                        <p class="text-gray-300">
                            Creamos sesiones completamente personalizadas según tus ideas. Tu creatividad no tiene límites.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Más Temáticas Disponibles --}}
            <div class="mb-20">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-white mb-4">Otras Sesiones Temáticas Disponibles</h2>
                    <p class="text-gray-400 text-lg">
                        Explora todas nuestras opciones de <strong class="text-white">reportajes temáticos en estudio</strong>
                    </p>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <div class="bg-gray-900/50 backdrop-blur-sm rounded-xl p-4 border border-gray-800 text-center hover:border-red-500/50 transition-all">
                        <div class="text-3xl mb-2">🌹</div>
                        <h4 class="text-white font-semibold text-sm">San Valentín</h4>
                    </div>
                    <div class="bg-gray-900/50 backdrop-blur-sm rounded-xl p-4 border border-gray-800 text-center hover:border-orange-500/50 transition-all">
                        <div class="text-3xl mb-2">🎃</div>
                        <h4 class="text-white font-semibold text-sm">Halloween</h4>
                    </div>
                    <div class="bg-gray-900/50 backdrop-blur-sm rounded-xl p-4 border border-gray-800 text-center hover:border-green-500/50 transition-all">
                        <div class="text-3xl mb-2">🎄</div>
                        <h4 class="text-white font-semibold text-sm">Navidad</h4>
                    </div>
                    <div class="bg-gray-900/50 backdrop-blur-sm rounded-xl p-4 border border-gray-800 text-center hover:border-pink-500/50 transition-all">
                        <div class="text-3xl mb-2">🌸</div>
                        <h4 class="text-white font-semibold text-sm">Primavera</h4>
                    </div>
                    <div class="bg-gray-900/50 backdrop-blur-sm rounded-xl p-4 border border-gray-800 text-center hover:border-blue-500/50 transition-all">
                        <div class="text-3xl mb-2">🎂</div>
                        <h4 class="text-white font-semibold text-sm">Smash Cake</h4>
                    </div>
                    <div class="bg-gray-900/50 backdrop-blur-sm rounded-xl p-4 border border-gray-800 text-center hover:border-purple-500/50 transition-all">
                        <div class="text-3xl mb-2">🎉</div>
                        <h4 class="text-white font-semibold text-sm">Cumpleaños</h4>
                    </div>
                    <div class="bg-gray-900/50 backdrop-blur-sm rounded-xl p-4 border border-gray-800 text-center hover:border-yellow-500/50 transition-all">
                        <div class="text-3xl mb-2">🎭</div>
                        <h4 class="text-white font-semibold text-sm">Temáticas</h4>
                    </div>
                    <div class="bg-gray-900/50 backdrop-blur-sm rounded-xl p-4 border border-gray-800 text-center hover:border-indigo-500/50 transition-all">
                        <div class="text-3xl mb-2">👤</div>
                        <h4 class="text-white font-semibold text-sm">Retratos</h4>
                    </div>
                </div>
            </div>

            {{-- Qué Incluyen las Sesiones de Estudio --}}
            <div class="mb-20">
                <div class="text-center mb-12">
                    <h2 class="text-3xl sm:text-4xl font-bold text-white mb-4">Qué Incluyen las Sesiones en Nuestro Estudio</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="bg-gradient-to-br from-gray-900 to-gray-950 rounded-xl p-6 border border-gray-800">
                        <h3 class="text-xl font-bold text-white mb-4 flex items-center gap-3">
                            <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Durante la Sesión
                        </h3>
                        <ul class="space-y-3 text-gray-300">
                            <li class="flex items-start gap-2">
                                <span class="text-indigo-400 mt-1">•</span>
                                <span>Sesión de 60-120 minutos según el tipo</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-indigo-400 mt-1">•</span>
                                <span>Iluminación profesional de estudio</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-indigo-400 mt-1">•</span>
                                <span>Decoración y atrezzo temático completo</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-indigo-400 mt-1">•</span>
                                <span>Vestuario disponible (según temática)</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-indigo-400 mt-1">•</span>
                                <span>Asesoramiento profesional de poses</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-indigo-400 mt-1">•</span>
                                <span>Ambiente relajado y divertido</span>
                            </li>
                        </ul>
                    </div>

                    <div class="bg-gradient-to-br from-gray-900 to-gray-950 rounded-xl p-6 border border-gray-800">
                        <h3 class="text-xl font-bold text-white mb-4 flex items-center gap-3">
                            <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Después de la Sesión
                        </h3>
                        <ul class="space-y-3 text-gray-300">
                            <li class="flex items-start gap-2">
                                <span class="text-purple-400 mt-1">•</span>
                                <span>Edición profesional de todas las fotos</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-purple-400 mt-1">•</span>
                                <span>Entrega en alta resolución</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-purple-400 mt-1">•</span>
                                <span>Galería online privada</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-purple-400 mt-1">•</span>
                                <span>USB personalizado con las fotos</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-purple-400 mt-1">•</span>
                                <span>Opciones de impresión y productos</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-purple-400 mt-1">•</span>
                                <span>Entrega en 2-3 semanas</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- CTA Final --}}
            <div class="text-center bg-gradient-to-r from-indigo-600/10 to-purple-600/10 rounded-2xl p-10 border border-indigo-500/30">
                <h2 class="text-3xl font-bold text-white mb-4">¿Listo para Tu Sesión en Nuestro Estudio Fotográfico?</h2>
                <p class="text-gray-300 text-lg mb-8 max-w-2xl mx-auto">
                    Reserva tu sesión temática o creativa en nuestro estudio fotográfico en Almería.
                    Consulta disponibilidad y paquetes especiales para cada temporada.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('gallery') }}"
                        class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-bold rounded-xl shadow-lg shadow-indigo-500/20 transition-all duration-300 transform hover:scale-105">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Ver Galería de Estudio
                    </a>
                    <button onclick="Livewire.dispatch('openContactModal')"
                        class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-500 hover:to-pink-500 text-white font-bold rounded-xl shadow-lg shadow-purple-500/20 transition-all duration-300 transform hover:scale-105 cursor-pointer">
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
                    <a href="{{ route('weddings') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-indigo-600/20 hover:bg-indigo-600/30 border border-indigo-500/30 rounded-lg text-indigo-300 hover:text-indigo-200 transition-all duration-300 transform hover:scale-105">
                        Reportajes de Bodas
                    </a>
                    <a href="{{ route('comuniones') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-purple-600/20 hover:bg-purple-600/30 border border-purple-500/30 rounded-lg text-purple-300 hover:text-purple-200 transition-all duration-300 transform hover:scale-105">
                        Fotografía de Comuniones
                    </a>
                    <a href="{{ route('newborn.index') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-blue-600/20 hover:bg-blue-600/30 border border-blue-500/30 rounded-lg text-blue-300 hover:text-blue-200 transition-all duration-300 transform hover:scale-105">
                        Fotografía Newborn
                    </a>
                    <a href="{{ route('embarazo.index') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-pink-600/20 hover:bg-pink-600/30 border border-pink-500/30 rounded-lg text-pink-300 hover:text-pink-200 transition-all duration-300 transform hover:scale-105">
                        Fotografía de Embarazo
                    </a>
                </div>
            </div>
        </div>
    </section>


    <x-self.superPie></x-self.superPie>
</x-app-layout>
