{{-- resources/views/layouts/admin.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - Admin</title> {{-- Título específico --}}

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        <x-banner />

        <div class="min-h-screen bg-gray-100">
             {{-- Incluye la navegación superior principal (ya simplificada) --}}
            @livewire('navigation-menu')

            {{-- Contenedor principal con Sidebar + Contenido --}}
            <div class="flex"> {{-- Usamos Flexbox para sidebar y contenido --}}

                {{-- Incluye la barra lateral de admin --}}
                @include('layouts.partials.admin-sidebar')

                {{-- Contenido Principal de la Página Admin --}}
                <main class="flex-1 p-6"> {{-- flex-1 hace que ocupe el espacio restante --}}
                    @if (isset($header))
                        <header class="bg-white shadow mb-6">
                            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                                {{ $header }}
                            </div>
                        </header>
                    @endif

                    {{-- El contenido del componente Livewire (ej: dashboard) irá aquí --}}
                    {{ $slot }}
                </main>

            </div> {{-- Fin Flex Container --}}

        </div> {{-- Fin min-h-screen --}}

        @stack('modals')
        @livewireScripts
    </body>
</html>
