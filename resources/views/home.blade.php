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
       <!-- LightWidget WIDGET --><script src="https://cdn.lightwidget.com/widgets/lightwidget.js"></script><iframe src="https://cdn.lightwidget.com/widgets/dc659f761d0e5c979b015e094fe2b18f.html" scrolling="no" allowtransparency="true" class="darkwidget-widget"
         style="width:100%;border:0;overflow:hidden;background-color:black;"></iframe>

        @livewire('team-directory')
    </div>

   <div id="main-content" class="bg-black py-20 sm:py-28">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">

        <p class="text-base font-semibold text-indigo-400">NUESTRA PASIÓN, TU AVENTURA</p>

        <h2 class="mt-4 text-4xl lg:text-5xl font-bold tracking-tight text-white">
            Explora Nuestro Trabajo
        </h2>

        <p class="mt-6 text-lg leading-8 text-gray-300 max-w-2xl mx-auto">
            Descubre historias visuales capturadas alrededor del mundo. Cada imagen es una ventana a una nueva
            aventura y una memoria que perdura.
        </p>

        <div class="mt-10">
            <a href="{{ route('albums') }}"
                class="inline-block rounded-md bg-indigo-500 px-6 py-3 text-base font-semibold text-white shadow-lg
                       hover:bg-indigo-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2
                       focus-visible:outline-indigo-500 transition-all duration-300 transform hover:scale-105">
                Ver Álbumes
            </a>
        </div>
    </div>
</div>
    <x-self.superPie></x-self.superPie>

</x-app-layout>
