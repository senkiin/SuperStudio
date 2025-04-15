{{-- ========================================================================= --}}
{{--  Archivo: resources/views/livewire/liked-photos.blade.php               --}}
{{--  Descripción: Muestra las fotos marcadas como favoritas por el usuario.   --}}
{{-- ========================================================================= --}}
<div class="p-4 sm:p-6 lg:p-8">

    {{-- Indicador de Carga Global (Opcional, si haces acciones como unlike) --}}
    <div wire:loading ...> ... </div>

    {{-- Título de la Página --}}
    <h1 class="text-2xl font-semibold text-gray-900 mb-6">Mis Fotos Favoritas</h1>

    {{-- Rejilla de Fotos con Like --}}
    {{-- Verifica si la variable existe y tiene elementos (importante si puede ser null) --}}
    @if($likedPhotos && $likedPhotos->count() > 0)
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
            @foreach ($likedPhotos as $photo)
                <div class="relative group aspect-square rounded-lg shadow-sm overflow-hidden border border-transparent hover:border-gray-300"
                     wire:key="liked-photo-{{ $photo->id }}">

                    {{-- Imagen (Usa thumbnail si existe) --}}
                    <img src="{{ $photo->thumbnail_path && Storage::disk('public')->exists($photo->thumbnail_path) ? Storage::url($photo->thumbnail_path) : ($photo->file_path && Storage::disk('public')->exists($photo->file_path) ? Storage::url($photo->file_path) : asset('images/placeholder-photo.png')) }}"
                         alt="Foto favorita del álbum {{ $photo->album?->name ?? 'Desconocido' }}" {{-- Muestra nombre del álbum si existe --}}
                         loading="lazy"
                         class="block w-full h-full object-cover">

                    {{-- Botón para Quitar Like (Opcional) --}}
                    <button wire:click="unlikePhoto({{ $photo->id }})" wire:loading.attr="disabled"
                            title="Quitar de Favoritos"
                            class="absolute top-1.5 right-1.5 z-20 p-1 bg-red-600 text-white rounded-full shadow opacity-0 group-hover:opacity-100 focus:opacity-100 transition-opacity duration-200 ease-in-out hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1">
                        {{-- Icono 'X' o Papelera como ejemplo --}}
                         <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>

                    {{-- Información del Álbum (Opcional) --}}
                    @if($photo->album)
                        <div class="absolute bottom-0 left-0 right-0 p-1.5 bg-gradient-to-t from-black/60 to-transparent text-white text-xs truncate text-center pointer-events-none">
                            {{ $photo->album->name }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Paginación para las fotos favoritas --}}
        <div class="mt-8">
            {{ $likedPhotos->links() }}
        </div>
    @elseif(is_null($likedPhotos))
        {{-- Mensaje si el usuario no está logueado (o si elegiste devolver null) --}}
         <p class="text-center text-gray-500 py-10">Debes iniciar sesión para ver tus fotos favoritas.</p>
    @else
        {{-- Mensaje si el usuario está logueado pero no tiene favoritos --}}
        <div class="text-center text-gray-500 py-16">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" ><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" /></svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Sin Favoritos</h3>
            <p class="mt-1 text-sm text-gray-500">Aún no has marcado ninguna foto como favorita. ¡Haz doble clic en una foto que te guste!</p>
          </div>
    @endif
</div>
