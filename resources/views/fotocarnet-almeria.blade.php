{{-- resources/views/fotocarnet-almeria.blade.php --}}
<x-app-layout>
    {{-- SEO Meta Tags --}}
    @section('title', $pageTitle ?? 'Fotocarnet Almería | Fotovalera')
    @section('metaDescription', $metaDescription ?? 'Servicio de fotocarnet profesional en Almería. Rápido y de calidad para DNI, pasaporte y otros documentos. ¡Reserva tu cita!')
    @push('meta_tags')
        <meta name="keywords" content="{{ $metaKeywords ?? 'fotocarnet almeria, foto dni, foto pasaporte, fotos carnet, fotovalera' }}">
        <meta property="og:title" content="{{ $pageTitle ?? 'Fotocarnet Almería | Fotovalera' }}">
        <meta property="og:description" content="{{ $metaDescription ?? 'Servicio de fotocarnet profesional en Almería.' }}">
        {{-- <meta property="og:image" content="{{ asset('images/fotocarnet-almeria-og.jpg') }}"> --}} {{-- DEBES CREAR ESTA IMAGEN --}}
        <meta property="og:url" content="{{ route('fotocarnet.almeria') }}">
        <meta name="robots" content="index, follow"> {{-- Importante para SEO --}}
    @endpush

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
                    <h1>Fotocarnet en Almería: Calidad Profesional al Instante</h1>
                    <p>En <strong>Fotovalera</strong>, entendemos la importancia de una foto de carnet impecable. Ya sea para tu <strong>DNI</strong>, <strong>pasaporte</strong>, carnet de conducir, visado, currículum o cualquier otro documento oficial, te garantizamos una imagen que cumple con todos los requisitos normativos y resalta tu mejor versión.</p>

                    <h2>¿Por qué Elegir Fotovalera para tu Fotocarnet en Almería?</h2>
                    <ul>
                        <li><strong>Calidad Profesional Garantizada:</strong> Utilizamos equipamiento fotográfico de última generación e iluminación de estudio profesional para asegurar fotos nítidas, con colores precisos y una exposición perfecta.</li>
                        <li><strong>Rapidez y Eficiencia:</strong> Sabemos que tu tiempo es valioso. Nuestro servicio de fotocarnet es ágil, y en la mayoría de los casos, tendrás tus fotos listas para llevar en pocos minutos.</li>
                        <li><strong>Cumplimiento Estricto de Normativas:</strong> Estamos al día con los requisitos específicos para fotos de DNI español, pasaporte europeo e internacional, así como para otros documentos oficiales y visados de diferentes países.</li>
                        <li><strong>Atención Personalizada y Asesoramiento:</strong> Te guiamos para que tu expresión y postura sean las más adecuadas, asegurando un resultado profesional que te satisfaga plenamente.</li>
                        <li><strong>Ubicación Céntrica en Almería:</strong> Nuestro estudio es de fácil acceso, facilitando tu visita.</li>
                    </ul>

                    <h3>Nuestros Servicios de Fotografía de Carnet Incluyen:</h3>
                    <ul class="list-disc space-y-1 pl-5">
                        <li>Fotos para DNI y NIE.</li>
                        <li>Fotos para Pasaporte (España y estándares internacionales).</li>
                        <li>Fotos para Carnet de Conducir (todos los permisos).</li>
                        <li>Fotos para Visados de cualquier país.</li>
                        <li>Fotos para Currículum Vitae y Perfiles Profesionales (LinkedIn, etc.).</li>
                        <li>Fotos para Carnets Escolares, Universitarios, de biblioteca y otros carnets de identificación.</li>
                        <li>Fotos para Licencias y Permisos especiales (caza, armas, federaciones deportivas).</li>
                        <li>Tamaños especiales y adaptaciones digitales bajo petición.</li>
                    </ul>
                    <p>No dejes tu imagen oficial al azar. Confía en la experiencia y profesionalismo de Fotovalera para obtener tu <strong>fotocarnet en Almería</strong>. Reserva tu cita online de forma rápida y sencilla y evita esperas.</p>
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
