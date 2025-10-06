{{-- resources/views/fotocarnet-almeria.blade.php --}}
<x-app-layout>
    {{-- ========================================================== --}}
    {{--    SEO COMPLETO Y PROFESIONAL PARA FOTOCARNET ALMERÍA      --}}
    {{-- ========================================================== --}}
    <x-slot name="head">
        {{-- Meta Tags Básicos --}}
        <title>Fotocarnet al Instante en Almería | DNI, Pasaporte, Carnet Conducir - Foto Valera</title>
        <meta name="description" content="Fotocarnet profesional en Almería. Fotos para DNI, pasaporte, carnet de conducir, visados y documentos oficiales. Calidad garantizada, listo en minutos. Estudio profesional en centro de Almería.">
        <meta name="keywords" content="fotocarnet almeria, foto dni almeria, foto pasaporte almeria, fotos carnet almeria, fotocarnet rapido almeria, foto carnet conducir almeria, fotos visado almeria, fotos documento oficial almeria, estudio fotografico almeria, fotovalera almeria, foto nie almeria, foto curriculum almeria">
        <meta name="author" content="Foto Valera">
        <meta name="publisher" content="Foto Valera">
        <meta name="robots" content="index, follow">
        <meta name="language" content="es">
        <meta name="geo.region" content="ES-AL">
        <meta name="geo.placename" content="Almería">
        <meta name="geo.position" content="36.8381;-2.4597">
        <meta name="ICBM" content="36.8381, -2.4597">

        {{-- URL Canónica --}}
        <link rel="canonical" href="{{ route('fotocarnet.almeria') }}">

        {{-- Open Graph / Facebook --}}
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ route('fotocarnet.almeria') }}">
        <meta property="og:title" content="Fotocarnet al Instante en Almería | DNI, Pasaporte, Carnet Conducir - Foto Valera">
        <meta property="og:description" content="Fotocarnet profesional en Almería. Fotos para DNI, pasaporte, carnet de conducir, visados y documentos oficiales. Calidad garantizada, listo en minutos. Estudio profesional en centro de Almería.">
        <meta property="og:image" content="{{ asset('images/og_fotocarnet_almeria.jpg') }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:image:alt" content="Fotocarnet profesional en Almería - Foto Valera">
        <meta property="og:site_name" content="Foto Valera">
        <meta property="og:locale" content="es_ES">

        {{-- Twitter Card --}}
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:site" content="@foto_valera">
        <meta name="twitter:creator" content="@foto_valera">
        <meta name="twitter:title" content="Fotocarnet al Instante en Almería | DNI, Pasaporte, Carnet Conducir - Foto Valera">
        <meta name="twitter:description" content="Fotocarnet profesional en Almería. Fotos para DNI, pasaporte, carnet de conducir, visados y documentos oficiales. Calidad garantizada, listo en minutos.">
        <meta name="twitter:image" content="{{ asset('images/twitter_fotocarnet_almeria.jpg') }}">
        <meta name="twitter:image:alt" content="Fotocarnet profesional en Almería - Foto Valera">

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
            "name": "Foto Valera - Fotocarnet Almería",
            "description": "Estudio fotográfico profesional especializado en fotocarnet para documentos oficiales en Almería",
            "url": "{{ route('fotocarnet.almeria') }}",
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
            "priceRange": "€€",
            "image": "{{ asset('images/og_fotocarnet_almeria.jpg') }}",
            "logo": "{{ asset('images/logo.png') }}",
            "sameAs": [
                "https://www.facebook.com/fotovalera",
                "https://www.instagram.com/fotovalera",
                "https://www.twitter.com/foto_valera"
            ],
            "hasOfferCatalog": {
                "@type": "OfferCatalog",
                "name": "Servicios de Fotocarnet",
                "itemListElement": [
                    {
                        "@type": "Offer",
                        "itemOffered": {
                            "@type": "Service",
                            "name": "Fotos para DNI",
                            "description": "Fotocarnet para DNI español cumpliendo normativa oficial"
                        }
                    },
                    {
                        "@type": "Offer",
                        "itemOffered": {
                            "@type": "Service",
                            "name": "Fotos para Pasaporte",
                            "description": "Fotocarnet para pasaporte español e internacional"
                        }
                    },
                    {
                        "@type": "Offer",
                        "itemOffered": {
                            "@type": "Service",
                            "name": "Fotos para Carnet de Conducir",
                            "description": "Fotocarnet para carnet de conducir y permisos de circulación"
                        }
                    },
                    {
                        "@type": "Offer",
                        "itemOffered": {
                            "@type": "Service",
                            "name": "Fotos para Visados",
                            "description": "Fotocarnet para visados de cualquier país"
                        }
                    }
                ]
            },
            "aggregateRating": {
                "@type": "AggregateRating",
                "ratingValue": "4.8",
                "reviewCount": "127"
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
                    "name": "¿Cuánto tiempo tarda en estar listo mi fotocarnet?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "En la mayoría de los casos, tu fotocarnet estará listo en pocos minutos. Nuestro servicio es ágil y eficiente para que no tengas que esperar."
                    }
                },
                {
                    "@type": "Question",
                    "name": "¿Qué documentos oficiales cubre vuestro servicio de fotocarnet?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Cubrimos fotos para DNI, pasaporte, carnet de conducir, visados, NIE, currículum, carnets escolares, universitarios y cualquier otro documento oficial."
                    }
                },
                {
                    "@type": "Question",
                    "name": "¿Cumplen las fotos con la normativa oficial?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Sí, estamos al día con todos los requisitos específicos para fotos de DNI español, pasaporte europeo e internacional, así como para otros documentos oficiales y visados de diferentes países."
                    }
                }
            ]
        }
        </script>
    </x-slot>

    {{-- Componente para la cabecera de la página --}}
    @livewire('configurable-page-header', [
        'identifier' => 'fotocarnet_almeria_header',
        'defaultTitle' => 'Fotocarnet en Almería',
        'defaultSubtitle' => 'Calidad Profesional para tus Documentos: DNI, Pasaporte, Carnets y Más.',
        // 'defaultImage' => Storage::url('path/to/your/default_fotocarnet_header.jpg') // Sube una imagen por defecto a S3 y usa Storage::url()
    ])

    <div class="py-12 bg-gray-100 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8 items-start">

                {{-- Columna de Contenido SEO --}}
                <article class="prose prose-lg dark:prose-invert text-gray-700 dark:text-gray-300 space-y-6">
                    <header>
                        <h1>Fotocarnet al Instante en Almería | DNI, Pasaporte, Carnet Conducir - Foto Valera</h1>
                        <p class="lead">En <strong>Foto Valera</strong>, entendemos la importancia de una foto de carnet impecable. Ya sea para tu <strong>DNI</strong>, <strong>pasaporte</strong>, carnet de conducir, visado, currículum o cualquier otro documento oficial, te garantizamos una imagen que cumple con todos los requisitos normativos y resalta tu mejor versión.</p>
                    </header>

                    <section>
                        <h2>¿Por qué Elegir Foto Valera para tu Fotocarnet en Almería?</h2>
                        <p>Somos el estudio fotográfico de referencia en Almería para fotocarnet profesional. Nuestro compromiso con la calidad y el servicio al cliente nos ha convertido en la primera opción para miles de almerienses.</p>

                        <ul>
                            <li><strong>Calidad Profesional Garantizada:</strong> Utilizamos equipamiento fotográfico de última generación e iluminación de estudio profesional para asegurar fotos nítidas, con colores precisos y una exposición perfecta.</li>
                            <li><strong>Rapidez y Eficiencia:</strong> Sabemos que tu tiempo es valioso. Nuestro servicio de fotocarnet es ágil, y en la mayoría de los casos, tendrás tus fotos listas para llevar en pocos minutos.</li>
                            <li><strong>Cumplimiento Estricto de Normativas:</strong> Estamos al día con los requisitos específicos para fotos de DNI español, pasaporte europeo e internacional, así como para otros documentos oficiales y visados de diferentes países.</li>
                            <li><strong>Atención Personalizada y Asesoramiento:</strong> Te guiamos para que tu expresión y postura sean las más adecuadas, asegurando un resultado profesional que te satisfaga plenamente.</li>
                            <li><strong>Ubicación Céntrica en Almería:</strong> Nuestro estudio está ubicado en el centro de Almería, facilitando tu visita y ahorrándote tiempo en desplazamientos.</li>
                            <li><strong>Precios Competitivos:</strong> Ofrecemos tarifas justas y transparentes sin sorpresas ni costes ocultos.</li>
                        </ul>
                    </section>

                    <section>
                        <h2>Nuestros Servicios de Fotografía de Carnet en Almería</h2>
                        <p>En Foto Valera cubrimos todas tus necesidades de fotocarnet para documentos oficiales:</p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 my-6">
                            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
                                <h3 class="text-lg font-semibold mb-2">📄 Documentos de Identidad</h3>
                                <ul class="text-sm space-y-1">
                                    <li>• Fotos para DNI español</li>
                                    <li>• Fotos para NIE (Número de Identidad de Extranjero)</li>
                                    <li>• Fotos para Pasaporte (España y estándares internacionales)</li>
                                </ul>
                            </div>

                            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
                                <h3 class="text-lg font-semibold mb-2">🚗 Permisos y Licencias</h3>
                                <ul class="text-sm space-y-1">
                                    <li>• Fotos para Carnet de Conducir (todos los permisos)</li>
                                    <li>• Fotos para Licencias especiales</li>
                                    <li>• Fotos para Permisos de caza y armas</li>
                                </ul>
                            </div>

                            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
                                <h3 class="text-lg font-semibold mb-2">🌍 Visados y Documentos Internacionales</h3>
                                <ul class="text-sm space-y-1">
                                    <li>• Fotos para Visados de cualquier país</li>
                                    <li>• Fotos para Documentos de trabajo</li>
                                    <li>• Fotos para Estudios en el extranjero</li>
                                </ul>
                            </div>

                            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
                                <h3 class="text-lg font-semibold mb-2">💼 Profesionales y Académicos</h3>
                                <ul class="text-sm space-y-1">
                                    <li>• Fotos para Currículum Vitae</li>
                                    <li>• Fotos para Perfiles Profesionales (LinkedIn)</li>
                                    <li>• Fotos para Carnets Escolares y Universitarios</li>
                                </ul>
                            </div>
                        </div>
                    </section>

                    <section>
                        <h2>Proceso de Fotocarnet en Nuestro Estudio de Almería</h2>
                        <ol class="list-decimal space-y-3 pl-6">
                            <li><strong>Reserva tu Cita:</strong> Puedes reservar online o llamarnos directamente. Te confirmamos la disponibilidad inmediatamente.</li>
                            <li><strong>Llegada al Estudio:</strong> Te recibimos en nuestro estudio profesional ubicado en el centro de Almería.</li>
                            <li><strong>Sesión Fotográfica:</strong> Nuestro fotógrafo profesional te guía para obtener la mejor imagen cumpliendo todos los requisitos.</li>
                            <li><strong>Revisión y Aprobación:</strong> Revisamos juntos las fotos para asegurar que cumplan con tus expectativas y la normativa.</li>
                            <li><strong>Entrega Inmediata:</strong> En la mayoría de casos, tus fotos están listas en pocos minutos.</li>
                        </ol>
                    </section>

                    <section>
                        <h2>Requisitos para Fotocarnet en Almería</h2>
                        <p>Para garantizar que tu fotocarnet cumpla con la normativa oficial, te recordamos los requisitos básicos:</p>

                        <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg my-4">
                            <h3 class="font-semibold mb-2">📋 Requisitos Generales:</h3>
                            <ul class="text-sm space-y-1">
                                <li>• Fondo blanco o ligeramente gris</li>
                                <li>• Rostro completamente visible y centrado</li>
                                <li>• Expresión neutra, sin sonreír</li>
                                <li>• Sin gafas de sol ni accesorios que oculten el rostro</li>
                                <li>• Ropa de colores contrastantes con el fondo</li>
                                <li>• Tamaño específico según el documento (32x26mm para DNI, 35x45mm para pasaporte)</li>
                            </ul>
                        </div>
                    </section>

                    <section>
                        <h2>Preguntas Frecuentes sobre Fotocarnet en Almería</h2>

                        <div class="space-y-4">
                            <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
                                <h3 class="font-semibold mb-2">¿Cuánto tiempo tarda en estar listo mi fotocarnet?</h3>
                                <p class="text-sm">En la mayoría de los casos, tu fotocarnet estará listo en pocos minutos. Nuestro servicio es ágil y eficiente para que no tengas que esperar.</p>
                            </div>

                            <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
                                <h3 class="font-semibold mb-2">¿Qué documentos oficiales cubre vuestro servicio de fotocarnet?</h3>
                                <p class="text-sm">Cubrimos fotos para DNI, pasaporte, carnet de conducir, visados, NIE, currículum, carnets escolares, universitarios y cualquier otro documento oficial.</p>
                            </div>

                            <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
                                <h3 class="font-semibold mb-2">¿Cumplen las fotos con la normativa oficial?</h3>
                                <p class="text-sm">Sí, estamos al día con todos los requisitos específicos para fotos de DNI español, pasaporte europeo e internacional, así como para otros documentos oficiales y visados de diferentes países.</p>
                            </div>

                            <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
                                <h3 class="font-semibold mb-2">¿Puedo reservar cita online?</h3>
                                <p class="text-sm">Sí, puedes reservar tu cita directamente desde esta página usando nuestro sistema de reservas online. Es rápido, fácil y te ahorra tiempo.</p>
                            </div>
                        </div>
                    </section>

                    <section>
                        <h2>Contacto y Ubicación - Foto Valera Almería</h2>
                        <p>Nuestro estudio está ubicado en el centro de Almería, con fácil acceso en transporte público y aparcamiento cercano. Te ofrecemos:</p>

                        <ul class="space-y-2">
                            <li>📍 <strong>Ubicación:</strong> C. Alcalde Muñoz, 13, 04004 Almería</li>
                            <li>🕒 <strong>Horarios:</strong> Lunes a Viernes 9:00-19:00, Sábados 9:00-14:00</li>
                            <li>📞 <strong>Teléfono:</strong> +34 660 581 178</li>
                            <li>✉️ <strong>Email:</strong> info@fotovalera.com</li>
                            <li>🚗 <strong>Aparcamiento:</strong> Zona de aparcamiento público cercana</li>
                        </ul>
                    </section>

                    <footer class="bg-gray-100 dark:bg-gray-800 p-6 rounded-lg mt-8">
                        <h2>Reserva tu Fotocarnet en Almería Ahora</h2>
                        <p>No dejes tu imagen oficial al azar. Confía en la experiencia y profesionalismo de <strong>Foto Valera</strong> para obtener tu <strong>fotocarnet en Almería</strong>. Reserva tu cita online de forma rápida y sencilla y evita esperas.</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-4">Estudio fotográfico profesional en Almería | Fotocarnet para DNI, Pasaporte, Carnet de Conducir | Calidad garantizada | Listo en minutos</p>
                    </footer>
                </article>

                {{-- Columna del Componente de Reservas --}}
                <aside class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-xl shadow-2xl sticky top-24"> {{-- Sticky para que se mantenga visible al hacer scroll --}}
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6 text-center">Reserva tu Cita para Fotocarnet</h2>
                    @livewire('appointment-scheduler', ['pageContext' => 'fotocarnet'])
                </aside>
            </div>
        </div>
    </div>
    <x-self.superPie></x-self.superPie>
</x-app-layout>
