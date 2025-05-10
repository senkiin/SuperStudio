<div class="py-12 bg-black text-white">
    <div x-data x-intersect:enter="$el.classList.add('aos-animate')"
        x-intersect:leave="$el.classList.remove('aos-animate')"
        class="title-animate-down text-center mb-10 md:mb-14 relative">
        <h2 class="text-3xl sm:text-4xl md:text-5xl font-extrabold tracking-tight uppercase"
            style="font-family: Arial, sans-serif;">
            {{ $sectionTitle }}
        </h2>
        <hr class="border-t-2 border-gray-700 w-24 mx-auto mt-4">
        @if ($isAdmin)
            <div class="absolute top-0 right-0 -mt-2 md:mt-0">
                <x-button wire:click="openConfigurationModal" class="bg-gray-800 hover:bg-gray-700 text-xs px-3 py-1.5">
                    Configurar
                </x-button>
            </div>
        @endif
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-x-6 gap-y-14 px-6 md:px-12">

        @foreach ($displayedAlbums as $album)
            <div x-data x-intersect:enter="$el.classList.add('aos-animate')"
                x-intersect:leave="$el.classList.remove('aos-animate')"
                class="card-animate-up flex flex-col text-white group transition-transform duration-300 hover:scale-105">
                {{-- Imagen cuadrada pequeña --}}
                <a href="{{ route('album.show', $album) }}"
                    class="block relative w-full aspect-square overflow-hidden rounded shadow-lg">
                    @if ($album->cover_image)
                        <img src="{{ Storage::url($album->cover_image) }}" alt="{{ $album->name }}"
                            class="w-full h-full object-cover transition-opacity duration-300 group-hover:opacity-85">
                    @else
                        <div class="w-full h-full bg-gray-800 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    @endif

                    {{-- Etiqueta tipo SOLD OUT --}}
                    @if ($album->is_closed ?? false)
                        <div
                            class="absolute top-2 left-2 bg-white text-black px-2 py-1 text-xs font-bold shadow uppercase">
                            SOLD OUT / CLOSED
                        </div>
                    @endif
                </a>

                {{-- Texto inferior --}}
                <div class="mt-5">
                    <h3 class="text-lg font-extrabold uppercase tracking-tight mb-1"
                        style="font-family: 'Arial Black', Gadget, sans-serif;">
                        {{ $album->name ?? $album->title }}
                    </h3>

                    @if ($album->start_date && $album->end_date)
                        <p class="text-sm text-indigo-400 mb-2">
                            {{ \Carbon\Carbon::parse($album->start_date)->format('j M') }} -
                            {{ \Carbon\Carbon::parse($album->end_date)->format('j M Y') }}
                        </p>
                    @endif

                    <p class="text-gray-400 text-sm leading-relaxed">
                        {{ $album->description ?? 'No description available.' }}
                    </p>
                </div>
            </div>
        @endforeach
    </div>




    {{-- MODAL DE CONFIGURACIÓN PRINCIPAL --}}
    <x-dialog-modal wire:model.live="showConfigurationModal" maxWidth="4xl">
        <x-slot name="title">
            Configurar Sección de Álbumes
        </x-slot>

        <x-slot name="content">
            <div class="space-y-6">
                {{-- Título de la Sección --}}
                <div>
                    <x-label for="editableSectionTitle" value="Título de la Sección (ej: 2025, Destacados)" />
                    <x-input id="editableSectionTitle" type="text" class="mt-1 block w-full"
                        wire:model="editableSectionTitle" />
                    @error('editableSectionTitle')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <hr class="my-6 border-gray-600">

                {{-- Gestión de Álbumes Seleccionados --}}
                <div>
                    <div class="flex justify-between items-center mb-3">
                        <h4 class="text-lg font-medium text-gray-200">Álbumes Seleccionados para Mostrar</h4>
                        <x-secondary-button wire:click="openAlbumSelectionSubModal">
                            Añadir / Cambiar Álbumes
                        </x-secondary-button>
                    </div>

                    @if (empty($selectedAlbumOrder))
                        <p class="text-gray-500 text-sm">No has seleccionado ningún álbum todavía. Haz clic en "Añadir /
                            Cambiar Álbumes".</p>
                    @else
                        {{-- Contenedor de la lista de álbumes SIN wire:sortable --}}
                        <div class="mt-2 space-y-2 max-h-96 overflow-y-auto pr-2">
                            {{-- Usamos $albumsInConfiguration para la preview en el modal --}}
                            @foreach ($albumsInConfiguration as $album)
                                {{-- Ítem de la lista SIN wire:sortable.item --}}
                                <div wire:key="config-album-{{ $album->id }}"
                                    class="flex items-center justify-between p-3 bg-gray-700 rounded-md shadow">
                                    {{-- Removido hover:bg-gray-600 si ya no es interactivo para drag --}}
                                    <div class="flex items-center min-w-0">
                                        {{-- ELIMINADO el div del config-drag-handle --}}
                                        @if ($album->cover_image)
                                            <img src="{{ Storage::url($album->cover_image) }}"
                                                alt="{{ $album->name }}"
                                                class="w-12 h-10 object-cover rounded mr-3 flex-shrink-0">
                                        @else
                                            <div class="w-12 h-10 bg-gray-600 rounded mr-3 flex-shrink-0"></div>
                                        @endif
                                        <span class="text-sm font-medium text-gray-200 truncate mr-2"
                                            {{-- Añadido mr-2 para espaciar del botón de eliminar --}}
                                            title="{{ $album->name }}">{{ $album->name }}</span>
                                    </div>
                                    <button wire:click="removeAlbumFromSelection({{ $album->id }})"
                                        class="p-1 text-red-400 hover:text-red-300 rounded-full focus:outline-none focus:ring-2 focus:ring-red-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                        {{-- Texto de ayuda actualizado o eliminado si ya no hay forma de reordenar --}}
                        @if (method_exists($this, 'updateDisplayOrder'))
                            {{-- Solo mostrar si aún existe el método, por si se implementa otra forma de ordenar --}}
                            <p class="text-xs text-gray-500 mt-2 italic">El orden se guarda tal como se seleccionan los
                                álbumes o mediante otras opciones de ordenamiento si están disponibles.</p>
                        @endif
                    @endif
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('showConfigurationModal')" wire:loading.attr="disabled">
                Cancelar
            </x-secondary-button>
            <x-button class="ml-3" wire:click="saveConfiguration" wire:loading.attr="disabled">
                Guardar Configuración
            </x-button>
        </x-slot>
    </x-dialog-modal>


    {{-- SUB-MODAL PARA SELECCIONAR ÁLBUMES --}}
    <x-dialog-modal wire:model.live="showAlbumSelectionModal" maxWidth="4xl"> {{-- Más Ancho --}}
        <x-slot name="title">
            Seleccionar Álbumes
        </x-slot>

        <x-slot name="content">
            <div class="space-y-4">
                {{-- Barra de Búsqueda y Filtros para Álbumes Disponibles --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    <div class="md:col-span-2">
                        <x-label for="albumSearchQuery" value="Buscar Álbum (Nombre o Descripción)" />
                        <x-input id="albumSearchQuery" type="search" class="mt-1 block w-full"
                            wire:model.live.debounce.300ms="albumSearchQuery" placeholder="Escribe para buscar..." />
                    </div>
                    <div>
                        <x-label for="albumSortField" value="Ordenar por" />
                        <select id="albumSortField" wire:model.live="albumSortField"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                            <option value="created_at">Fecha Creación</option>
                            <option value="name">Nombre</option>
                            <option value="type">Tipo</option>
                        </select>
                        {{-- Botón para cambiar dirección de orden --}}
                        <button wire:click="sortBy('{{ $albumSortField }}')"
                            class="mt-1 text-sm text-indigo-400 hover:text-indigo-300">
                            {{ $albumSortDirection === 'asc' ? 'Ascendente ↑' : 'Descendente ↓' }}
                        </button>
                    </div>
                </div>

                {{-- Lista de Álbumes Disponibles con Checkboxes --}}
                <div wire:loading.remove wire:target="albumSearchQuery, albumSortField, albumSortDirection"
                    class="mt-4 space-y-2 max-h-[50vh] overflow-y-auto border border-gray-700 rounded-md p-3">
                    @if ($this->availableAlbums && $this->availableAlbums->total() > 0)
                        @foreach ($this->availableAlbums as $album)
                            <label for="modal-album-{{ $album->id }}"
                                class="flex items-center p-3 border-b border-gray-700 hover:bg-gray-700 transition-colors cursor-pointer last:border-b-0 {{ in_array($album->id, $tempSelectedAlbumIds) ? 'bg-indigo-900/50 border-indigo-700' : '' }}">
                                <input id="modal-album-{{ $album->id }}" type="checkbox"
                                    value="{{ $album->id }}" wire:model.live="tempSelectedAlbumIds"
                                    class="h-5 w-5 text-indigo-500 border-gray-600 bg-gray-800 rounded focus:ring-indigo-600 focus:ring-offset-gray-800">
                                <div class="ml-3 text-sm min-w-0">
                                    <span class="font-medium text-gray-200 block truncate"
                                        title="{{ $album->name }}">{{ $album->name }}</span>
                                    <span class="text-gray-400 block truncate text-xs"
                                        title="{{ $album->description }}">{{ Str::limit($album->description, 80) }}</span>
                                </div>
                            </label>
                        @endforeach
                    @else
                        <p class="text-gray-500 text-center py-4">No se encontraron álbumes que coincidan con tu
                            búsqueda o filtros.</p>
                    @endif
                </div>
                <div wire:loading wire:target="albumSearchQuery, albumSortField, albumSortDirection"
                    class="text-center text-gray-400 py-4">Cargando álbumes...</div>


                {{-- Paginación para álbumes disponibles --}}
                @if ($this->availableAlbums && $this->availableAlbums->hasPages())
                    <div class="mt-4">
                        {{ $this->availableAlbums->links('vendor.livewire.tailwind-dark') }} {{-- Asumiendo que tienes una vista de paginación oscura --}}
                    </div>
                @endif
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="closeAlbumSelectionSubModal" wire:loading.attr="disabled">
                Cancelar
            </x-secondary-button>
            <x-button class="ml-3" wire:click="confirmAndCloseAlbumSelection" wire:loading.attr="disabled">
                Confirmar Selección ({{ count($tempSelectedAlbumIds) }})
            </x-button>
        </x-slot>
    </x-dialog-modal>

</div>
