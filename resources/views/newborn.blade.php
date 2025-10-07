<x-app-layout>
    {{-- ========================================================== --}}
    {{--    SEO COMPLETO Y PROFESIONAL PARA FOTOGRAFÍA NEWBORN      --}}
    {{-- ========================================================== --}}
    <x-slot name="head">
        {{-- Meta Tags Básicos --}}
        <title>Fotografía Newborn en Almería | Sesiones de Recién Nacidos Profesionales | Foto Valera</title>
        <meta name="description" content="Foto Valera, especialistas en fotografía newborn en Almería. Sesiones de recién nacido seguras, tiernas y profesionales. Capturamos la magia de los primeros días de tu bebé con más de 23 años de experiencia.">
        <meta name="keywords" content="fotografia newborn almeria, fotografo newborn almeria, fotografo recien nacido almeria, sesion fotos bebe almeria, fotos recien nacidos almeria, estudio fotografia bebes almeria, sesiones newborn almeria, fotografo bebes almeria, fotos bebes recien nacidos, sesion newborn profesional, fotografo newborn profesional almeria, sesion recien nacido almeria">
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

    {{-- Sección SEO: Contenido Optimizado para Fotografía Newborn Almería --}}
    <section class="bg-gradient-to-b from-black via-gray-950 to-black py-20 sm:py-28">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Fotografía Newborn en Almería - Contenido Principal --}}
            <div class="mb-20">
                <div class="text-center mb-12">
                    <p class="text-blue-400 font-semibold text-sm uppercase tracking-wide mb-3">Especialistas en Recién Nacidos</p>
                    <h2 class="text-4xl sm:text-5xl font-bold text-white mb-6">
                        Fotografía Newborn Profesional en Almería
                    </h2>
                    <div class="w-24 h-1 bg-gradient-to-r from-blue-500 via-cyan-500 to-teal-500 mx-auto rounded-full"></div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-16">
                    <div class="bg-gradient-to-br from-gray-900 to-gray-950 rounded-2xl p-8 border border-gray-800 shadow-xl">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="flex items-center justify-center w-14 h-14 bg-blue-600/20 rounded-xl">
                                <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-white">Sesiones Newborn Seguras y Tiernas</h3>
                        </div>
                        <p class="text-gray-300 leading-relaxed mb-4">
                            Como <strong class="text-white">fotógrafos especializados en newborn en Almería</strong>, entendemos que la seguridad y el
                            bienestar de tu bebé son lo más importante. Nuestras <strong class="text-white">sesiones de fotografía newborn</strong> están diseñadas
                            para ser completamente seguras, con una temperatura controlada de 25-27°C, iluminación suave y manipulación delicada en todo momento.
                        </p>
                        <p class="text-gray-300 leading-relaxed">
                            Con más de <strong class="text-white">23 años de experiencia fotografiando recién nacidos</strong>, hemos perfeccionado cada detalle
                            para crear un ambiente tranquilo donde tu bebé se sienta cómodo y seguro. Cada <strong class="text-white">sesión de fotos de recién nacido</strong>
                            se realiza con paciencia infinita, respetando los tiempos del bebé para alimentarse, dormir y ser consolado.
                        </p>
                    </div>

                    <div class="bg-gradient-to-br from-gray-900 to-gray-950 rounded-2xl p-8 border border-gray-800 shadow-xl">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="flex items-center justify-center w-14 h-14 bg-cyan-600/20 rounded-xl">
                                <svg class="w-8 h-8 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-white">Fotografía Artística de Bebés</h3>
                        </div>
                        <p class="text-gray-300 leading-relaxed mb-4">
                            Nuestras <strong class="text-white">sesiones newborn en Almería</strong> combinan técnica profesional con sensibilidad artística.
                            Utilizamos props cuidadosamente seleccionados, mantitas suaves, gorros tejidos a mano y accesorios adorables para crear imágenes
                            tiernas y atemporales. Cada elemento está lavado y desinfectado para garantizar la <strong class="text-white">seguridad de tu bebé</strong>.
                        </p>
                        <p class="text-gray-300 leading-relaxed">
                            Como <strong class="text-white">fotógrafos de recién nacidos en Almería</strong>, capturamos esos detalles únicos e irrepetibles:
                            los deditos perfectos, las pestañas delicadas, los bostezos tiernos y las expresiones angelicales de tu bebé. Cada
                            <strong class="text-white">fotografía newborn</strong> es una obra de arte que celebra el milagro de la vida.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Momento Ideal para la Sesión Newborn --}}
            <div class="mb-20 bg-gradient-to-r from-blue-950/50 to-cyan-950/50 rounded-2xl p-8 lg:p-12 border border-blue-800/30 backdrop-blur-sm">
                <div class="text-center mb-10">
                    <h2 class="text-3xl font-bold text-white mb-4">¿Cuándo Hacer la Sesión Newborn?</h2>
                    <p class="text-gray-300 text-lg max-w-3xl mx-auto">
                        El timing perfecto para tu <strong class="text-white">sesión de fotos newborn</strong>
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                    <div class="bg-gray-900/60 rounded-lg p-6 text-center border border-gray-700">
                        <div class="text-4xl mb-3">👶</div>
                        <h4 class="text-white font-bold mb-2 text-lg">5-10 Días</h4>
                        <p class="text-gray-400 text-sm">El momento ideal. Los bebés duermen más profundamente y son más flexibles para las poses.</p>
                    </div>
                    <div class="bg-gray-900/60 rounded-lg p-6 text-center border border-gray-700">
                        <div class="text-4xl mb-3">🕐</div>
                        <h4 class="text-white font-bold mb-2 text-lg">Hasta 15 Días</h4>
                        <p class="text-gray-400 text-sm">Aún es posible lograr hermosas poses newborn, aunque el bebé esté más despierto.</p>
                    </div>
                    <div class="bg-gray-900/60 rounded-lg p-6 text-center border border-gray-700">
                        <div class="text-4xl mb-3">📞</div>
                        <h4 class="text-white font-bold mb-2 text-lg">Reserva Anticipada</h4>
                        <p class="text-gray-400 text-sm">Te recomendamos reservar durante el embarazo para asegurar disponibilidad.</p>
                    </div>
                </div>

                <p class="text-gray-300 text-center leading-relaxed">
                    Como <strong class="text-white">fotógrafos especializados en newborn</strong>, recomendamos contactarnos durante el embarazo
                    (idealmente en el tercer trimestre) para reservar tu <strong class="text-white">sesión de fotos de recién nacido en Almería</strong>.
                    Así podremos planificar todo con antelación y estar preparados cuando llegue tu bebé. Las <strong class="text-white">sesiones newborn</strong>
                    duran aproximadamente 2-3 horas, respetando siempre los tiempos del bebé.
                </p>
            </div>

            {{-- Qué Incluyen Nuestras Sesiones Newborn --}}
            <div class="mb-20">
                <div class="text-center mb-12">
                    <h2 class="text-3xl sm:text-4xl font-bold text-white mb-4">Qué Incluyen Nuestras Sesiones Newborn</h2>
                    <p class="text-gray-400 text-lg max-w-3xl mx-auto">
                        Paquetes completos de fotografía newborn en Almería
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="bg-gradient-to-br from-gray-900 to-gray-950 rounded-xl p-6 border border-gray-800">
                        <h3 class="text-xl font-bold text-white mb-4 flex items-center gap-3">
                            <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            La Sesión Fotográfica Newborn
                        </h3>
                        <ul class="space-y-3 text-gray-300">
                            <li class="flex items-start gap-2">
                                <span class="text-blue-400 mt-1">•</span>
                                <span>Sesión de 2-3 horas con total flexibilidad</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-blue-400 mt-1">•</span>
                                <span>Estudio climatizado a 25-27°C</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-blue-400 mt-1">•</span>
                                <span>Diferentes poses y escenarios</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-blue-400 mt-1">•</span>
                                <span>Props, mantitas, gorros y accesorios</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-blue-400 mt-1">•</span>
                                <span>Fotos con padres y hermanos</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-blue-400 mt-1">•</span>
                                <span>Música relajante y ruido blanco</span>
                            </li>
                        </ul>
                    </div>

                    <div class="bg-gradient-to-br from-gray-900 to-gray-950 rounded-xl p-6 border border-gray-800">
                        <h3 class="text-xl font-bold text-white mb-4 flex items-center gap-3">
                            <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Entrega y Productos
                        </h3>
                        <ul class="space-y-3 text-gray-300">
                            <li class="flex items-start gap-2">
                                <span class="text-cyan-400 mt-1">•</span>
                                <span>Todas las fotos editadas profesionalmente</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-cyan-400 mt-1">•</span>
                                <span>Entrega en alta resolución</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-cyan-400 mt-1">•</span>
                                <span>Galería online privada</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-cyan-400 mt-1">•</span>
                                <span>USB personalizado con las fotos</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-cyan-400 mt-1">•</span>
                                <span>Opciones de álbumes y lienzos</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-cyan-400 mt-1">•</span>
                                <span>Entrega en 2-3 semanas</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="mt-8 bg-gray-900/50 border border-gray-800 rounded-xl p-6">
                    <p class="text-gray-300 leading-relaxed">
                        Nuestras <strong class="text-white">sesiones de fotografía newborn en Almería</strong> están diseñadas con el máximo cuidado y profesionalidad.
                        Como <strong class="text-white">fotógrafos especializados en bebés</strong>, tenemos formación específica en seguridad newborn y
                        conocemos todas las técnicas para manipular a los bebés con delicadeza. Todo nuestro equipo y accesorios están limpiados y desinfectados
                        antes de cada sesión, garantizando un ambiente higiénico y seguro para tu recién nacido.
                    </p>
                </div>
            </div>

            {{-- Consejos para la Sesión Newborn --}}
            <div class="mb-20 bg-gradient-to-br from-gray-900 to-gray-950 rounded-2xl p-8 lg:p-12 border border-gray-800">
                <div class="text-center mb-10">
                    <h2 class="text-3xl font-bold text-white mb-4">Consejos para tu Sesión de Fotos Newborn</h2>
                    <p class="text-gray-300 text-lg">
                        Cómo preparar a tu bebé para la sesión fotográfica
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-blue-600 to-cyan-600 rounded-2xl mb-4 shadow-lg shadow-blue-500/20">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3">Alimenta al Bebé Antes</h3>
                        <p class="text-gray-300">
                            Un bebé bien alimentado estará más tranquilo y dormirá mejor durante la sesión.
                        </p>
                    </div>

                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-cyan-600 to-teal-600 rounded-2xl mb-4 shadow-lg shadow-cyan-500/20">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3">Mantén el Hogar Cálido</h3>
                        <p class="text-gray-300">
                            Si vienes desde casa, mantén al bebé abrigado hasta llegar al estudio climatizado.
                        </p>
                    </div>

                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-teal-600 to-blue-600 rounded-2xl mb-4 shadow-lg shadow-teal-500/20">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3">Sin Prisas</h3>
                        <p class="text-gray-300">
                            La sesión dura lo necesario. Tomamos descansos para cambios de pañal y alimentación.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Por Qué Elegirnos --}}
            <div class="mb-20">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-white mb-4">Por Qué Elegir Foto Valera para tu Sesión Newborn</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-blue-600 to-cyan-600 rounded-2xl mb-4 shadow-lg shadow-blue-500/20">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3">Seguridad Garantizada</h3>
                        <p class="text-gray-300">
                            Formación especializada en seguridad newborn. El bienestar de tu bebé es nuestra prioridad absoluta.
                        </p>
                    </div>

                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-cyan-600 to-teal-600 rounded-2xl mb-4 shadow-lg shadow-cyan-500/20">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3">+23 Años de Experiencia</h3>
                        <p class="text-gray-300">
                            Más de dos décadas como <strong class="text-white">fotógrafos profesionales de bebés</strong> en Almería.
                        </p>
                    </div>

                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-teal-600 to-blue-600 rounded-2xl mb-4 shadow-lg shadow-teal-500/20">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3">Paciencia y Cariño</h3>
                        <p class="text-gray-300">
                            Trabajamos con amor y paciencia infinita. Respetamos los tiempos y necesidades de tu bebé.
                        </p>
                    </div>
                </div>
            </div>

            {{-- CTA Final --}}
            <div class="text-center bg-gradient-to-r from-blue-600/10 to-cyan-600/10 rounded-2xl p-10 border border-blue-500/30">
                <h2 class="text-3xl font-bold text-white mb-4">¿Lista para Reservar tu Sesión Newborn en Almería?</h2>
                <p class="text-gray-300 text-lg mb-8 max-w-2xl mx-auto">
                    Captura la magia de los primeros días de tu bebé con una sesión fotográfica newborn profesional y segura.
                    Contáctanos hoy mismo para reservar.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('gallery') }}"
                        class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-500 hover:to-cyan-500 text-white font-bold rounded-xl shadow-lg shadow-blue-500/20 transition-all duration-300 transform hover:scale-105">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Ver Más Sesiones Newborn
                    </a>
                    <button onclick="Livewire.dispatch('openContactModal')"
                        class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-gradient-to-r from-cyan-600 to-teal-600 hover:from-cyan-500 hover:to-teal-500 text-white font-bold rounded-xl shadow-lg shadow-cyan-500/20 transition-all duration-300 transform hover:scale-105 cursor-pointer">
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
                    <a href="{{ route('embarazo.index') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-pink-600/20 hover:bg-pink-600/30 border border-pink-500/30 rounded-lg text-pink-300 hover:text-pink-200 transition-all duration-300 transform hover:scale-105">
                        Fotografía de Embarazo
                    </a>
                    <a href="{{ route('weddings') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-purple-600/20 hover:bg-purple-600/30 border border-purple-500/30 rounded-lg text-purple-300 hover:text-purple-200 transition-all duration-300 transform hover:scale-105">
                        Reportajes de Bodas
                    </a>
                    <a href="{{ route('comuniones') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-indigo-600/20 hover:bg-indigo-600/30 border border-indigo-500/30 rounded-lg text-indigo-300 hover:text-indigo-200 transition-all duration-300 transform hover:scale-105">
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
