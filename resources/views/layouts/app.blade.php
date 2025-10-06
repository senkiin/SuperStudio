<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="canonical" href="{{ url()->current() }}" />

    @php
        use Illuminate\Support\Facades\Storage;

        $disk = Storage::disk('favicons');
        $expires = now()->addMinutes(60); // tiempo de validez de la URL

        $favicon16 = $disk->temporaryUrl('favicon-16x16.png', $expires);
        $favicon32 = $disk->temporaryUrl('favicon-32x32.png', $expires);
        $chrome192 = $disk->temporaryUrl('android-chrome-192x192.png', $expires);
        $chrome512 = $disk->temporaryUrl('android-chrome-512x512.png', $expires);
        $apple180 = $disk->temporaryUrl('apple-touch-icon.png', $expires);
        $faviconIco = $disk->temporaryUrl('favicon.ico', $expires);
    @endphp

    <!-- Favicons con URLs temporales -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ $favicon16 }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ $favicon32 }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ $chrome192 }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ $chrome512 }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ $apple180 }}">
    <link rel="shortcut icon" href="{{ $faviconIco }}">


    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @trixassets
    @stack('styles')

    {{-- Slot para meta tags dinámicos --}}
    {{ $head ?? '' }}
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
    <livewire:contact-fab />


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
