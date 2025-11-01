{{-- resources/views/home.blade.php --}}
<x-app-layout>
    {{-- ========================================================== --}}
    {{--    SEO COMPLETO Y PROFESIONAL PARA LA PÁGINA PRINCIPAL      --}}
    {{-- ========================================================== --}}
    <x-slot name="head">
        @php
            // Para home, obtenemos la primera imagen del carrusel
            $firstCarouselImage = \App\Models\CarouselImage::where('is_active', true)
                ->orderBy('order')
                ->first();

            if ($firstCarouselImage && $firstCarouselImage->image_path) {
                $pageImageUrl = Storage::disk('s3')->url($firstCarouselImage->image_path);
            } else {
                // Fallback al logo si no hay imágenes en el carrusel
                $pageImageUrl = Storage::disk('logos')->url('SuperLogo.png');
            }
            $pageImageWidth = 1200;
            $pageImageHeight = 630;
        @endphp
        {{-- Meta Tags Básicos Optimizados para SEO 2025 --}}
        <title>Fotógrafos y Videógrafos en Almería | Reportajes de Bodas | Foto Valera</title>
        <meta name="description" content="Fotógrafos y videógrafos profesionales en Almería con +23 años de experiencia. Especialistas en reportajes de bodas, vídeo de bodas, comuniones, sesiones de estudio y eventos. Los mejores fotógrafos de Almería para tu gran día. ¡Presupuesto sin compromiso!">
        <meta name="keywords" content="fotografos almeria, fotografo almeria, videografos almeria, videografo almeria, reportajes de bodas, reportajes de bodas almeria, fotografia de boda almeria, video de boda almeria, estudio fotografico almeria, fotografo comunion almeria, fotografo embarazo almeria, fotografo newborn almeria, fotocarnet almeria, fotovalera, foto valera, reportaje fotografico almeria, fotografos profesionales almeria, videografia de bodas, fotografo bodas almeria">
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
        <meta name="classification" content="Fotografía Profesional, Videografía, Eventos">

        {{-- URL Canónica --}}
        <link rel="canonical" href="{{ route('home') }}">

        {{-- Open Graph / Facebook --}}
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ route('home') }}">
        <meta property="og:title" content="Fotógrafos y Videógrafos en Almería | Reportajes de Bodas | Foto Valera">
        <meta property="og:description" content="Fotógrafos y videógrafos profesionales en Almería. Especialistas en reportajes de bodas, vídeo de bodas, comuniones y eventos. +23 años de experiencia capturando tus momentos más especiales.">
        <meta property="og:image" content="{{ $pageImageUrl }}">
        <meta property="og:image:width" content="{{ $pageImageWidth }}">
        <meta property="og:image:height" content="{{ $pageImageHeight }}">
        <meta property="og:image:alt" content="Foto Valera - Fotógrafo Profesional en Almería">
        <meta property="og:site_name" content="Foto Valera">
        <meta property="og:locale" content="es_ES">

        {{-- Twitter Card --}}
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:site" content="@foto_valera">
        <meta name="twitter:creator" content="@foto_valera">
        <meta name="twitter:title" content="Fotógrafos y Videógrafos en Almería | Reportajes de Bodas | Foto Valera">
        <meta name="twitter:description" content="Fotógrafos y videógrafos profesionales en Almería. Especialistas en reportajes de bodas, vídeo de bodas y eventos. +23 años de experiencia.">
        <meta name="twitter:image" content="{{ $pageImageUrl }}">
        <meta name="twitter:image:alt" content="Foto Valera - Fotógrafo Profesional en Almería">

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
            "@type": "LocalBusiness",
            "name": "Foto Valera",
            "description": "Fotógrafos y videógrafos profesionales en Almería especializados en reportajes de bodas, vídeo de bodas, eventos y sesiones fotográficas",
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
            "openingHours": "Mo-Fr 09:00-19:00, Sa 09:00-14:00",
            "priceRange": "€€€",
            "image": "{{ Storage::disk('logos')->url('SuperLogo.png') }}",
            "logo": "{{ Storage::disk('logos')->url('SuperLogo.png') }}",
            "sameAs": [
                "https://www.facebook.com/fotovalera",
                "https://www.instagram.com/fotovalera",
                "https://www.twitter.com/foto_valera"
            ],
            "hasOfferCatalog": {
                "@type": "OfferCatalog",
                "name": "Servicios Fotográficos",
                "itemListElement": [
                    {
                        "@type": "Offer",
                        "itemOffered": {
                            "@type": "Service",
                            "name": "Fotografía de Bodas",
                            "description": "Reportajes fotográficos y de vídeo para bodas en Almería y provincia"
                        }
                    },
                    {
                        "@type": "Offer",
                        "itemOffered": {
                            "@type": "Service",
                            "name": "Fotografía de Comuniones",
                            "description": "Sesiones fotográficas para primeras comuniones y eventos religiosos"
                        }
                    },
                    {
                        "@type": "Offer",
                        "itemOffered": {
                            "@type": "Service",
                            "name": "Sesiones de Estudio",
                            "description": "Fotografía profesional en estudio para retratos y sesiones creativas"
                        }
                    },
                    {
                        "@type": "Offer",
                        "itemOffered": {
                            "@type": "Service",
                            "name": "Fotografía de Embarazo",
                            "description": "Sesiones artísticas para capturar la belleza de la maternidad"
                        }
                    },
                    {
                        "@type": "Offer",
                        "itemOffered": {
                            "@type": "Service",
                            "name": "Fotografía Newborn",
                            "description": "Sesiones tiernas y seguras para recién nacidos"
                        }
                    },
                    {
                        "@type": "Offer",
                        "itemOffered": {
                            "@type": "Service",
                            "name": "Fotocarnet",
                            "description": "Fotos para documentos oficiales: DNI, pasaporte, carnets"
                        }
                    }
                ]
            },
            "aggregateRating": {
                "@type": "AggregateRating",
                "ratingValue": "4.9",
                "reviewCount": "156"
            },
            "foundingDate": "2001",
            "numberOfEmployees": "1-5",
            "areaServed": {
                "@type": "City",
                "name": "Almería"
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
                    "name": "¿Qué servicios fotográficos ofrecen en Foto Valera?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Ofrecemos fotografía de bodas, comuniones, sesiones de estudio, embarazo, newborn, fotocarnet y videografía profesional. Con más de 23 años de experiencia en Almería."
                    }
                },
                {
                    "@type": "Question",
                    "name": "¿Dónde está ubicado el estudio de Foto Valera?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Nuestro estudio está ubicado en C. Alcalde Muñoz, 13, 04004 Almería, en el centro de la ciudad con fácil acceso."
                    }
                },
                {
                    "@type": "Question",
                    "name": "¿Cuántos años de experiencia tiene Foto Valera?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Foto Valera cuenta con más de 23 años de experiencia en fotografía profesional, especializándose en bodas, eventos y sesiones de estudio."
                    }
                },
                {
                    "@type": "Question",
                    "name": "¿Realizan sesiones de fotocarnet?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Sí, ofrecemos servicio de fotocarnet profesional para DNI, pasaporte, carnets de conducir y otros documentos oficiales."
                    }
                },
                {
                    "@type": "Question",
                    "name": "¿Qué incluyen los reportajes de bodas en Almería?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Nuestros reportajes de bodas en Almería incluyen cobertura completa del evento: preparativos, ceremonia, celebración, sesión de pareja y postproducción profesional. Disponemos de fotógrafos y videógrafos experimentados con equipo de última generación."
                    }
                },
                {
                    "@type": "Question",
                    "name": "¿Son fotógrafos y videógrafos profesionales?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Sí, somos fotógrafos y videógrafos profesionales en Almería con más de 23 años de experiencia. Contamos con equipo profesional de fotografía y vídeo, drones, y realizamos edición y postproducción de alta calidad."
                    }
                },
                {
                    "@type": "Question",
                    "name": "¿Ofrecen vídeo de bodas además de fotografía?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Sí, nuestros videógrafos en Almería crean películas de boda cinematográficas en 4K con drones, estabilizadores y edición profesional. Ofrecemos paquetes combinados de fotografía y vídeo de bodas."
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
            "description": "Foto Valera es un estudio fotográfico profesional en Almería con más de 23 años de experiencia. Especializados en reportajes de bodas, vídeo de bodas, comuniones, sesiones de estudio, embarazo, newborn y fotocarnet. Fotógrafos y videógrafos profesionales certificados con equipo de última generación.",
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
            "numberOfEmployees": {
                "@type": "QuantitativeValue",
                "minValue": 1,
                "maxValue": 5
            },
            "aggregateRating": {
                "@type": "AggregateRating",
                "ratingValue": "4.9",
                "reviewCount": "156",
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
                "Fotografía de Comuniones",
                "Fotografía Newborn",
                "Fotografía de Embarazo",
                "Fotografía de Estudio",
                "Fotocarnet Profesional"
            ],
            "areaServed": {
                "@type": "City",
                "name": "Almería",
                "sameAs": "https://es.wikipedia.org/wiki/Almer%C3%ADa"
            }
        }
        </script>

        {{-- Schema.org para Person (E-E-A-T reforzado) --}}
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "Person",
            "name": "Valeriy",
            "jobTitle": "Fotógrafo y Videógrafo Principal",
            "worksFor": {
                "@type": "Organization",
                "name": "Foto Valera"
            },
            "description": "Fotógrafo profesional con más de 20 años de experiencia especializado en bodas, comuniones y reportajes familiares. Piloto certificado de drones con experiencia en videografía cinematográfica.",
            "knowsAbout": ["Fotografía de Bodas", "Videografía", "Fotografía de Eventos", "Fotografía Aérea con Drones"],
            "hasCredential": {
                "@type": "EducationalOccupationalCredential",
                "credentialCategory": "Certificación",
                "recognizedBy": {
                    "@type": "Organization",
                    "name": "Agencia Estatal de Seguridad Aérea"
                }
            }
        }
        </script>
    </x-slot>
    <div class="p-0 m-0 flex flex-col">


            @livewire('homepage-carousel')


        @livewire('admin.manage-homepage-carousel')

        @livewire('homepage.info-blocks-manager')
        @livewire('homepage.hero-section')
        @livewire('homepage.content-cards')
        @livewire('google-reviews-slider')
        {{-- <h2>Gestionar Video de la Home</h2>
        @livewire('home-video-manager') --}}
       <!-- LightWidget WIDGET --><script src="https://cdn.lightwidget.com/widgets/lightwidget.js"></script><iframe src="https://cdn.lightwidget.com/widgets/dc659f761d0e5c979b015e094fe2b18f.html" scrolling="no" allowtransparency="true" class="darkwidget-widget"
         style="width:100%;border:0;overflow:hidden;background-color:black;"></iframe>

        @livewire('team-directory')
    </div>

   {{-- Sección SEO: Fotógrafos y Videógrafos Almería --}}
   <section class="bg-gradient-to-b from-black via-gray-950 to-black py-20 sm:py-28">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Título Principal SEO --}}
        <div class="text-center mb-16">
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white mb-6">
                Fotógrafos y Videógrafos Profesionales en Almería
            </h2>
            <div class="w-24 h-1 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 mx-auto rounded-full"></div>
        </div>

        {{-- Grid de Contenido SEO --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 mb-16">

            {{-- Bloque 1: Reportajes de Bodas --}}
            <div class="bg-gradient-to-br from-gray-900 to-gray-950 rounded-2xl p-8 border border-gray-800 shadow-xl hover:shadow-2xl hover:shadow-indigo-500/10 transition-all duration-500">
                <div class="flex items-center gap-4 mb-6">
                    <div class="flex items-center justify-center w-14 h-14 bg-indigo-600/20 rounded-xl">
                        <svg class="w-8 h-8 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white">Reportajes de Bodas en Almería</h3>
                </div>
                <p class="text-gray-300 leading-relaxed mb-4">
                    Somos <strong class="text-white">fotógrafos especialistas en bodas en Almería</strong> con más de dos décadas creando
                    <strong class="text-white">reportajes de bodas</strong> únicos e irrepetibles. Nuestro equipo de
                    <strong class="text-white">fotógrafos y videógrafos profesionales</strong> captura cada momento de tu gran día con un
                    estilo natural, emotivo y cinematográfico. Descubre nuestra galería de
                    <a href="{{ route('weddings') }}" class="text-indigo-400 hover:text-indigo-300 underline font-semibold transition-colors">bodas en Almería</a>.
                </p>
                <p class="text-gray-300 leading-relaxed">
                    Los <strong class="text-white">reportajes de bodas en Almería</strong> que realizamos incluyen cobertura completa:
                    preparativos, ceremonia, celebración y sesiones de pareja. Trabajamos con equipo profesional de última generación
                    para garantizar <strong class="text-white">fotografía y vídeo de bodas</strong> de máxima calidad. También ofrecemos
                    servicios para <a href="{{ route('comuniones') }}" class="text-purple-400 hover:text-purple-300 underline transition-colors">comuniones</a>
                    y otros eventos especiales.
                </p>
                <div class="mt-6 flex flex-wrap gap-2">
                    <span class="inline-block px-3 py-1 bg-indigo-500/10 text-indigo-300 text-sm rounded-full border border-indigo-500/20">Fotografía de Bodas</span>
                    <span class="inline-block px-3 py-1 bg-purple-500/10 text-purple-300 text-sm rounded-full border border-purple-500/20">Vídeo de Bodas</span>
                    <span class="inline-block px-3 py-1 bg-pink-500/10 text-pink-300 text-sm rounded-full border border-pink-500/20">Reportajes Completos</span>
                </div>
            </div>

            {{-- Bloque 2: Videógrafos Almería --}}
            <div class="bg-gradient-to-br from-gray-900 to-gray-950 rounded-2xl p-8 border border-gray-800 shadow-xl hover:shadow-2xl hover:shadow-purple-500/10 transition-all duration-500">
                <div class="flex items-center gap-4 mb-6">
                    <div class="flex items-center justify-center w-14 h-14 bg-purple-600/20 rounded-xl">
                        <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white">Videógrafos en Almería</h3>
                </div>
                <p class="text-gray-300 leading-relaxed mb-4">
                    Como <strong class="text-white">videógrafos profesionales en Almería</strong>, creamos películas emotivas que
                    narran tu historia de manera única. Nuestro servicio de <strong class="text-white">videografía de bodas</strong>
                    combina técnicas cinematográficas con un enfoque documental para capturar la esencia de cada momento.
                    Mira nuestra colección de <a href="{{ route('videos') }}" class="text-purple-400 hover:text-purple-300 underline font-semibold transition-colors">vídeos de bodas</a>.
                </p>
                <p class="text-gray-300 leading-relaxed">
                    Los <strong class="text-white">videógrafos de Foto Valera en Almería</strong> utilizamos cámaras de alta definición,
                    drones, estabilizadores y equipos de audio profesional. Cada vídeo se edita con dedicación, añadiendo música,
                    efectos y postproducción de color para crear una pieza audiovisual memorable. También capturamos momentos especiales en
                    <a href="{{ route('embarazo.index') }}" class="text-pink-400 hover:text-pink-300 underline transition-colors">sesiones de embarazo</a>
                    y <a href="{{ route('newborn.index') }}" class="text-indigo-400 hover:text-indigo-300 underline transition-colors">fotografía newborn</a>.
                </p>
                <div class="mt-6 flex flex-wrap gap-2">
                    <span class="inline-block px-3 py-1 bg-purple-500/10 text-purple-300 text-sm rounded-full border border-purple-500/20">Vídeo 4K</span>
                    <span class="inline-block px-3 py-1 bg-pink-500/10 text-pink-300 text-sm rounded-full border border-pink-500/20">Drones</span>
                    <span class="inline-block px-3 py-1 bg-indigo-500/10 text-indigo-300 text-sm rounded-full border border-indigo-500/20">Edición Profesional</span>
                </div>
            </div>
        </div>

        {{-- Sección: ¿Por qué elegirnos? --}}
        <div class="bg-gradient-to-r from-indigo-950/50 to-purple-950/50 rounded-2xl p-8 lg:p-12 border border-indigo-800/30 backdrop-blur-sm">
            <div class="text-center mb-10">
                <h3 class="text-3xl font-bold text-white mb-4">
                    ¿Por Qué Elegir a Nuestros Fotógrafos en Almería?
                </h3>
                <p class="text-gray-300 text-lg max-w-3xl mx-auto">
                    Más de 23 años siendo referentes entre los <strong class="text-white">fotógrafos y videógrafos de Almería</strong>
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                {{-- Ventaja 1 --}}
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-2xl mb-4 shadow-lg shadow-indigo-500/20">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h4 class="text-xl font-bold text-white mb-3">Experiencia Demostrable</h4>
                    <p class="text-gray-300">
                        Más de <strong class="text-white">1,000 reportajes de bodas</strong> realizados por nuestros
                        <strong class="text-white">fotógrafos en Almería</strong>. Tu boda está en manos expertas.
                    </p>
                </div>

                {{-- Ventaja 2 --}}
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-purple-600 to-pink-600 rounded-2xl mb-4 shadow-lg shadow-purple-500/20">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                        </svg>
                    </div>
                    <h4 class="text-xl font-bold text-white mb-3">Equipo Profesional</h4>
                    <p class="text-gray-300">
                        Trabajamos con cámaras de última generación, lentes profesionales y equipos de iluminación.
                        Los mejores <strong class="text-white">videógrafos de Almería</strong> a tu servicio.
                    </p>
                </div>

                {{-- Ventaja 3 --}}
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-pink-600 to-red-600 rounded-2xl mb-4 shadow-lg shadow-pink-500/20">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </div>
                    <h4 class="text-xl font-bold text-white mb-3">Pasión y Dedicación</h4>
                    <p class="text-gray-300">
                        Cada <strong class="text-white">reportaje fotográfico y de vídeo</strong> lo tratamos como único.
                        Nuestros <strong class="text-white">fotógrafos profesionales</strong> se involucran en tu historia.
                    </p>
                </div>
            </div>

            {{-- CTA Final --}}
            <div class="mt-12 text-center">
                <p class="text-gray-300 text-lg mb-6">
                    ¿Buscas <strong class="text-white">fotógrafos para bodas en Almería</strong>? ¿Necesitas
                    <strong class="text-white">videógrafos profesionales</strong> para tu evento?
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('weddings') }}"
                        class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-bold rounded-xl shadow-lg shadow-indigo-500/20 transition-all duration-300 transform hover:scale-105 hover:shadow-indigo-500/40">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Ver Reportajes de Bodas
                    </a>
                    <a href="{{ route('videos') }}"
                        class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-500 hover:to-pink-500 text-white font-bold rounded-xl shadow-lg shadow-purple-500/20 transition-all duration-300 transform hover:scale-105 hover:shadow-purple-500/40">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        Ver Vídeos de Bodas
                    </a>
                    <a href="{{ route('gallery') }}"
                        class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-gradient-to-r from-pink-600 to-red-600 hover:from-pink-500 hover:to-red-500 text-white font-bold rounded-xl shadow-lg shadow-pink-500/20 transition-all duration-300 transform hover:scale-105 hover:shadow-pink-500/40">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Ver Galería Completa
                    </a>
                </div>
            </div>
        </div>

        {{-- Servicios Adicionales SEO --}}
        <div class="mt-16 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <a href="{{ route('videos') }}" class="bg-gray-900/50 backdrop-blur-sm rounded-xl p-6 border border-gray-800 text-center hover:border-indigo-500/50 hover:bg-gray-900/70 transition-all duration-300 transform hover:scale-105 group">
                <div class="text-3xl mb-3 group-hover:scale-110 transition-transform">🎥</div>
                <h4 class="text-white font-bold mb-2">Vídeo de Bodas</h4>
                <p class="text-gray-400 text-sm">Películas cinematográficas de tu boda por videógrafos expertos en Almería</p>
                <span class="inline-block mt-3 text-indigo-400 text-sm font-semibold group-hover:text-indigo-300">Ver vídeos →</span>
            </a>
            <a href="{{ route('weddings') }}" class="bg-gray-900/50 backdrop-blur-sm rounded-xl p-6 border border-gray-800 text-center hover:border-purple-500/50 hover:bg-gray-900/70 transition-all duration-300 transform hover:scale-105 group">
                <div class="text-3xl mb-3 group-hover:scale-110 transition-transform">📸</div>
                <h4 class="text-white font-bold mb-2">Fotografía de Bodas</h4>
                <p class="text-gray-400 text-sm">Reportajes fotográficos completos en Almería y provincia</p>
                <span class="inline-block mt-3 text-purple-400 text-sm font-semibold group-hover:text-purple-300">Ver bodas →</span>
            </a>
            <a href="{{ route('comuniones') }}" class="bg-gray-900/50 backdrop-blur-sm rounded-xl p-6 border border-gray-800 text-center hover:border-pink-500/50 hover:bg-gray-900/70 transition-all duration-300 transform hover:scale-105 group">
                <div class="text-3xl mb-3 group-hover:scale-110 transition-transform">🎉</div>
                <h4 class="text-white font-bold mb-2">Comuniones</h4>
                <p class="text-gray-400 text-sm">Reportajes de comuniones únicos y memorables en Almería</p>
                <span class="inline-block mt-3 text-pink-400 text-sm font-semibold group-hover:text-pink-300">Ver más →</span>
            </a>
            <a href="{{ route('fotocarnet.almeria') }}" class="bg-gray-900/50 backdrop-blur-sm rounded-xl p-6 border border-gray-800 text-center hover:border-red-500/50 hover:bg-gray-900/70 transition-all duration-300 transform hover:scale-105 group">
                <div class="text-3xl mb-3 group-hover:scale-110 transition-transform">🆔</div>
                <h4 class="text-white font-bold mb-2">Fotocarnet Almería</h4>
                <p class="text-gray-400 text-sm">Fotos para DNI, pasaporte y documentos oficiales</p>
                <span class="inline-block mt-3 text-red-400 text-sm font-semibold group-hover:text-red-300">Reservar →</span>
            </a>
        </div>

        {{-- Enlaces Adicionales de Servicios --}}
        <div class="mt-12 bg-gradient-to-r from-gray-900/50 to-gray-950/50 rounded-2xl p-8 border border-gray-800">
            <h3 class="text-2xl font-bold text-white text-center mb-6">Más Servicios Profesionales</h3>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="{{ route('embarazo.index') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-pink-600/20 hover:bg-pink-600/30 border border-pink-500/30 rounded-lg text-pink-300 hover:text-pink-200 transition-all duration-300 transform hover:scale-105">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                    Fotografía de Embarazo
                </a>
                <a href="{{ route('newborn.index') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-indigo-600/20 hover:bg-indigo-600/30 border border-indigo-500/30 rounded-lg text-indigo-300 hover:text-indigo-200 transition-all duration-300 transform hover:scale-105">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Fotografía Newborn
                </a>
                <a href="{{ route('studio.index') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-purple-600/20 hover:bg-purple-600/30 border border-purple-500/30 rounded-lg text-purple-300 hover:text-purple-200 transition-all duration-300 transform hover:scale-105">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                    Sesiones de Estudio
                </a>
                <a href="{{ route('gallery') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-green-600/20 hover:bg-green-600/30 border border-green-500/30 rounded-lg text-green-300 hover:text-green-200 transition-all duration-300 transform hover:scale-105">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Galería Completa
                </a>
                <a href="{{ route('blog.index') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-yellow-600/20 hover:bg-yellow-600/30 border border-yellow-500/30 rounded-lg text-yellow-300 hover:text-yellow-200 transition-all duration-300 transform hover:scale-105">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    Blog de Fotografía
                </a>
            </div>
        </div>
    </div>
   </section>

   {{-- Contenido Principal SEO Optimizado --}}
   <main id="main-content" class="bg-black py-20 sm:py-28">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">

        <p class="text-base font-semibold text-indigo-400">FOTÓGRAFO PROFESIONAL EN ALMERÍA</p>

        <h1 class="mt-4 text-4xl lg:text-5xl font-bold tracking-tight text-white">
            Capturamos Tus Momentos Más Especiales
        </h1>

        <p class="mt-6 text-lg leading-8 text-gray-300 max-w-3xl mx-auto">
            Con más de <strong>23 años de experiencia</strong>, <strong>Foto Valera</strong> es un referente en
            <strong>fotografía y vídeo en Almería</strong>. Especializados en
            <a href="{{ route('weddings') }}" class="text-indigo-400 hover:text-indigo-300 font-bold underline transition-colors">bodas</a>,
            <a href="{{ route('comuniones') }}" class="text-purple-400 hover:text-purple-300 font-bold underline transition-colors">comuniones</a>,
            <a href="{{ route('studio.index') }}" class="text-pink-400 hover:text-pink-300 font-bold underline transition-colors">sesiones de estudio</a>,
            <a href="{{ route('embarazo.index') }}" class="text-indigo-400 hover:text-indigo-300 font-bold underline transition-colors">embarazo</a>,
            <a href="{{ route('newborn.index') }}" class="text-purple-400 hover:text-purple-300 font-bold underline transition-colors">newborn</a> y
            <a href="{{ route('fotocarnet.almeria') }}" class="text-pink-400 hover:text-pink-300 font-bold underline transition-colors">fotografía de carnet</a>,
            capturamos cada instante con sensibilidad, técnica y pasión. Nuestro objetivo es
            transformar tus momentos más importantes en <strong>recuerdos eternos</strong>,
            combinando <strong>creatividad</strong>, <strong>profesionalidad</strong> y un
            <strong>estilo único</strong> que distingue cada reportaje.
        </p>

        {{-- Servicios Destacados --}}
        <div class="mt-12 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-4xl mx-auto">
            <a href="{{ route('weddings') }}" class="bg-gray-900/50 backdrop-blur-sm rounded-lg p-6 border border-gray-800 hover:border-indigo-500/50 hover:bg-gray-900/70 transition-all duration-300 transform hover:scale-105 group">
                <h3 class="text-lg font-semibold text-white mb-2 group-hover:text-indigo-300 transition-colors">📸 Bodas en Almería</h3>
                <p class="text-gray-400 text-sm">Reportajes fotográficos y de vídeo para tu día más especial</p>
            </a>
            <a href="{{ route('newborn.index') }}" class="bg-gray-900/50 backdrop-blur-sm rounded-lg p-6 border border-gray-800 hover:border-purple-500/50 hover:bg-gray-900/70 transition-all duration-300 transform hover:scale-105 group">
                <h3 class="text-lg font-semibold text-white mb-2 group-hover:text-purple-300 transition-colors">👶 Newborn en Almería</h3>
                <p class="text-gray-400 text-sm">Sesiones tiernas y seguras para recién nacidos</p>
            </a>
            <a href="{{ route('fotocarnet.almeria') }}" class="bg-gray-900/50 backdrop-blur-sm rounded-lg p-6 border border-gray-800 hover:border-pink-500/50 hover:bg-gray-900/70 transition-all duration-300 transform hover:scale-105 group">
                <h3 class="text-lg font-semibold text-white mb-2 group-hover:text-pink-300 transition-colors">🆔 Fotocarnet en Almería</h3>
                <p class="text-gray-400 text-sm">Fotos profesionales para documentos oficiales</p>
            </a>
        </div>


        {{-- Información de Contacto --}}
        <div class="mt-16 pt-8 border-t border-gray-800">
            <h2 class="text-2xl font-bold text-white mb-6">¿Listo para Capturar Tus Momentos?</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
                <div>
                    <h3 class="text-lg font-semibold text-white mb-2">📍 Ubicación</h3>
                    <p class="text-gray-400">C. Alcalde Muñoz, 13<br>04004 Almería</p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-white mb-2">🕒 Horarios</h3>
                    <p class="text-gray-400">Lun-Vie: 9:00-19:00<br>Sáb: 9:00-14:00</p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-white mb-2">📞 Contacto</h3>
                    <p class="text-gray-400">+34 660 581 178<br>info@fotovalera.com</p>
                </div>
            </div>
        </div>
    </div>
</main>
    <x-self.superPie></x-self.superPie>

</x-app-layout>
