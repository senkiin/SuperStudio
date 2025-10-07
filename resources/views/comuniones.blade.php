<x-app-layout>
    {{-- ========================================================== --}}
    {{--    SEO COMPLETO Y PROFESIONAL PARA FOTOGRAFÍA DE COMUNIONES --}}
    {{-- ========================================================== --}}
    <x-slot name="head">
        {{-- Meta Tags Básicos --}}
        <title>Fotógrafo de Comuniones en Almería | Reportajes de Primera Comunión Profesionales | Foto Valera</title>
        <meta name="description" content="Foto Valera, especialistas en fotografía de comuniones en Almería. Reportajes de primera comunión creativos, emotivos y profesionales. Capturamos la inocencia y alegría de este día tan especial con más de 23 años de experiencia.">
        <meta name="keywords" content="fotografo comunion almeria, fotografos comuniones almeria, reportaje comunion almeria, fotos primera comunion almeria, reportajes de comuniones almeria, album comunion almeria, fotovalera comuniones, sesion fotos comunion almeria, fotografo infantil almeria, primera comunion almeria, fotografo comunion profesional almeria, fotos comunion en almeria, estudio fotografico comuniones almeria">
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
                "telephone": "+34-660-581-178",
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

    {{-- Sección SEO: Contenido Optimizado para Fotografía de Comuniones Almería --}}
    <section class="bg-gradient-to-b from-black via-gray-950 to-black py-20 sm:py-28">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Fotografía de Comuniones en Almería - Contenido Principal --}}
            <div class="mb-20">
                <div class="text-center mb-12">
                    <p class="text-purple-400 font-semibold text-sm uppercase tracking-wide mb-3">Especialistas en Primeras Comuniones</p>
                    <h2 class="text-4xl sm:text-5xl font-bold text-white mb-6">
                        Fotógrafos de Comuniones Profesionales en Almería
                    </h2>
                    <div class="w-24 h-1 bg-gradient-to-r from-purple-500 via-pink-500 to-rose-500 mx-auto rounded-full"></div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-16">
                    <div class="bg-gradient-to-br from-gray-900 to-gray-950 rounded-2xl p-8 border border-gray-800 shadow-xl">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="flex items-center justify-center w-14 h-14 bg-purple-600/20 rounded-xl">
                                <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-white">Reportajes de Comunión Creativos</h3>
                        </div>
                        <p class="text-gray-300 leading-relaxed mb-4">
                            Como <strong class="text-white">fotógrafos especializados en comuniones en Almería</strong>, capturamos la alegría, inocencia y
                            emoción de este día tan importante. Nuestros <strong class="text-white">reportajes de primera comunión</strong> combinan fotografías
                            tradicionales con momentos espontáneos y naturales que reflejan la personalidad única de cada niño y niña.
                        </p>
                        <p class="text-gray-300 leading-relaxed">
                            Con más de <strong class="text-white">23 años de experiencia fotografiando comuniones</strong>, sabemos cómo hacer que los niños
                            se sientan cómodos y naturales frente a la cámara. Cada <strong class="text-white">sesión de fotos de comunión</strong> es divertida,
                            relajada y diseñada para capturar sonrisas auténticas y momentos memorables.
                        </p>
                    </div>

                    <div class="bg-gradient-to-br from-gray-900 to-gray-950 rounded-2xl p-8 border border-gray-800 shadow-xl">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="flex items-center justify-center w-14 h-14 bg-pink-600/20 rounded-xl">
                                <svg class="w-8 h-8 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-white">Reportajes Completos de Comunión</h3>
                        </div>
                        <p class="text-gray-300 leading-relaxed mb-4">
                            Nuestros <strong class="text-white">reportajes de comuniones en Almería</strong> incluyen diferentes opciones: sesiones previas en
                            <strong class="text-white">estudio fotográfico</strong> con fondos personalizados, sesiones en exteriores aprovechando los paisajes
                            de Almería, y opcionalmente cobertura de la ceremonia en la iglesia. También realizamos
                            <strong class="text-white">sesiones familiares</strong> para capturar este momento con padres, hermanos y abuelos.
                        </p>
                        <p class="text-gray-300 leading-relaxed">
                            Como <strong class="text-white">fotógrafos de comuniones en Almería</strong>, utilizamos técnicas avanzadas para crear imágenes
                            luminosas, alegres y llenas de vida. Cada fotografía refleja la felicidad y la importancia de este sacramento especial en la vida
                            de vuestro hijo o hija.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Tipos de Sesiones de Comunión --}}
            <div class="mb-20">
                <div class="text-center mb-12">
                    <h2 class="text-3xl sm:text-4xl font-bold text-white mb-4">Tipos de Reportajes de Comunión</h2>
                    <p class="text-gray-400 text-lg max-w-3xl mx-auto">
                        Opciones personalizadas para la primera comunión de tu hijo o hija
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="bg-gray-900/50 backdrop-blur-sm rounded-xl p-6 border border-gray-800 hover:border-purple-500/50 transition-all duration-300 transform hover:scale-105">
                        <div class="text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-purple-600 to-pink-600 rounded-2xl mb-4 shadow-lg shadow-purple-500/20">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-3">Sesión en Estudio</h3>
                            <p class="text-gray-400">
                                En nuestro <strong class="text-white">estudio fotográfico en Almería</strong>, con fondos personalizados,
                                atrezzo especial y decoración temática para comuniones.
                            </p>
                        </div>
                    </div>

                    <div class="bg-gray-900/50 backdrop-blur-sm rounded-xl p-6 border border-gray-800 hover:border-pink-500/50 transition-all duration-300 transform hover:scale-105">
                        <div class="text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-pink-600 to-rose-600 rounded-2xl mb-4 shadow-lg shadow-pink-500/20">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-3">Sesión en Exteriores</h3>
                            <p class="text-gray-400">
                                Aprovechamos los <strong class="text-white">paisajes únicos de Almería</strong>: parques, jardines, playas,
                                campo. Luz natural para fotos frescas y alegres.
                            </p>
                        </div>
                    </div>

                    <div class="bg-gray-900/50 backdrop-blur-sm rounded-xl p-6 border border-gray-800 hover:border-rose-500/50 transition-all duration-300 transform hover:scale-105">
                        <div class="text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-rose-600 to-red-600 rounded-2xl mb-4 shadow-lg shadow-rose-500/20">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-3">Cobertura en Iglesia</h3>
                            <p class="text-gray-400">
                                Capturamos la <strong class="text-white">ceremonia religiosa</strong> con discreción y profesionalidad,
                                inmortalizando cada momento importante del sacramento.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Cuándo Hacer las Fotos de Comunión --}}
            <div class="mb-20 bg-gradient-to-r from-purple-950/50 to-pink-950/50 rounded-2xl p-8 lg:p-12 border border-purple-800/30 backdrop-blur-sm">
                <div class="text-center mb-10">
                    <h2 class="text-3xl font-bold text-white mb-4">¿Cuándo Hacer las Fotos de Comunión?</h2>
                    <p class="text-gray-300 text-lg max-w-3xl mx-auto">
                        Opciones para tu <strong class="text-white">reportaje de primera comunión</strong>
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                    <div class="bg-gray-900/60 rounded-lg p-6 text-center border border-gray-700">
                        <div class="text-4xl mb-3">📸</div>
                        <h4 class="text-white font-bold mb-2 text-lg">Sesión Previa</h4>
                        <p class="text-gray-400 text-sm">1-2 semanas antes. Más relajada, sin prisas. Los niños están más tranquilos y descansados.</p>
                    </div>
                    <div class="bg-gray-900/60 rounded-lg p-6 text-center border border-gray-700">
                        <div class="text-4xl mb-3">⛪</div>
                        <h4 class="text-white font-bold mb-2 text-lg">El Día de la Comunión</h4>
                        <p class="text-gray-400 text-sm">Cobertura de la ceremonia y celebración. Capturamos la emoción del momento real.</p>
                    </div>
                    <div class="bg-gray-900/60 rounded-lg p-6 text-center border border-gray-700">
                        <div class="text-4xl mb-3">✨</div>
                        <h4 class="text-white font-bold mb-2 text-lg">Paquete Completo</h4>
                        <p class="text-gray-400 text-sm">Sesión previa + cobertura del día. Lo mejor de ambas opciones en un solo paquete.</p>
                    </div>
                </div>

                <p class="text-gray-300 text-center leading-relaxed">
                    Como <strong class="text-white">fotógrafos especializados en comuniones</strong>, recomendamos la sesión previa para tener tiempo
                    de crear fotos creativas y relajadas, sin las prisas del día de la ceremonia. Sin embargo, también ofrecemos
                    <strong class="text-white">cobertura completa del día de la comunión en Almería</strong>, desde los preparativos en casa hasta
                    la celebración familiar. Nos adaptamos completamente a vuestras preferencias y horarios.
                </p>
            </div>

            {{-- Qué Incluyen Nuestros Reportajes de Comunión --}}
            <div class="mb-20">
                <div class="text-center mb-12">
                    <h2 class="text-3xl sm:text-4xl font-bold text-white mb-4">Qué Incluyen Nuestros Reportajes de Comunión</h2>
                    <p class="text-gray-400 text-lg max-w-3xl mx-auto">
                        Paquetes completos de fotografía de comuniones en Almería
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="bg-gradient-to-br from-gray-900 to-gray-950 rounded-xl p-6 border border-gray-800">
                        <h3 class="text-xl font-bold text-white mb-4 flex items-center gap-3">
                            <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            La Sesión Fotográfica
                        </h3>
                        <ul class="space-y-3 text-gray-300">
                            <li class="flex items-start gap-2">
                                <span class="text-purple-400 mt-1">•</span>
                                <span>Sesión de 60-90 minutos</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-purple-400 mt-1">•</span>
                                <span>Diferentes poses y escenarios</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-purple-400 mt-1">•</span>
                                <span>Fotos individuales del niño/niña</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-purple-400 mt-1">•</span>
                                <span>Fotos con familia y hermanos</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-purple-400 mt-1">•</span>
                                <span>Atrezzo y decoración temática</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-purple-400 mt-1">•</span>
                                <span>Ambiente divertido y natural</span>
                            </li>
                        </ul>
                    </div>

                    <div class="bg-gradient-to-br from-gray-900 to-gray-950 rounded-xl p-6 border border-gray-800">
                        <h3 class="text-xl font-bold text-white mb-4 flex items-center gap-3">
                            <svg class="w-6 h-6 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Entrega y Productos
                        </h3>
                        <ul class="space-y-3 text-gray-300">
                            <li class="flex items-start gap-2">
                                <span class="text-pink-400 mt-1">•</span>
                                <span>Todas las fotos editadas profesionalmente</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-pink-400 mt-1">•</span>
                                <span>Entrega en alta resolución</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-pink-400 mt-1">•</span>
                                <span>Galería online privada para compartir</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-pink-400 mt-1">•</span>
                                <span>USB personalizado con las fotos</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-pink-400 mt-1">•</span>
                                <span>Álbumes personalizados disponibles</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-pink-400 mt-1">•</span>
                                <span>Entrega en 2-3 semanas</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="mt-8 bg-gray-900/50 border border-gray-800 rounded-xl p-6">
                    <p class="text-gray-300 leading-relaxed">
                        Nuestros <strong class="text-white">reportajes de comuniones en Almería</strong> están diseñados para ser una experiencia divertida
                        y memorable tanto para los niños como para las familias. Como <strong class="text-white">fotógrafos profesionales de comuniones</strong>,
                        sabemos cómo conectar con los niños, hacerlos sentir cómodos y capturar sus sonrisas más auténticas. Utilizamos iluminación profesional,
                        técnicas de postproducción avanzadas y editamos cada imagen para crear recuerdos hermosos y atemporales de este día tan especial.
                    </p>
                </div>
            </div>

            {{-- Por Qué Elegirnos --}}
            <div class="mb-20 bg-gradient-to-br from-gray-900 to-gray-950 rounded-2xl p-8 lg:p-12 border border-gray-800">
                <div class="text-center mb-10">
                    <h2 class="text-3xl font-bold text-white mb-4">Por Qué Elegir a Foto Valera para la Comunión</h2>
                    <p class="text-gray-300 text-lg">
                        Experiencia y dedicación en <strong class="text-white">fotografía de comuniones en Almería</strong>
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-purple-600 to-pink-600 rounded-2xl mb-4 shadow-lg shadow-purple-500/20">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3">+23 Años de Experiencia</h3>
                        <p class="text-gray-300">
                            Más de dos décadas como <strong class="text-white">fotógrafos de comuniones</strong>, cientos de familias satisfechas
                            en toda Almería.
                        </p>
                    </div>

                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-pink-600 to-rose-600 rounded-2xl mb-4 shadow-lg shadow-pink-500/20">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3">Conexión con los Niños</h3>
                        <p class="text-gray-300">
                            Sabemos cómo hacer que los niños se sientan cómodos y se diviertan durante la sesión de fotos.
                        </p>
                    </div>

                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-rose-600 to-red-600 rounded-2xl mb-4 shadow-lg shadow-rose-500/20">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3">Creatividad y Calidad</h3>
                        <p class="text-gray-300">
                            Cada <strong class="text-white">reportaje de comunión</strong> es único, creativo y refleja la personalidad de tu hijo/a.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Consejos para las Fotos de Comunión --}}
            <div class="mb-20">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-white mb-4">Consejos para las Fotos de Primera Comunión</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-gray-900/50 backdrop-blur-sm rounded-xl p-5 border border-gray-800 text-center">
                        <div class="text-3xl mb-2">👗</div>
                        <h4 class="text-white font-bold mb-2">Traje Impecable</h4>
                        <p class="text-gray-400 text-sm">Traed el traje de comunión planchado y en perfectas condiciones.</p>
                    </div>
                    <div class="bg-gray-900/50 backdrop-blur-sm rounded-xl p-5 border border-gray-800 text-center">
                        <div class="text-3xl mb-2">😊</div>
                        <h4 class="text-white font-bold mb-2">Niños Relajados</h4>
                        <p class="text-gray-400 text-sm">Que descansen bien antes de la sesión y vengan tranquilos.</p>
                    </div>
                    <div class="bg-gray-900/50 backdrop-blur-sm rounded-xl p-5 border border-gray-800 text-center">
                        <div class="text-3xl mb-2">🎨</div>
                        <h4 class="text-white font-bold mb-2">Ideas Creativas</h4>
                        <p class="text-gray-400 text-sm">Podéis compartir ideas o inspiración que os guste para la sesión.</p>
                    </div>
                    <div class="bg-gray-900/50 backdrop-blur-sm rounded-xl p-5 border border-gray-800 text-center">
                        <div class="text-3xl mb-2">👨‍👩‍👧‍👦</div>
                        <h4 class="text-white font-bold mb-2">Fotos Familiares</h4>
                        <p class="text-gray-400 text-sm">Aprovechad para hacer fotos con toda la familia reunida.</p>
                    </div>
                </div>
            </div>

            {{-- CTA Final --}}
            <div class="text-center bg-gradient-to-r from-purple-600/10 to-pink-600/10 rounded-2xl p-10 border border-purple-500/30">
                <h2 class="text-3xl font-bold text-white mb-4">¿Listos para Reservar vuestro Reportaje de Comunión en Almería?</h2>
                <p class="text-gray-300 text-lg mb-8 max-w-2xl mx-auto">
                    Captura la alegría y la inocencia de este día tan especial con un reportaje fotográfico profesional de primera comunión.
                    Contáctanos para conocer nuestros paquetes.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('gallery') }}"
                        class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-500 hover:to-pink-500 text-white font-bold rounded-xl shadow-lg shadow-purple-500/20 transition-all duration-300 transform hover:scale-105">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Ver Más Comuniones
                    </a>
                    <button onclick="Livewire.dispatch('openContactModal')"
                        class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-gradient-to-r from-pink-600 to-rose-600 hover:from-pink-500 hover:to-rose-500 text-white font-bold rounded-xl shadow-lg shadow-pink-500/20 transition-all duration-300 transform hover:scale-105 cursor-pointer">
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
                    <a href="{{ route('weddings') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-indigo-600/20 hover:bg-indigo-600/30 border border-indigo-500/30 rounded-lg text-indigo-300 hover:text-indigo-200 transition-all duration-300 transform hover:scale-105">
                        Reportajes de Bodas
                    </a>
                    <a href="{{ route('newborn.index') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-blue-600/20 hover:bg-blue-600/30 border border-blue-500/30 rounded-lg text-blue-300 hover:text-blue-200 transition-all duration-300 transform hover:scale-105">
                        Fotografía Newborn
                    </a>
                    <a href="{{ route('embarazo.index') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-pink-600/20 hover:bg-pink-600/30 border border-pink-500/30 rounded-lg text-pink-300 hover:text-pink-200 transition-all duration-300 transform hover:scale-105">
                        Fotografía de Embarazo
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
