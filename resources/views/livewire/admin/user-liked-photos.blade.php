{{-- ========================================================================= --}}
{{--  Archivo: resources/views/livewire/admin/user-liked-photos.blade.php    --}}
{{--  Descripción: Permite a un admin ver las fotos favoritas de un cliente.  --}}
{{-- ========================================================================= --}}
<div class="p-4 sm:p-6 lg:p-8">

    {{-- Título (y verificación de permiso por si acaso) --}}
    @if(auth()->user()?->role == 'admin') {{-- ¡Ajusta rol! --}}

        <h1 class="text-2xl font-semibold text-gray-900 mb-6">Ver Fotos Favoritas por Cliente</h1>

        {{-- Mensajes Flash --}}
        @if (session()->has('error')) <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">{{ session('error') }}</div> @endif

        {{-- Selector de Cliente --}}
        <div class="mb-6 max-w-md">
            <label for="clientSelector" class="block text-sm font-medium text-gray-700 mb-1">Selecciona un Cliente:</label>
            <select id="clientSelector"
                    wire:model.live="selectedClientId" {{-- .live actualiza al cambiar --}}
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <option value="">-- Todos los Clientes --</option> {{-- Opcional: o '-- Selecciona --' si es obligatorio elegir uno --}}
                @forelse($users as $client)
                    <option value="{{ $client->id }}">{{ $client->name }} ({{ $client->email }})</option>
                @empty
                    <option disabled>No hay clientes para mostrar</option>
                @endforelse
            </select>
        </div>

        {{-- Rejilla de Fotos Favoritas (Solo si se ha seleccionado un cliente y hay fotos) --}}
        @if($selectedClientId && $likedPhotos)
            <hr class="my-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Fotos Favoritas de {{ $users->firstWhere('id', $selectedClientId)?->name ?? 'Cliente Desconocido' }}</h2>

            @if($likedPhotos->count() > 0)
                <div wire:loading.remove wire:target="adminUnlikePhoto"> {{-- Ocultar mientras se quita like --}}
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                        @foreach($likedPhotos as $photo)
                            <div class="relative group aspect-square">
                                 <img src="{{ $photo->thumbnail_path && Storage::disk('public')->exists($photo->thumbnail_path) ? Storage::url($photo->thumbnail_path) : ($photo->file_path && Storage::disk('public')->exists($photo->file_path) ? Storage::url($photo->file_path) : asset('images/placeholder-photo.png')) }}"
                                      alt="Foto favorita del álbum {{ $photo->album?->name ?? 'Desconocido' }}"
                                      loading="lazy"
                                      class="rounded-lg shadow object-cover w-full h-full">

                                 {{-- Botón para quitar like (Admin) --}}
                                 <button wire:click="adminUnlikePhoto({{ $photo->id }})" wire:loading.attr="disabled" wire:target="adminUnlikePhoto({{ $photo->id }})"
                                         title="Quitar Like (Admin)"
                                         class="absolute top-1 right-1 z-20 p-1 bg-red-600 text-white rounded-full shadow opacity-0 group-hover:opacity-100 focus:opacity-100 transition-opacity duration-200 ease-in-out hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1">
                                    {{-- Icono loading --}}
                                    <svg wire:loading wire:target="adminUnlikePhoto({{ $photo->id }})" class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">...</svg>
                                     {{-- Icono 'X' o Papelera --}}
                                     <svg wire:loading.remove wire:target="adminUnlikePhoto({{ $photo->id }})" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                                 </button>

                                 {{-- Nombre del álbum --}}
                                 @if($photo->album)
                                 <div class="absolute bottom-0 left-0 right-0 p-1.5 bg-gradient-to-t from-black/60 to-transparent text-white text-xs truncate text-center pointer-events-none">
                                     Álbum: {{ $photo->album->name }}
                                 </div>
                                 @endif
                            </div>
                        @endforeach
                    </div>
                    {{-- Paginación --}}
                    <div class="mt-6">
                        {{ $likedPhotos->links() }}
                    </div>
                </div>
                 {{-- Indicador loading para unlike --}}
                <div wire:loading wire:target="adminUnlikePhoto" class="text-sm text-red-600 mt-2">Quitando like...</div>

            @else
                {{-- Mensaje si el cliente seleccionado no tiene favoritos --}}
                <p class="text-gray-500 mt-4">Este cliente no ha marcado ninguna foto como favorita.</p>
            @endif

        {{-- Mostrar mensaje inicial si no se ha seleccionado cliente --}}
        @elseif(!$selectedClientId && auth()->user()?->role == 'admin')
             <p class="text-gray-500 mt-4">Por favor, selecciona un cliente para ver sus fotos favoritas.</p>
        @endif

    {{-- Mensaje si el usuario no es admin --}}
    @else
         <p class="text-red-600">No tienes permiso para acceder a esta sección.</p>
    @endif

</div>
