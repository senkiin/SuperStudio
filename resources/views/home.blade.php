{{-- resources/views/home.blade.php --}}
<x-app-layout>
    {{-- SEO Meta Tags --}}
    <x-slot name="title">
        Fotógrafos y Videógrafos en Almería | TuNombreDeEmpresa | +23 Años de Experiencia
    </x-slot>
    <x-slot name="metaDescription">
        En TuNombreDeEmpresa, ofrecemos servicios profesionales de fotografía y videografía en Almería. Con más de 23 años de experiencia, capturamos bodas, eventos y momentos únicos. ¡Inmortaliza tus recuerdos con nosotros!
    </x-slot>
    <x-slot name="metaKeywords">
        fotografos almeria, videografos almeria, fotografia bodas almeria, video bodas almeria, fotografo profesional almeria, estudio fotografia almeria, reportajes fotograficos almeria, videos corporativos almeria
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
       <!-- LightWidget WIDGET --><script src="https://cdn.lightwidget.com/widgets/lightwidget.js"></script><iframe src="https://cdn.lightwidget.com/widgets/dc659f761d0e5c979b015e094fe2b18f.html" scrolling="no" allowtransparency="true" class="lightwidget-widget" style="width:100%;border:0;overflow:hidden;"></iframe>
        @livewire('team-directory')
    </div>
    {{-- Contenido Principal de la Página (Debajo del Carrusel) --}}
    {{-- Este es el punto al que apunta el botón de scroll --}}
    <div id="main-content" class="py-16 lg:py-24 bg-black">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Aquí va el resto del contenido de tu página --}}
            <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">
                Explora Nuestro Trabajo
            </h2>
            <p class="text-center text-gray-600 max-w-2xl mx-auto mb-12">
                Descubre historias visuales capturadas alrededor del mundo. Cada imagen es una ventana a una nueva
                aventura.
            </p>
            {{-- Podrías incluir aquí una mini-galería, enlaces a álbumes, etc. --}}
            <div class="text-center">
                <a href="{{ route('albums') }}"
                    class="inline-block px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 transition duration-150 ease-in-out">
                    Ver Álbumes
                </a>
            </div>
        </div>
    </div>
    <x-self.superPie></x-self.superPie>

</x-app-layout>
