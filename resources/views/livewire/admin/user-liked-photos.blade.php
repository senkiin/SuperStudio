{{-- ========================================================================= --}}
{{--  Archivo: resources/views/livewire/admin/user-liked-photos.blade.php     --}}
{{--  Descripción: Permite a un admin buscar un cliente y ver sus fotos fav. --}}
{{-- ========================================================================= --}}
<div class="p-4 sm:p-6 lg:p-8">

    {{-- Verificación de permiso --}}
    @if(auth()->user()?->role == 'admin') {{-- ¡Ajusta rol! --}}

        <h1 class="text-2xl font-semibold text-gray-900 mb-6">Ver Fotos Favoritas por Cliente</h1>

        {{-- Mensajes Flash --}}
        @if (session()->has('message')) <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">{{ session('message') }}</div> @endif
        @if (session()->has('error')) <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">{{ session('error') }}</div> @endif

        {{-- Campo de Búsqueda de Cliente --}}
        <div class="mb-1 max-w-md relative"> {{-- Añadido relative para el loading --}}
            <label for="clientSearch" class="block text-sm font-medium text-gray-700 mb-1">Buscar Cliente (Nombre o Email):</label>
            <input type="text" id="clientSearch"
                   wire:model.live.debounce.300ms="searchQuery"
                   placeholder="Escribe para buscar..."
                   autocomplete="off" {{-- Evita sugerencias del navegador que interfieran --}}
                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm pr-10"> {{-- Añadido padding derecho para el spinner --}}
            {{-- Indicador de carga para la búsqueda --}}
            <div wire:loading wire:target="searchQuery" class="absolute top-7 right-2 mt-1">
                 <svg class="animate-spin h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
            </div>
        </div>

        {{-- Resultados de la Búsqueda --}}
        @if(!empty($searchQuery) && $filteredClients->isNotEmpty())
            <div class="mb-4 max-w-md border rounded-md shadow-sm bg-white max-h-60 overflow-y-auto z-10 absolute w-full" style="max-width: inherit;"> {{-- Asegura que esté sobre otros elementos si es necesario --}}
                <ul>
                    @foreach($filteredClients as $client)
                        <li class="px-3 py-2 hover:bg-indigo-100 cursor-pointer border-b last:border-b-0 text-sm"
                            wire:click="selectClient({{ $client->id }})"
                            wire:key="client-{{ $client->id }}"> {{-- wire:key es bueno para listas dinámicas --}}
                            {{ $client->name }} <span class="text-gray-500">({{ $client->email }})</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @elseif(!empty($searchQuery) && $filteredClients->isEmpty())
            <div class="mb-4 max-w-md text-sm text-gray-500 px-3 py-2 border rounded-md bg-gray-50">
                No se encontraron clientes que coincidan con "{{ $searchQuery }}".
            </div>
        @endif


        {{-- Rejilla de Fotos Favoritas (Solo si se ha seleccionado un cliente y hay fotos) --}}
        <div class="mt-6"> {{-- Añadido margen superior para separar de la búsqueda/resultados --}}
            @if($selectedClient && $likedPhotos)
                <hr class="my-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">
                    Fotos Favoritas de: <span class="font-bold">{{ $selectedClient->name }}</span>
                    <span class="text-sm text-gray-600">({{ $selectedClient->email }})</span>
                </h2>

                @if($likedPhotos->count() > 0)
                    <div wire:loading.remove wire:target="adminUnlikePhoto, selectClient"> {{-- Ocultar mientras se quita like O SE CAMBIA DE CLIENTE --}}
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                            @foreach($likedPhotos as $photo)
                                <div class="relative group aspect-square">
                                      {{-- Imagen (con fallback) --}}
                                    <img src="{{ $photo->thumbnail_path && Storage::disk('public')->exists($photo->thumbnail_path) ? Storage::url($photo->thumbnail_path) : ($photo->file_path && Storage::disk('public')->exists($photo->file_path) ? Storage::url($photo->file_path) : asset('images/placeholder-photo.png')) }}"
                                         alt="Foto favorita del álbum {{ $photo->album?->name ?? 'Desconocido' }}"
                                         loading="lazy"
                                         class="rounded-lg shadow object-cover w-full h-full">

                                    {{-- Botón para quitar like (Admin) --}}
                                    <button wire:click="adminUnlikePhoto({{ $photo->id }})"
                                            wire:loading.attr="disabled" wire:target="adminUnlikePhoto({{ $photo->id }})"
                                            wire:key="unlike-{{ $photo->id }}-{{ $selectedClientId }}" {{-- Key única --}}
                                            title="Quitar Like (Admin)"
                                            class="absolute top-1 right-1 z-20 p-1 bg-red-600 text-white rounded-full shadow opacity-0 group-hover:opacity-100 focus:opacity-100 transition-opacity duration-200 ease-in-out hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1">
                                        {{-- Icono loading para unlike --}}
                                        <svg wire:loading wire:target="adminUnlikePhoto({{ $photo->id }})" class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
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
                    {{-- Indicador loading general para unlike --}}
                    <div wire:loading wire:target="adminUnlikePhoto" class="text-sm text-red-600 mt-2">Quitando like...</div>

                @else
                    {{-- Mensaje si el cliente seleccionado no tiene favoritos --}}
                    <p class="text-gray-500 mt-4">Este cliente no ha marcado ninguna foto como favorita.</p>
                @endif

            {{-- Mensaje inicial si no se ha seleccionado cliente y no se está buscando --}}
            @elseif(!$selectedClientId && empty($searchQuery))
                <p class="text-gray-500 mt-4 italic">Por favor, busca y selecciona un cliente para ver sus fotos favoritas.</p>
            @endif
        </div>

    {{-- Mensaje si el usuario no es admin --}}
    @else
        <p class="text-red-600">No tienes permiso para acceder a esta sección.</p>
    @endif

</div>
