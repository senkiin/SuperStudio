{{-- resources/views/home.blade.php --}}
<x-app-layout> {{-- O x-guest-layout si no quieres la barra de navegación aquí --}}

    {{-- Incluir el componente Livewire del carrusel --}}
    @livewire('homepage-carousel')

    {{-- Contenido Principal de la Página (Debajo del Carrusel) --}}
    {{-- Este es el punto al que apunta el botón de scroll --}}
    <div id="main-content" class="py-16 lg:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Aquí va el resto del contenido de tu página --}}
            <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">
                Explora Nuestro Trabajo
            </h2>
            <p class="text-center text-gray-600 max-w-2xl mx-auto mb-12">
                Descubre historias visuales capturadas alrededor del mundo. Cada imagen es una ventana a una nueva aventura.
            </p>
            {{-- Podrías incluir aquí una mini-galería, enlaces a álbumes, etc. --}}
             <div class="text-center">
                 <a href="{{ route('albums') }}" class="inline-block px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 transition duration-150 ease-in-out">
                     Ver Álbumes
                 </a>
             </div>
        </div>
    </div>
    {{-- Otro contenido... sección sobre mí, contacto, etc. --}}

</x-app-layout>
