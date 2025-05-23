<div>
    @section('title', $pageTitle)
    @section('metaDescription', $metaDescription)

    <div class="bg-black text-gray-300 min-h-screen pt-8 md:pt-12 pb-16">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6 md:mb-8">
                <a href="{{ route('weddings') }}" {{-- Ajusta 'weddings.index' a tu nombre de ruta real --}}
                   class="inline-flex items-center px-4 py-2 border border-gray-600 text-sm font-medium rounded-md text-gray-300 bg-gray-700 hover:bg-gray-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-black focus:ring-indigo-500 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 rtl:mr-0 rtl:ml-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L7.414 9H15a1 1 0 110 2H7.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Volver a Bodas
                </a>
            </div>
            <div class="mb-8 md:mb-12 text-center">
                <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold tracking-tight mb-3 text-white">{{ $album->name ?? $album->title }}</h1>
                @if ($album->description)
                    <p class="text-lg text-gray-400 max-w-3xl mx-auto">{{ $album->description }}</p>
                @endif
            </div>

            @if ($photos->isNotEmpty())
                <div class="photo-gallery">
                    {{-- El $loop->index aquí es el índice dentro de la página actual de paginación --}}
                    @foreach ($photos as $photo)
                        <div class="gallery-item group"
                             wire:click="openCustomLightbox({{ $photo->id }})"
                             wire:key="gallery-photo-{{ $photo->id }}"
                             role="button"
                             tabindex="0"
                             aria-label="Ver foto {{ $loop->iteration }}">
                            <img src="{{ $photo->thumbnail_path
    ? Storage::disk($disk)->url($photo->thumbnail_path)
    : Storage::disk($disk)->url($photo->file_path) }}"
                                 alt="Foto del álbum {{ $album->name }}"
                                 loading="lazy"
                                 class="w-full h-full object-cover transition-transform duration-300 ease-in-out group-hover:scale-105 group-hover:opacity-90">
                        </div>
                    @endforeach
                </div>

                @if ($photos->hasPages())
                    <div class="mt-8 md:mt-12">
                        {{ $photos->links('vendor.livewire.tailwind-dark') }}
                    </div>
                @endif
            @else
                {{-- ... Mensaje de álbum vacío ... --}}
            @endif
        </div>
    </div>

    {{-- MODAL PERSONALIZADO PARA LIGHTBOX --}}
    @if ($showCustomLightbox && $currentLightboxPhoto)
        <div
            x-data="{ show: @entangle('showCustomLightbox') }"
            x-show="show"
            x-on:keydown.escape.window="show = false; @this.call('closeCustomLightbox')"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/90" {{-- Fondo oscuro semi-transparente --}}
            wire:click.self="closeCustomLightbox" {{-- Cierra al hacer clic en el fondo --}}
        >
            <div
                x-show="show"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative bg-white w-full max-w-4xl max-h-[90vh] shadow-xl flex flex-col" {{-- Fondo blanco para el contenido del modal --}}
                @click.stop {{-- Evita que el clic dentro del modal lo cierre --}}
            >
                {{-- Imagen Principal --}}
                <div class="flex-grow flex items-center justify-center overflow-hidden p-2 sm:p-4 md:p-6">
                    <img src="{{ $photo->thumbnail_path
    ? Storage::disk($disk)->url($photo->thumbnail_path)
    : Storage::disk($disk)->url($photo->file_path) }}"
                        alt="Foto del álbum {{ $album->name }}"
                         class="block max-w-full max-h-[calc(90vh-100px)] object-contain"> {{-- Ajustar max-h según padding y footer --}}
                </div>

                {{-- Footer del Modal (Caption y Contador) --}}
                <div class="flex-shrink-0 flex justify-between items-center px-4 py-2 sm:px-6 sm:py-3 border-t border-gray-200 text-xs sm:text-sm text-gray-700">
                    <div>
                        {{-- Asume un campo 'filename' o similar en tu modelo Photo o usa el ID --}}
                        <span>{{ pathinfo($currentLightboxPhoto->file_path, PATHINFO_FILENAME) ?? 'Foto ' . $currentLightboxPhoto->id }}</span>
                    </div>
                    <div>
                        <span>{{ $currentLightboxPhotoIndex + 1 }} / {{ $photosForCustomLightbox->count() }}</span>
                    </div>
                </div>

                {{-- Botón de Cierre (X) --}}
                <button wire:click="closeCustomLightbox" title="Cerrar (Esc)"
                        class="absolute top-2 right-2 sm:top-3 sm:right-3 z-10 p-1.5 text-gray-500 hover:text-gray-800 bg-white/70 hover:bg-white/90 rounded-full transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                {{-- Botón Anterior --}}
                @if ($currentLightboxPhotoIndex > 0)
                    <button wire:click="previousPhotoInLightbox" title="Anterior"
                            class="absolute left-1 sm:left-2 top-1/2 -translate-y-1/2 z-10 p-2 text-gray-600 hover:text-black bg-white/50 hover:bg-white/80 rounded-full transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 sm:h-7 sm:w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                @endif

                {{-- Botón Siguiente --}}
                @if ($currentLightboxPhotoIndex < ($photosForCustomLightbox->count() - 1))
                    <button wire:click="nextPhotoInLightbox" title="Siguiente"
                            class="absolute right-1 sm:right-2 top-1/2 -translate-y-1/2 z-10 p-2 text-gray-600 hover:text-black bg-white/50 hover:bg-white/80 rounded-full transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 sm:h-7 sm:w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                @endif
            </div>
        </div>
    @endif
</div>

@push('styles')
<style>
    .photo-gallery {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 4px; /* Espacio muy pequeño entre imágenes */
    }
    .gallery-item {
        aspect-ratio: 1 / 1; /* Hace que cada item de la galería sea cuadrado */
        overflow: hidden;
        cursor: pointer;
    }
    .gallery-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
</style>
@endpush


