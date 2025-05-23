<x-app-layout> {{-- O tu archivo de layout principal --}}
    @section('title', 'Studio – ' . config('app.name'))
    @section('meta_description', 'Explora Studio: tu espacio creativo para contenidos multimedia, tutorials y más.')
    @section('meta_keywords', 'studio, espacio creativo, multimedia, tutorials, arte')

    @section('og_title', 'Studio – ' . config('app.name'))
    @section('og_description', 'Visita Studio y descubre nuestro contenido exclusivo de arte y multimedia.')
    @section('og_image', asset('images/studio-og.jpg'))

    @section('twitter_title', 'Studio – ' . config('app.name'))
    @section('twitter_description', 'Contenido exclusivo en Studio: arte, música, tutoriales y más.')
    @section('twitter_image', asset('images/studio-twitter.jpg'))

    @livewire('dynamic-carousel')
    <div>
        <x-self.section-text title="Reportajes de Haloween"
            subtitle="Este es un texto estático que aparece debajo de la cabecera hero." />

        @livewire('curated-portrait-gallery', [
            'identifier' => 'Halloween_gallery',
        ])


    </div>
     <div>
        <x-self.section-text title="Reportajes de Na vidad"
            subtitle="Este es un texto estático que aparece debajo de la cabecera hero." />

        @livewire('curated-portrait-gallery', [
            'identifier' => 'Navidad_gallery',
        ])
    </div>

    <x-self.superPie></x-self.superPie>
</x-app-layout>
