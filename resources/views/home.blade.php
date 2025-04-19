{{-- resources/views/home.blade.php --}}
<x-app-layout> {{-- Usando el layout principal (incluirá tu barra de navegación) --}}

    {{-- Sección Hero Principal --}}
    {{-- Fondo de imagen, altura completa de pantalla, flex para centrar contenido --}}
    <div class="relative h-screen flex items-center justify-center bg-gray-800 text-white overflow-hidden">
        {{-- Imagen de Fondo con léger overlay oscuro --}}
        <div class="absolute inset-0 z-0">
            {{-- La imagen ocupa todo, object-cover para rellenar sin distorsionar --}}
            <img src="{{ $heroImageUrl }}" alt="Imagen de fondo estudio fotográfico" class="w-full h-full object-cover">
            {{-- Overlay oscuro para mejorar contraste del texto --}}
            <div class="absolute inset-0 bg-black opacity-50"></div>
        </div>

        {{-- Contenedor del Texto (relativo para estar sobre el fondo) --}}
        <div class="relative z-10 text-center px-4">
            {{-- Título Principal (Editable por Admin) --}}
            <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-bold tracking-tight mb-4 animate-fade-in-down">
                {{ $heroTitle }}
            </h1>
            {{-- Subtítulo (Editable por Admin) --}}
            <p class="text-lg sm:text-xl md:text-2xl text-gray-200 max-w-3xl mx-auto mb-8 animate-fade-in-up animation-delay-300">
                {{ $heroSubtitle }}
            </p>

            {{-- Botón Opcional (Scroll Down o Llamada a la acción) --}}
            <a href="#contenido-principal" {{-- Apunta a una sección más abajo --}}
               class="inline-block px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 transition duration-150 ease-in-out animate-fade-in-up animation-delay-600">
               Descubrir Más
            </a>
             {{-- O un botón de scroll down como en la captura --}}
             {{-- <button @click="window.scrollTo({ top: document.getElementById('contenido-principal').offsetTop, behavior: 'smooth' })" class="mt-8 text-sm tracking-wider border px-4 py-2 rounded-full hover:bg-white hover:text-black transition">SCROLL DOWN</button> --}}

        </div>

        {{-- Flechas de Navegación (Opcional - Requiere JS/Alpine para Slider) --}}
        {{-- <button class="absolute left-4 top-1/2 -translate-y-1/2 z-20 p-2 bg-black/30 rounded-full hover:bg-black/50 transition"> < </button> --}}
        {{-- <button class="absolute right-4 top-1/2 -translate-y-1/2 z-20 p-2 bg-black/30 rounded-full hover:bg-black/50 transition"> > </button> --}}

         {{-- ENLACES DE ADMINISTRACIÓN (Visibles solo para admins) --}}
         @auth {{-- Primero verifica si está logueado --}}
             @if(Auth::user()->role === 'admin') {{-- Luego verifica si es admin --}}
                 <div class="absolute bottom-4 right-4 z-20 bg-white/80 backdrop-blur-sm text-black p-3 rounded-lg shadow text-xs">
                     <p class="font-semibold mb-1">Opciones de Admin:</p>
                     {{-- Enlace a la página (a crear) donde se edita el contenido de esta portada --}}
                     <a href="{{ route('admin.dashboard') }}#edit-homepage" {{-- Ejemplo de ancla o ruta específica --}}
                        class="text-blue-600 hover:underline block">Editar Contenido Portada</a>
                     {{-- Enlace al panel de admin general --}}
                     <a href="{{ route('admin.dashboard') }}"
                        class="text-blue-600 hover:underline block mt-1">Ir al Panel Admin</a>
                 </div>
             @endif
         @endauth

    </div>

    {{-- Contenido Principal de la Página (Debajo del Hero) --}}
    <div id="contenido-principal" class="py-12 lg:py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Aquí iría el resto del contenido de tu página de inicio --}}
            {{-- Por ejemplo: Sección "Sobre Nosotros", últimos trabajos, servicios, etc. --}}
            <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">Nuestros Servicios</h2>
            {{-- ... (Más contenido) ... --}}
        </div>
    </div>

</x-app-layout>
