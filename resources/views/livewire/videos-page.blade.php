<div>
    @section('title', $pageTitle)
    @section('metaDescription', $metaDescription)

    {{-- @livewire('header-component') --}}
 @livewire('configurable-page-header', [
        'identifier' => 'bodas_header', // ¡Nuevo identificador!
        'defaultTitle' => 'Bodas de Ensueño',
        'defaultSubtitle' => 'Vuestro día más especial, contado a través de imágenes que emocionan.',
        'defaultImage' => null // O '/images/bodas_default_bg.jpg'
    ])


    {{-- Album Section --}}

    @livewire('configurable-album-section', ['identifier' => 'home_albums'])

    @livewire('video-gallery-manager', [
        'identifier' => 'homepage-tour-videos',
        'defaultTitle' => 'TOUR VIDEOS',
        'defaultDescription' => 'Descubre los destinos más increíbles a través de nuestros videos de tours.'
    ])
    {{-- Placeholder for potential admin modal to edit header --}}
    {{-- You would create a separate Livewire component or Alpine modal for this --}}
    {{-- @livewire('admin.edit-section-modal') --}}

    {{-- Notification Toast (Example with AlpineJS) --}}
    <div x-data="{ show: false, message: '', type: '' }"
         @notify.window="message = $event.detail.message; type = $event.detail.type; show = true; setTimeout(() => show = false, 5000)"
         x-show="show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform translate-y-2"
         :class="{ 'bg-green-500': type === 'success', 'bg-red-500': type === 'error', 'bg-blue-500': type === 'info' }"
         class="fixed bottom-5 right-5 text-white px-6 py-3 rounded-lg shadow-lg z-50 text-sm"
         style="display: none;">
        <p x-text="message"></p>
    </div>
        <x-self.superPie></x-self.superPie>

</div>
