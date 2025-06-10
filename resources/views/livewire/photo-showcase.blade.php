<div>
    {{-- Visualización de la Galería --}}
    <div class="bg-black text-white py-8 md:py-12">
        <div class="container mx-auto px-4">
            @if ($gallery && $gallery->title)
                <h2 class="text-3xl font-bold text-center mb-8">{{ $gallery->title }}</h2>
            @endif

            @if ($photosToDisplay && $photosToDisplay->isNotEmpty())
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2 md:gap-4" {{-- wire:sortable="updatePhotoOrder" --}}
                    {{-- wire:sortable.options="{animation: 150}" --}}>
                    @foreach ($photosToDisplay as $index => $photo)
                        @php
                            $animationClass = match ($index % 3) {
                                0 => 'fade-left',
                                1 => 'fade-bottom',
                                2 => 'fade-right',
                            };
                        @endphp

                        <div x-data x-intersect:enter="$el.classList.add('aos-animate')"
                            x-intersect:leave="$el.classList.remove('aos-animate')"
                            class="aspect-square relative group cursor-pointer {{ $animationClass }}"
                            wire:key="showcase-photo-{{ $photo->id }}-{{ $index }}"
                            wire:click="openCustomLightbox({{ $photo->id }})" role="button" tabindex="0"
                            aria-label="Ver foto {{ $photo->filename ?? $loop->iteration }}">
                            <img src="{{ Storage::url($photo->thumbnail_path ?: $photo->file_path) }}"
                                alt="{{ $photo->filename ?? 'Foto de la galería' }}"
                                class="w-full h-full object-cover rounded-md">

                            @if ($isAdmin)
                                <button wire:click.stop="removePhotoFromShowcase({{ $photo->id }})"
                                    title="Quitar foto"
                                    class="absolute top-1 right-1 p-1.5 bg-red-600 hover:bg-red-700 text-white rounded-full text-xs opacity-0 group-hover:opacity-100 transition-opacity z-10">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-center text-gray-500">Esta galería está vacía por el momento.</p>
            @endif

            @if ($isAdmin)
                <div class="text-center mt-8">
                    <button wire:click="openManageModal"
                        class="px-6 py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-md shadow-md">
                        Administrar Galería
                    </button>
                </div>
            @endif
        </div>
    </div>

    {{-- Modal de Administración (sin cambios, se mantiene como en la respuesta anterior) --}}
    @if ($isAdmin)
        <x-dialog-modal wire:model.live="showManageModal" maxWidth="4xl">
            {{-- ... Contenido del modal de administración como lo tenías ... --}}
            {{-- Asegúrate de que el contenido esté completo aquí de la respuesta anterior --}}
            <x-slot name="title">
                Administrar Fotos de la Galería
            </x-slot>

            <x-slot name="content">
                {{-- Aquí va todo el contenido del modal de administración que te proporcioné antes --}}
                {{-- Sección 1: Subir Nuevas Fotos --}}
                {{-- Sección 2: Añadir desde Álbumes --}}
                {{-- Sección 3: Añadir Fotos con "Me Gusta" --}}
                {{-- Sección 4: Buscar y Añadir --}}
                {{-- Botón de añadir selecciones --}}
                <div class="space-y-6">

                    {{-- Sección 1: Subir Nuevas Fotos --}}
                    <div class="p-4 border border-gray-700 rounded-md">
                        <h3 class="text-lg font-medium text-gray-200 mb-3">1. Subir Nuevas Fotos Directamente</h3>
                        <input type="file" wire:model="newPhotos" multiple
                            class="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-500 file:text-white hover:file:bg-indigo-600 cursor-pointer mb-2">
                        <div wire:loading wire:target="newPhotos" class="text-indigo-400 text-xs">Cargando imágenes...
                        </div>
                        @error('newPhotos.*')
                            <span class="text-red-400 text-xs">{{ $message }}</span>
                        @enderror

                        @if ($newPhotos)
                            <div class="mt-2 grid grid-cols-3 gap-2">
                                @foreach ($newPhotos as $newPhotoPreview)
                                    @if (method_exists($newPhotoPreview, 'temporaryUrl'))
                                        <img src="{{ $newPhotoPreview->temporaryUrl() }}"
                                            class="h-20 w-auto object-cover rounded">
                                    @endif
                                @endforeach
                            </div>
                        @endif
                        <div class="mt-3 text-right">
                            <x-button wire:click="addUploadedPhotos" wire:loading.attr="disabled"
                                wire:target="newPhotos, addUploadedPhotos">
                                Añadir Fotos Subidas
                            </x-button>
                        </div>
                    </div>

                    {{-- Sección 2: Añadir desde Álbumes Existentes --}}
                    @if ($albums && $albums->isNotEmpty())
                        <div class="p-4 border border-gray-700 rounded-md">
                            <h3 class="text-lg font-medium text-gray-200 mb-3">2. Añadir desde Álbum Existente</h3>
                            <select wire:model.live="selectedAlbumId"
                                class="form-select dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 w-full rounded-md mb-3">
                                <option value="">Selecciona un álbum...</option>
                                @foreach ($albums as $album)
                                    <option value="{{ $album->id }}">{{ $album->name }}</option>
                                @endforeach
                            </select>

                            @if (!empty($photosInSelectedAlbum))
                                <p class="text-sm text-gray-400 mb-2">Selecciona las fotos a añadir:</p>
                                <div
                                    class="max-h-60 overflow-y-auto grid grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-2 p-2 bg-gray-800 rounded">
                                    @foreach ($photosInSelectedAlbum as $photo)
                                        <label for="album_photo_{{ $photo->id }}" class="cursor-pointer relative">
                                            <input type="checkbox" id="album_photo_{{ $photo->id }}"
                                                value="{{ $photo->id }}" wire:model="selectedPhotosFromAlbum"
                                                class="sr-only peer">
                                            <img src="{{ Storage::url($photo->thumbnail_path ?: $photo->file_path) }}"
                                                alt="{{ $photo->filename }}"
                                                class="w-full h-24 object-cover rounded-md transition-all peer-checked:ring-2 peer-checked:ring-indigo-500 peer-checked:opacity-75">
                                            <div
                                                class="absolute inset-0 bg-indigo-500 opacity-0 peer-checked:opacity-50 rounded-md transition-opacity flex items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            @elseif($selectedAlbumId)
                                <p class="text-sm text-gray-500">Este álbum no tiene fotos.</p>
                            @endif
                        </div>
                    @endif

                    {{-- Sección 3: Añadir Fotos con "Me Gusta" --}}
                    @if ($likedPhotos && $likedPhotos->isNotEmpty())
                        <div class="p-4 border border-gray-700 rounded-md">
                            <h3 class="text-lg font-medium text-gray-200 mb-3">3. Añadir Fotos Marcadas con "Me Gusta"
                            </h3>
                            <div
                                class="max-h-60 overflow-y-auto grid grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-2 p-2 bg-gray-800 rounded">
                                @foreach ($likedPhotos as $photo)
                                    <label for="liked_photo_{{ $photo->id }}" class="cursor-pointer relative">
                                        <input type="checkbox" id="liked_photo_{{ $photo->id }}"
                                            value="{{ $photo->id }}" wire:model="selectedLikedPhotos"
                                            class="sr-only peer">
                                        <img src="{{ Storage::url($photo->thumbnail_path ?: $photo->file_path) }}"
                                            alt="{{ $photo->filename }}"
                                            class="w-full h-24 object-cover rounded-md transition-all peer-checked:ring-2 peer-checked:ring-indigo-500 peer-checked:opacity-75">
                                        <div
                                            class="absolute inset-0 bg-indigo-500 opacity-0 peer-checked:opacity-50 rounded-md transition-opacity flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Sección 4: Buscar y Añadir Fotos Existentes --}}
                    <div class="p-4 border border-gray-700 rounded-md">
                        <h3 class="text-lg font-medium text-gray-200 mb-3">4. Buscar y Añadir Fotos Existentes</h3>
                        <div class="flex">
                            <x-input type="search" wire:model.lazy="photoSearchQuery"
                                placeholder="Buscar por nombre de archivo..." class="flex-grow" />
                            <x-button wire:click="searchExistingPhotos" class="ml-2">Buscar</x-button>
                        </div>
                        <div wire:loading wire:target="searchExistingPhotos, photoSearchQuery"
                            class="text-indigo-400 text-xs mt-1">Buscando...</div>

                        @if ($photosForSearchModal)
                            @if ($photosForSearchModal->isNotEmpty())
                                <p class="text-sm text-gray-400 mt-3 mb-2">Selecciona las fotos a añadir:</p>
                                <div
                                    class="max-h-60 overflow-y-auto grid grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-2 p-2 bg-gray-800 rounded">
                                    @foreach ($photosForSearchModal as $photo)
                                        <label for="searched_photo_{{ $photo->id }}"
                                            class="cursor-pointer relative">
                                            <input type="checkbox" id="searched_photo_{{ $photo->id }}"
                                                value="{{ $photo->id }}" wire:model="selectedExistingPhotos"
                                                class="sr-only peer">
                                            <img src="{{ Storage::url($photo->thumbnail_path ?: $photo->file_path) }}"
                                                alt="{{ $photo->filename }}"
                                                class="w-full h-24 object-cover rounded-md transition-all peer-checked:ring-2 peer-checked:ring-indigo-500 peer-checked:opacity-75">
                                            <div
                                                class="absolute inset-0 bg-indigo-500 opacity-0 peer-checked:opacity-50 rounded-md transition-opacity flex items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                                <div class="mt-3">
                                    {{ $photosForSearchModal->links('vendor.livewire.tailwind') }}
                                </div>
                            @elseif(strlen($photoSearchQuery) >= 3)
                                <p class="text-sm text-gray-500 mt-3">No se encontraron fotos con
                                    "{{ $photoSearchQuery }}".</p>
                            @endif
                        @endif
                    </div>

                    @if (($albums && $albums->isNotEmpty()) || ($likedPhotos && $likedPhotos->isNotEmpty()) || $photosForSearchModal)
                        <div class="mt-6 text-right border-t border-gray-700 pt-4">
                            <x-button wire:click="addSelectedPhotos" wire:loading.attr="disabled"
                                wire:target="addSelectedPhotos">
                                Añadir Fotos Seleccionadas (de 2, 3 y 4)
                            </x-button>
                        </div>
                    @endif
                </div>
            </x-slot>

            <x-slot name="footer">
                <x-secondary-button wire:click="closeManageModal" wire:loading.attr="disabled">
                    Cerrar
                </x-secondary-button>
            </x-slot>
        </x-dialog-modal>
    @endif


    {{-- MODAL PERSONALIZADO PARA LIGHTBOX --}}
    @if ($showCustomLightbox && $currentLightboxPhoto)
        <div x-data="{ show: @entangle('showCustomLightbox') }" x-show="show"
            x-on:keydown.escape.window="show = false; @this.call('closeCustomLightbox')"
            x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/90"
            wire:click.self="closeCustomLightbox">
            <div x-show="show" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative bg-gray-900 text-white w-full max-w-5xl max-h-[95vh] shadow-xl flex flex-col rounded-lg"
                @click.stop>
                {{-- Imagen Principal --}}
                <div class="flex-grow flex items-center justify-center overflow-hidden p-2 sm:p-4 md:p-6 relative">
                    {{-- Botón Anterior --}}
                    @if ($this->photosForCustomLightbox->count() > 1 && $currentLightboxPhotoIndex > 0)
                        <button wire:click="previousPhotoInLightbox" title="Anterior"
                            class="absolute left-2 sm:left-4 top-1/2 -translate-y-1/2 z-20 p-2 text-gray-300 hover:text-white bg-black/30 hover:bg-black/50 rounded-full transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 sm:h-8 sm:w-8" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                    @endif

                    <img src="{{ Storage::url($currentLightboxPhoto->file_path) }}"
                        alt="{{ $currentLightboxPhoto->filename ?? 'Foto actual' }}"
                        class="block max-w-full max-h-[calc(95vh-120px)] object-contain"> {{-- Ajustar max-h --}}

                    {{-- Botón Siguiente --}}
                    @if (
                        $this->photosForCustomLightbox->count() > 1 &&
                            $currentLightboxPhotoIndex < $this->photosForCustomLightbox->count() - 1)
                        <button wire:click="nextPhotoInLightbox" title="Siguiente"
                            class="absolute right-2 sm:right-4 top-1/2 -translate-y-1/2 z-20 p-2 text-gray-300 hover:text-white bg-black/30 hover:bg-black/50 rounded-full transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 sm:h-8 sm:w-8" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    @endif
                </div>

                {{-- Footer del Modal (Caption y Contador) --}}
                <div
                    class="flex-shrink-0 flex justify-between items-center px-4 py-2 sm:px-6 sm:py-3 border-t border-gray-700 text-xs sm:text-sm">
                    <div>
                        <span>{{ $currentLightboxPhoto->filename ?? (pathinfo($currentLightboxPhoto->file_path, PATHINFO_FILENAME) ?? 'Foto ' . $currentLightboxPhoto->id) }}</span>
                    </div>
                    @if ($this->photosForCustomLightbox->count() > 0)
                        <div>
                            <span>{{ $currentLightboxPhotoIndex + 1 }} /
                                {{ $this->photosForCustomLightbox->count() }}</span>
                        </div>
                    @endif
                </div>

                {{-- Botón de Cierre (X) --}}
                <button wire:click="closeCustomLightbox" title="Cerrar (Esc)"
                    class="absolute top-2 right-2 sm:top-3 sm:right-3 z-20 p-1.5 text-gray-300 hover:text-white bg-black/50 hover:bg-black/70 rounded-full transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    @endif


    {{-- Mensajes Flash (ya los tienes definidos antes) --}}
    @if (session()->has('message'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed bottom-5 right-5 bg-green-600 text-white py-2 px-4 rounded-md shadow-lg z-[101]">
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed bottom-5 right-5 bg-red-600 text-white py-2 px-4 rounded-md shadow-lg z-[101]">
            {{ session('error') }}
        </div>
    @endif
</div>

{{-- Estilos para la galería si no los tienes globales --}}
@push('styles')
    <style>
        .photo-gallery {
            /* Si usas este nombre de clase para la galería principal */
            /* display: grid; ya está en el div */
            /* grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); ya está en el div */
            /* gap: 4px; ya está en el div */
        }

        /* .gallery-item { ya está en el div con aspect-square y cursor-pointer } */
    </style>
@endpush
