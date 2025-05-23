{{-- ========================================================================= --}}
{{--  Archivo: resources/views/livewire/albums.blade.php                     --}}
{{--  Descripción: Vista completa para el componente Livewire 'Albums',       --}}
{{--               incluyendo búsqueda, rejilla de álbumes, modal con        --}}
{{--               galería de fotos, subida, selección múltiple, borrado y like. --}}
{{-- ========================================================================= --}}

<div class="p-4 sm:p-6 lg:p-8"> {{-- Padding general --}}



    {{-- --------------------------------------------------------------------- --}}
    {{-- Componente Base (Wrapper opcional para tu layout)                     --}}
    {{-- --------------------------------------------------------------------- --}}
    <x-self.base>

        {{-- ========================================================================= --}}
        {{-- Barra Superior: Búsqueda, Ordenación y Botón Crear   --}}
        {{-- ========================================================================= --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 md:gap-6 mb-8">
            {{-- Parte Izquierda/Principal: Búsqueda y Ordenación --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-4 gap-4 sm:gap-0 flex-grow min-w-0">
                {{-- flex-grow para que ocupe espacio disponible, min-w-0 para evitar overflow --}}

                {{-- Barra de Búsqueda --}}
                <div class="relative flex-shrink-0 w-full sm:w-64 md:w-72"> {{-- Ancho fijo o adaptable --}}
                    <label for="albumSearchModern" class="sr-only">Buscar álbumes</label>
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none" aria-hidden="true">
                        <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input wire:model.live.debounce.300ms="cadena" type="search" id="albumSearchModern"
                        placeholder="Buscar álbum..."
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm shadow-sm transition duration-150 ease-in-out">
                    {{-- Borde/Sombra/Transición/Focus mejorado --}}
                </div>

                {{-- Controles de Ordenación (Estilo Botones/Pills) --}}
                <div class="flex items-center space-x-2 pt-1 sm:pt-0">
                    <span class="text-sm font-medium text-gray-600 hidden sm:inline">Ordenar:</span>
                    {{-- Botón Fecha --}}
                    <button wire:click="sortBy('created_at')" type="button" title="Ordenar por Fecha"
                        class="px-3 py-1 text-xs font-medium rounded-full transition-colors duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-indigo-400 {{ $campo === 'created_at' ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        Fecha @if ($campo === 'created_at')
                            <span>{{ $order === 'asc' ? '↑' : '↓' }}</span>
                        @endif
                    </button>
                    {{-- Botón Nombre --}}
                    <button wire:click="sortBy('name')" type="button" title="Ordenar por Nombre"
                        class="px-3 py-1 text-xs font-medium rounded-full transition-colors duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-indigo-400 {{ $campo === 'name' ? 'bg-indigo-100 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        Nombre @if ($campo === 'name')
                            <span>{{ $order === 'asc' ? '↑' : '↓' }}</span>
                        @endif
                    </button>
                    {{-- Botón Tipo --}}
                    <button wire:click="sortBy('type')" type="button" title="Ordenar por Tipo"
                        class="px-3 py-1 text-xs font-medium rounded-full transition-colors duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-indigo-400 {{ $campo === 'type' ? 'bg-indigo-100 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        Tipo @if ($campo === 'type')
                            <span>{{ $order === 'asc' ? '↑' : '↓' }}</span>
                        @endif
                    </button>
                </div>
            </div>

            {{-- Parte Derecha: Botón Crear (Solo Admin) --}}
            <div class="flex-shrink-0"> {{-- Evita que este botón crezca --}}
                @if (auth()->user()?->role == 'admin')
                    {{-- ¡Ajusta comprobación de rol! --}}
                    <button wire:click="openCreateAlbumModal" type="button"
                        class="inline-flex items-center justify-center w-full sm:w-auto px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition ease-in-out duration-150">
                        <svg class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path
                                d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                        </svg>
                        Crear Álbum
                    </button>
                @endif
            </div>

        </div>
        {{-- --------------------------------------------------------------------- --}}
        {{-- Rejilla de Álbumes                                                    --}}
        {{-- --------------------------------------------------------------------- --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @forelse ($albums as $album)
                {{-- Tarjeta de Álbum (Clickable) --}}
                <div wire:click="openModal({{ $album->id }})" wire:key="album-{{ $album->id }}"
                    class="cursor-pointer group bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-300 ease-in-out">
                    <div class="p-4 flex justify-between items-center"> {{-- Usar flex --}}
                        <h3
                            class="text-base font-semibold text-gray-800 truncate group-hover:text-blue-600 transition-colors pr-2">
                            {{ $album->name }}</h3>
                        {{-- Botón Editar visible para Admin o Dueño (¡Ajusta lógica rol!) --}}
                        @if (auth()->user() && (auth()->user()->role == 'admin' || $album->user_id == auth()->id()))
                            <button wire:click.stop="openEditAlbumModal({{ $album->id }})" {{-- LLAMA A openEditAlbumModal CON ID Y DETIENE PROPAGACIÓN --}}
                                type="button"
                                class="p-1 text-gray-400 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-blue-500 rounded-md transition-colors">
                                <span class="sr-only">Editar Álbum</span>
                                {{-- Icono Lápiz (Heroicons example) --}}
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                    </path>
                                </svg>
                            </button>
                        @endif
                    </div>
                    {{-- Contenedor de Imagen con Proporción --}}
                    <div class="aspect-video overflow-hidden bg-gray-100">
                        @if ($album->cover_image)
                            <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300 ease-in-out"
                                src="{{ $album->cover_image
                                    ? Storage::disk('albums')->url($album->cover_image)
                                    : asset('images/placeholder-fallback.jpg') }}"
                                alt="{{ $album->name }}" loading="lazy">
                        @else
                            {{-- Placeholder SVG si no hay imagen --}}
                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>
                        @endif
                    </div>
                    {{-- Información del Álbum --}}
                    <div class="p-4">
                        <h3
                            class="text-base font-semibold text-gray-800 truncate group-hover:text-blue-600 transition-colors">
                            {{ $album->name }}</h3>
                        {{-- Puedes añadir más info si quieres --}}
                        {{-- <p class="text-xs text-gray-500 mt-1">Creado: {{ $album->created_at->format('d/m/Y') }}</p> --}}
                    </div>
                </div>
            @empty
                {{-- Mensaje si no hay resultados --}}
                <div class="col-span-full text-center text-gray-500 py-16">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" aria-hidden="true">
                        <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No se encontraron álbumes</h3>
                    <p class="mt-1 text-sm text-gray-500">Intenta ajustar los términos de búsqueda.</p>
                </div>
            @endforelse {{-- Fin del forelse --}}
        </div>

        {{-- Paginación Álbumes --}}
        <div class="mt-8">
            {{ $albums->links(data: ['scrollTo' => false]) }}
        </div>

    </x-self.base>

    {{-- =================================================== --}}
    {{--           MODAL PARA GALERÍA DE FOTOS               --}}
    {{-- =================================================== --}}
    @if ($showModal && $selectedAlbum)
        <div x-data="{ show: @entangle('showModal') }" x-init="$watch('show', value => { if (!value) { @this.call('closeModal') } })" {{-- Asegura reset al cerrar con Alpine --}} x-show="show"
            @keydown.escape.window="show = false; @this.call('closeModal')" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-800/80 backdrop-blur-sm z-50 flex justify-center items-center p-4 overflow-y-auto"
            {{-- Quitamos wire:click.self para evitar problemas con subida de archivos --}}>
            {{-- Contenedor del Modal --}}
            <div class="bg-white rounded-2xl shadow-xl max-w-6xl w-full max-h-[95vh] flex flex-col"
                {{-- Aumentado max-w y max-h --}} @click.outside="show = false; @this.call('closeModal')" x-show="show"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

                {{-- Cabecera del Modal --}}
                <div class="flex justify-between items-center border-b border-gray-200 p-4 flex-shrink-0">
                    <h3 class="text-lg md:text-xl font-semibold text-gray-900 truncate pr-4">
                        {{ $selectedAlbum->name }}
                    </h3>
                    <div class="flex items-center space-x-3">
                        {{-- Botón Seleccionar / Cancelar --}}
                        @if (Auth::user()->role === 'admin')
                            <button wire:click="toggleSelectionMode"
                                class="px-3 py-1.5 text-xs font-semibold rounded-md shadow-sm transition-colors duration-150 {{ $selectionMode ? 'bg-gray-600 hover:bg-gray-700 focus:ring-gray-500' : 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500' }} text-white focus:outline-none focus:ring-2 focus:ring-offset-2"
                                wire:loading.attr="disabled">
                                {{ $selectionMode ? 'Cancelar Selección' : 'Seleccionar Fotos' }}
                            </button>
                        @endif

                        {{-- Botón Cerrar --}}
                        <button wire:click="closeModal" wire:loading.attr="disabled"
                            class="p-1.5 rounded-full text-gray-400 hover:bg-gray-100 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-gray-400 transition-colors"
                            aria-label="Cerrar modal">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Cuerpo del Modal (con scroll) --}}
                <div class="p-6 overflow-y-auto flex-grow">
                    {{-- Mensajes Flash/Error --}}
                    <div class="mb-4 space-y-2">
                        @if (session()->has('message'))
                            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                                class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded relative text-sm"
                                role="alert">{{ session('message') }}</div>
                        @endif
                        @if (session()->has('error'))
                            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                                class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded relative text-sm"
                                role="alert">{{ session('error') }}</div>
                        @endif
                        @error('delete_error')
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded relative text-sm"
                                role="alert">{{ $message }}</div>
                        @enderror
                    </div>
                    {{-- === ZONA PARA AÑADIR FOTOS === --}}
                    @if (Auth::user()->role === 'admin')
                        <div class="mb-6 border-b pb-4">
                            <h4 class="text-lg font-medium mb-2">Añadir Nuevas Fotos</h4>
                            <form wire:submit.prevent="savePhotos">
                                <input type="file" wire:model="uploadedPhotos" multiple
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 mb-2">

                                {{-- Indicador de carga para la subida --}}
                                <div wire:loading wire:target="uploadedPhotos">
                                    <span class="text-sm text-gray-500">Cargando previsualización...</span>
                                </div>

                                {{-- Previsualización simple (opcional) --}}
                                @if ($uploadedPhotos)
                                    <div class="mt-2 text-sm text-gray-600">Previsualización:</div>
                                    <div class="flex flex-wrap gap-2 mt-1">
                                        @foreach ($uploadedPhotos as $uploadedPhoto)
                                            <img src="{{ $uploadedPhoto->temporaryUrl() }}"
                                                class="h-16 w-16 object-cover rounded">
                                        @endforeach
                                    </div>
                                @endif

                                {{-- Mostrar errores de validación --}}
                                @error('uploadedPhotos.*')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror

                                {{-- Botón para guardar (se activa si hay archivos) --}}
                                @if ($uploadedPhotos)
                                    <button type="submit"
                                        class="mt-3 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded hover:bg-blue-700 disabled:opacity-50"
                                        wire:loading.attr="disabled" wire:target="savePhotos">
                                        <span wire:loading wire:target="savePhotos">Guardando...</span>
                                        <span wire:loading.remove wire:target="savePhotos">Guardar Fotos</span>
                                    </button>
                                @endif
                            </form>
                        </div>
                    @endif

                    {{-- === FIN ZONA AÑADIR FOTOS === --}}

                    {{-- Título de Galería y Botón Borrar --}}
                    <div class="flex justify-between items-center mb-4 border-t border-gray-200 pt-4">
                        <h4 class="text-base font-semibold text-gray-800">Fotos del Álbum</h4>
                        @if (!empty($selectedPhotos) && $selectionMode)
                            <button wire:click="deleteSelectedPhotos"
                                wire:confirm="¿Estás seguro de eliminar las {{ count($selectedPhotos) }} fotos seleccionadas? Esta acción no se puede deshacer."
                                class="px-3 py-1.5 bg-red-600 text-white text-xs font-semibold rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50"
                                wire:loading.attr="disabled" wire:target="deleteSelectedPhotos">
                                <span wire:loading.remove wire:target="deleteSelectedPhotos">Eliminar
                                    ({{ count($selectedPhotos) }})</span>
                                {{-- Icono loading para borrado --}}
                                <svg wire:loading wire:target="deleteSelectedPhotos"
                                    class="animate-spin inline-block h-4 w-4 text-white" fill="none"
                                    viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                <span wire:loading wire:target="deleteSelectedPhotos">Eliminando...</span>
                            </button>
                        @endif
                    </div>

                    {{-- Galería de Fotos Existentes --}}
                    @if ($photosInModal && $photosInModal->total() > 0) {{-- Comprobar con ->total() por paginación --}}
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 mb-6">
                            {{-- Reducido gap --}}
                            @foreach ($photosInModal as $photo)
                                {{-- Contenedor Foto --}}
                                <div class="relative group aspect-square rounded-lg shadow-sm overflow-hidden border
                                        {{-- Borde azul si está seleccionada y en modo selección --}}
                                        {{ $selectionMode && in_array($photo->id, $selectedPhotos) ? 'border-blue-500 border-2' : 'border-transparent hover:border-gray-300' }}
                                        {{-- Cambiar cursor según modo --}}
                                        @if ($selectionMode) cursor-pointer @else cursor-pointer @endif {{-- Siempre pointer ahora? O zoom-in si no seleccionas? --}}
                                        transition-all duration-150"
                                    wire:key="modal-photo-{{ $photo->id }}" {{-- DOBLE CLIC: Siempre abre el visor grande --}}
                                    wire:dblclick="viewPhoto({{ $photo->id }})" {{-- CLIC SIMPLE: Llama a toggleLike O toggleSelection según el modo --}}
                                    wire:click="{{ $selectionMode ? 'toggleSelection(' . $photo->id . ')' : 'toggleLike(' . $photo->id . ')' }}">
                                    {{-- Imagen (Usa thumbnail) --}}
                                    <img src="{{ $photo->thumbnail_path && Storage::disk('albums')->exists($photo->thumbnail_path)
                                        ? Storage::disk('albums')->url($photo->thumbnail_path)
                                        : ($photo->file_path && Storage::disk('albums')->exists($photo->file_path)
                                            ? Storage::disk('albums')->url($photo->file_path)
                                            : asset('images/placeholder-photo.png')) }}"
                                        alt="Foto {{ $photo->id }}" loading="lazy"
                                        class="block w-full h-full object-cover pointer-events-none">

                                    {{-- Indicador Selección (Tick - solo visible en modo selección) --}}
                                    @if ($selectionMode && in_array($photo->id, $selectedPhotos))
                                        <div class="absolute top-1.5 right-1.5 z-20 bg-blue-600 text-white rounded-full p-0.5 shadow pointer-events-none"
                                            title="Seleccionada">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                stroke-width="3" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                    @endif

                                    {{-- Indicador Like (Corazón - siempre visible si tiene like) --}}
                                    @if ($photo->liked_by_current_user)
                                        <div class="absolute bottom-1.5 left-1.5 z-10 p-1 bg-black/50 backdrop-blur-sm rounded-full pointer-events-none"
                                            title="Te gusta">
                                            <svg class="w-3.5 h-3.5 text-red-500" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    @endif
                                </div> {{-- Fin Contenedor Foto --}}
                            @endforeach
                        </div>
                        {{-- Paginación Fotos Modal --}}
                        <div class="mt-4">
                            {{ $photosInModal->links(data: ['scrollTo' => false]) }}
                        </div>
                    @else
                        {{-- Mensaje si no hay fotos --}}
                        <div class="text-center text-gray-500 py-10">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm16.5-1.875a.375.375 0 1 1-1.5.75.375.375 0 0 1 1.5-.75Zm0 0a.375.375 0 1 0 0-1.5.375.375 0 0 0 0 1.5Z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Álbum Vacío</h3>
                            <p class="mt-1 text-sm text-gray-500">Aún no has añadido fotos a este álbum.</p>
                        </div>
                    @endif
                </div> {{-- Fin Cuerpo del Modal --}}

                {{-- Pie del Modal --}}
                <div class="border-t border-gray-200 p-4 text-right bg-gray-50 rounded-b-lg flex-shrink-0">
                    <button wire:click="closeModal" wire:loading.attr="disabled"
                        class="px-4 py-2 bg-white text-gray-700 rounded-md border border-gray-300 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors text-sm font-medium">Cerrar</button>
                </div>
            </div>
        </div>
    @endif


    @if ($showCreateAlbumModal)
        <div x-data="{ showCreate: @entangle('showCreateAlbumModal') }" x-show="showCreate"
            @keydown.escape.window="showCreate = false; @this.call('closeCreateAlbumModal')"
            x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-800/80 backdrop-blur-sm z-50 flex justify-center items-center p-4 overflow-y-auto"
            aria-labelledby="modal-create-album-title" role="dialog" aria-modal="true">
            {{-- Contenedor del Modal --}}
            <div class="bg-white rounded-lg shadow-xl max-w-lg w-full flex flex-col"
                @click.outside="showCreate = false; @this.call('closeCreateAlbumModal')" x-show="showCreate"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                <form wire:submit.prevent="createAlbum">
                    {{-- Cabecera --}}
                    <div class="flex justify-between items-center border-b border-gray-200 p-4 sm:px-6 flex-shrink-0">
                        <h3 class="text-lg font-semibold text-gray-900" id="modal-create-album-title">Crear Nuevo
                            Álbum</h3>
                        <button type="button" wire:click="closeCreateAlbumModal" class="..."
                            aria-label="Cerrar modal"> {{-- Estilos botón cerrar --}}
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    {{-- Cuerpo (Formulario) --}}
                    <div class="p-6 space-y-4 overflow-y-auto">
                        {{-- Nombre --}}
                        <div>
                            <label for="newAlbumName" class="block text-sm font-medium text-gray-700">Nombre del
                                Álbum</label>
                            <input type="text" id="newAlbumName" wire:model="newAlbumName"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 ... @error('newAlbumName') border-red-500 @enderror">
                            @error('newAlbumName')
                                <span class="text-xs text-red-600 mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        {{-- Descripción --}}
                        <div>
                            <label for="newAlbumDescription"
                                class="block text-sm font-medium text-gray-700">Descripción (Opcional)</label>
                            <textarea id="newAlbumDescription" wire:model="newAlbumDescription" rows="3"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 ... @error('newAlbumDescription') border-red-500 @enderror"></textarea>
                            @error('newAlbumDescription')
                                <span class="text-xs text-red-600 mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        {{-- Tipo --}}
                        <div>
                            <label for="newAlbumType" class="block text-sm font-medium text-gray-700">Tipo de
                                Álbum</label>
                            <select id="newAlbumType" wire:model.live="newAlbumType"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 ... @error('newAlbumType') border-red-500 @enderror">
                                <option value="public">Público</option>
                                <option value="private">Privado (Solo Admin)</option>
                                <option value="client">Cliente</option>
                            </select>
                            @error('newAlbumType')
                                <span class="text-xs text-red-600 mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- === NUEVO: Selector de Cliente (Condicional) === --}}
                        @if ($newAlbumType === 'client')
                            <div x-data x-transition> {{-- Añade transición suave si quieres --}}
                                <label for="newAlbumClientId" class="block text-sm font-medium text-gray-700">Asignar
                                    a Cliente</label>
                                <select id="newAlbumClientId" wire:model="newAlbumClientId"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 ... @error('newAlbumClientId') border-red-500 @enderror">
                                    <option value="">-- Selecciona un cliente --</option>
                                    {{-- Iterar sobre la lista de clientes cargada en el componente --}}
                                    @forelse($usuarios as $client)
                                        <option value="{{ $client->id }}">{{ $client->name }}
                                            ({{ $client->email }})
                                        </option>
                                    @empty
                                        <option value="" disabled>No hay clientes disponibles</option>
                                    @endforelse
                                </select>
                                @error('newAlbumClientId')
                                    <span class="text-xs text-red-600 mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif
                        {{-- === FIN Selector de Cliente === --}}


                        {{-- === NUEVO: Input para Imagen de Portada === --}}
                        <div>
                            <label for="newAlbumCover" class="block text-sm font-medium text-gray-700">Imagen de
                                Portada (Opcional)</label>
                            <input type="file" id="newAlbumCover" wire:model="newAlbumCover"
                                class="mt-1 block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border file:border-solid file:border-gray-300 file:text-sm file:font-semibold file:bg-gray-50 file:text-gray-700 hover:file:bg-gray-100 file:cursor-pointer cursor-pointer">
                            <div wire:loading wire:target="newAlbumCover" class="mt-1 text-xs text-blue-600">Cargando
                                portada...</div>
                            {{-- Previsualización simple de portada --}}
                            @if ($newAlbumCover && !$errors->has('newAlbumCover'))
                                <div class="mt-2">
                                    <img src="{{ $newAlbumCover->temporaryUrl() }}" alt="Previsualización Portada"
                                        class="h-20 w-auto object-cover rounded border shadow-sm">
                                </div>
                            @endif
                            @error('newAlbumCover')
                                <span class="text-xs text-red-600 mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        {{-- === FIN Input Imagen Portada === --}}

                    </div>

                    {{-- Pie (Botones) --}}
                    <div
                        class="border-t border-gray-200 p-4 bg-gray-50 rounded-b-lg flex justify-end space-x-3 flex-shrink-0">
                        <button type="button" wire:click="closeCreateAlbumModal"
                            class="px-4 py-2 bg-white ...">Cancelar</button>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 ..."
                            wire:loading.attr="disabled" wire:target="createAlbum">
                            <svg wire:loading wire:target="createAlbum" ...></svg>
                            <span wire:loading wire:target="createAlbum">Creando...</span>
                            <span wire:loading.remove wire:target="createAlbum">Crear Álbum</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif


    {{-- =================================================== --}}
    {{--        MODAL VISOR FOTO GRANDE (MODIFICADO)         --}}
    {{-- =================================================== --}}
    @if ($showPhotoViewer && $viewingPhoto)
        <div {{-- Usamos variable Alpine diferente y @entangle --}} x-data="{ showPhotoViewerState: @entangle('showPhotoViewer') }" x-show="showPhotoViewerState" {{-- Listener Escape: Llama a closePhotoViewer solo si este modal está activo --}}
            @keydown.escape.window="if(showPhotoViewerState) { showPhotoViewerState = false; @this.call('closePhotoViewer') }"
            x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black/90 backdrop-blur-md z-[60] flex items-center justify-center p-4 sm:p-8"
            {{-- Cerrar SOLO el visor al hacer clic en el fondo --}} wire:click.self="closePhotoViewer" role="dialog" aria-modal="true"
            aria-label="Visor de Fotos">

            {{-- Contenedor principal del visor --}}
            <div class="relative max-w-full max-h-full bg-transparent" {{-- Contenedor solo para imagen y botones --}} {{-- Detener propagación de clics para que no afecte al @click.outside del modal padre --}}
                @click.stop {{-- Click outside específico para este modal --}}
                @click.outside="if(showPhotoViewerState) { showPhotoViewerState = false; @this.call('closePhotoViewer') }"
                {{-- Transiciones Alpine para el contenido del visor --}} x-show="showPhotoViewerState" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95">

                {{-- Imagen Grande --}}
                <img src="{{ $viewingPhoto->file_path && Storage::disk('albums')->exists($viewingPhoto->file_path)
                    ? Storage::disk('albums')->url($viewingPhoto->file_path)
                    : asset('images/placeholder-photo-large.png') }}"
                    alt="Foto {{ $viewingPhoto->id }} del álbum {{ $selectedAlbum->name }}"
                    class="block max-w-full max-h-[88vh] object-contain rounded-lg shadow-lg pointer-events-none"
                    {{-- pointer-events-none si los botones están encima --}}>

                {{-- Botón de cerrar (llama a closePhotoViewer) --}}
                <button wire:click="closePhotoViewer" wire:loading.attr="disabled"
                    class="absolute top-0 right-0 z-[70] m-4 p-2 rounded-full text-white/70 bg-black/40 hover:bg-black/60 focus:outline-none focus:ring-2 focus:ring-white transition-colors"
                    aria-label="Cerrar visor">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>

                {{-- Botón Anterior (Estilo Sugerido 1) --}}
                @if ($this->previousPhotoId)
                    <button wire:click="viewPreviousPhoto" wire:loading.attr="disabled"
                        wire:target="viewPreviousPhoto, viewNextPhoto"
                        class="absolute left-2 sm:left-4 top-1/2 -translate-y-1/2 z-[70] p-2 rounded-full text-white/90 bg-black/25 hover:bg-black/50 focus:outline-none focus:ring-2 focus:ring-white focus:ring-opacity-75 transition-all duration-200 ease-in-out hover:scale-105"
                        aria-label="Foto Anterior">
                        {{-- Icono loading (ajusta tamaño si cambias flecha) --}}
                        <svg wire:loading wire:target="viewPreviousPhoto" class="animate-spin h-6 w-6 text-white"
                            fill="none" viewBox="0 0 24 24">...</svg>
                        {{-- Icono Flecha (más pequeño) --}}
                        <svg wire:loading.remove wire:target="viewPreviousPhoto" class="w-6 h-6" fill="none"
                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                @endif

                {{-- Botón Siguiente (Estilo Sugerido 1) --}}
                @if ($this->nextPhotoId)
                    <button wire:click="viewNextPhoto" wire:loading.attr="disabled"
                        wire:target="viewPreviousPhoto, viewNextPhoto"
                        class="absolute right-2 sm:right-4 top-1/2 -translate-y-1/2 z-[70] p-2 rounded-full text-white/90 bg-black/25 hover:bg-black/50 focus:outline-none focus:ring-2 focus:ring-white focus:ring-opacity-75 transition-all duration-200 ease-in-out hover:scale-105"
                        aria-label="Foto Siguiente">
                        {{-- Icono loading (ajusta tamaño si cambias flecha) --}}
                        <svg wire:loading wire:target="viewNextPhoto" class="animate-spin h-6 w-6 text-white"
                            fill="none" viewBox="0 0 24 24">...</svg>
                        {{-- Icono Flecha (más pequeño) --}}
                        <svg wire:loading.remove wire:target="viewNextPhoto" class="w-6 h-6" fill="none"
                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                @endif

            </div> {{-- Fin contenedor principal del visor --}}
        </div> {{-- Fin fondo/overlay del visor --}}
    @endif
    {{-- =================================================== --}}
    {{--           MODAL PARA EDITAR ÁLBUM (MEJORADO)        --}}
    {{-- =================================================== --}}
    @if ($showEditAlbumModal && $editingAlbum)
        <div {{-- Controladores y Transiciones AlpineJS (igual que antes) --}} x-data="{ showEdit: @entangle('showEditAlbumModal') }" x-show="showEdit"
            @keydown.escape.window="showEdit = false; @this.call('closeEditAlbumModal')"
            x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-800/80 backdrop-blur-sm z-50 flex justify-center items-center p-4 overflow-y-auto"
            aria-labelledby="modal-edit-album-title" role="dialog" aria-modal="true">
            {{-- Contenedor del Modal --}}
            <div class="bg-white rounded-lg shadow-xl max-w-lg w-full flex flex-col"
                @click.outside="showEdit = false; @this.call('closeEditAlbumModal')" x-show="showEdit"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                {{-- Formulario que llama al método updateAlbum de Livewire --}}
                <form wire:submit.prevent="updateAlbum">
                    {{-- Cabecera del Modal --}}
                    <div class="flex justify-between items-center border-b border-gray-200 p-4 sm:px-6 flex-shrink-0">
                        <h3 class="text-lg font-semibold text-gray-900" id="modal-edit-album-title">Editar Álbum</h3>
                        {{-- Botón Cerrar (Estilo mejorado) --}}
                        <button type="button" wire:click="closeEditAlbumModal"
                            class="p-1.5 rounded-full text-gray-400 hover:bg-gray-100 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-gray-400 transition-colors"
                            aria-label="Cerrar modal">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    {{-- Cuerpo del Modal (Formulario) --}}
                    <div class="p-6 space-y-5 overflow-y-auto"> {{-- Aumentado space-y --}}
                        {{-- Campo Nombre --}}
                        <div>
                            <label for="editAlbumName"
                                class="block text-sm font-medium leading-6 text-gray-900">Nombre del Álbum</label>
                            <div class="mt-1">
                                <input type="text" id="editAlbumName" wire:model="editAlbumName"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('editAlbumName') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror">
                            </div>
                            @error('editAlbumName')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Campo Descripción --}}
                        <div>
                            <label for="editAlbumDescription"
                                class="block text-sm font-medium leading-6 text-gray-900">Descripción
                                (Opcional)</label>
                            <div class="mt-1">
                                <textarea id="editAlbumDescription" wire:model="editAlbumDescription" rows="4"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('editAlbumDescription') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror"></textarea>
                            </div>
                            @error('editAlbumDescription')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Campo Tipo --}}
                        <div>
                            <label for="editAlbumType"
                                class="block text-sm font-medium leading-6 text-gray-900">Tipo</label>
                            <select id="editAlbumType" wire:model.live="editAlbumType" {{-- .live para que el select de cliente aparezca/desaparezca al instante --}}
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('editAlbumType') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror">
                                <option value="public">Público</option>
                                <option value="private">Privado (Solo Admin)</option>
                                <option value="client">Cliente</option>
                            </select>
                            @error('editAlbumType')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Selector de Cliente (Condicional) --}}
                        {{-- === NUEVO: Selector de Cliente (Condicional) === --}}
                        @if ($editAlbumType === 'client')
                            <div x-data x-transition> {{-- Añade transición suave si quieres --}}
                                <label for="editAlbumClientId" class="block text-sm font-medium text-gray-700">Asignar
                                    a Cliente</label>
                                <select id="editAlbumClientId" wire:model="editAlbumClientId"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 ... @error('editAlbumClientId') border-red-500 @enderror">
                                    <option value="">-- Selecciona un cliente --</option>
                                    {{-- Iterar sobre la lista de clientes cargada en el componente --}}
                                    @forelse($usuarios as $client)
                                        <option value="{{ $client->id }}">{{ $client->name }}
                                            ({{ $client->email }})
                                        </option>
                                    @empty
                                        <option value="" disabled>No hay clientes disponibles</option>
                                    @endforelse
                                </select>
                                @error('editAlbumClientId')
                                    <span class="text-xs text-red-600 mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif
                        {{-- Usamos x-show y transition para mostrar/ocultar suavemente con Alpine --}}
                        <div x-data="{ showClientSelect: @entangle('editAlbumType').live === 'client' }" x-show="showClientSelect" x-transition>
                            <label for="editAlbumClientId"
                                class="block text-sm font-medium leading-6 text-gray-900">Asignar a Cliente</label>
                            <select id="editAlbumClientId" wire:model="editAlbumClientId"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('editAlbumClientId') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror">
                                <option value="">-- Selecciona un cliente --</option>
                                @forelse($clients as $client)
                                    <option value="{{ $client->id }}">{{ $client->name }} ({{ $client->email }})
                                    </option> {{-- Mostramos email para diferenciar --}}
                                @empty
                                    <option value="" disabled>No hay clientes registrados</option>
                                @endforelse
                            </select>
                            @error('editAlbumClientId')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</span>
                                @enderror
                        </div>

                        {{-- Imagen de Portada --}}
                        <div>
                            <label for="editAlbumNewCover"
                                class="block text-sm font-medium leading-6 text-gray-900">Cambiar Imagen de Portada
                                (Opcional)</label>
                            <div class="mt-2 flex items-center gap-x-3">
                                {{-- Previsualización Portada Actual o Nueva --}}
                                @php
                                    $coverPreviewUrl =
                                        $editAlbumNewCover && !$errors->has('editAlbumNewCover')
                                            ? $editAlbumNewCover->temporaryUrl()
                                            : ($editAlbumCurrentCover
                                                ? Storage::url($editAlbumCurrentCover)
                                                : null);
                                @endphp
                                @if ($coverPreviewUrl)
                                    <img src="{{ $coverPreviewUrl }}" alt="Portada"
                                        class="h-16 w-16 object-cover rounded-md border border-gray-200 shadow-sm">
                                @else
                                    {{-- Placeholder si no hay imagen ni nueva ni actual --}}
                                    <div
                                        class="h-16 w-16 bg-gray-100 rounded-md flex items-center justify-center text-gray-400 border">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                    </div>
                                @endif
                                {{-- Input File (usamos label como botón) --}}
                                <div>
                                    <label for="editAlbumNewCover"
                                        class="cursor-pointer rounded-md bg-white px-2.5 py-1.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                        <span>{{ $editAlbumCurrentCover || $editAlbumNewCover ? 'Cambiar' : 'Subir imagen' }}</span>
                                        <input id="editAlbumNewCover" wire:model="editAlbumNewCover" type="file"
                                            class="sr-only"> {{-- Ocultamos el input por defecto --}}
                                    </label>
                                    {{-- Botón para quitar la imagen seleccionada (si hay una previsualizada) --}}
                                    @if ($editAlbumNewCover)
                                        <button type="button" wire:click="$set('editAlbumNewCover', null)"
                                            class="ml-2 text-xs text-red-600 hover:text-red-800">(Quitar)</button>
                                    @endif
                                </div>
                            </div>
                            <div wire:loading wire:target="editAlbumNewCover" class="mt-1 text-xs text-blue-600">
                                Cargando...</div>
                            @error('editAlbumNewCover')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                    </div> {{-- Fin Cuerpo Modal --}}

                    {{-- Pie del Modal (Botones) --}}
                    <div
                        class="border-t border-gray-200 p-4 bg-gray-50 rounded-b-lg flex justify-end space-x-3 flex-shrink-0">
                        {{-- Botón Cancelar (Estilo secundario) --}}
                        <button type="button" wire:click="closeEditAlbumModal"
                            class="px-4 py-2 bg-white text-gray-700 rounded-md border border-gray-300 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 transition-colors text-sm font-medium">
                            Cancelar
                        </button>
                        {{-- Botón Actualizar (Estilo primario) --}}
                        <button type="submit"
                            class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
                            wire:loading.attr="disabled" wire:target="updateAlbum">
                            {{-- Icono de carga --}}
                            <svg wire:loading wire:target="updateAlbum"
                                class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span wire:loading wire:target="updateAlbum">Actualizando...</span>
                            <span wire:loading.remove wire:target="updateAlbum">Actualizar Álbum</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif


</div> {{-- Fin Div principal del componente --}}
