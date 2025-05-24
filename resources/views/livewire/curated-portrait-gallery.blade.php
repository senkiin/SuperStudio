<div>
    <section class="py-12 md:py-16 bg-black text-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Título y Descripción de la Galería --}}
            @if ($galleryTitle)
                <h2 x-data x-intersect:enter="$el.classList.add('aos-animate')"
                    class="text-3xl sm:text-4xl font-bold tracking-tight text-center mb-4 fade-up">
                    {{ $galleryTitle }}
                </h2>
            @endif
            @if ($galleryDescription)
                <p x-data x-intersect="$el.classList.add('aos-animate')"
                    class="text-gray-400 text-center max-w-3xl mx-auto mb-10 md:mb-12 fade-up">
                    {{ $galleryDescription }}
                </p>
            @endif

            {{-- Grid de Fotos --}}
            @if ($photosForDisplay->isNotEmpty())
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 gap-1 sm:gap-2">
                    @foreach ($photosForDisplay as $photo)
                        @php
                            // Determinar clase de animación basada en el índice del bucle
                            $animationClass = match ($loop->index % 3) {
                                0 => 'fade-left',
                                1 => 'fade-up',
                                2 => 'fade-right',
                                default => 'fade-up',
                            };
                            $pathValue = $photo->thumbnail_path ?: $photo->file_path;
                        @endphp
                        <div x-data x-intersect:enter="$el.classList.add('aos-animate')"
                             class="aspect-w-1 aspect-h-1 relative group bg-gray-800 rounded-md overflow-hidden cursor-pointer {{ $animationClass }}"
                             wire:key="display-collection-photo-{{ $collectionConfig->id ?? 'gallery' }}-{{ $photo->id }}-{{ $loop->index }}"
                             wire:click="openCustomLightbox({{ $photo->id }})"
                             role="button"
                             tabindex="0"
                             aria-label="Ver foto {{ $photo->filename ?? 'Foto ' . ($loop->index + 1) }}">
                            <img src="{{ $pathValue ? Storage::disk($disk)->url($pathValue) : '' }}"
                                 alt="{{ $photo->filename ?? 'Foto de la colección' }}"
                                 loading="lazy"
                                 class="w-full h-full object-cover transition-transform duration-300 ease-in-out group-hover:scale-105">
                            @if ($isAdmin)
                                <button wire:click.stop="removeFromCollection({{ $photo->id }})"
                                        wire:confirm="¿Estás seguro de quitar esta foto de la colección?"
                                        title="Quitar foto de la colección"
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
                    <h3 class="mt-2 text-lg font-semibold text-white">Colección de Fotos Vacía</h3>
                    @if($isAdmin)
                        <p class="mt-1 text-sm text-gray-400">Esta colección de fotos aún no tiene imágenes. <button wire:click="openManagerModal" class="text-indigo-400 hover:underline">¡Comienza a añadir algunas!</button></p>
                    @else
                        <p class="mt-1 text-sm text-gray-400">Pronto habrá una selección especial de fotos aquí. ¡Vuelve pronto!</p>
                    @endif
                </div>
            @endif

            @if ($isAdmin)
                <div class="text-center mt-10">
                    <button wire:click="openManagerModal"
                        class="px-6 py-3 bg-indigo-700 hover:bg-indigo-600 text-white font-semibold rounded-lg shadow-md transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-black">
                        Administrar Galería: {{ $galleryTitle ?: $identifier }}
                    </button>
                </div>
            @endif
        </div>
    </section>

    {{-- Modal de Administración --}}
    @if ($isAdmin && $showManagerModal)
        <x-dialog-modal wire:model.live="showManagerModal" maxWidth="4xl">
            <x-slot name="title">
                Administrar Galería de Retratos: <span class="font-normal italic text-indigo-300">{{ $galleryTitle ?: $identifier }}</span>
            </x-slot>
            <x-slot name="content">
                @if (session()->has('cpg_modal_message'))
                    <div class="mb-4 p-3 bg-green-600 text-white rounded-md text-sm">{{ session('cpg_modal_message') }}</div>
                @endif
                @if (session()->has('cpg_modal_error'))
                    <div class="mb-4 p-3 bg-red-600 text-white rounded-md text-sm">{{ session('cpg_modal_error') }}</div>
                @endif

                <div class="space-y-6 text-gray-200">
                    {{-- Editar Título y Descripción de la Galería --}}
                    <div class="p-4 border border-gray-700 rounded-md bg-gray-800 shadow-sm">
                        <h4 class="text-lg font-semibold text-gray-100 mb-3">Configuración de la Galería</h4>
                        <div class="space-y-4">
                            <div>
                                <label for="editableCollectionTitleModal" class="block text-sm font-medium mb-1">Título de la Galería:</label>
                                <input type="text" wire:model.defer="editableCollectionTitleModal" id="editableCollectionTitleModal" placeholder="Ej: Retratos Expresivos"
                                       class="w-full p-2.5 rounded bg-gray-700 text-white border border-gray-600 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                @error('editableCollectionTitleModal') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="editableCollectionDescriptionModal" class="block text-sm font-medium mb-1">Descripción (opcional):</label>
                                <textarea wire:model.defer="editableCollectionDescriptionModal" id="editableCollectionDescriptionModal" rows="3" placeholder="Una breve descripción para esta colección de retratos..."
                                          class="w-full p-2.5 rounded bg-gray-700 text-white border border-gray-600 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                                @error('editableCollectionDescriptionModal') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div class="text-right">
                                <x-secondary-button wire:click="saveGalleryMetadata" wire:loading.attr="disabled" wire:target="saveGalleryMetadata" class="text-xs px-4 py-2">
                                    Guardar Configuración
                                </x-secondary-button>
                            </div>
                        </div>
                    </div>

                    {{-- 1. Subir Nuevas Fotos --}}
                    <div class="p-4 border border-gray-700 rounded-md bg-gray-800 shadow-sm">
                        <h4 class="text-lg font-semibold text-gray-100 mb-3">1. Subir Nuevas Fotos Directamente</h4>
                        <input type="file" wire:model="newPhotosToUploadModal" multiple id="upload-collection-photos-{{ $this->getId() }}"
                            class="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-500 cursor-pointer mb-2">
                        <div wire:loading wire:target="newPhotosToUploadModal" class="text-indigo-400 text-xs mt-1">Cargando imágenes...</div>
                        @error('newPhotosToUploadModal.*') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror

                        @if ($newPhotosToUploadModal)
                        <div class="mt-3 grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-2">
                            @foreach ($newPhotosToUploadModal as $imgPrev)
                                @if(method_exists($imgPrev, 'temporaryUrl'))
                                <img src="{{ $imgPrev->temporaryUrl() }}" class="h-20 w-20 object-cover rounded-md shadow-sm">
                                @endif
                            @endforeach
                        </div>
                        <div class="mt-3 text-right">
                            <x-button wire:click="uploadAndAttachToCollection" wire:loading.attr="disabled" wire:target="newPhotosToUploadModal, uploadAndAttachToCollection" class="text-sm px-4 py-2">
                                Subir y Añadir Fotos
                            </x-button>
                        </div>
                        @endif
                    </div>

                    {{-- Pestañas para seleccionar fotos existentes --}}
                    <div x-data="{ currentPhotoTab: 'fromAlbums' }" class="border border-gray-700 rounded-md bg-gray-800 shadow-sm">
                        <div class="flex border-b border-gray-600 bg-gray-750 rounded-t-md">
                            @if($allAlbumsModalCollection && $allAlbumsModalCollection->isNotEmpty())
                            <button @click="currentPhotoTab = 'fromAlbums'" :class="{ 'bg-indigo-600 text-white border-indigo-500': currentPhotoTab === 'fromAlbums', 'bg-gray-700 hover:bg-gray-600 text-gray-300 border-transparent': currentPhotoTab !== 'fromAlbums' }" class="flex-1 px-3 py-2.5 text-sm font-medium focus:outline-none border-b-2 transition-colors">Desde Álbumes</button>
                            @endif
                            @if($likedPhotosForUserModal && $likedPhotosForUserModal->isNotEmpty())
                            <button @click="currentPhotoTab = 'fromLiked'" :class="{ 'bg-indigo-600 text-white border-indigo-500': currentPhotoTab === 'fromLiked', 'bg-gray-700 hover:bg-gray-600 text-gray-300 border-transparent': currentPhotoTab !== 'fromLiked' }" class="flex-1 px-3 py-2.5 text-sm font-medium focus:outline-none border-b-2 transition-colors {{ ($allAlbumsModalCollection && $allAlbumsModalCollection->isNotEmpty()) ? '' : 'rounded-tl-md' }}">Favoritas</button>
                            @endif
                            <button @click="currentPhotoTab = 'fromSearch'" :class="{ 'bg-indigo-600 text-white border-indigo-500': currentPhotoTab === 'fromSearch', 'bg-gray-700 hover:bg-gray-600 text-gray-300 border-transparent': currentPhotoTab !== 'fromSearch' }" class="flex-1 px-3 py-2.5 text-sm font-medium focus:outline-none border-b-2 transition-colors {{ ((!$allAlbumsModalCollection || $allAlbumsModalCollection->isEmpty()) && (!$likedPhotosForUserModal || $likedPhotosForUserModal->isEmpty())) ? 'rounded-tl-md' : '' }} {{ (!$allAlbumsModalCollection || $allAlbumsModalCollection->isEmpty()) && ($likedPhotosForUserModal && $likedPhotosForUserModal->isNotEmpty()) ? '' : 'rounded-tr-md' }}">Buscar Foto</button>
                        </div>
                        <div class="p-4">
                            {{-- Contenido de cada pestaña --}}
                            @if($allAlbumsModalCollection && $allAlbumsModalCollection->isNotEmpty())
                            <div x-show="currentPhotoTab === 'fromAlbums'" x-transition.opacity.duration.300ms class="space-y-3">
                                <h4 class="text-lg font-semibold text-gray-100">2. Añadir desde Álbum</h4>
                                <select wire:model.live="selectedAlbumIdModal" class="form-select bg-gray-700 border-gray-600 text-gray-300 w-full rounded-md shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">-- Selecciona un álbum --</option>
                                    @foreach ($allAlbumsModalCollection as $album) <option value="{{ $album->id }}">{{ $album->name }} ({{ $album->photos_count }} fotos)</option> @endforeach
                                </select>
                                @if ($photosFromAlbumModal->isNotEmpty())
                                    <div class="max-h-60 overflow-y-auto grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-2 p-2 bg-gray-700/70 rounded-md border border-gray-600">
                                        @foreach ($photosFromAlbumModal as $photo)
                                            @php $pathValueModalAlbum = $photo->thumbnail_path ?: $photo->file_path; @endphp
                                            <label for="modal_collection_album_photo_{{ $photo->id }}" class="cursor-pointer relative aspect-square block">
                                                <input type="checkbox" id="modal_collection_album_photo_{{ $photo->id }}" value="{{ $photo->id }}" wire:model.defer="selectedPhotosFromAlbumModalArray" class="sr-only peer">
                                                <img src="{{ $pathValueModalAlbum ? Storage::disk($disk)->url($pathValueModalAlbum) : '' }}" alt="{{ $photo->filename }}" class="w-full h-full object-cover rounded-md transition-all duration-150 peer-checked:ring-2 peer-checked:ring-offset-2 peer-checked:ring-offset-gray-800 peer-checked:ring-indigo-500 peer-checked:opacity-60">
                                                <div class="absolute inset-0 bg-indigo-700 opacity-0 peer-checked:opacity-40 rounded-md transition-opacity flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg></div>
                                            </label>
                                        @endforeach
                                    </div>
                                @elseif($selectedAlbumIdModal) <p class="text-sm text-gray-500 italic">Este álbum no contiene fotos.</p>
                                @endif
                            </div>
                            @endif
                            @if($likedPhotosForUserModal && $likedPhotosForUserModal->isNotEmpty())
                            <div x-show="currentPhotoTab === 'fromLiked'" x-transition.opacity.duration.300ms class="space-y-3">
                                <h4 class="text-lg font-semibold text-gray-100">3. Añadir desde "Mis Favoritas" (Admin)</h4>
                                <div class="max-h-60 overflow-y-auto grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-2 p-2 bg-gray-700/70 rounded-md border border-gray-600">
                                    @foreach ($likedPhotosForUserModal as $photo)
                                        @php $pathValueModalLiked = $photo->thumbnail_path ?: $photo->file_path; @endphp
                                        <label for="modal_collection_liked_photo_{{ $photo->id }}" class="cursor-pointer relative aspect-square block">
                                            <input type="checkbox" id="modal_collection_liked_photo_{{ $photo->id }}" value="{{ $photo->id }}" wire:model.defer="selectedLikedPhotosModalArray" class="sr-only peer">
                                            <img src="{{ $pathValueModalLiked ? Storage::disk($disk)->url($pathValueModalLiked) : '' }}" alt="{{ $photo->filename }}" class="w-full h-full object-cover rounded-md transition-all duration-150 peer-checked:ring-2 peer-checked:ring-offset-2 peer-checked:ring-offset-gray-800 peer-checked:ring-indigo-500 peer-checked:opacity-60">
                                            <div class="absolute inset-0 bg-indigo-700 opacity-0 peer-checked:opacity-40 rounded-md transition-opacity flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg></div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                            <div x-show="currentPhotoTab === 'fromSearch'" x-transition.opacity.duration.300ms class="space-y-3">
                                <h4 class="text-lg font-semibold text-gray-100">4. Buscar Foto Existente</h4>
                                <div class="flex">
                                    <input type="search" wire:model.lazy="searchQueryModal" placeholder="Buscar por nombre de archivo..." class="flex-grow p-2.5 text-sm rounded-l-md bg-gray-700 text-white border border-gray-600 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                    <button wire:click="searchPhotosInModal" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium rounded-r-md">Buscar</button>
                                </div>
                                <div wire:loading wire:target="searchPhotosInModal, searchQueryModal" class="text-indigo-400 text-xs mt-1">Buscando...</div>

                                @if ($searchedPhotosModalPaginator && $searchedPhotosModalPaginator->isNotEmpty())
                                    <div class="max-h-60 overflow-y-auto grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-2 p-2 mt-2 bg-gray-700/70 rounded-md border border-gray-600">
                                        @foreach ($searchedPhotosModalPaginator as $photo)
                                            @php $pathValueModalSearch = $photo->thumbnail_path ?: $photo->file_path; @endphp
                                            <label for="modal_collection_searched_photo_{{ $photo->id }}" class="cursor-pointer relative aspect-square block">
                                                <input type="checkbox" id="modal_collection_searched_photo_{{ $photo->id }}" value="{{ $photo->id }}" wire:model.defer="selectedExistingPhotosModalArray" class="sr-only peer">
                                                <img src="{{ $pathValueModalSearch ? Storage::disk($disk)->url($pathValueModalSearch) : '' }}" alt="{{ $photo->filename }}" class="w-full h-full object-cover rounded-md transition-all duration-150 peer-checked:ring-2 peer-checked:ring-offset-2 peer-checked:ring-offset-gray-800 peer-checked:ring-indigo-500 peer-checked:opacity-60">
                                                <div class="absolute inset-0 bg-indigo-700 opacity-0 peer-checked:opacity-40 rounded-md transition-opacity flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg></div>
                                            </label>
                                        @endforeach
                                    </div>
                                    <div class="mt-2 text-xs">{{ $searchedPhotosModalPaginator->links('vendor.livewire.tailwind-dark-small') }}</div>
                                @elseif(strlen($searchQueryModal) >= 3)
                                    <p class="text-sm text-gray-500 italic mt-2">No se encontraron fotos con "{{ $searchQueryModal }}".</p>
                                @endif
                            </div>
                        </div>
                        <div class="mt-6 text-right border-t border-gray-600 pt-4">
                             <x-button wire:click="attachExistingPhotosToCollection" wire:loading.attr="disabled" wire:target="attachExistingPhotosToCollection" class="text-sm px-5 py-2.5">
                                Añadir Seleccionadas (de Álbum/Favoritas/Búsqueda)
                            </x-button>
                        </div>
                    </div>
                </div>
            </x-slot>
            <x-slot name="footer">
                <x-secondary-button wire:click="closeManagerModal" wire:loading.attr="disabled">
                    Cerrar Panel de Gestión
                </x-secondary-button>
            </x-slot>
        </x-dialog-modal>
    @endif

    {{-- MODAL PERSONALIZADO PARA LIGHTBOX (Previsualización) --}}
    @if ($showCustomLightbox && $currentLightboxPhoto)
        <div
            x-data="{ showLightbox: @entangle('showCustomLightbox') }"
            x-show="showLightbox"
            x-on:keydown.escape.window="showLightbox = false; @this.call('closeCustomLightbox')"
            x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/90 backdrop-blur-sm"
            wire:click.self="closeCustomLightbox"
        >
            <div
                x-show="showLightbox"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                class="relative bg-gray-900 text-white w-full max-w-5xl max-h-[95vh] shadow-xl flex flex-col rounded-lg overflow-hidden"
                @click.stop
            >
                {{-- Imagen Principal --}}
                <div class="flex-grow flex items-center justify-center overflow-hidden p-2 sm:p-4 md:p-6 relative">
                     {{-- Botón Anterior --}}
                    @if ($photosForDisplay->count() > 1 && $currentLightboxPhotoIndex > 0)
                        <button wire:click="previousPhotoInLightbox" title="Anterior" aria-label="Foto anterior"
                                class="absolute left-2 sm:left-4 top-1/2 -translate-y-1/2 z-20 p-2 text-gray-300 hover:text-white bg-black/40 hover:bg-black/60 rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 sm:h-8 sm:w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                    @endif

                    @php
                        $path = $currentLightboxPhoto->file_path;
                        // Ensure $disk is available, assuming it's passed or public in Livewire component
                        $url  = ($path && isset($disk) && Storage::disk($disk)->exists($path))
                            ? Storage::disk($disk)->url($path)
                            : '';
                    @endphp

                    @if($url)
                        <img
                          src="{{ $url }}"
                          alt="{{ $currentLightboxPhoto->filename ?? 'Foto actual en previsualización' }}"
                          class="block max-w-full max-h-[calc(95vh-120px)] object-contain select-none"
                        >
                    @else
                        <div class="flex items-center justify-center w-full h-full bg-gray-800">
                           <span class="text-gray-400">Imagen no disponible</span>
                        </div>
                    @endif

                     {{-- Botón Siguiente --}}
                    @if ($photosForDisplay->count() > 1 && $currentLightboxPhotoIndex < ($photosForDisplay->count() - 1))
                        <button wire:click="nextPhotoInLightbox" title="Siguiente" aria-label="Siguiente foto"
                                class="absolute right-2 sm:right-4 top-1/2 -translate-y-1/2 z-20 p-2 text-gray-300 hover:text-white bg-black/40 hover:bg-black/60 rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 sm:h-8 sm:w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    @endif
                </div>

                {{-- Footer del Modal Lightbox (Caption y Contador) --}}
                <div class="flex-shrink-0 flex justify-between items-center px-4 py-2 sm:px-6 sm:py-3 border-t border-gray-700 text-xs sm:text-sm">
                    <div>
                        <span>{{ $currentLightboxPhoto->filename ?? pathinfo($currentLightboxPhoto->file_path ?? '', PATHINFO_FILENAME) ?? 'Foto ' . $currentLightboxPhoto->id }}</span>
                    </div>
                    @if($photosForDisplay->count() > 0)
                    <div>
                        <span>{{ $currentLightboxPhotoIndex + 1 }} / {{ $photosForDisplay->count() }}</span>
                    </div>
                    @endif
                </div>

                {{-- Botón de Cierre (X) del Lightbox --}}
                <button wire:click="closeCustomLightbox" title="Cerrar Previsualización (Esc)" aria-label="Cerrar previsualización"
                        class="absolute top-2 right-2 sm:top-3 sm:right-3 z-20 p-1.5 text-gray-300 hover:text-white bg-black/50 hover:bg-black/70 rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    @endif

    {{-- Mensajes Flash generales de la página (fuera del modal) --}}
    @if (session()->has('cpg_message'))
        <div x-data="{ showFlashCPG: true }" x-init="setTimeout(() => showFlashCPG = false, 3500)" x-show="showFlashCPG"
            x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed bottom-5 right-5 bg-green-600 text-white py-2.5 px-5 rounded-lg shadow-xl z-[101] text-sm">
            {{ session('cpg_message') }}
        </div>
    @endif
    @if (session()->has('cpg_error'))
        <div x-data="{ showFlashCPG: true }" x-init="setTimeout(() => showFlashCPG = false, 3500)" x-show="showFlashCPG"
             x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed bottom-5 right-5 bg-red-600 text-white py-2.5 px-5 rounded-lg shadow-xl z-[101] text-sm">
            {{ session('cpg_error') }}
        </div>
    @endif
</div>

@push('scripts')
<script>
    // Código para SortableJS (si decides implementarlo para reordenar las fotos del grid)
    // ... (script de SortableJS como en la respuesta anterior, adaptando selectores si es necesario) ...
</script>
@endpush