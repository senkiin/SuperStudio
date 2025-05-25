<div>
    @section('title', $pageTitle)
    @section('metaDescription', $metaDescription)

    {{-- @livewire('header-component') --}}
 @livewire('configurable-page-header', [
        'identifier' => 'videos_header', // ¡Nuevo identificador!
    ])

    @livewire('video-gallery-manager', [
        'identifier' => 'homepage-tour-videos',
        'defaultTitle' => 'TOUR VIDEOS',
        'defaultDescription' => 'Descubre los destinos más increíbles a través de nuestros videos de tours.'
    ])
    {{-- Placeholder for potential admin modal to edit header --}}
    {{-- You would create a separate Livewire component or Alpine modal for this --}}
    {{-- @livewire('admin.edit-section-modal') --}}


    <x-self.superPie></x-self.superPie>

</div>
