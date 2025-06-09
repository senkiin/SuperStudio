<div>
  <x-slot name="title">
        Fotógrafos de Bodas en Almería | Reportajes Únicos - Fotovalera
    </x-slot>
    <x-slot name="metaDescription">
        Especialistas en fotografía y videografía de bodas en Almería. Capturamos la magia de vuestro día con un estilo natural y emotivo. Más de 23 años creando recuerdos inolvidables. ¡Contacta y cuéntanos tu sueño!
    </x-slot>
    <x-slot name="metaKeywords">
        fotografo bodas almeria, videografo bodas almeria, reportaje boda almeria, fotografia nupcial almeria, video de boda almeria, fotografo profesional bodas almeria, bodas en almeria, Fotovalera bodas
    </x-slot>

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

  <x-self.section-text
        title="Fotografía y Vídeo de Bodas en Almería: Vuestra Historia, Nuestra Pasión
"
        subtitle="En Fotovalera, entendemos que vuestra boda es uno de los capítulos más importantes y emotivos de vuestra vida. Con más de 23 años de experiencia como fotógrafos y videógrafos especializados en bodas en Almería, nos dedicamos a inmortalizar cada sonrisa, cada lágrima de alegría y cada detalle con un enfoque artístico, natural y profundamente personalizado.
                Nuestro compromiso es capturar la esencia de vuestro amor y la atmósfera única de vuestro gran día, creando un reportaje de boda que no solo recordaréis, sino que reviviréis una y otra vez. Dejad que nuestra experiencia y pasión por la fotografía de bodas cuenten vuestra historia de amor de una manera auténtica y memorable en los maravillosos escenarios que Almería ofrece.


" />
        <x-self.superPie></x-self.superPie>

</div>
