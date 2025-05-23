{{-- resources/views/livewire/admin/manage-homepage-carousel.blade.php --}}

{{-- Usamos el componente x-dialog-modal de Jetstream --}}
{{-- wire:model.live="showModal" vincula directamente la visibilidad al $showModal de Livewire --}}
<x-dialog-modal wire:model.live="showModal" maxWidth="4xl">

    <x-slot name="title">
        Gestionar Carrusel de Inicio
    </x-slot>

    <x-slot name="content">
        {{-- El contenido principal del modal va aquí --}}
        {{-- Feedback visual durante cargas de Livewire --}}
        <div wire:loading.class="opacity-50 pointer-events-none"
            wire:target="openModal,closeModal,uploadImage,deleteImage,addFromFavorite,toggleActive,updateImageOrder,startEditing,saveEditing,cancelEditing">

            {{-- Mensajes Flash --}}
            @if (session()->has('message'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                    class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded text-sm">
                    {{ session('message') }}
                </div>
            @endif
            @if (session()->has('error'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                    class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded text-sm">
                    {{ session('error') }}
                </div>
            @endif

            {{-- 1. Sección para Añadir Nueva Imagen --}}
            <div class="mb-8 border rounded-lg p-4">
                <h3 class="text-lg font-medium mb-3">Añadir Nueva Imagen</h3>
                <form wire:submit.prevent="uploadImage">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Input File --}}
                        <div>
                            <label for="newImage" class="block text-sm font-medium text-gray-700">Archivo de
                                Imagen</label>
                            <input type="file" id="newImage" wire:model="newImage"
                                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            <div wire:loading wire:target="newImage" class="mt-1 text-xs text-blue-600">Cargando
                                imagen...</div>
                            @error('newImage')
                                <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                            @enderror

                            {{-- Previsualización --}}
                            @if ($newImage && !$errors->has('newImage'))
                                <div class="mt-2">
                                    <span class="block text-xs text-gray-500 mb-1">Previsualización:</span>
                                    <img src="{{ $newImage->temporaryUrl() }}" alt="Previsualización"
                                        class="h-24 w-auto object-cover rounded border shadow-sm">
                                </div>
                            @endif
                        </div>

                        {{-- Otros Campos --}}
                        <div>
                            <div>
                                <label for="newCaption" class="block text-sm font-medium text-gray-700">Título
                                    (Opcional)</label>
                                <input type="text" id="newCaption" wire:model="newCaption"
                                    placeholder="Ej: Viaje a Islandia"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                @error('newCaption')
                                    <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mt-3">
                                <label for="newLinkUrl" class="block text-sm font-medium text-gray-700">Enlace URL
                                    (Opcional)</label>
                                <input type="url" id="newLinkUrl" wire:model="newLinkUrl"
                                    placeholder="https://ejemplo.com/destino"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                @error('newLinkUrl')
                                    <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 text-right">
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-50 transition ease-in-out duration-150"
                            wire:loading.attr="disabled" wire:target="uploadImage">
                            <svg wire:loading wire:target="uploadImage"
                                class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span wire:loading.remove wire:target="uploadImage">Añadir Imagen</span>
                            <span wire:loading wire:target="uploadImage">Añadiendo...</span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- 2. Sección para Imágenes Actuales en el Carrusel --}}
            <div class="mb-8">
                <h3 class="text-lg font-medium mb-3">Imágenes Actuales en el Carrusel</h3>
                {{-- Contenedor Sortable con ID único --}}
                <div wire:sortable="updateImageOrder" id="carousel-sortable-container-modal" class="space-y-3">
                    @forelse ($currentImages as $image)
                        {{-- Contenedor principal del item --}}
                        <div wire:sortable.item="{{ $image->id }}" wire:key="carousel-item-{{ $image->id }}"
                            class="border rounded-md transition group flex flex-col sm:flex-row {{ $editingImageId === $image->id ? 'bg-indigo-50 border-indigo-200 shadow-sm' : 'hover:bg-gray-50' }}">

                            {{-- Contenedor Izquierdo: Handle, Miniatura e Info/Formulario Edición --}}
                            <div class="flex items-center p-3 flex-grow space-x-3">
                                {{-- Handle para Drag & Drop --}}
                                <button wire:sortable.handle title="Mover"
                                    class="cursor-move text-gray-400 hover:text-gray-600 flex-shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 6h16M4 12h16M4 18h16"></path>
                                    </svg>
                                </button>
                                {{-- Miniatura --}}
                                <img src="{{ $image->thumbnail_url ?? ($image->image_url ?? asset('images/placeholder-thumb.png')) }}"alt="Miniatura"
                                    class="h-16 w-24 object-cover rounded flex-shrink-0">
                                {{-- Contenido: Info Estática o Formulario de Edición --}}
                                <div class="flex-grow min-w-0">
                                    @if ($editingImageId === $image->id)
                                        {{-- FORMULARIO DE EDICIÓN --}}
                                        <div class="space-y-2">
                                            <div>
                                                <label for="edit-caption-{{ $image->id }}"
                                                    class="sr-only">Título</label>
                                                <input type="text" id="edit-caption-{{ $image->id }}"
                                                    wire:model="editingImageCaption" placeholder="Título (opcional)"
                                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-1.5 px-2 @error('editingImageCaption') border-red-500 @enderror">
                                                @error('editingImageCaption')
                                                    <span class="text-xs text-red-600 mt-1">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div>
                                                <label for="edit-link-{{ $image->id }}" class="sr-only">Enlace
                                                    URL</label>
                                                <input type="url" id="edit-link-{{ $image->id }}"
                                                    wire:model="editingImageLinkUrl"
                                                    placeholder="Enlace URL (opcional)"
                                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-1.5 px-2 @error('editingImageLinkUrl') border-red-500 @enderror">
                                                @error('editingImageLinkUrl')
                                                    <span class="text-xs text-red-600 mt-1">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="flex items-center justify-end space-x-2 pt-1">
                                                <button wire:click="cancelEditing" type="button"
                                                    class="px-2.5 py-1 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-gray-400">Cancelar</button>
                                                <button wire:click="saveEditing" type="button"
                                                    wire:loading.attr="disabled" wire:target="saveEditing"
                                                    class="inline-flex items-center px-2.5 py-1 text-xs font-semibold text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-green-500 disabled:opacity-50">
                                                    <svg wire:loading wire:target="saveEditing"
                                                        class="animate-spin -ml-0.5 mr-1.5 h-3 w-3 text-white"
                                                        fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12"
                                                            r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor"
                                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                        </path>
                                                    </svg>
                                                    <span wire:loading.remove wire:target="saveEditing">Guardar</span>
                                                    <span wire:loading wire:target="saveEditing">Guardando...</span>
                                                </button>
                                            </div>
                                        </div>
                                    @else
                                        {{-- INFORMACIÓN ESTÁTICA --}}
                                        <div class="text-sm">
                                            <p class="font-medium text-gray-800 truncate"
                                                title="{{ $image->caption }}">{{ $image->caption ?: '(Sin título)' }}
                                            </p>
                                            @if ($image->link_url)
                                                <a href="{{ $image->link_url }}" target="_blank"
                                                    class="text-xs text-blue-600 hover:underline truncate block max-w-xs"
                                                    title="{{ $image->link_url }}">{{ $image->link_url }}</a>
                                            @else
                                                <span class="text-xs text-gray-400">(Sin enlace)</span>
                                            @endif
                                            <p class="text-xs text-gray-400 mt-1">Orden: {{ $image->order }} | Estado:
                                                {{ $image->is_active ? 'Activa' : 'Inactiva' }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Contenedor Derecho: Acciones --}}
                            @if ($editingImageId !== $image->id)
                                <div
                                    class="flex items-center space-x-1 sm:space-x-2 p-3 border-t sm:border-t-0 sm:border-l border-gray-200 flex-shrink-0 justify-end">
                                    {{-- Botón Editar --}}
                                    <button wire:click="startEditing({{ $image->id }})"
                                        title="Editar título/enlace"
                                        class="p-1.5 rounded-md text-gray-500 hover:text-blue-600 hover:bg-blue-100 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10">
                                            </path>
                                        </svg>
                                    </button>
                                    {{-- Botón Activar/Desactivar --}}
                                    <button wire:click="toggleActive({{ $image->id }})"
                                        title="{{ $image->is_active ? 'Desactivar' : 'Activar' }}"
                                        class="p-1.5 rounded-full {{ $image->is_active ? 'hover:bg-gray-200' : 'hover:bg-green-100' }} transition">
                                        @if ($image->is_active)
                                            <svg class="w-5 h-5 text-green-500" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5 text-gray-400" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                        @endif
                                    </button>
                                    {{-- Botón Eliminar --}}
                                    <button wire:click="deleteImage({{ $image->id }})"
                                        wire:confirm="¿Estás seguro de eliminar esta imagen del carrusel? Esta acción borrará también el archivo."
                                        wire:loading.attr="disabled" wire:target="deleteImage({{ $image->id }})"
                                        title="Eliminar imagen"
                                        class="p-1.5 rounded-full text-red-500 hover:bg-red-100 transition">
                                        <svg wire:loading.remove wire:target="deleteImage({{ $image->id }})"
                                            class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        <svg wire:loading wire:target="deleteImage({{ $image->id }})"
                                            class="animate-spin h-5 w-5 text-red-500"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                    </button>
                                </div>
                            @endif
                        </div> {{-- Fin Contenedor principal del item --}}
                    @empty
                        <p class="text-gray-500 text-sm text-center py-4">No hay imágenes en el carrusel actualmente.
                        </p>
                    @endforelse
                </div> {{-- Fin Contenedor Sortable --}}
                <p class="text-xs text-gray-500 mt-2 italic">Puedes arrastrar y soltar las imágenes usando el icono
                    <svg class="inline-block w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg> para reordenarlas.</p>
            </div>

            {{-- 3. Sección para Añadir desde Favoritos --}}
            <div class="mb-8 border-t pt-6">
                <h3 class="text-lg font-medium mb-3">Añadir desde Mis Favoritos</h3>
                {{-- Condicional para mostrar solo si el modal está abierto --}}
                @if ($showModal && $favoritePhotos && $favoritePhotos->total() > 0)
                    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-3">
                        @foreach ($favoritePhotos as $favPhoto)
                            <div class="relative group aspect-square" wire:key="fav-{{ $favPhoto->id }}">
                                <img src="{{ $favPhoto->thumbnail_path && Storage::disk('albums')->exists($favPhoto->thumbnail_path)
                                    ? Storage::disk('albums')->url($favPhoto->thumbnail_path)
                                    : ($favPhoto->file_path && Storage::disk('albums')->exists($favPhoto->file_path)
                                        ? Storage::disk('albums')->url($favPhoto->file_path)
                                        : asset('images/placeholder-photo.png')) }}"
                                    alt="Favorita: {{ $favPhoto->album->name ?? '' }}"
                                    class="w-full h-full object-cover rounded-md border">
                                <button wire:click="addFromFavorite({{ $favPhoto->id }})"
                                    wire:loading.attr="disabled" wire:target="addFromFavorite({{ $favPhoto->id }})"
                                    title="Añadir al Carrusel"
                                    class="absolute top-1 right-1 z-10 p-1.5 bg-blue-600 text-white rounded-full shadow opacity-0 group-hover:opacity-100 focus:opacity-100 transition-opacity duration-200 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <svg wire:loading wire:target="addFromFavorite({{ $favPhoto->id }})"
                                        class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    <svg wire:loading.remove wire:target="addFromFavorite({{ $favPhoto->id }})"
                                        class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 4.5v15m7.5-7.5h-15"></path>
                                    </svg>
                                </button>
                                @if ($favPhoto->album)
                                    <div
                                        class="absolute bottom-0 left-0 right-0 p-1 bg-gradient-to-t from-black/60 to-transparent text-white text-[10px] truncate text-center pointer-events-none rounded-b-md">
                                        {{ $favPhoto->album->name }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4">
                        {{-- Paginación de Livewire --}}
                        {{ $favoritePhotos->links(data: ['scrollTo' => false]) }}
                    </div>
                @elseif ($showModal)
                    <p class="text-gray-500 text-sm">No tienes fotos marcadas como favoritas.</p>
                @else
                    {{-- Placeholder o spinner mientras carga --}}
                    <div class="text-center text-gray-500 text-sm py-4">Cargando favoritos...</div>
                @endif
            </div>

        </div> {{-- Fin div con wire:loading --}}
    </x-slot>

    <x-slot name="footer">
        {{-- Botón secundario para cerrar el modal --}}
        <x-secondary-button wire:click="closeModal" wire:loading.attr="disabled">
            Cerrar
        </x-secondary-button>

        {{-- Ejemplo de botón primario si fuera necesario --}}
        {{-- <x-button class="ml-3" wire:click="someAction" wire:loading.attr="disabled">
            Guardar
        </x-button> --}}
    </x-slot>

</x-dialog-modal>


{{-- Script para SortableJS --}}
{{-- ESTE DEBE SER EL ÚNICO BLOQUE @pushOnce('scripts') EN ESTE ARCHIVO --}}
@pushOnce('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script>
        function initializeCarouselSortable(elementId) {
            // Usaremos un intervalo corto para verificar si el elemento está listo
            let checkCount = 0;
            let maxChecks = 20; // Intentar por 2 segundos máximo (20 * 100ms)
            let checkInterval = setInterval(() => {
                checkCount++;
                let el = document.getElementById(elementId);
                console.log(`(Check ${checkCount}/${maxChecks}) Checking for Sortable element: #${elementId}`,
                el); // Log: Verifica si se encuentra

                if (el) {
                    clearInterval(checkInterval); // Detener la verificación una vez encontrado
                    if (!el.sortableInstance) {
                        console.log('Element found. Initializing SortableJS on:', elementId);
                        el.sortableInstance = Sortable.create(el, {
                            handle: '[wire\\:sortable\\.handle]',
                            animation: 150,
                            ghostClass: 'bg-indigo-100 opacity-50',
                            onEnd: function(evt) {
                                console.log('SortableJS onEnd event fired.'); // Log
                                let items = Array.from(evt.to.children).map((item, index) => ({
                                    order: index,
                                    value: item.getAttribute('wire:sortable.item')
                                }));
                                let componentId = el.closest('[wire\\:id]')?.getAttribute(
                                'wire:id'); // Safe navigation
                                if (componentId) {
                                    let component = Livewire.find(componentId);
                                    if (component) {
                                        console.log('Calling updateImageOrder with:', items); // Log
                                        component.call('updateImageOrder', items);
                                    } else {
                                        console.error('Livewire component instance not found for ID:',
                                            componentId);
                                    }
                                } else {
                                    console.error('Could not find parent Livewire component ID.');
                                }
                            }
                        });
                    } else {
                        console.log('SortableJS already initialized on:', elementId);
                    }
                } else if (checkCount >= maxChecks) {
                    clearInterval(checkInterval); // Detener si no se encuentra
                    console.error(`Sortable element #${elementId} not found after ${maxChecks * 100}ms.`);
                }
            }, 100); // Revisa cada 100ms
        }

        document.addEventListener('livewire:init', () => {
            let sortableContainerId = 'carousel-sortable-container-modal';
            console.log('Livewire init: Setting up listener for openCarouselModal'); // Log

            window.Livewire.on('openCarouselModal', () => {
                console.log('Event openCarouselModal received.'); // Log
                let el = document.getElementById(sortableContainerId);
                if (el && el.sortableInstance) {
                    console.log('Destroying previous SortableJS instance.');
                    try {
                        el.sortableInstance.destroy();
                    } catch (e) {
                        console.error("Error destroying sortable:", e);
                    }
                    el.sortableInstance = null;
                } else {
                    console.log(
                        'No previous SortableJS instance found to destroy or element not found yet.');
                }
                initializeCarouselSortable(sortableContainerId);
            });
        });
    </script>
@endPushOnce
