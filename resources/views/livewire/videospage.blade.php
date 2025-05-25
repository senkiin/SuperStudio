<div>
    @section('title', $pageTitle)
    @section('metaDescription', $metaDescription)

    {{-- @livewire('header-component') --}}
 @livewire('configurable-page-header', [
        'identifier' => 'Videos_header', // ¡Nuevo identificador!
        'defaultTitle' => 'Videos de Ensueño',
        'defaultSubtitle' => 'Vuestro día más especial, contado a través de imágenes que emocionan.',
        'defaultImage' => null // O '/images/bodas_default_bg.jpg'
    ])


    @livewire('video-gallery-manager', [
        'identifier' => 'videos-tour-videos',
        'defaultTitle' => 'Videografos VIDEOS',
    ])
   

        <x-self.superPie></x-self.superPie>

</div>
