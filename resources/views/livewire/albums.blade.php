{{-- ========================================================================= --}}
{{--  Archivo: resources/views/livewire/albums.blade.php                     --}}
{{--  Descripción: Vista completa para el componente Livewire 'Albums',       --}}
{{--               incluyendo búsqueda, rejilla de álbumes, modal con        --}}
{{--               galería de fotos, subida, selección múltiple, borrado y like.--}}
{{-- ========================================================================= --}}

{{-- Aplicamos fondo negro a todo el componente y texto base claro --}}
<div class="bg-black text-gray-300 min-h-screen p-4 sm:p-6 lg:p-8 selection:bg-indigo-500 selection:text-white">

    {{-- ========================================================================= --}}
    {{-- Barra Superior: Búsqueda, Ordenación y Botón Crear (MODIFICADA)         --}}
    {{-- ========================================================================= --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 md:gap-6 mt-12 mb-8 p-5 sm:p-6 rounded-xl bg-gray-700/15 backdrop-blur-md border border-gray-700/50 shadow-lg">
        {{-- Parte Izquierda/Principal: Búsqueda y Ordenación --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-4 gap-4 sm:gap-0 flex-grow min-w-0">

            {{-- Barra de Búsqueda de Álbumes --}}
            <div class="relative flex-shrink-0 w-full sm:w-72 md:w-80">
                <label for="albumSearchModern" class="sr-only">Buscar álbumes</label>
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none" aria-hidden="true">
                    <svg class="h-5 w-5 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <input wire:model.live.debounce.300ms="cadena" type="search" id="albumSearchModern"
                    placeholder="Buscar álbum..."
                    class="block w-full pl-10 pr-4 py-2.5 border border-gray-600/80 rounded-lg leading-5 bg-gray-700/80 text-gray-200 placeholder-gray-400 focus:outline-none focus:placeholder-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm shadow-md transition duration-150 ease-in-out">
            </div>

            {{-- Controles de Ordenación (Estilo Botones/Pills) --}}
            <div class="flex items-center space-x-2 pt-2 sm:pt-0">
                <span class="text-sm font-medium text-gray-400 hidden sm:inline">Ordenar por:</span>
                {{-- Botón Fecha --}}
                <button wire:click="sortBy('created_at')" type="button" title="Ordenar por Fecha"
                    class="px-3.5 py-1.5 text-xs font-medium rounded-full transition-all duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-700/15 focus:ring-indigo-500
                    {{ $campo === 'created_at' ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-300 bg-gray-600/70 hover:bg-gray-500/70 hover:text-white' }}">
                    Fecha @if ($campo === 'created_at')
                        <span>{{ $order === 'asc' ? '↑' : '↓' }}</span>
                    @endif
                </button>
                {{-- Botón Nombre --}}
                <button wire:click="sortBy('name')" type="button" title="Ordenar por Nombre"
                    class="px-3.5 py-1.5 text-xs font-medium rounded-full transition-all duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-700/15 focus:ring-indigo-500
                    {{ $campo === 'name' ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-300 bg-gray-600/70 hover:bg-gray-500/70 hover:text-white' }}">
                    Nombre @if ($campo === 'name')
                        <span>{{ $order === 'asc' ? '↑' : '↓' }}</span>
                    @endif
                </button>
                {{-- Botón Tipo --}}
                <button wire:click="sortBy('type')" type="button" title="Ordenar por Tipo"
                    class="px-3.5 py-1.5 text-xs font-medium rounded-full transition-all duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-700/15 focus:ring-indigo-500
                    {{ $campo === 'type' ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-300 bg-gray-600/70 hover:bg-gray-500/70 hover:text-white' }}">
                    Tipo @if ($campo === 'type')
                        <span>{{ $order === 'asc' ? '↑' : '↓' }}</span>
                    @endif
                </button>
            </div>
        </div>

        {{-- Parte Derecha: Botón Crear (Solo Admin) --}}
        <div class="flex-shrink-0">
            @if (auth()->user()?->role == 'admin')
                <button wire:click="openCreateAlbumModal" type="button"
                    class="inline-flex items-center justify-center w-full sm:w-auto px-5 py-2.5 bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-500 text-white text-sm font-semibold rounded-lg shadow-lg hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-700/15 focus:ring-indigo-500 transition-all ease-in-out duration-200 transform hover:scale-105">
                    <svg class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path
                            d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                    </svg>
                    Crear Álbum
                </button>
            @endif
        </div>
    </div>

    {{-- Global Flash Messages --}}
    <div class="my-4 px-5 sm:px-6 space-y-3">
        @if (session()->has('message'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4500)"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="bg-green-500/20 border border-green-500/50 text-green-200 px-4 py-3 rounded-lg relative text-sm shadow-md" role="alert">
                <div class="flex">
                    <div class="py-1"><svg class="fill-current h-6 w-6 text-green-400 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM6.7 9.29L9 11.6l4.3-4.3 1.4 1.42L9 14.4l-3.7-3.7 1.4-1.42z"/></svg></div>
                    <div>
                        <strong class="font-semibold">✓ Éxito:</strong>
                        <span class="block sm:inline">{{ session('message') }}</span>
                    </div>
                    <button @click="show = false" class="absolute top-0 bottom-0 right-0 px-4 py-3 text-green-200 hover:text-white ml-auto">
                        <svg class="fill-current h-6 w-6" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Cerrar</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                    </button>
                </div>
            </div>
        @endif
        @if (session()->has('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="bg-red-500/20 border border-red-500/50 text-red-200 px-4 py-3 rounded-lg relative text-sm shadow-md" role="alert">
                <div class="flex">
                    <div class="py-1"><svg class="fill-current h-6 w-6 text-red-400 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 5h2v6H9V5zm0 8h2v2H9v-2z"/></svg></div>
                    <div>
                        <strong class="font-semibold">✗ Error:</strong>
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                    <button @click="show = false" class="absolute top-0 bottom-0 right-0 px-4 py-3 text-red-200 hover:text-white ml-auto">
                        <svg class="fill-current h-6 w-6" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Cerrar</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                    </button>
                </div>
            </div>
        @endif
    </div>

    {{-- --------------------------------------------------------------------- --}}
    {{-- Rejilla de Álbumes (DISEÑO DE TARJETA CUADRADA CON PIE TRANSLÚCIDO)   --}}
    {{-- --------------------------------------------------------------------- --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-6 gap-y-8 p-5 sm:p-6">
        @forelse ($albums as $album)
            <div wire:click="openModal({{ $album->id }})"
                 class="group aspect-square relative cursor-pointer overflow-hidden rounded-xl shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-black focus:ring-indigo-500 transition-all duration-300 ease-in-out hover:shadow-indigo-500/50 transform hover:-translate-y-1.5 hover:scale-[1.02]">

                {{-- Imagen de Fondo --}}
                @if ($album->cover_image && Storage::disk('albums')->exists($album->cover_image))
                    <img class="absolute inset-0 w-full h-full object-cover transition-transform duration-300 ease-in-out group-hover:scale-110"
                        src="{{ Storage::disk('albums')->url($album->cover_image) }}"
                        alt="Portada del álbum {{ $album->name }}" loading="lazy">
                @else
                    <div class="absolute inset-0 w-full h-full flex items-center justify-center bg-gray-700 text-gray-500">
                        <svg class="w-1/3 h-1/3 opacity-50" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                @endif

                {{-- Degradado General para Contraste del Texto Principal --}}
                <div class="absolute inset-0 bg-gradient-to-b from-black/50 via-transparent to-black/60 transition-all duration-300 group-hover:from-black/60 group-hover:to-black/70"></div>

                {{-- Contenido de Texto Superpuesto Principal (Centrado) --}}
                <div class="absolute inset-0 flex flex-col items-center justify-center p-4 sm:p-6 text-center pointer-events-none">
                    <h3 class="text-2xl sm:text-3xl lg:text-4xl font-black text-white uppercase tracking-wide leading-tight" style="text-shadow: 2px 2px 8px rgba(0,0,0,0.8);">
                        {{ Str::limit($album->name, 20) }}
                    </h3>
                    <p class="mt-1.5 text-xs sm:text-sm text-gray-100 uppercase tracking-wider font-semibold" style="text-shadow: 1px 1px 4px rgba(0,0,0,0.8);">
                        @if ($album->type === 'client' && $album->clientUser)
                            {{ Str::limit($album->clientUser->name, 18) }}
                        @else
                            {{ ucfirst($album->type ?? 'Galería') }}
                        @endif
                    </p>
                </div>

                {{-- Pie de Álbum Translúcido (Información Adicional) --}}
                <div class="absolute bottom-0 left-0 right-0 p-3 sm:p-4 bg-black/70 backdrop-blur-md text-left pointer-events-none transition-all duration-300 opacity-90 group-hover:opacity-100">
                    <h4 class="text-sm sm:text-md font-semibold text-white truncate" title="{{ $album->name }}">
                        {{ $album->name }}
                    </h4>
                    <p class="text-xs text-gray-300 mt-0.5 truncate" title="{{ $album->description ?: ($album->photos_count . ' Fotos') }}">
                        {{ $album->description ? Str::limit($album->description, 35) : ($album->photos_count . ' Foto(s)  ·  ' . $album->created_at->translatedFormat('d M Y')) }}
                    </p>
                </div>

                {{-- Contenedor para botones de acción (Editar y Eliminar) --}}
                <div class="absolute top-2.5 right-2.5 z-30 flex flex-col space-y-2 opacity-0 group-hover:opacity-100 focus-within:opacity-100 transition-opacity duration-300 pointer-events-none group-focus-within:opacity-100">
                    {{-- Botón de Editar --}}
                    @if (auth()->user() && (auth()->user()->role == 'admin' || $album->user_id == auth()->id()))
                        <button wire:click.stop="openEditAlbumModal({{ $album->id }})"
                            type="button"
                            class="p-2 bg-black/60 backdrop-blur-sm text-white rounded-full hover:bg-indigo-600 focus:outline-none focus:ring-2 ring-offset-2 ring-offset-black focus:ring-indigo-500 pointer-events-auto transition-all"
                            title="Editar Álbum">
                            <span class="sr-only">Editar Álbum</span>
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                            </svg>
                        </button>
                    @endif

                    {{-- Botón de Eliminar (Solo Admin o dueño, según lógica en PHP) --}}
                    @if (auth()->user() && (auth()->user()->role == 'admin' || $album->user_id == auth()->id()))
                        <button wire:click.stop="deleteAlbum({{ $album->id }})"
                                wire:confirm.prompt="¿Estás SEGURO de eliminar el álbum '{{ addslashes(htmlspecialchars($album->name)) }}' y TODAS sus fotos?\n\n¡Esta acción NO SE PUEDE DESHACER!\n\nEscribe 'ELIMINAR' para confirmar.|ELIMINAR"
                                type="button"
                                class="p-2 bg-black/60 backdrop-blur-sm text-white rounded-full hover:bg-red-600 focus:outline-none focus:ring-2 ring-offset-2 ring-offset-black focus:ring-red-500 pointer-events-auto transition-all"
                                title="Eliminar Álbum">
                            <span class="sr-only">Eliminar Álbum</span>
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full text-center text-gray-500 py-20">
                <svg class="mx-auto h-16 w-16 text-gray-600" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" aria-hidden="true">
                    <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="1.5"
                        d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                </svg>
                <h3 class="mt-4 text-lg font-semibold text-gray-400">No se encontraron álbumes</h3>
                <p class="mt-2 text-sm text-gray-500">Intenta ajustar los términos de búsqueda o crea un nuevo álbum.</p>
            </div>
        @endforelse
    </div>

    {{-- Paginación Álbumes --}}
    @if ($albums->hasPages())
    <div class="mt-8 p-5 sm:p-6 rounded-xl bg-gray-700/15 backdrop-blur-md border border-gray-700/50 shadow-lg">
        {{ $albums->links('vendor.livewire.tailwind-dark') }}
    </div>
    @endif

    {{-- =================================================== --}}
    {{--           MODAL PARA GALERÍA DE FOTOS               --}}
    {{-- =================================================== --}}
    @if ($showModal && $selectedAlbum)
        <div x-data="{ show: @entangle('showModal').live }" x-init="$watch('show', value => { if (!value) { @this.call('closeModal') } })" x-show="show"
            @keydown.escape.window="show = false; @this.call('closeModal')" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black/80 backdrop-blur-md z-50 flex justify-center items-center p-4 overflow-y-auto">
            <div class="bg-gray-850 text-gray-300 rounded-xl shadow-2xl max-w-6xl w-full max-h-[95vh] flex flex-col"
                @click.outside="show = false; @this.call('closeModal')" x-show="show"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

                <div class="flex justify-between items-center border-b border-gray-700 p-5 flex-shrink-0">
                    <h3 class="text-xl md:text-2xl font-semibold text-white truncate pr-4">
                        {{ $selectedAlbum->name }}
                    </h3>
                    <div class="flex items-center space-x-3">
                        @if (Auth::check() && Auth::user()->role === 'admin') {{-- Auth::check() para seguridad --}}
                            <button wire:click="toggleSelectionMode"
                                class="px-3.5 py-1.5 text-xs font-semibold rounded-lg shadow-md transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-850
                                {{ $selectionMode ? 'bg-gray-600 hover:bg-gray-500 focus:ring-gray-400 text-white' : 'bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500 text-white' }}"
                                wire:loading.attr="disabled">
                                {{ $selectionMode ? 'Cancelar Selección' : 'Seleccionar Fotos' }}
                            </button>
                        @endif
                        <button wire:click="closeModal" wire:loading.attr="disabled"
                            class="p-2 rounded-full text-gray-400 hover:bg-gray-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-offset-gray-850 focus:ring-gray-500 transition-colors"
                            aria-label="Cerrar modal">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="p-6 overflow-y-auto flex-grow">
                    <div class="mb-4 space-y-3">
                        @if (session()->has('message'))
                            <div x-data="{ showMsg: true }" x-show="showMsg" x-init="setTimeout(() => showMsg = false, 3500)"
                                class="bg-green-500/20 border border-green-500/50 text-green-300 px-4 py-2.5 rounded-lg relative text-sm shadow-md" role="alert">
                                <strong class="font-semibold">✓ Éxito:</strong> {{ session('message') }}
                            </div>
                        @endif
                        @if (session()->has('error'))
                            <div x-data="{ showError: true }" x-show="showError" x-init="setTimeout(() => showError = false, 5500)"
                                class="bg-red-500/20 border border-red-500/50 text-red-300 px-4 py-2.5 rounded-lg relative text-sm shadow-md" role="alert">
                                <strong class="font-semibold">✗ Error:</strong> {{ session('error') }}
                            </div>
                        @endif
                        @error('delete_error') {{-- Asumiendo que podrías tener un error específico de borrado --}}
                            <div class="bg-red-500/20 border border-red-500/50 text-red-300 px-4 py-2.5 rounded-lg relative text-sm shadow-md" role="alert">
                                <strong class="font-semibold">✗ Error de Borrado:</strong> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    @if (Auth::check() && Auth::user()->role === 'admin')
                        <div class="mb-6 border-b border-gray-700 pb-6">
                            <h4 class="text-lg font-semibold text-gray-100 mb-3">Añadir Nuevas Fotos</h4>
                            <form wire:submit="savePhotos"> {{-- Quitamos .prevent si no es necesario --}}
                                <input type="file" wire:model="uploadedPhotos" multiple
                                    class="block w-full text-sm text-gray-400 file:mr-4 file:py-2.5 file:px-5 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 cursor-pointer file:cursor-pointer mb-3 border border-gray-600 bg-gray-750 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">

                                <div wire:loading wire:target="uploadedPhotos" class="mt-2">
                                    <span class="text-sm text-indigo-400 animate-pulse">Cargando previsualización...</span>
                                </div>

                                @if ($uploadedPhotos && !$errors->has('uploadedPhotos.*'))
                                    <div class="mt-3 text-sm text-gray-300">Previsualización:</div>
                                    <div class="flex flex-wrap gap-3 mt-2">
                                        @foreach ($uploadedPhotos as $uploadedPhoto)
                                            @if(method_exists($uploadedPhoto, 'temporaryUrl'))
                                                <img src="{{ $uploadedPhoto->temporaryUrl() }}" class="h-20 w-20 object-cover rounded-md shadow-md border border-gray-700" alt="Previsualización">
                                            @endif
                                        @endforeach
                                    </div>
                                @endif

                                @error('uploadedPhotos.*') <span class="text-red-400 text-xs mt-2 block">{{ $message }}</span> @enderror

                                @if ($uploadedPhotos && !$errors->has('uploadedPhotos.*'))
                                    <button type="submit"
                                        class="mt-4 px-5 py-2 bg-green-600 text-white text-sm font-medium rounded-lg shadow-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-850 focus:ring-green-500 disabled:opacity-60 transition-colors"
                                        wire:loading.attr="disabled" wire:target="savePhotos, uploadedPhotos">
                                        <span wire:loading wire:target="savePhotos">Guardando...</span>
                                        <span wire:loading.remove wire:target="savePhotos">Guardar Fotos</span>
                                    </button>
                                @endif
                            </form>
                        </div>
                    @endif

                    <div class="flex justify-between items-center mb-4 pt-4">
                        <h4 class="text-lg font-semibold text-gray-100">Fotos del Álbum</h4>
                        @if (!empty($selectedPhotos) && $selectionMode)
                            <button wire:click="deleteSelectedPhotos"
                                wire:confirm="¿Estás seguro de eliminar las {{ count($selectedPhotos) }} fotos seleccionadas? Esta acción no se puede deshacer."
                                class="px-3.5 py-1.5 bg-red-600 text-white text-xs font-semibold rounded-lg shadow-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-850 focus:ring-red-500 disabled:opacity-60 transition-colors"
                                wire:loading.attr="disabled" wire:target="deleteSelectedPhotos">
                                <span wire:loading.remove wire:target="deleteSelectedPhotos">Eliminar ({{ count($selectedPhotos) }})</span>
                                <svg wire:loading wire:target="deleteSelectedPhotos" class="animate-spin inline-block h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"> <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle> <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path> </svg>
                                <span wire:loading wire:target="deleteSelectedPhotos">Eliminando...</span>
                            </button>
                        @endif
                    </div>

                    @if ($photosInModal && $photosInModal->total() > 0)
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3.5 mb-6">
                            @foreach ($photosInModal as $photo)
                                <div class="relative group aspect-square rounded-lg shadow-md overflow-hidden border-2
                                        {{ $selectionMode && in_array($photo->id, $selectedPhotos) ? 'border-indigo-500 ring-2 ring-indigo-500 ring-offset-2 ring-offset-gray-850' : 'border-gray-700 hover:border-indigo-500/70' }}
                                        {{ $selectionMode ? 'cursor-pointer' : 'cursor-pointer' }}
                                        transition-all duration-200"
                                    wire:key="modal-photo-{{ $photo->id }}"
                                    wire:dblclick="viewPhoto({{ $photo->id }})"
                                    wire:click="{{ $selectionMode ? 'toggleSelection(' . $photo->id . ')' : (Auth::check() ? 'toggleLike(' . $photo->id . ')' : '') }}"> {{-- Solo permitir like si está logueado --}}
                                    <img src="{{ $photo->thumbnail_path && Storage::disk('albums')->exists($photo->thumbnail_path)
                                        ? Storage::disk('albums')->url($photo->thumbnail_path)
                                        : ($photo->file_path && Storage::disk('albums')->exists($photo->file_path)
                                            ? Storage::disk('albums')->url($photo->file_path)
                                            : asset('images/placeholder-photo-dark.png')) }}"
                                        alt="Foto {{ $photo->id }}" loading="lazy"
                                        class="block w-full h-full object-cover pointer-events-none group-hover:opacity-80 transition-opacity duration-200">

                                    @if ($selectionMode && in_array($photo->id, $selectedPhotos))
                                        <div class="absolute top-1.5 right-1.5 z-20 bg-indigo-600 text-white rounded-full p-1 shadow-lg pointer-events-none" title="Seleccionada">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                        </div>
                                    @endif

                                    @if (Auth::check() && $photo->liked_by_current_user) {{-- Solo mostrar si está logueado --}}
                                        <div class="absolute bottom-1.5 left-1.5 z-10 p-1.5 bg-black/60 backdrop-blur-sm rounded-full pointer-events-none" title="Te gusta">
                                            <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" /></svg>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        @if ($photosInModal->hasPages())
                        <div class="mt-6">
                            {{ $photosInModal->links('vendor.livewire.tailwind-dark') }}
                        </div>
                        @endif
                    @else
                        <div class="text-center text-gray-500 py-12">
                            <svg class="mx-auto h-14 w-14 text-gray-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"> <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm16.5-1.875a.375.375 0 1 1-1.5.75.375.375 0 0 1 1.5-.75Zm0 0a.375.375 0 1 0 0-1.5.375.375 0 0 0 0 1.5Z" /> </svg>
                            <h3 class="mt-3 text-md font-semibold text-gray-400">Álbum Vacío</h3>
                            <p class="mt-1.5 text-sm text-gray-500">Aún no has añadido fotos a este álbum.</p>
                        </div>
                    @endif
                </div>

                <div class="border-t border-gray-700 p-4 text-right bg-gray-800 rounded-b-xl flex-shrink-0">
                    <button wire:click="closeModal" wire:loading.attr="disabled"
                        class="px-5 py-2 bg-gray-600 text-gray-200 rounded-lg border border-gray-500 shadow-sm hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-indigo-500 transition-colors text-sm font-medium">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- =================================================== --}}
    {{--           MODAL PARA CREAR ÁLBUM                    --}}
    {{-- =================================================== --}}
    @if ($showCreateAlbumModal)
        <div x-data="{ showCreate: @entangle('showCreateAlbumModal').live }" x-show="showCreate"
            @keydown.escape.window="showCreate = false; @this.call('closeCreateAlbumModal')"
            x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black/80 backdrop-blur-md z-50 flex justify-center items-center p-4 overflow-y-auto"
            aria-labelledby="modal-create-album-title" role="dialog" aria-modal="true">
            <div class="bg-gray-850 text-gray-300 rounded-xl shadow-2xl max-w-lg w-full flex flex-col"
                @click.outside="showCreate = false; @this.call('closeCreateAlbumModal')" x-show="showCreate"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                <form wire:submit="createAlbum">
                    <div class="flex justify-between items-center border-b border-gray-700 p-5 flex-shrink-0">
                        <h3 class="text-xl font-semibold text-white" id="modal-create-album-title">Crear Nuevo Álbum</h3>
                        <button type="button" wire:click="closeCreateAlbumModal"
                            class="p-2 rounded-full text-gray-400 hover:bg-gray-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-offset-gray-850 focus:ring-gray-500 transition-colors"
                            aria-label="Cerrar modal">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div class="p-6 space-y-5 overflow-y-auto">
                        <div>
                            <label for="newAlbumName" class="block text-sm font-medium text-gray-300 mb-1">Nombre del Álbum</label>
                            <input type="text" id="newAlbumName" wire:model.defer="newAlbumName"
                                class="mt-1 block w-full bg-gray-700 border-gray-600 text-gray-200 rounded-lg shadow-sm py-2.5 px-3 focus:ring-indigo-500 focus:border-indigo-500 @error('newAlbumName') border-red-500 @enderror">
                            @error('newAlbumName') <span class="text-xs text-red-400 mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="newAlbumDescription" class="block text-sm font-medium text-gray-300 mb-1">Descripción (Opcional)</label>
                            <textarea id="newAlbumDescription" wire:model.defer="newAlbumDescription" rows="3"
                                class="mt-1 block w-full bg-gray-700 border-gray-600 text-gray-200 rounded-lg shadow-sm py-2.5 px-3 focus:ring-indigo-500 focus:border-indigo-500 @error('newAlbumDescription') border-red-500 @enderror"></textarea>
                            @error('newAlbumDescription') <span class="text-xs text-red-400 mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="newAlbumType" class="block text-sm font-medium text-gray-300 mb-1">Tipo de Álbum</label>
                            <select id="newAlbumType" wire:model.live="newAlbumType"
                                class="mt-1 block w-full bg-gray-700 border-gray-600 text-gray-200 rounded-lg shadow-sm py-2.5 px-3 focus:ring-indigo-500 focus:border-indigo-500 @error('newAlbumType') border-red-500 @enderror">
                                <option value="public">Público</option>
                                <option value="private">Privado (Solo Admin)</option>
                                <option value="client">Cliente</option>
                            </select>
                            @error('newAlbumType') <span class="text-xs text-red-400 mt-1">{{ $message }}</span> @enderror
                        </div>

                        @if ($newAlbumType === 'client')
                            <div x-data x-transition.opacity.duration.300ms>
                                <label for="newAlbumClientId" class="block text-sm font-medium text-gray-300 mb-1">Asignar a Cliente</label>

                                {{-- Barra de Búsqueda de Clientes --}}
                                <div class="mt-1 mb-2 relative">
                                    <input type="text" wire:model.live.debounce.300ms="clientSearchEmail"
                                           placeholder="Buscar cliente por nombre o email..."
                                           class="block w-full bg-gray-700 border-gray-600 text-gray-200 rounded-lg shadow-sm py-2.5 px-3 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <div wire:loading wire:target="clientSearchEmail" class="absolute right-3 top-1/2 -translate-y-1/2">
                                        <svg class="animate-spin h-5 w-5 text-indigo-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                </div>

                                <select id="newAlbumClientId" wire:model.defer="newAlbumClientId"
                                    class="mt-1 block w-full bg-gray-700 border-gray-600 text-gray-200 rounded-lg shadow-sm py-2.5 px-3 focus:ring-indigo-500 focus:border-indigo-500 @error('newAlbumClientId') border-red-500 @enderror">
                                    <option value="">-- Selecciona un cliente --</option>
                                    @forelse($clients as $client)
                                        <option value="{{ $client->id }}">{{ $client->name }} ({{ $client->email }})</option>
                                    @empty
                                        <option value="" disabled>
                                            @if(empty($clientSearchEmail))
                                                No hay clientes (rol 'client'). Escribe para buscar.
                                            @else
                                                Ningún cliente encontrado para "{{ $clientSearchEmail }}".
                                            @endif
                                        </option>
                                    @endforelse
                                </select>
                                @error('newAlbumClientId') <span class="text-xs text-red-400 mt-1">{{ $message }}</span> @enderror
                            </div>
                        @endif

                        <div>
                            <label for="newAlbumCover" class="block text-sm font-medium text-gray-300 mb-1">Imagen de Portada (Opcional)</label>
                            <input type="file" id="newAlbumCover" wire:model="newAlbumCover"
                                class="mt-1 block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 cursor-pointer file:cursor-pointer border border-gray-600 bg-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                            <div wire:loading wire:target="newAlbumCover" class="mt-1 text-xs text-indigo-400 animate-pulse">Cargando portada...</div>
                            @if ($newAlbumCover && !$errors->has('newAlbumCover') && method_exists($newAlbumCover, 'temporaryUrl'))
                                <div class="mt-3">
                                    <img src="{{ $newAlbumCover->temporaryUrl() }}" alt="Previsualización Portada" class="h-24 w-auto object-cover rounded-md border border-gray-700 shadow-md">
                                </div>
                            @endif
                            @error('newAlbumCover') <span class="text-xs text-red-400 mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="border-t border-gray-700 p-5 bg-gray-800 rounded-b-xl flex justify-end space-x-3 flex-shrink-0">
                        <button type="button" wire:click="closeCreateAlbumModal"
                            class="px-5 py-2 bg-gray-600 text-gray-200 rounded-lg border border-gray-500 shadow-sm hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-indigo-500 transition-colors text-sm font-medium">Cancelar</button>
                        <button type="submit"
                            class="inline-flex items-center px-5 py-2 bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-500 text-white text-sm font-semibold rounded-lg shadow-lg hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-indigo-500 transition-all ease-in-out duration-200 transform hover:scale-105 disabled:opacity-70"
                            wire:loading.attr="disabled" wire:target="createAlbum, newAlbumCover">
                            <svg wire:loading wire:target="createAlbum, newAlbumCover" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span wire:loading wire:target="createAlbum, newAlbumCover">Creando...</span>
                            <span wire:loading.remove wire:target="createAlbum, newAlbumCover">Crear Álbum</span>
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
        <div x-data="{ showPhotoViewerState: @entangle('showPhotoViewer').live }" x-show="showPhotoViewerState"
            @keydown.escape.window="if(showPhotoViewerState) { showPhotoViewerState = false; @this.call('closePhotoViewer') }"
            @keydown.arrow-left.window="if(showPhotoViewerState && @this.previousPhotoId) { @this.call('viewPreviousPhoto') }"
            @keydown.arrow-right.window="if(showPhotoViewerState && @this.nextPhotoId) { @this.call('viewNextPhoto') }"
            x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black/95 backdrop-blur-lg z-[60] flex items-center justify-center p-3 sm:p-6"
            wire:click.self="closePhotoViewer" role="dialog" aria-modal="true" aria-label="Visor de Fotos">

            <div class="relative max-w-full max-h-full bg-transparent flex items-center justify-center"
                @click.stop {{-- Evita que el click en la imagen cierre el modal --}}
                x-show="showPhotoViewerState"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90">

                @if ($viewingPhoto->file_path && Storage::disk('albums')->exists($viewingPhoto->file_path))
                    <img src="{{ Storage::disk('albums')->url($viewingPhoto->file_path) }}"
                        alt="Foto {{ $viewingPhoto->id }} del álbum {{ $selectedAlbum?->name }}"
                        class="block max-w-full max-h-[90vh] object-contain rounded-lg shadow-2xl shadow-black/50 pointer-events-none">
                @else
                     <img src="{{ asset('images/placeholder-photo-large-dark.png') }}"
                        alt="Imagen no disponible"
                        class="block max-w-full max-h-[90vh] object-contain rounded-lg shadow-2xl shadow-black/50 pointer-events-none">
                @endif


                <button wire:click="closePhotoViewer" wire:loading.attr="disabled"
                    class="absolute top-2 right-2 sm:top-4 sm:right-4 z-[70] p-2.5 rounded-full text-white/80 bg-black/50 hover:bg-black/70 focus:outline-none focus:ring-2 focus:ring-white/70 transition-all duration-200"
                    aria-label="Cerrar visor (Esc)">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>

                @if ($this->previousPhotoId)
                    <button wire:click="viewPreviousPhoto" wire:loading.attr="disabled" wire:target="viewPreviousPhoto, viewNextPhoto"
                        class="absolute left-1 sm:left-3 top-1/2 -translate-y-1/2 z-[70] p-3 rounded-full text-white/90 bg-black/40 hover:bg-black/60 focus:outline-none focus:ring-2 focus:ring-white/70 transition-all duration-200 ease-in-out hover:scale-110 active:scale-100"
                        aria-label="Foto Anterior (←)">
                        <svg wire:loading wire:target="viewPreviousPhoto" class="animate-spin h-7 w-7 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <svg wire:loading.remove wire:target="viewPreviousPhoto" class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
                    </button>
                @endif

                @if ($this->nextPhotoId)
                    <button wire:click="viewNextPhoto" wire:loading.attr="disabled" wire:target="viewPreviousPhoto, viewNextPhoto"
                        class="absolute right-1 sm:right-3 top-1/2 -translate-y-1/2 z-[70] p-3 rounded-full text-white/90 bg-black/40 hover:bg-black/60 focus:outline-none focus:ring-2 focus:ring-white/70 transition-all duration-200 ease-in-out hover:scale-110 active:scale-100"
                        aria-label="Foto Siguiente (→)">
                        <svg wire:loading wire:target="viewNextPhoto" class="animate-spin h-7 w-7 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <svg wire:loading.remove wire:target="viewNextPhoto" class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                @endif
            </div>
        </div>
    @endif

    {{-- =================================================== --}}
    {{--           MODAL PARA EDITAR ÁLBUM (MEJORADO)        --}}
    {{-- =================================================== --}}
    @if ($showEditAlbumModal && $editingAlbum)
        <div x-data="{ showEdit: @entangle('showEditAlbumModal').live }" x-show="showEdit"
            @keydown.escape.window="showEdit = false; @this.call('closeEditAlbumModal')"
            x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black/80 backdrop-blur-md z-50 flex justify-center items-center p-4 overflow-y-auto"
            aria-labelledby="modal-edit-album-title" role="dialog" aria-modal="true">
            <div class="bg-gray-850 text-gray-300 rounded-xl shadow-2xl max-w-lg w-full flex flex-col"
                @click.outside="showEdit = false; @this.call('closeEditAlbumModal')" x-show="showEdit"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                <form wire:submit="updateAlbum">
                    <div class="flex justify-between items-center border-b border-gray-700 p-5 flex-shrink-0">
                        <h3 class="text-xl font-semibold text-white" id="modal-edit-album-title">Editar Álbum</h3>
                        <button type="button" wire:click="closeEditAlbumModal"
                            class="p-2 rounded-full text-gray-400 hover:bg-gray-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-offset-gray-850 focus:ring-gray-500 transition-colors"
                            aria-label="Cerrar modal">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div class="p-6 space-y-5 overflow-y-auto">
                        <div>
                            <label for="editAlbumName" class="block text-sm font-medium text-gray-300 mb-1">Nombre del Álbum</label>
                            <input type="text" id="editAlbumName" wire:model.defer="editAlbumName"
                                class="block w-full bg-gray-700 border-gray-600 text-gray-200 rounded-lg shadow-sm py-2.5 px-3 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('editAlbumName') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror">
                            @error('editAlbumName') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="editAlbumDescription" class="block text-sm font-medium text-gray-300 mb-1">Descripción (Opcional)</label>
                            <textarea id="editAlbumDescription" wire:model.defer="editAlbumDescription" rows="4"
                                class="block w-full bg-gray-700 border-gray-600 text-gray-200 rounded-lg shadow-sm py-2.5 px-3 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('editAlbumDescription') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror"></textarea>
                            @error('editAlbumDescription') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="editAlbumType" class="block text-sm font-medium text-gray-300 mb-1">Tipo</label>
                            <select id="editAlbumType" wire:model.live="editAlbumType"
                                class="block w-full bg-gray-700 border-gray-600 text-gray-200 rounded-lg shadow-sm py-2.5 px-3 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('editAlbumType') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror">
                                <option value="public">Público</option>
                                <option value="private">Privado (Solo Admin)</option>
                                <option value="client">Cliente</option>
                            </select>
                            @error('editAlbumType') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                        </div>

                        @if ($editAlbumType === 'client')
                            <div x-data x-transition.opacity.duration.300ms>
                                <label for="editAlbumClientId" class="block text-sm font-medium text-gray-300 mb-1">Asignar a Cliente</label>

                                {{-- Barra de Búsqueda de Clientes --}}
                                <div class="mt-1 mb-2 relative">
                                    <input type="text" wire:model.live.debounce.300ms="clientSearchEmail"
                                           placeholder="Buscar cliente por nombre o email..."
                                           class="block w-full bg-gray-700 border-gray-600 text-gray-200 rounded-lg shadow-sm py-2.5 px-3 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                     <div wire:loading wire:target="clientSearchEmail" class="absolute right-3 top-1/2 -translate-y-1/2">
                                        <svg class="animate-spin h-5 w-5 text-indigo-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                </div>

                                <select id="editAlbumClientId" wire:model.defer="editAlbumClientId"
                                    class="mt-1 block w-full bg-gray-700 border-gray-600 text-gray-200 rounded-lg shadow-sm py-2.5 px-3 focus:ring-indigo-500 focus:border-indigo-500 @error('editAlbumClientId') border-red-500 @enderror">
                                    <option value="">-- Selecciona un cliente --</option>
                                    @forelse($clients as $client)
                                        <option value="{{ $client->id }}" @if( (string) $client->id === (string) $editAlbumClientId) selected @endif>{{ $client->name }} ({{ $client->email }})</option>
                                    @empty
                                        <option value="" disabled>
                                             @if(empty($clientSearchEmail))
                                                No hay clientes (rol 'client'). Escribe para buscar.
                                            @else
                                                Ningún cliente encontrado para "{{ $clientSearchEmail }}".
                                            @endif
                                        </option>
                                    @endforelse
                                </select>
                                @error('editAlbumClientId') <span class="text-xs text-red-400 mt-1">{{ $message }}</span> @enderror
                            </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Imagen de Portada Actual</label>
                            <div class="mt-2 flex items-center gap-x-4">
                                @php
                                    $coverPreviewUrl = null;
                                    if ($editAlbumNewCover && !$errors->has('editAlbumNewCover') && method_exists($editAlbumNewCover, 'temporaryUrl')) {
                                        $coverPreviewUrl = $editAlbumNewCover->temporaryUrl();
                                    } elseif ($editAlbumCurrentCover && Storage::disk('albums')->exists($editAlbumCurrentCover)) {
                                        $coverPreviewUrl = Storage::disk('albums')->url($editAlbumCurrentCover);
                                    }
                                @endphp
                                @if ($coverPreviewUrl)
                                    <img src="{{ $coverPreviewUrl }}" alt="Portada" class="h-20 w-20 object-cover rounded-lg border border-gray-700 shadow-md">
                                @else
                                    <div class="h-20 w-20 bg-gray-700 rounded-lg flex items-center justify-center text-gray-500 border border-gray-600">
                                        <svg class="w-10 h-10 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                @endif
                                <div>
                                    <label for="editAlbumNewCover" class="cursor-pointer rounded-lg bg-indigo-600 px-3.5 py-2 text-sm font-semibold text-white shadow-md hover:bg-indigo-700 focus-within:outline-none focus-within:ring-2 focus-within:ring-indigo-500 focus-within:ring-offset-2 focus-within:ring-offset-gray-850">
                                        <span>{{ $editAlbumCurrentCover || $editAlbumNewCover ? 'Cambiar Portada' : 'Subir Portada' }}</span>
                                        <input id="editAlbumNewCover" wire:model="editAlbumNewCover" type="file" class="sr-only">
                                    </label>
                                    @if ($editAlbumNewCover && !$errors->has('editAlbumNewCover'))
                                        <button type="button" wire:click="$set('editAlbumNewCover', null)" class="ml-3 text-xs text-red-400 hover:text-red-300 transition-colors">(Quitar selección)</button>
                                    @endif
                                </div>
                            </div>
                            <div wire:loading wire:target="editAlbumNewCover" class="mt-1 text-xs text-indigo-400 animate-pulse">Cargando nueva portada...</div>
                            @error('editAlbumNewCover') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="border-t border-gray-700 p-5 bg-gray-800 rounded-b-xl flex justify-end space-x-3 flex-shrink-0">
                        <button type="button" wire:click="closeEditAlbumModal"
                            class="px-5 py-2 bg-gray-600 text-gray-200 rounded-lg border border-gray-500 shadow-sm hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-indigo-500 transition-colors text-sm font-medium">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="inline-flex items-center justify-center px-5 py-2 bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-500 text-white text-sm font-semibold rounded-lg shadow-lg hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-indigo-500 transition-all ease-in-out duration-200 transform hover:scale-105 disabled:opacity-70"
                            wire:loading.attr="disabled" wire:target="updateAlbum, editAlbumNewCover">
                            <svg wire:loading wire:target="updateAlbum, editAlbumNewCover" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span wire:loading wire:target="updateAlbum, editAlbumNewCover">Actualizando...</span>
                            <span wire:loading.remove wire:target="updateAlbum, editAlbumNewCover">Actualizar Álbum</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
