{{-- resources/views/home.blade.php --}}
<x-app-layout>
    {{-- ========================================================== --}}
    {{--    SEO COMPLETO Y PROFESIONAL PARA LA PÁGINA PRINCIPAL      --}}
    {{-- ========================================================== --}}
    <x-slot name="head">
        {{-- Meta Tags Básicos --}}
        <title>Foto Valera | Fotógrafo Profesional en Almería | Bodas, Eventos y Estudio</title>
        <meta name="description" content="Foto Valera, fotógrafo y videógrafo profesional en Almería con más de 23 años de experiencia. Especialistas en bodas, comuniones, sesiones de estudio, embarazo, newborn y fotocarnet. Capturamos tus momentos más especiales con creatividad y pasión.">
        <meta name="keywords" content="fotografo almeria, fotografos en almeria, fotografia de boda almeria, video de boda almeria, estudio fotografico almeria, fotografo comunion almeria, fotografo embarazo almeria, fotografo newborn almeria, fotocarnet almeria, fotovalera, foto valera, videografo almeria, reportaje fotografico almeria">
        <meta name="author" content="Foto Valera">
        <meta name="publisher" content="Foto Valera">
        <meta name="robots" content="index, follow">
        <meta name="language" content="es">
        <meta name="geo.region" content="ES-AL">
        <meta name="geo.placename" content="Almería">
        <meta name="geo.position" content="36.8381;-2.4597">
        <meta name="ICBM" content="36.8381, -2.4597">

        {{-- URL Canónica --}}
        <link rel="canonical" href="{{ route('home') }}">

        {{-- Open Graph / Facebook --}}
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ route('home') }}">
        <meta property="og:title" content="Foto Valera | Fotógrafo Profesional en Almería | Bodas, Eventos y Estudio">
        <meta property="og:description" content="Foto Valera, fotógrafo y videógrafo profesional en Almería. Especialistas en bodas, comuniones, sesiones de estudio, embarazo, newborn y fotocarnet. Más de 23 años capturando momentos únicos.">
        <meta property="og:image" content="{{ Storage::disk('logos')->url('SuperLogo.png') }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:image:alt" content="Foto Valera - Fotógrafo Profesional en Almería">
        <meta property="og:site_name" content="Foto Valera">
        <meta property="og:locale" content="es_ES">

        {{-- Twitter Card --}}
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:site" content="@foto_valera">
        <meta name="twitter:creator" content="@foto_valera">
        <meta name="twitter:title" content="Foto Valera | Fotógrafo Profesional en Almería | Bodas, Eventos y Estudio">
        <meta name="twitter:description" content="Foto Valera, fotógrafo y videógrafo profesional en Almería. Especialistas en bodas, comuniones, sesiones de estudio, embarazo, newborn y fotocarnet.">
        <meta name="twitter:image" content="{{ Storage::disk('logos')->url('SuperLogo.png') }}">
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
            "description": "Fotógrafo y videógrafo profesional especializado en bodas, eventos y reportajes en Almería",
            "url": "{{ route('home') }}",
            "telephone": "+34-XXX-XXX-XXX",
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
                }
            ]
        }
        </script>

        {{-- Schema.org para Organization --}}
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "Organization",
            "name": "Foto Valera",
            "url": "{{ route('home') }}",
            "logo": "{{ Storage::disk('logos')->url('SuperLogo.png') }}",
            "description": "Fotógrafo y videógrafo profesional en Almería especializado en bodas, eventos y reportajes",
            "address": {
                "@type": "PostalAddress",
                "streetAddress": "C. Alcalde Muñoz, 13",
                "addressLocality": "Almería",
                "addressRegion": "Andalucía",
                "postalCode": "04004",
                "addressCountry": "ES"
            },
            "contactPoint": {
                "@type": "ContactPoint",
                "telephone": "+34-XXX-XXX-XXX",
                "contactType": "customer service",
                "availableLanguage": "Spanish"
            },
            "sameAs": [
                "https://www.facebook.com/fotovalera",
                "https://www.instagram.com/fotovalera",
                "https://www.twitter.com/foto_valera"
            ]
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

   {{-- Contenido Principal SEO Optimizado --}}
   <main id="main-content" class="bg-black py-20 sm:py-28">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">

        <p class="text-base font-semibold text-indigo-400">FOTÓGRAFO PROFESIONAL EN ALMERÍA</p>

        <h1 class="mt-4 text-4xl lg:text-5xl font-bold tracking-tight text-white">
            Capturamos Tus Momentos Más Especiales
        </h1>

        <p class="mt-6 text-lg leading-8 text-gray-300 max-w-3xl mx-auto">
            Con más de 23 años de experiencia, Foto Valera es tu fotógrafo de confianza en Almería.
            Especialistas en <strong>bodas</strong>, <strong>comuniones</strong>, <strong>sesiones de estudio</strong>,
            <strong>embarazo</strong>, <strong>newborn</strong> y <strong>fotocarnet</strong>.
            Transformamos momentos únicos en recuerdos eternos con creatividad y pasión profesional.
        </p>

        {{-- Servicios Destacados --}}
        <div class="mt-12 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-4xl mx-auto">
            <div class="bg-gray-900/50 backdrop-blur-sm rounded-lg p-6 border border-gray-800">
                <h3 class="text-lg font-semibold text-white mb-2">📸 Bodas</h3>
                <p class="text-gray-400 text-sm">Reportajes fotográficos y de vídeo para tu día más especial</p>
            </div>
            <div class="bg-gray-900/50 backdrop-blur-sm rounded-lg p-6 border border-gray-800">
                <h3 class="text-lg font-semibold text-white mb-2">👶 Newborn</h3>
                <p class="text-gray-400 text-sm">Sesiones tiernas y seguras para recién nacidos</p>
            </div>
            <div class="bg-gray-900/50 backdrop-blur-sm rounded-lg p-6 border border-gray-800">
                <h3 class="text-lg font-semibold text-white mb-2">🆔 Fotocarnet</h3>
                <p class="text-gray-400 text-sm">Fotos profesionales para documentos oficiales</p>
            </div>
        </div>

        {{-- Botones de Acción --}}
        <div class="mt-10 flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('albums') }}"
                class="inline-block rounded-md bg-indigo-500 px-6 py-3 text-base font-semibold text-white shadow-lg
                       hover:bg-indigo-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2
                       focus-visible:outline-indigo-500 transition-all duration-300 transform hover:scale-105">
                Ver Nuestro Trabajo
            </a>
            <a href="{{ route('fotocarnet.almeria') }}"
                class="inline-block rounded-md bg-green-600 px-6 py-3 text-base font-semibold text-white shadow-lg
                       hover:bg-green-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2
                       focus-visible:outline-green-500 transition-all duration-300 transform hover:scale-105">
                Reservar Fotocarnet
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
                    <p class="text-gray-400">+34 XXX XXX XXX<br>info@fotovalera.com</p>
                </div>
            </div>
        </div>
    </div>
</main>
    <x-self.superPie></x-self.superPie>

</x-app-layout>
