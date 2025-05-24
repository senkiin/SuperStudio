<div
    x-data="{
        showModal: false,
        modalIndex: 0,
        photos: @json($alpinePhotos) {{-- Uses the public property from the Livewire component --}}
    }"
    class="p-4 sm:p-6 lg:p-8 bg-gray-50 dark:bg-gray-900 min-h-screen"
>
    {{-- Loading Indicator --}}
    <div wire:loading.flex class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-[60]">
        <div class="text-white text-lg p-4 rounded flex items-center">
            <svg class="animate-spin h-8 w-8 text-white inline-block mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Cargando...
        </div>
    </div>

    <header class="mt-8 mb-6 md:mb-8">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-gray-100">Mis Fotos Favoritas</h1>
    </header>

    @if ($likedPhotos && $likedPhotos->count() > 0)
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3 sm:gap-4">
            @foreach ($likedPhotos as $photo)
                @php
                    // Use thumbnail_path for grid if available, otherwise file_path
                    $imageGridPath = $photo->thumbnail_path ?: $photo->file_path;
                @endphp
                <div
                    class="relative group aspect-w-1 aspect-h-1 bg-gray-200 dark:bg-gray-800 rounded-lg overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 ease-in-out cursor-pointer"
                    wire:key="liked-photo-{{ $photo->id }}"
                    @click="modalIndex = {{ $loop->index }}; showModal = true"
                    title="Ver foto: {{ $photo->filename ?? 'Foto Favorita' }}"
                >
                    <img
                        src="{{ $imageGridPath && $disk ? Storage::disk($disk)->url($imageGridPath) : asset('images/placeholder-image.jpg') }}"
                        alt="Foto: {{ e($photo->filename ?? 'Favorita') }}{{ $photo->album?->name ? ' del álbum ' . e($photo->album->name) : '' }}"
                        loading="lazy"
                        class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                    >

                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                    <button
                        wire:click.stop="unlikePhoto({{ $photo->id }})"
                        wire:loading.attr="disabled"
                        wire:target="unlikePhoto({{ $photo->id }})"
                        title="Quitar de Favoritos"
                        aria-label="Quitar de favoritos"
                        class="absolute top-2 right-2 z-20 p-1.5 bg-red-600 hover:bg-red-700 text-white rounded-full shadow-lg opacity-0 group-hover:opacity-100 focus:opacity-100 transition-all duration-300 ease-in-out transform group-hover:scale-110 focus:scale-110"
                    >
                        <span wire:loading.remove wire:target="unlikePhoto({{ $photo->id }})">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </span>
                        <span wire:loading wire:target="unlikePhoto({{ $photo->id }})" class="block">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                    </button>

                    @if($photo->filename || $photo->album?->name)
                        <div class="absolute bottom-0 left-0 right-0 p-2.5 bg-gradient-to-t from-black/80 to-transparent">
                            @if($photo->filename)
                                <p class="text-white text-sm font-semibold truncate" title="{{ $photo->filename }}">{{ $photo->filename }}</p>
                            @endif
                            @if($photo->album?->name)
                                <p class="text-gray-200 text-xs truncate" title="{{ $photo->album->name }}">{{ $photo->album->name }}</p>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Pagination Links --}}
        @if ($likedPhotos->hasPages())
            <div class="mt-8">
                {{ $likedPhotos->links() }}
            </div>
        @endif

        {{-- Modal for Lightbox (uses photos from x-data, which is now $alpinePhotos) --}}
        <template x-if="showModal && photos.length > 0">
            <div
                class="fixed inset-0 bg-black bg-opacity-90 backdrop-blur-md flex items-center justify-center z-[70] p-4"
                @keydown.escape.window="showModal = false"
                @click.self="showModal = false"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
            >
                <div
                    class="relative bg-gray-900 dark:bg-black shadow-2xl rounded-lg w-full max-w-5xl max-h-[95vh] flex flex-col"
                    @click.stop
                    x-show="showModal"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                >
                    <div class="flex-grow flex items-center justify-center relative overflow-hidden p-2 sm:p-4 md:p-6">
                        <img
                            :src="photos[modalIndex] ? photos[modalIndex].url : '{{ asset('images/placeholder-image.jpg') }}'"
                            :alt="photos[modalIndex] ? photos[modalIndex].alt : 'Previsualización de foto'"
                            class="block max-w-full max-h-[calc(95vh-80px)] object-contain select-none rounded-md"
                        >
                    </div>

                    {{-- Navigation and Close Buttons --}}
                     <button @click="showModal = false" class="absolute top-2 right-2 sm:top-3 sm:right-3 z-10 p-1.5 text-gray-300 hover:text-white bg-black/40 hover:bg-black/60 rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-white" aria-label="Cerrar previsualización">
                        <svg class="w-6 h-6 sm:w-7 sm:h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>

                    <template x-if="photos.length > 1"> {{-- Show navigation only if there's more than one photo --}}
                        <div>
                            <button @click="modalIndex = (modalIndex - 1 + photos.length) % photos.length" class="absolute left-1 sm:left-3 top-1/2 transform -translate-y-1/2 z-10 p-2 text-gray-300 hover:text-white bg-black/40 hover:bg-black/60 rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-white" aria-label="Foto anterior">
                                <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                            <button @click="modalIndex = (modalIndex + 1) % photos.length" class="absolute right-1 sm:right-3 top-1/2 transform -translate-y-1/2 z-10 p-2 text-gray-300 hover:text-white bg-black/40 hover:bg-black/60 rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-white" aria-label="Siguiente foto">
                                <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                    </template>
                    <div class="text-center text-gray-400 text-xs py-2" x-show="photos.length > 0"> {{-- Show index if photos exist --}}
                        <span x-text="modalIndex + 1"></span> / <span x-text="photos.length"></span>
                    </div>
                </div>
            </div>
        </template>

    @elseif (is_null($likedPhotos))
        {{-- User not logged in --}}
        <div class="text-center text-gray-500 dark:text-gray-400 py-16">
            <svg class="mx-auto h-16 w-16 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" > <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /> </svg>
            <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-gray-100">Acceso Requerido</h3>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Debes iniciar sesión para ver tus fotos favoritas.</p>
        </div>
    @else
        {{-- No liked photos --}}
        <div class="text-center text-gray-500 dark:text-gray-400 py-16">
            <svg class="mx-auto h-16 w-16 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
            </svg>
            <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-gray-100">Sin Favoritos</h3>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Aún no has marcado ninguna foto como favorita. ¡Explora y encuentra tus preferidas!</p>
        </div>
    @endif
</div>
