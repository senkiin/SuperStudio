<div class="p-4 sm:p-6 lg:p-8"> {{-- Añadido padding general --}}

    {{-- Indicador de Carga Global (Ejemplo Simple) --}}
    <div wire:loading class="fixed top-0 left-0 right-0 z-50 bg-white bg-opacity-75 flex items-center justify-center h-16 shadow">
        <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
        <span class="ml-2">Cargando...</span>
    </div>

    {{-- Tu Componente Base (si proporciona algún layout) --}}
    <x-self.base>

        {{-- Barra de Búsqueda Mejorada --}}
        <div class="mb-8"> {{-- Más margen inferior --}}
            <label for="search" class="sr-only">Buscar álbumes</label> {{-- Para accesibilidad --}}
            <div class="relative rounded-md shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    {{-- Icono de Lupa (Heroicons) --}}
                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                </div>
                <input
                    wire:model.live.debounce.300ms="cadena"
                    type="search" {{-- Usar type="search" es semánticamente mejor --}}
                    id="search"
                    placeholder="Buscar álbum por nombre o descripción..."
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                >
            </div>
        </div>

        {{-- Rejilla de Álbumes Mejorada --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6"> {{-- Ajustado número de columnas y gap --}}
            @forelse ($albums as $album) {{-- Usar forelse para manejar el caso vacío --}}
                <div wire:click="openModal({{ $album->id }})"
                     class="cursor-pointer group bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300 ease-in-out">
                    <div class="aspect-video overflow-hidden"> {{-- Contenedor con aspect ratio --}}
                        @if($album->cover_image)
                            <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300 ease-in-out"
                                 src="{{ Storage::url($album->cover_image) }}" alt="{{ $album->name }}">
                        @else
                             {{-- Placeholder Mejorado --}}
                             <div class="w-full h-full bg-gray-200 flex items-center justify-center text-gray-400">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                             </div>
                        @endif
                    </div>
                    {{-- Nombre del Álbum con Padding --}}
                    <div class="p-3 text-center">
                         <h3 class="text-sm font-semibold text-gray-800 truncate group-hover:text-blue-600 transition-colors">{{ $album->name }}</h3>
                         {{-- Podrías añadir la fecha o el autor aquí si quieres --}}
                         {{-- <p class="text-xs text-gray-500">{{ $album->created_at->format('d/m/Y') }}</p> --}}
                    </div>
                </div>
            @empty
                {{-- Mensaje si no hay álbumes que coincidan con la búsqueda/filtros --}}
                <div class="col-span-full text-center text-gray-500 py-10">
                    No se encontraron álbumes.
                </div>
            @endforelse {{-- Fin del forelse --}}
        </div>

        {{-- Paginación Álbumes --}}
        <div class="mt-8">  {{-- Más margen superior --}}
            {{ $albums->links(data: ['scrollTo' => false]) }}
        </div>

    </x-self.base>

    {{-- --------------------------------------------------- --}}
    {{-- MODAL MEJORADO con Alpine.js para transiciones     --}}
    {{-- --------------------------------------------------- --}}
    @if($showModal && $selectedAlbum)
        <div x-data="{ show: @entangle('showModal') }" x-show="show" @keydown.escape.window="show = false; @this.call('closeModal')" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-800 bg-opacity-75 backdrop-blur-sm z-50 flex justify-center items-center p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-5xl w-full max-h-[90vh] flex flex-col" @click.outside="show = false; @this.call('closeModal')" x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

                {{-- Cabecera del Modal --}}
                <div class="flex justify-between items-center border-b p-4 flex-shrink-0">
                    <h3 class="text-xl font-semibold text-gray-900">{{ $selectedAlbum->name }}</h3>
                    <div class="flex items-center space-x-2">
                        {{-- NUEVO BOTÓN: Seleccionar / Cancelar --}}
                        <button
                            wire:click="toggleSelectionMode"
                            class="px-3 py-1 text-xs font-medium rounded transition-colors {{ $selectionMode ? 'bg-gray-500 hover:bg-gray-600' : 'bg-blue-600 hover:bg-blue-700' }} text-white"
                            wire:loading.attr="disabled"
                        >
                            {{ $selectionMode ? 'Cancelar Selección' : 'Seleccionar Fotos' }}
                        </button>

                         {{-- Botón Cerrar --}}
                         <button wire:click="closeModal" wire:loading.attr="disabled" class="p-1 rounded-full text-gray-400 hover:bg-gray-200 hover:text-gray-600 transition-colors" aria-label="Cerrar modal">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                         </button>
                    </div>
                </div>

                {{-- Cuerpo del Modal --}}
                <div class="p-6 overflow-y-auto flex-grow">
                    {{-- Mensajes Flash ... --}}
                    @if (session()->has('message')) <div class="bg-green-100 ... mb-4">{{ session('message') }}</div> @endif
                    @if (session()->has('error')) <div class="bg-red-100 ... mb-4">{{ session('error') }}</div> @endif
                    @error('delete_error') <div class="bg-red-100 ... mb-4">{{ $message }}</div> @enderror

                    {{-- Zona para Añadir Fotos (sin cambios) --}}
                    {{-- ... --}}

                    {{-- Título de Galería y Botón Borrar --}}
                    <div class="flex justify-between items-center mb-4 mt-6 {{-- Ajusta margen si quitaste zona de añadir --}}">
                        <h4 class="text-lg font-medium">Fotos del Álbum</h4>
                        {{-- MOVIDO AQUÍ: Botón Borrar Selección --}}
                         @if(!empty($selectedPhotos) && $selectionMode) {{-- Mostrar solo si hay seleccionadas Y estamos en modo selección --}}
                            <button
                                wire:click="deleteSelectedPhotos"
                                wire:confirm="¿Estás seguro de que quieres eliminar las {{ count($selectedPhotos) }} fotos seleccionadas? Esta acción no se puede deshacer."
                                class="px-3 py-1 bg-red-600 text-white text-xs font-medium rounded hover:bg-red-700 disabled:opacity-50"
                                wire:loading.attr="disabled"
                                wire:target="deleteSelectedPhotos"
                                >
                                <span wire:loading wire:target="deleteSelectedPhotos" class="animate-spin inline-block w-3 h-3 border-2 border-white border-t-transparent rounded-full" role="status" aria-hidden="true"></span>
                                <span wire:loading.remove wire:target="deleteSelectedPhotos">Eliminar ({{ count($selectedPhotos) }})</span>
                                <span wire:loading wire:target="deleteSelectedPhotos">Eliminando...</span>
                            </button>
                         @endif
                    </div>


                    {{-- Galería de Fotos Existentes --}}
                    @if($photosInModal && $photosInModal->count() > 0)
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 mb-6">
                            @foreach ($photosInModal as $photo)
                                {{-- Contenedor de foto con nueva UI de selección y like --}}
                                {{-- Añadimos cursor-pointer solo si estamos en modo selección --}}
                                <div class="relative group aspect-square rounded-md shadow overflow-hidden @if($selectionMode) cursor-pointer @else cursor-default @endif"
                                     wire:key="photo-{{ $photo->id }}"
                                     wire:dblclick="toggleLike({{ $photo->id }})" {{-- Like con doble clic (funciona siempre) --}}
                                     @if($selectionMode) wire:click="toggleSelection({{ $photo->id }})" @endif {{-- Selección SÓLO si modo está activo --}}
                                     >

                                     {{-- Capa sutil para indicar selección (solo si está en modo selección Y seleccionada) --}}
                                     @if($selectionMode && in_array($photo->id, $selectedPhotos))
                                        <div class="absolute inset-0 bg-blue-500 bg-opacity-30 transition-opacity duration-200 z-10 pointer-events-none"></div>
                                     @endif

                                    {{-- Imagen --}}
                                    <img src="{{ Storage::url($photo->thumbnail_path ?? $photo->file_path) }}"
                                         alt="Photo ID: {{ $photo->id }}"
                                         class="w-full h-full object-cover">

                                    {{-- Icono TICK VERDE (solo si está en modo selección Y seleccionada) --}}
                                    @if($selectionMode && in_array($photo->id, $selectedPhotos))
                                    <div class="absolute top-2 right-2 z-20 bg-green-500 text-white rounded-full p-[3px] shadow pointer-events-none"> {{-- Ajusta padding/tamaño --}}
                                        {{-- Icono Check (Heroicons) --}}
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    @endif


                                    {{-- Icono LIKE (Corazón) - Se muestra siempre si tiene like --}}
                                    @if($photo->like)
                                        <div class="absolute bottom-2 left-2 z-10 p-1 bg-black bg-opacity-30 rounded-full pointer-events-none"> {{-- Cambiado a bottom-left --}}
                                            <svg class="w-4 h-4 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    @endif
                                </div> {{-- Fin contenedor foto --}}
                            @endforeach
                        </div>

                        {{-- Paginación Fotos Modal --}}
                        <div class="mt-4">
                            {{ $photosInModal->links(data: ['scrollTo' => false]) }}
                        </div>
                    @else
                        <p class="text-center text-gray-500 py-8">Este álbum no contiene fotos.</p>
                    @endif
                </div>

                {{-- Pie del Modal (sin cambios) --}}
                {{-- ... --}}
            </div>
        </div>
    @endif

</div>
