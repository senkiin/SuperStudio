<div>
    @section('title', $pageTitle ?? $album->name ?? 'Álbum de Fotos')
    @section('metaDescription', $metaDescription ?? $album->description ?? 'Galería de fotos.')

    <div class="bg-black text-gray-300 min-h-screen pt-12 md:pt-16 pb-20 selection:bg-indigo-500 selection:text-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">

            <div class="mb-10 md:mb-14 text-center">
                <h1 class="text-4xl sm:text-5xl md:text-6xl font-extrabold tracking-tight mb-4 text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 via-purple-400 to-pink-400 animate-gradient-x">
                    {{ $album->name ?? $album->title ?? 'Galería de Fotos' }}
                </h1>
                @if ($album->description)
                    <p class="text-lg sm:text-xl text-gray-400 max-w-3xl mx-auto leading-relaxed">{{ $album->description }}</p>
                @endif
            </div>

            <div class="mb-8 md:mb-10">
                <a href="{{ route('weddings') }}" {{-- Ajusta 'weddings' a tu nombre de ruta real si es diferente --}}
                   class="inline-flex items-center px-5 py-2.5 border border-gray-700 text-sm font-semibold rounded-lg text-gray-300 bg-gray-800 hover:bg-gray-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-black focus:ring-indigo-500 transition ease-in-out duration-200 group shadow-lg hover:shadow-indigo-500/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 rtl:mr-0 rtl:ml-2 transform transition-transform duration-200 group-hover:-translate-x-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L7.414 9H15a1 1 0 110 2H7.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Volver a Bodas
                </a>
            </div>

            @if ($photos->isNotEmpty())
                <div class="photo-gallery">
                    @foreach ($photos as $key => $photo)
                        <div class="gallery-item group relative overflow-hidden rounded-lg shadow-lg"
                             wire:click="openCustomLightbox({{ $photo->id }})"
                             wire:key="gallery-photo-{{ $album->id }}-{{ $photo->id }}-{{ $key }}"
                             role="button"
                             tabindex="0"
                             aria-label="Ver foto {{ $loop->iteration }}">
                            <img src="{{ $photo->thumbnail_path && Storage::disk($disk)->exists($photo->thumbnail_path)
                                        ? Storage::disk($disk)->url($photo->thumbnail_path)
                                        : (Storage::disk($disk)->exists($photo->file_path)
                                            ? Storage::disk($disk)->url($photo->file_path)
                                            : asset('images/placeholder-gallery-dark.png')) }}"
                                 alt="Foto {{ $loop->iteration }} del álbum {{ $album->name }}"
                                 loading="lazy"
                                 class="w-full h-full object-cover transition-all duration-300 ease-in-out transform group-hover:scale-110">
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition-colors duration-300 flex items-center justify-center">
                                <svg class="w-10 h-10 text-white opacity-0 group-hover:opacity-80 transition-all duration-300 transform scale-75 group-hover:scale-100" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607zM13.5 10.5h-3m-1.5 0h7.5m-7.5 0V7.5m0 3V13.5" />
                                </svg>
                            </div>
                        </div>

                    @endforeach


                </div>

    @if ($photos->hasPages())
        {{ $photos->links('vendor.livewire.tailwind') }}
    @endif



            @else
                <div class="text-center py-16 sm:py-24">
                    <svg class="mx-auto h-16 w-16 text-gray-700" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.243 4.243a3 3 0 11-4.243-4.243" />
                    </svg>
                    <h3 class="mt-4 text-xl font-semibold text-gray-400">Álbum Vacío</h3>
                    <p class="mt-2 text-md text-gray-500">Parece que aún no hay fotos en este álbum.</p>
                    <div class="mt-8">
                         <a href="{{ route('weddings') }}"
                           class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-500 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-black focus:ring-indigo-500 transition ease-in-out duration-150 transform hover:scale-105">
                            Explorar otras bodas
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- MODAL PERSONALIZADO PARA LIGHTBOX (Dark Theme) --}}
    @if ($showCustomLightbox && $currentLightboxPhoto)
        <div
            x-data="{ show: @entangle('showCustomLightbox') }"
            x-show="show"
            x-on:keydown.escape.window="show = false; @this.call('closeCustomLightbox')"
            x-on:keydown.arrow-left.window="if(show) { @this.call('previousPhotoInLightbox') }"
            x-on:keydown.arrow-right.window="if(show) { @this.call('nextPhotoInLightbox') }"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[100] flex items-center justify-center p-2 sm:p-4 bg-black/95 backdrop-blur-lg"
            wire:click.self="closeCustomLightbox"
            role="dialog" aria-modal="true" aria-labelledby="lightbox-caption"
        >
            <div
                x-show="show"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-90"
                class="relative bg-transparent w-full max-w-screen-xl max-h-[95vh] shadow-2xl flex flex-col items-center justify-center"
                @click.stop
            >
                <div class="relative flex items-center justify-center w-full h-full">
                    <img src="{{ Storage::disk($disk)->url($currentLightboxPhoto->file_path) }}"
                         alt="Foto {{ $currentLightboxPhotoIndex + 1 }} del álbum {{ $album->name }}"
                         class="block max-w-full max-h-[calc(95vh-80px)] object-contain rounded-lg shadow-black/50 shadow-2xl"
                         style="image-rendering: -webkit-optimize-contrast;"
                         >

                    <div id="lightbox-caption" class="absolute bottom-0 left-0 right-0 p-3 sm:p-4 bg-gradient-to-t from-black/80 to-transparent text-center">
                        <div class="flex justify-between items-center text-xs sm:text-sm text-gray-300">
                            <span>{{ pathinfo($currentLightboxPhoto->file_path, PATHINFO_FILENAME) ?? 'Foto ' . $currentLightboxPhoto->id }}</span>
                            <span>{{ $currentLightboxPhotoIndex + 1 }} / {{ $photosForCustomLightbox->count() }}</span>
                        </div>
                    </div>
                </div>

                <button wire:click="closeCustomLightbox" title="Cerrar (Esc)"
                        class="absolute top-2 right-2 sm:top-3 sm:right-3 z-[110] p-2.5 text-gray-300 hover:text-white bg-black/50 hover:bg-black/70 rounded-full transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-white/70">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 sm:h-7 sm:w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                @if ($currentLightboxPhotoIndex > 0)
                    <button wire:click="previousPhotoInLightbox" title="Anterior (←)"
                            class="absolute left-1 sm:left-3 top-1/2 -translate-y-1/2 z-[110] p-3 text-gray-300 hover:text-white bg-black/40 hover:bg-black/60 rounded-full transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-white/70 transform hover:scale-105 active:scale-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 sm:h-8 sm:w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                @else
                    <div class="absolute left-1 sm:left-3 top-1/2 -translate-y-1/2 z-[110] p-3 h-[calc(2rem+1.5rem)] w-[calc(2rem+1.5rem)] sm:h-[calc(2.25rem+1.5rem)] sm:w-[calc(2.25rem+1.5rem)]"></div>
                @endif

                @if ($currentLightboxPhotoIndex < ($photosForCustomLightbox->count() - 1))
                    <button wire:click="nextPhotoInLightbox" title="Siguiente (→)"
                            class="absolute right-1 sm:right-3 top-1/2 -translate-y-1/2 z-[110] p-3 text-gray-300 hover:text-white bg-black/40 hover:bg-black/60 rounded-full transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-white/70 transform hover:scale-105 active:scale-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 sm:h-8 sm:w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                @else
                    <div class="absolute right-1 sm:right-3 top-1/2 -translate-y-1/2 z-[110] p-3 h-[calc(2rem+1.5rem)] w-[calc(2rem+1.5rem)] sm:h-[calc(2.25rem+1.5rem)] sm:w-[calc(2.25rem+1.5rem)]"></div>
                @endif
            </div>
        </div>
    @endif
</div>

@push('styles')
<style>
    .photo-gallery {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); /* Columnas responsivas, tamaño mínimo 240px */
        gap: 12px; /* Espacio entre imágenes */
    }
    .gallery-item {
        aspect-ratio: 1 / 1;
        overflow: hidden;
        cursor: pointer;
        border: 3px solid transparent; /* Borde inicial transparente */
        transition: border-color 0.3s ease-in-out, transform 0.3s ease-in-out;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1), 0 1px 3px rgba(0,0,0,0.08); /* Sombra sutil */
    }
    .gallery-item:hover, .gallery-item:focus-visible {
        border-color: #6366f1; /* Indigo-500 para el hover/focus */
        transform: scale(1.03); /* Ligero zoom */
        box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.3), 0 4px 6px -2px rgba(99, 102, 241, 0.2); /* Sombra índigo más pronunciada */
    }
    .gallery-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    @keyframes gradient-animation {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
    .animate-gradient-x {
        background-size: 300% 300%; /* Aumentado para un degradado más suave */
        animation: gradient-animation 8s ease infinite; /* Duración más larga */
    }

    /* Ejemplo de estilos para paginación oscura de Livewire */
    /* Asegúrate de tener una vista vendor.livewire. o personaliza esta sección */
    nav[role="navigation"] span[aria-current="page"] span {
        background-color: #4f46e5 !important; /* Indigo-600 */
        color: white !important;
        border-color: #4338ca !important; /* Indigo-700 */
        font-weight: 600;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    }
    nav[role="navigation"] a:hover {
        background-color: #3730a3 !important; /* Indigo-800 */
        color: white !important;
        border-color: #312e81 !important; /* Indigo-900 */
    }
    nav[role="navigation"] a {
        color: #d1d5db; /* Gray-300 */
        background-color: #1f2937; /* Gray-800 */
        border-color: #374151; /* Gray-700 */
        transition: background-color 0.2s ease-in-out, color 0.2s ease-in-out;
    }
     nav[role="navigation"] a,
     nav[role="navigation"] span {
        border-radius: 0.375rem; /* rounded-md */
        padding-left: 0.75rem;
        padding-right: 0.75rem;
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
     }
    nav[role="navigation"] span[aria-disabled="true"] span {
        color: #4b5563 !important; /* Gray-600 */
        background-color: #111827 !important; /* Gray-900 */
        border-color: #1f2937 !important; /* Gray-800 */
    }
    .dark .pagination { /* Si tienes una clase .dark en tu body/html */
        /* ... más estilos específicos si es necesario ... */
    }
</style>
@endpush
