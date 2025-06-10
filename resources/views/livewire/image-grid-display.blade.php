<div>
    <section class="py-12 md:py-16 bg-black text-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">

            @if ($gridTitle)
                <h2 x-data x-intersect="$el.classList.add('aos-animate')"
                    class="text-3xl sm:text-4xl font-bold tracking-tight text-center mb-6 fade-up">
                    {{ $gridTitle }}
                </h2>
            @endif
            @if ($gridDescription)
                <p x-data x-intersect="$el.classList.add('aos-animate')"
                    class="text-gray-400 text-center max-w-3xl mx-auto mb-10 md:mb-12 fade-up">
                    {{ $gridDescription }}
                </p>
            @endif

            @if ($photosInGallery->isNotEmpty())
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-1 sm:gap-2"
                    {{-- Para SortableJS (necesitarás inicializarlo y adaptar el método updateGridPhotoOrder) --}}
                    {{-- wire:sortable="updateGridPhotoOrder" --}}
                    {{-- wire:sortable.options="{animation: 150, ghostClass: 'opacity-30'}" --}}
                >
                    @foreach ($photosInGallery as $photo)
                        <div class="aspect-square relative group bg-gray-800 rounded-md overflow-hidden"
                             wire:key="grid-photo-{{ $imageGrid->id }}-{{ $photo->id }}"
                             {{-- wire:sortable.item="{{ $photo->id }}" --}}>
                            <img src="{{ Storage::url($photo->thumbnail_path ?: $photo->file_path) }}"
                                 alt="{{ $photo->filename ?? 'Foto de la galería' }}"
                                 loading="lazy"
                                 class="w-full h-full object-cover transition-transform duration-300 ease-in-out group-hover:scale-105">
                            @if ($isAdmin)
                                {{-- <button title="Arrastrar para ordenar" wire:sortable.handle class="absolute top-1 left-1 p-1 bg-black/50 rounded-full cursor-move text-white text-xs z-10">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                                </button> --}}
                                <button wire:click="removeFromGrid({{ $photo->id }})"
                                        wire:confirm="¿Estás seguro de quitar esta foto de la galería?"
                                        title="Quitar foto de la galería"
                                        class="absolute top-1 right-1 p-1.5 bg-red-600 hover:bg-red-700 text-white rounded-full shadow-md text-xs opacity-0 group-hover:opacity-100 transition-opacity z-10">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                    </svg>
                    <h3 class="mt-2 text-lg font-semibold text-white">Galería Vacía</h3>
                    @if($isAdmin)
                        <p class="mt-1 text-sm text-gray-400">Parece que no hay fotos aquí. <button wire:click="openManagePhotosModal" class="text-indigo-400 hover:underline">¡Añade algunas!</button></p>
                    @else
                        <p class="mt-1 text-sm text-gray-400">Pronto habrá contenido visual increíble aquí. ¡Vuelve a visitarnos!</p>
                    @endif
                </div>
            @endif

            @if ($isAdmin)
                <div class="text-center mt-10">
                    <button wire:click="openManagePhotosModal"
                        class="px-6 py-3 bg-indigo-700 hover:bg-indigo-600 text-white font-semibold rounded-lg shadow-md transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-black">
                        Administrar Galería: {{ $gridTitle ?: $identifier }}
                    </button>
                </div>
            @endif
        </div>
    </section>

    {{-- Modal de Administración --}}
    @if ($isAdmin)
        <x-dialog-modal wire:model.live="showManagePhotosModal" maxWidth="4xl"> {{-- O 3xl / 5xl según necesidad --}}
            <x-slot name="title">
                Administrar Fotos: <span class="font-normal italic">{{ $imageGrid->title ?: $identifier }}</span>
            </x-slot>

            <x-slot name="content">
                @if (session()->has('modal_message'))
                    <div class="mb-4 p-3 bg-green-600 text-white rounded-md text-sm">{{ session('modal_message') }}</div>
                @endif
                @if (session()->has('modal_error'))
                    <div class="mb-4 p-3 bg-red-600 text-white rounded-md text-sm">{{ session('modal_error') }}</div>
                @endif

                <div class="space-y-6 text-gray-200">
                    {{-- Editar Título y Descripción de la Galería --}}
                    <div class="p-4 border border-gray-700 rounded-md bg-gray-800/30">
                        <h4 class="text-lg font-semibold text-gray-100 mb-3">Configuración de la Galería</h4>
                        <div class="space-y-4">
                            <div>
                                <label for="editableGridTitle" class="block text-sm font-medium mb-1">Título de la Galería:</label>
                                <input type="text" wire:model.defer="editableGridTitle" id="editableGridTitle" placeholder="Ej: Retratos Destacados"
                                       class="w-full p-2.5 rounded bg-gray-700 text-white border border-gray-600 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                @error('editableGridTitle') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="editableGridDescription" class="block text-sm font-medium mb-1">Descripción (opcional):</label>
                                <textarea wire:model.defer="editableGridDescription" id="editableGridDescription" rows="3" placeholder="Una breve descripción de esta galería..."
                                          class="w-full p-2.5 rounded bg-gray-700 text-white border border-gray-600 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                                @error('editableGridDescription') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div class="text-right">
                                <x-secondary-button wire:click="saveGalleryMeta" wire:loading.attr="disabled" wire:target="saveGalleryMeta" class="text-xs px-4 py-2">
                                    Guardar Configuración
                                </x-secondary-button>
                            </div>
                        </div>
                    </div>

                    {{-- 1. Subir Nuevas Fotos --}}
                    <div class="p-4 border border-gray-700 rounded-md bg-gray-800/30">
                        <h4 class="text-lg font-semibold text-gray-100 mb-3">1. Subir Nuevas Fotos</h4>
                        <input type="file" wire:model="newPhotosToUpload" multiple id="upload-photos-{{ $this->getId() }}"
                            class="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-500 cursor-pointer mb-2">
                        <div wire:loading wire:target="newPhotosToUpload" class="text-indigo-400 text-xs mt-1">Cargando imágenes...</div>
                        @error('newPhotosToUpload.*') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror

                        @if ($newPhotosToUpload)
                        <div class="mt-3 grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2">
                            @foreach ($newPhotosToUpload as $imgPrev)
                                @if(method_exists($imgPrev, 'temporaryUrl'))
                                <img src="{{ $imgPrev->temporaryUrl() }}" class="h-20 w-20 object-cover rounded-md shadow">
                                @endif
                            @endforeach
                        </div>
                        <div class="mt-3 text-right">
                            <x-button wire:click="uploadAndAttachPhotos" wire:loading.attr="disabled" wire:target="newPhotosToUpload, uploadAndAttachPhotos" class="text-sm px-4 py-2">
                                Añadir Fotos Subidas
                            </x-button>
                        </div>
                        @endif
                    </div>

                    {{-- Pestañas para seleccionar existentes --}}
                    <div x-data="{ currentTab: 'fromAlbums' }" class="border border-gray-700 rounded-md bg-gray-800/30">
                        <div class="flex border-b border-gray-600">
                            @if($allAlbumsForModal && $allAlbumsForModal->isNotEmpty())
                            <button @click="currentTab = 'fromAlbums'" :class="{ 'bg-indigo-600 text-white': currentTab === 'fromAlbums', 'bg-gray-700 hover:bg-gray-600 text-gray-300': currentTab !== 'fromAlbums' }" class="flex-1 px-3 py-2.5 text-sm font-medium focus:outline-none rounded-tl-md">Desde Álbumes</button>
                            @endif
                            @if($modalLikedPhotos && $modalLikedPhotos->isNotEmpty())
                            <button @click="currentTab = 'fromLiked'" :class="{ 'bg-indigo-600 text-white': currentTab === 'fromLiked', 'bg-gray-700 hover:bg-gray-600 text-gray-300': currentTab !== 'fromLiked' }" class="flex-1 px-3 py-2.5 text-sm font-medium focus:outline-none {{ ($allAlbumsForModal && $allAlbumsForModal->isNotEmpty()) ? '' : 'rounded-tl-md' }}">Mis Favoritas</button>
                            @endif
                            <button @click="currentTab = 'fromSearch'" :class="{ 'bg-indigo-600 text-white': currentTab === 'fromSearch', 'bg-gray-700 hover:bg-gray-600 text-gray-300': currentTab !== 'fromSearch' }" class="flex-1 px-3 py-2.5 text-sm font-medium focus:outline-none rounded-tr-md {{ (!$allAlbumsForModal || $allAlbumsForModal->isEmpty()) && (!$modalLikedPhotos || $modalLikedPhotos->isEmpty()) ? 'rounded-tl-md' : '' }}">Buscar Foto</button>
                        </div>

                        <div class="p-4">
                            {{-- 2. Añadir desde Álbumes --}}
                             @if($allAlbumsForModal && $allAlbumsForModal->isNotEmpty())
                            <div x-show="currentTab === 'fromAlbums'" x-transition.opacity.duration.300ms class="space-y-3">
                                <h4 class="text-lg font-semibold text-gray-100">2. Añadir desde Álbum</h4>
                                <select wire:model.live="modalSelectedAlbumId" class="form-select bg-gray-700 border-gray-600 text-gray-300 w-full rounded-md shadow-sm text-sm">
                                    <option value="">-- Selecciona un álbum --</option>
                                    @foreach ($allAlbumsForModal as $album) <option value="{{ $album->id }}">{{ $album->name }} ({{ $album->photos_count }} fotos)</option> @endforeach
                                </select>
                                @if ($modalPhotosFromAlbum->isNotEmpty())
                                    <div class="max-h-60 overflow-y-auto grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2 p-2 bg-gray-700/50 rounded-md border border-gray-600">
                                        @foreach ($modalPhotosFromAlbum as $photo)
                                            <label for="modal_album_photo_{{ $photo->id }}" class="cursor-pointer relative aspect-square">
                                                <input type="checkbox" id="modal_album_photo_{{ $photo->id }}" value="{{ $photo->id }}" wire:model.defer="modalSelectedPhotosFromAlbum" class="sr-only peer">
                                                <img src="{{ Storage::url($photo->thumbnail_path ?: $photo->file_path) }}" alt="{{ $photo->filename }}" class="w-full h-full object-cover rounded-md transition-all peer-checked:ring-2 peer-checked:ring-offset-2 peer-checked:ring-offset-gray-800 peer-checked:ring-indigo-500 peer-checked:opacity-60">
                                                <div class="absolute inset-0 bg-indigo-700 opacity-0 peer-checked:opacity-40 rounded-md transition-opacity flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg></div>
                                            </label>
                                        @endforeach
                                    </div>
                                @elseif($modalSelectedAlbumId) <p class="text-sm text-gray-500 italic">Este álbum no contiene fotos.</p>
                                @endif
                            </div>
                            @endif

                            {{-- 3. Añadir Fotos con "Me Gusta" --}}
                            @if($modalLikedPhotos && $modalLikedPhotos->isNotEmpty())
                            <div x-show="currentTab === 'fromLiked'" x-transition.opacity.duration.300ms class="space-y-3">
                                <h4 class="text-lg font-semibold text-gray-100">3. Añadir desde "Mis Favoritas"</h4>
                                <div class="max-h-60 overflow-y-auto grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2 p-2 bg-gray-700/50 rounded-md border border-gray-600">
                                    @foreach ($modalLikedPhotos as $photo)
                                        <label for="modal_liked_photo_{{ $photo->id }}" class="cursor-pointer relative aspect-square">
                                            <input type="checkbox" id="modal_liked_photo_{{ $photo->id }}" value="{{ $photo->id }}" wire:model.defer="modalSelectedLikedPhotos" class="sr-only peer">
                                            <img src="{{ Storage::url($photo->thumbnail_path ?: $photo->file_path) }}" alt="{{ $photo->filename }}" class="w-full h-full object-cover rounded-md transition-all peer-checked:ring-2 peer-checked:ring-offset-2 peer-checked:ring-offset-gray-800 peer-checked:ring-indigo-500 peer-checked:opacity-60">
                                            <div class="absolute inset-0 bg-indigo-700 opacity-0 peer-checked:opacity-40 rounded-md transition-opacity flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg></div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            {{-- 4. Buscar y Añadir Fotos Existentes --}}
                            <div x-show="currentTab === 'fromSearch'" x-transition.opacity.duration.300ms class="space-y-3">
                                <h4 class="text-lg font-semibold text-gray-100">4. Buscar Foto Existente</h4>
                                <div class="flex">
                                    <input type="search" wire:model.lazy="modalPhotoSearchQuery" placeholder="Buscar por nombre de archivo..." class="flex-grow p-2.5 text-sm rounded-l-md bg-gray-700 text-white border border-gray-600 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                    <button wire:click="searchModalExistingPhotos" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium rounded-r-md">Buscar</button>
                                </div>
                                <div wire:loading wire:target="searchModalExistingPhotos, modalPhotoSearchQuery" class="text-indigo-400 text-xs mt-1">Buscando...</div>

                                @if ($currentModalSearchedPhotos && $currentModalSearchedPhotos->isNotEmpty())
                                    <div class="max-h-60 overflow-y-auto grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2 p-2 mt-2 bg-gray-700/50 rounded-md border border-gray-600">
                                        @foreach ($currentModalSearchedPhotos as $photo)
                                            <label for="modal_searched_photo_{{ $photo->id }}" class="cursor-pointer relative aspect-square">
                                                <input type="checkbox" id="modal_searched_photo_{{ $photo->id }}" value="{{ $photo->id }}" wire:model.defer="modalSelectedExistingPhotos" class="sr-only peer">
                                                <img src="{{ Storage::url($photo->thumbnail_path ?: $photo->file_path) }}" alt="{{ $photo->filename }}" class="w-full h-full object-cover rounded-md transition-all peer-checked:ring-2 peer-checked:ring-offset-2 peer-checked:ring-offset-gray-800 peer-checked:ring-indigo-500 peer-checked:opacity-60">
                                                <div class="absolute inset-0 bg-indigo-700 opacity-0 peer-checked:opacity-40 rounded-md transition-opacity flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg></div>
                                            </label>
                                        @endforeach
                                    </div>
                                    <div class="mt-2 text-xs">{{ $currentModalSearchedPhotos->links('vendor.livewire.tailwind-small') }}</div>
                                @elseif(strlen($modalPhotoSearchQuery) >= 3)
                                    <p class="text-sm text-gray-500 italic mt-2">No se encontraron fotos con "{{ $modalPhotoSearchQuery }}".</p>
                                @endif
                            </div>
                        </div>
                         {{-- Botón unificado para añadir todas las selecciones de pestañas --}}
                        <div class="mt-6 text-right border-t border-gray-600 pt-4">
                             <x-button wire:click="attachSelectedExistingPhotos" wire:loading.attr="disabled" wire:target="attachSelectedExistingPhotos" class="text-sm px-5 py-2.5">
                                Añadir Seleccionadas (de Álbum/Favoritas/Búsqueda)
                            </x-button>
                        </div>
                    </div>
                </div>
            </x-slot>

            <x-slot name="footer">
                <x-secondary-button wire:click="closeManagePhotosModal" wire:loading.attr="disabled">
                    Cerrar Panel de Gestión
                </x-secondary-button>
            </x-slot>
        </x-dialog-modal>
    @endif

    {{-- Mensajes Flash generales de la página (fuera del modal) --}}
    @if (session()->has('message'))
        <div x-data="{ showFlash: true }" x-init="setTimeout(() => showFlash = false, 3500)" x-show="showFlash"
            x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed bottom-5 right-5 bg-green-600 text-white py-2.5 px-5 rounded-lg shadow-xl z-[101] text-sm">
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div x-data="{ showFlash: true }" x-init="setTimeout(() => showFlash = false, 3500)" x-show="showFlash"
             x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed bottom-5 right-5 bg-red-600 text-white py-2.5 px-5 rounded-lg shadow-xl z-[101] text-sm">
            {{ session('error') }}
        </div>
    @endif
</div>

@push('scripts')
<script>
    // Código para SortableJS si decides implementarlo para reordenar las fotos
    // document.addEventListener('livewire:init', () => {
    //     Livewire.hook('element.updated', (el, component) => {
    //         if (component.name === 'image-grid-display' && el.hasAttribute('wire:sortable')) {
    //             if (el.sortableInstance) el.sortableInstance.destroy();
    //             el.sortableInstance = Sortable.create(el, {
    //                 animation: 150,
    //                 ghostClass: 'opacity-30',
    //                 // handle: '.drag-handle-class', // Si usas un handle específico
    //                 onEnd: function (evt) {
    //                     let order = Array.from(evt.target.children).map((child, index) => {
    //                         return {
    //                             value: child.getAttribute('wire:sortable.item'),
    //                             order: index + 1
    //                         };
    //                     });
    //                     @this.call('updateGridPhotoOrder', order);
    //                 }
    //             });
    //         }
    //     });
    //     // Inicialización para la primera carga
    //     let sortableGalleryEl = document.querySelector('[wire\\:sortable="updateGridPhotoOrder"]');
    //     if (sortableGalleryEl && !sortableGalleryEl.sortableInstance) {
    //         sortableGalleryEl.sortableInstance = Sortable.create(sortableGalleryEl, { /* ... opciones ... */ });
    //     }
    // });
</script>
@endpush
