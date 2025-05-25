{{-- resources/views/livewire/video-gallery-manager.blade.php --}}
<div class="py-8 md:py-12 bg-black text-white">
  <div class="container mx-auto px-4 sm:px-6 lg:px-8">

    {{-- Flash Messages --}}
    @if (session()->has('message'))
      <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3500)"
           x-transition:leave="transition ease-in duration-300"
           x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
           class="mb-6 p-4 text-sm text-green-700 bg-green-100 rounded-lg border border-green-300 shadow-sm"
           role="alert">
        {{ session('message') }}
      </div>
    @endif

    @if (session()->has('error'))
      <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
           x-transition:leave="transition ease-in duration-300"
           x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
           class="mb-6 p-4 text-sm text-red-700 bg-red-100 rounded-lg border border-red-300 shadow-sm"
           role="alert">
        {{ session('error') }}
      </div>
    @endif

    {{-- Section Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 md:mb-10">
      <div class="mb-4 sm:mb-0">
        @if($this->videoSection && $this->videoSection->title)
          <h2
            x-data
            x-init="$el.classList.add('zoom-in')"
            data-aos
            class="zoom-in text-3xl sm:text-4xl font-extrabold tracking-tight uppercase bg-black px-2 inline-block"
            style="font-family: 'Arial Black', sans-serif;"
          >
            {{ $this->videoSection->title }}
          </h2>
        @endif

        @if($this->videoSection && $this->videoSection->description)
          <p
            x-data
            x-init="$el.classList.add('fade-bottom')"
            data-aos
            class="fade-bottom mt-2 text-base sm:text-lg text-gray-400 max-w-2xl bg-black px-2 inline-block"
          >
            {{ $this->videoSection->description }}
          </p>
        @endif
      </div>

      @if($isAdmin)
        <button
          x-data
          x-init="$el.classList.add('zoom-in')"
          data-aos
          wire:click="openManageSectionModal"
          class="zoom-in px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold rounded-md shadow-md transition-colors"
        >
          Administrar Sección
        </button>
      @endif
    </div>

    {{-- Video Grid --}}
    @if($videos->isNotEmpty())
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 md:gap-8" wire:sortable="updateVideoOrder">
        @foreach($videos as $video)
          @php
            $animClasses = ['fade-left','fade-right','fade-bottom'];
            $anim = $animClasses[$loop->index % count($animClasses)];
          @endphp

          <div
            wire:sortable.item="{{ $video->id }}"
            wire:key="video-entry-{{ $video->id }}"
            x-data
            x-init="$el.classList.add('{{ $anim }}')"
            data-aos
            class="{{ $anim }} bg-gray-800 rounded-lg shadow-xl overflow-hidden group flex flex-col"
          >
            <div class="aspect-video relative">
              @if($video->video_source_type === 'vimeo' && $video->source_identifier)
                <iframe
                  src="https://player.vimeo.com/video/{{ $video->source_identifier }}?title=0&byline=0&portrait=0&autoplay=0&muted=1"
                  class="w-full h-full" frameborder="0"
                  allow="autoplay; fullscreen; picture-in-picture" allowfullscreen>
                </iframe>

              @elseif($video->video_source_type === 's3' && $video->source_identifier)
                @if(Storage::disk($this->s3Disk)->exists($video->source_identifier))
                  <video controls preload="metadata" class="w-full h-full object-cover">
                    <source src="{{ Storage::disk($this->s3Disk)->url($video->source_identifier) }}" type="video/mp4">
                    Tu navegador no soporta la etiqueta de video.
                  </video>
                @else
                  <div class="w-full h-full bg-gray-700 flex items-center justify-center text-gray-400 italic">
                    Video no encontrado en S3
                  </div>
                @endif

              @elseif($video->video_source_type === 'youtube' && $video->source_identifier)
                <iframe
                  class="w-full h-full"
                  src="https://www.youtube.com/embed/{{ $video->source_identifier }}"
                  title="YouTube video player"
                  frameborder="0"
                  allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                  allowfullscreen>
                </iframe>

              @else
                <div class="w-full h-full bg-gray-700 flex items-center justify-center text-gray-400 italic">
                  Fuente de video no válida o no configurada.
                </div>
              @endif
            </div>

            <div class="p-4 md:p-5 flex-grow flex flex-col justify-between bg-black">
              <div>
                <h3
                  x-data
                  data-aos
                  class="text-lg md:text-xl font-semibold text-gray-100 mb-1.5 truncate bg-black px-1"
                  title="{{ $video->entry_title }}"
                >
                  {{ $video->entry_title }}
                </h3>

                @if($video->entry_description)
                  <p
                    x-data
                    data-aos
                    class=" text-sm text-gray-400 line-clamp-2 bg-black px-1"
                    title="{{ $video->entry_description }}"
                  >
                    {{ $video->entry_description }}
                  </p>
                @endif
              </div>

              @if($isAdmin)
                <div class="mt-4 pt-3 border-t border-gray-700/50 flex items-center justify-end space-x-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                  <button wire:sortable.handle title="Reordenar"
                          class="p-1.5 text-gray-400 hover:text-white cursor-grab">
                    {{-- Icono --}}
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16m-7 6h7" />
                    </svg>
                  </button>
                  <button wire:click="openAddEditVideoModal({{ $video->id }})" title="Editar Video"
                          class="p-1.5 text-blue-400 hover:text-blue-300">
                    {{-- Icono --}}
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0
                               113.536 3.536L6.5 21H3v-3.5L14.732 3.732z" />
                    </svg>
                  </button>
                  <button wire:click="deleteVideoEntry({{ $video->id }})" wire:confirm="¿Estás seguro de eliminar este video?" title="Eliminar Video"
                          class="p-1.5 text-red-400 hover:text-red-300">
                    {{-- Icono --}}
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0
                               0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5
                               4v6m4-6v6m1-10V4a1 1 0
                               00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                </div>
              @endif
            </div>
          </div>
        @endforeach
      </div>
    @else
      <div class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
             aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round"
                d="m15.75 10.5l4.72-4.72a.75.75 0
                   011.28.53v11.38a.75.75 0
                   01-1.28.53l-4.72-4.72M4.5
                   18.75h9a2.25 2.25 0
                   002.25-2.25v-9a2.25 2.25 0
                   00-2.25-2.25h-9A2.25 2.25 0
                   002.25 7.5v9a2.25 2.25 0
                   002.25 2.25z" />
        </svg>
        <h3 class="mt-2 text-lg font-semibold text-white">No hay videos en esta sección</h3>
        @if($isAdmin)
          <p class="mt-1 text-sm text-gray-400">Comienza añadiendo videos en el panel de administración.</p>
        @else
          <p class="mt-1 text-sm text-gray-400">Vuelve más tarde para ver el contenido.</p>
        @endif
      </div>
    @endif

    {{-- Management Modal --}}
    @if($isAdmin)
    <x-dialog-modal wire:model.live="showManageSectionModal" maxWidth="3xl">
        <x-slot name="title">
            Administrar Sección de Videos: <span class="font-normal italic">{{ $identifier }}</span>
        </x-slot>

        <x-slot name="content">
            <div class="space-y-6 text-gray-200">
                {{-- Edit Section Title and Description --}}
                <div class="p-4 border border-gray-700 rounded-md bg-gray-800/50 shadow-sm">
                    <h4 class="text-lg font-semibold text-gray-100 mb-3">Detalles de la Sección</h4>
                    <div class="space-y-4">
                        <div>
                            <label for="sectionTitleModal" class="block text-sm font-medium text-gray-300 mb-1">Título de la Sección:</label>
                            <input type="text" wire:model.defer="sectionTitle" id="sectionTitleModal"
                                   class="w-full p-2.5 rounded bg-gray-700 text-white border border-gray-600 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                            @error('sectionTitle') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="sectionDescriptionModal" class="block text-sm font-medium text-gray-300 mb-1">Descripción de la Sección (opcional):</label>
                            <textarea wire:model.defer="sectionDescription" id="sectionDescriptionModal" rows="3"
                                      class="w-full p-2.5 rounded bg-gray-700 text-white border border-gray-600 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                            @error('sectionDescription') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div class="text-right">
                            <x-secondary-button wire:click="saveSectionDetails" wire:loading.attr="disabled" class="text-xs px-3 py-1.5">
                                Guardar Detalles
                            </x-secondary-button>
                        </div>
                    </div>
                </div>

                {{-- Add New Video Button --}}
                <div class="text-right">
                    <x-button wire:click="openAddEditVideoModal()" class="text-sm">
                        Añadir Nuevo Video
                    </x-button>
                </div>

                 {{-- List of existing videos (simplified for management modal) --}}
                 @if($videos->isNotEmpty())
                 <div class="mt-4 max-h-72 overflow-y-auto space-y-2">
                     <h4 class="text-md font-semibold text-gray-300 mb-2">Videos Actuales:</h4>
                     @foreach($videos as $video)
                     <div class="flex items-center justify-between p-2.5 bg-gray-700/60 rounded-md hover:bg-gray-700/80 transition-colors">
                         <div class="flex items-center space-x-3 min-w-0">
                             <span class="text-gray-400 text-xs w-6 text-center">{{ $loop->iteration }}.</span>
                             <span class="text-sm font-medium text-gray-100 truncate" title="{{ $video->entry_title }}">{{ Illuminate\Support\Str::limit($video->entry_title, 40) }}</span>
                             <span class="text-xs text-gray-500">({{ Illuminate\Support\Str::upper($video->video_source_type) }})</span>
                         </div>
                         <div class="flex space-x-2 flex-shrink-0">
                             <button wire:click="openAddEditVideoModal({{ $video->id }})" title="Editar" class="p-1 text-blue-400 hover:text-blue-300">
                                 <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21H3v-3.5L14.732 3.732z"></path></svg>
                             </button>
                             <button wire:click="deleteVideoEntry({{ $video->id }})" wire:confirm="¿Seguro que quieres eliminar este video de la sección?" title="Eliminar" class="p-1 text-red-400 hover:text-red-300">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                             </button>
                         </div>
                     </div>
                     @endforeach
                 </div>
                 @else
                 <p class="text-sm text-gray-500 text-center py-3">No hay videos en esta sección aún.</p>
                 @endif
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showManageSectionModal', false)" wire:loading.attr="disabled">
                Cerrar
            </x-secondary-button>
        </x-slot>
    </x-dialog-modal>
    @endif

    {{-- Add/Edit Video Entry Modal --}}
    @if($isAdmin)
    <x-dialog-modal wire:model.live="showAddEditVideoModal" maxWidth="xl">
        <x-slot name="title">
            {{ $editingVideoEntryId ? 'Editar Video' : 'Añadir Nuevo Video' }}
        </x-slot>

        <x-slot name="content">
            <form wire:submit.prevent="saveVideoEntry" class="space-y-5 text-gray-200">
                <div>
                    <label for="entry_title_modal" class="block text-sm font-medium text-gray-300 mb-1">Título del Video en la Tarjeta:</label>
                    <input type="text" wire:model.defer="entry_title" id="entry_title_modal" placeholder="Ej: Viaje a Patagonia"
                           class="w-full p-2.5 rounded bg-gray-700 text-white border border-gray-600 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                    @error('entry_title') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="entry_description_modal" class="block text-sm font-medium text-gray-300 mb-1">Descripción Breve (opcional):</label>
                    <textarea wire:model.defer="entry_description" id="entry_description_modal" rows="3" placeholder="Pequeña descripción o nombre del canal"
                              class="w-full p-2.5 rounded bg-gray-700 text-white border border-gray-600 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                    @error('entry_description') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="video_source_type_modal" class="block text-sm font-medium text-gray-300 mb-1">Fuente del Video:</label>
                    <select wire:model.live="video_source_type" id="video_source_type_modal"
                            class="w-full p-2.5 rounded bg-gray-700 text-white border border-gray-600 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="vimeo">Vimeo ID</option>
                        <option value="youtube">YouTube ID</option>
                        <option value="s3">Subir Archivo a S3</option>
                    </select>
                    @error('video_source_type') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                @if($video_source_type === 's3')
                    <div>
                        <label for="newVideoFileModal" class="block text-sm font-medium text-gray-300 mb-1">
                            {{ $editingVideoEntryId && $currentVideoPath ? 'Reemplazar Archivo S3 (opcional):' : 'Archivo de Video (MP4, MOV, etc.):' }}
                        </label>
                        <input type="file" wire:model="newVideoFile" id="newVideoFileModal-{{ $this->getId() }}"
                               class="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-500 cursor-pointer">
                        <div wire:loading wire:target="newVideoFile" class="text-indigo-400 text-xs mt-1">Subiendo...</div>
                        @error('newVideoFile') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror

                        {{-- Previsualización textual del archivo para S3 --}}
                        @if($newVideoFile && method_exists($newVideoFile, 'getClientOriginalName'))
                            <p class="text-xs text-gray-400 mt-2">Archivo seleccionado: <span class="font-medium">{{ $newVideoFile->getClientOriginalName() }}</span> ({{ round($newVideoFile->getSize() / 1024 / 1024, 2) }} MB)</p>
                        @elseif($editingVideoEntryId && $currentVideoPath)
                             <p class="text-xs text-gray-400 mt-2">Archivo actual en S3: {{ basename($currentVideoPath) }}</p>
                        @endif
                        {{-- Fin Previsualización textual --}}

                         @error('source_identifier') {{-- Error para S3 si no se sube nada al crear --}}
                            @if ($video_source_type === 's3' && !$editingVideoEntryId && !$newVideoFile)
                                <span class="text-red-400 text-xs mt-1">Debes seleccionar un archivo de video para S3.</span>
                            @endif
                        @enderror
                    </div>
                @else
                    <div>
                        <label for="source_identifier_modal" class="block text-sm font-medium text-gray-300 mb-1">
                            {{ $video_source_type === 'vimeo' ? 'Vimeo Video ID:' : ($video_source_type === 'youtube' ? 'YouTube Video ID:' : 'Identificador:') }}
                        </label>
                        <input type="text" wire:model.defer="source_identifier" id="source_identifier_modal"
                               placeholder="{{ $video_source_type === 'vimeo' ? 'Ej: 123456789' : ($video_source_type === 'youtube' ? 'Ej: dQw4w9WgXcQ' : '') }}"
                               class="w-full p-2.5 rounded bg-gray-700 text-white border border-gray-600 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('source_identifier') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                @endif
                {{-- Futuro: Input para thumbnail_url si es necesario para S3/YouTube --}}
            </form>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="resetVideoForm(); $set('showAddEditVideoModal', false)" wire:loading.attr="disabled">
                Cancelar
            </x-secondary-button>
            <x-button class="ml-3" wire:click="saveVideoEntry" wire:loading.attr="disabled" wire:target="saveVideoEntry, newVideoFile">
                {{ $editingVideoEntryId ? 'Actualizar Video' : 'Guardar Video' }}
            </x-button>
        </x-slot>
    </x-dialog-modal>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:init', () => {
        const initSortableVideos = (el) => {
            if (!el) return;
            if (el.sortableVideoInstance) el.sortableVideoInstance.destroy();

            el.sortableVideoInstance = Sortable.create(el, {
                handle: '[wire\\:sortable\\.handle]', // Elemento para arrastrar
                animation: 150, // Duración de la animación
                ghostClass: 'bg-gray-700 opacity-60', // Estilo del placeholder mientras se arrastra
                onEnd: function(evt) {
                    let items = Array.from(evt.to.children).map((item, index) => {
                        // Asegurarse de que el elemento es un item sortable y tiene el atributo
                        if (item.hasAttribute('wire:sortable.item')) {
                            return {
                                order: index, // SortableJS es 0-indexed.
                                value: item.getAttribute('wire:sortable.item')
                            };
                        }
                        return null; // Ignorar elementos que no sean items
                    }).filter(item => item !== null); // Eliminar nulos de la lista

                    if (items.length > 0) {
                        // Encontrar el ID del componente Livewire más cercano
                        let componentId = el.closest('[wire\\:id]')?.getAttribute('wire:id');
                        if (componentId) {
                           Livewire.find(componentId).call('updateVideoOrder', items);
                        } else {
                           console.warn('Sortable: Could not find Livewire component ID.');
                        }
                    }
                }
            });
        };

        // Hook para cuando un elemento de Livewire es actualizado.
        Livewire.hook('element.updated', (el, component) => {
            // Si el elemento actualizado es el contenedor sortable de esta instancia
            if (el.hasAttribute('wire:sortable') && el.getAttribute('wire:sortable') === 'updateVideoOrder') {
                initSortableVideos(el);
            }
        });

        // Función para inicializar Sortable en el contenedor de videos principal al cargar
        const initializeMainSortable = () => {
            // Usamos un ID más específico para el contenedor de la galería principal si es necesario,
            // o un data-attribute. Por ahora, el selector general de wire:sortable debería funcionar
            // si es el único en la página con ese método.
            let mainVideoListElement = document.querySelector('div[wire\\:id="{{ $this->getId() }}"] [wire\\:sortable="updateVideoOrder"]');
            if (mainVideoListElement) {
                initSortableVideos(mainVideoListElement);
            }
        };

        initializeMainSortable(); // Intentar al cargar la página

        // Observador para cuando el modal de gestión SE ABRE y su contenido (lista de videos) se renderiza
        // Esto es para reinicializar SortableJS en la lista DENTRO del modal
        const manageModalObserver = new MutationObserver((mutationsList, observer) => {
            for(const mutation of mutationsList) {
                // Observar cambios en el estilo (cuando el modal se hace visible) o si se añaden nodos
                if (mutation.type === 'attributes' && mutation.attributeName === 'style') {
                    const manageModalDialog = document.querySelector('[wire\\:model\\.live="showManageSectionModal"]');
                    // Comprobar si el modal está ahora visible
                    if (manageModalDialog && manageModalDialog.style.display !== 'none') {
                        let videoListInModal = manageModalDialog.querySelector('[wire\\:id="{{ $this->getId() }}"] [wire\\:sortable="updateVideoOrder"]'); // Asumiendo que la lista dentro del modal también usa este sortable
                        if (videoListInModal) {
                            // Esta inicialización puede ser redundante si la lista principal ya está inicializada
                            // y es la misma. Si la lista dentro del modal es una diferente, entonces sí es necesaria.
                            // Por ahora, asumimos que la lista es la misma y se gestiona a través del componente.
                            // initSortableVideos(videoListInModal);
                        }
                    }
                } else if (mutation.type === 'childList') {
                     mutation.addedNodes.forEach(node => {
                        // Si se añade el contenedor sortable o uno de sus padres
                        if (node.nodeType === 1 && node.hasAttribute('wire:sortable') && node.getAttribute('wire:sortable') === 'updateVideoOrder') {
                             initSortableVideos(node);
                        } else if (node.nodeType === 1) { // Si se añade un nodo que CONTIENE el sortable
                            let sortableChild = node.querySelector('[wire\\:id="{{ $this->getId() }}"] [wire\\:sortable="updateVideoOrder"]');
                            if (sortableChild) {
                                initSortableVideos(sortableChild);
                            }
                        }
                     });
                }
            }
        });

        // Observar cambios en el DOM, apuntando al cuerpo o a un contenedor más específico
        // que englobe el componente Livewire y sus modales.
        const targetNodeForObserver = document.body;
        const configObserver = { attributes: true, childList: true, subtree: true };
        manageModalObserver.observe(targetNodeForObserver, configObserver);

    });
</script>
@endpush
