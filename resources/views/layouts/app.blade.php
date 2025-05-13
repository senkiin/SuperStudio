<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Fotovalera'))</title>
    <meta name="description" content="@yield('metaDescription', 'Explora nuestros álbumes, comuniones y sesiones únicas en Almeria')">

    <meta property="og:title" content="@yield('title', config('app.name'))">
    <meta property="og:description" content="@yield('metaDescription', 'Explora nuestros álbumes y fotos destacadas')">
    <meta property="og:image" content="{{ Storage::url('favicon/apple-touch-icon.png') }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', config('app.name'))">
    <meta name="twitter:description" content="@yield('metaDescription', 'Explora nuestras sesiones y fotos profesionales')">
    <meta name="twitter:image" content="{{ Storage::url('favicon/apple-touch-icon.png') }}">

    <link rel="canonical" href="{{ url()->current() }}" />

    <!-- Favicons -->
    <link rel="icon" type="image/png" href="{{ Storage::url('favicon/favicon-96x96.png') }}" sizes="96x96">
    <link rel="icon" type="image/svg+xml" href="{{ Storage::url('favicon/favicon.svg') }}">
    <link rel="shortcut icon" href="{{ Storage::url('favicon/favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ Storage::url('favicon/apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ Storage::url('favicon/site.webmanifest') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>


<body class="font-sans antialiased ">
    <x-banner />

    <div class="min-h-screen bg-gray-100">
        {{-- Incluye el componente Livewire que renderiza navigation-menu.blade.php --}}
        @livewire('navigation-menu')

        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <main>
            {{ $slot }}
        </main>
    </div>


    @stack('modals') {{-- Si usas esto --}}
    @livewireScripts
    @stack('scripts')
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script>
        function startAOS() {
            if (typeof AOS !== 'undefined') {
                AOS.init({
                    once: false, // permite que se repita siempre
                    duration: 1200, // animación más larga y visible
                    easing: 'ease-in-out',
                    mirror: true, // activa al hacer scroll hacia arriba también
                    offset: 300, // comienza antes de que esté completamente visible
                });
                AOS.refreshHard();
            }
        }

        window.addEventListener('DOMContentLoaded', startAOS);
        document.addEventListener('livewire:load', startAOS);
        document.addEventListener('livewire:update', () => {
            setTimeout(() => AOS.refreshHard(), 100);
        });
    </script>



</body>

</html>
