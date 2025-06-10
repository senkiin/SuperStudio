<div
    x-data="{
        showModal: false,
        modalIndex: 0,
        photos: @json($alpinePhotos) {{-- Usa la propiedad pública del componente Livewire --}}
    }"
    class="bg-black text-gray-300 min-h-screen py-4 sm:py-6 lg:py-8 selection:bg-indigo-500 selection:text-white"
>
    {{-- Indicador de Carga --}}
    <div wire:loading.flex class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-[80]">
        <div class="text-white text-lg p-4 rounded flex items-center">
            <svg class="animate-spin h-8 w-8 text-indigo-400 inline-block mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="font-semibold tracking-wider">Cargando...</span>
        </div>
    </div>

    {{-- Contenedor Principal Centrado --}}
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <header class="mt-8 mb-10 md:mb-12 text-center sm:text-left">
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 via-purple-400 to-pink-400 animate-gradient-x">
                Mis Fotos Favoritas
            </h1>
        </header>

        @if ($likedPhotos && $likedPhotos->count() > 0)
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4 sm:gap-5">
                @foreach ($likedPhotos as $photo)
                    @php
                        $imageGridPath = $photo->thumbnail_path ?: $photo->file_path;
                    @endphp
                    <div
                        class="relative group aspect-square bg-gray-800 rounded-xl overflow-hidden shadow-lg hover:shadow-indigo-500/40 transition-all duration-300 ease-in-out cursor-pointer transform hover:scale-105"
                        wire:key="liked-photo-{{ $photo->id }}"
                        @click="modalIndex = {{ $loop->index }}; showModal = true"
                        title="Ver foto: {{ $photo->filename ?? 'Foto Favorita' }}"
                    >
                        <img
                            src="{{ $imageGridPath && $disk ? Storage::disk($disk)->url($imageGridPath) : asset('images/placeholder-image-dark.jpg') }}"
                            alt="Foto: {{ e($photo->filename ?? 'Favorita') }}{{ $photo->album?->name ? ' del álbum ' . e($photo->album->name) : '' }}"
                            loading="lazy"
                            class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110"
                        >
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent opacity-75 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>

                        <button
                            wire:click.stop="unlikePhoto({{ $photo->id }})"
                            wire:loading.attr="disabled"
                            wire:target="unlikePhoto({{ $photo->id }})"
                            title="Quitar de Favoritos"
                            aria-label="Quitar de favoritos"
                            class="absolute top-2.5 right-2.5 z-20 p-2 bg-red-600/90 hover:bg-red-500 text-white rounded-full shadow-xl transition-all duration-200 ease-in-out transform hover:scale-110 focus:scale-110 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-opacity-50"
                        >
                            <span wire:loading.remove wire:target="unlikePhoto({{ $photo->id }})">
                                {{-- Icono de corazón roto --}}
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path><path fill-rule="evenodd" d="M10 17.657l-1.172-1.171a4.004 4.004 0 00-5.656-5.656l-.293.293-.293-.293a4.004 4.004 0 00-5.656 5.656L10 17.657zm0-13.485L8.828 3.001a4.004 4.004 0 005.656 5.656l.293-.293.293.293a4.004 4.004 0 005.656-5.656L10 4.172zM6.75 9.25a.75.75 0 000 1.5h6.5a.75.75 0 000-1.5h-6.5z" transform="rotate(45 10 10)" clip-rule="evenodd"></path></svg>
                            </span>
                            <span wire:loading wire:target="unlikePhoto({{ $photo->id }})" class="block">
                                <svg class="animate-spin h-4 w-4 sm:h-5 sm:h-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                        </button>

                        @if($photo->filename || $photo->album?->name)
                            <div class="absolute bottom-0 left-0 right-0 p-2.5 sm:p-3 bg-gradient-to-t from-black/80 via-black/60 to-transparent pointer-events-none">
                                @if($photo->filename)
                                    <p class="text-white text-xs sm:text-sm font-semibold truncate" title="{{ $photo->filename }}">{{ Str::limit($photo->filename, 30) }}</p>
                                @endif
                                @if($photo->album?->name)
                                    <p class="text-gray-300 text-xs truncate" title="{{ $photo->album->name }}">{{ Str::limit($photo->album->name, 35) }}</p>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            @if ($likedPhotos->hasPages())
                <div class="mt-10 md:mt-12">
                    {{ $likedPhotos->links('vendor.livewire.tailwind') }}
                </div>
            @endif

        @elseif (is_null($likedPhotos))
            <div class="text-center text-gray-400 py-16 sm:py-24">
                <svg class="mx-auto h-20 w-20 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"> <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /> </svg>
                <h3 class="mt-6 text-xl lg:text-2xl font-semibold text-gray-100">Acceso Requerido</h3>
                <p class="mt-3 text-md lg:text-lg text-gray-400">Debes <a href="{{ route('login') }}" class="text-indigo-400 hover:text-indigo-300 font-medium underline_ hover:underline">iniciar sesión</a> para ver tus fotos favoritas.</p>
            </div>
        @else
            <div class="text-center text-gray-400 py-16 sm:py-24">
                <svg class="mx-auto h-20 w-20 text-gray-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                </svg>
                <h3 class="mt-6 text-xl lg:text-2xl font-semibold text-gray-100">Aún Sin Favoritos</h3>
                <p class="mt-3 text-md lg:text-lg text-gray-400">Parece que todavía no has añadido ninguna foto a tu lista.</p>
                <p class="mt-1 text-md lg:text-lg text-gray-400">¡Explora las galerías y marca las que más te gusten!</p>
            </div>
        @endif
    </div>

    {{-- Modal para Lightbox --}}
    <template x-if="showModal && photos.length > 0">
        <div
            class="fixed inset-0 bg-black/90 backdrop-blur-lg flex items-center justify-center z-[70] p-3 sm:p-4"
            @keydown.escape.window="showModal = false"
            @keydown.arrow-left.window="if(photos.length > 1) modalIndex = (modalIndex - 1 + photos.length) % photos.length"
            @keydown.arrow-right.window="if(photos.length > 1) modalIndex = (modalIndex + 1) % photos.length"
            @click.self="showModal = false"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            role="dialog" aria-modal="true" :aria-labelledby="'lightbox-photo-title-' + modalIndex"
        >
            <div
                class="relative bg-gray-850 text-gray-200 shadow-2xl rounded-xl w-full max-w-6xl max-h-[95vh] flex flex-col overflow-hidden"
                @click.stop
                x-show="showModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-90"
            >
                <div class="flex-grow flex items-center justify-center relative p-2 sm:p-4">
                    <img
                        :src="photos[modalIndex] ? photos[modalIndex].url : '{{ asset('images/placeholder-image-dark.jpg') }}'"
                        :alt="photos[modalIndex] ? photos[modalIndex].alt : 'Previsualización de foto'"
                        class="block max-w-full max-h-[calc(95vh-110px)] object-contain select-none rounded-lg shadow-xl"
                    >
                </div>
                <div class="flex-shrink-0 px-4 py-3 sm:px-6 sm:py-4 border-t border-gray-700/70">
                    <div class="flex items-center justify-between">
                        <div class="min-w-0">
                            <p class="text-sm sm:text-base font-medium text-gray-100 truncate" :id="'lightbox-photo-title-' + modalIndex" x-text="photos[modalIndex] ? photos[modalIndex].alt : 'Foto'"></p>
                        </div>
                        <div class="text-sm text-gray-400 ml-4 whitespace-nowrap">
                            <span x-text="modalIndex + 1"></span> / <span x-text="photos.length"></span>
                        </div>
                    </div>
                </div>

                {{-- Botones de Navegación y Cierre --}}
                <button @click="showModal = false" class="absolute top-3 right-3 sm:top-4 sm:right-4 z-[75] p-2 text-gray-300 hover:text-white bg-black/50 hover:bg-black/70 rounded-full transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-white/70" aria-label="Cerrar previsualización (Esc)">
                    <svg class="w-6 h-6 sm:w-7 sm:h-7" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>

                <template x-if="photos.length > 1">
                    <div>
                        <button @click="modalIndex = (modalIndex - 1 + photos.length) % photos.length" class="absolute left-2 sm:left-4 top-1/2 transform -translate-y-1/2 z-[75] p-2.5 sm:p-3 text-gray-300 hover:text-white bg-black/50 hover:bg-black/70 rounded-full transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-white/70 transform hover:scale-105 active:scale-100" aria-label="Foto anterior (Flecha Izquierda)">
                            <svg class="w-6 h-6 sm:w-8 sm:h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <button @click="modalIndex = (modalIndex + 1) % photos.length" class="absolute right-2 sm:right-4 top-1/2 transform -translate-y-1/2 z-[75] p-2.5 sm:p-3 text-gray-300 hover:text-white bg-black/50 hover:bg-black/70 rounded-full transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-white/70 transform hover:scale-105 active:scale-100" aria-label="Siguiente foto (Flecha Derecha)">
                            <svg class="w-6 h-6 sm:w-8 sm:h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </template>
            </div>
        </div>
    </template>
</div>

@push('styles')
<style>
    @keyframes gradient-animation {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
    .animate-gradient-x {
        background-size: 250% 250%;
        animation: gradient-animation 7s ease infinite;
    }
</style>
@endpush
