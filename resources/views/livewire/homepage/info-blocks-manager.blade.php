{{-- resources/views/livewire/homepage/info-blocks-manager.blade.php --}}
<div> {{-- Root div --}}
    <div class="bg-black">

        {{-- Contenedor CON MÁXIMO ANCHO para elementos NO repetitivos --}}
        <div class="bg-black">
            {{-- Mensajes Flash --}}
            @if (session()->has('message'))
                <div x-data="{ show: true }" x-show="show" x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-init="setTimeout(() => show = false, 3500)"
                    class="flex items-center p-4 mb-4 text-sm font-medium text-green-800 rounded-lg bg-green-100 dark:bg-gray-800 dark:text-green-400 border border-green-200 dark:border-green-700 shadow-sm"
                    role="alert">
                    <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                    </svg>
                    <span class="sr-only">Info</span>
                    <div>{{ session('message') }}</div>
                </div>
            @endif
            @if (session()->has('error'))
                <div x-data="{ show: true }" x-show="show" x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-init="setTimeout(() => show = false, 3500)"
                    class="flex items-center p-4 mb-4 text-sm font-medium text-red-800 rounded-lg bg-red-100 dark:bg-gray-800 dark:text-red-400 border border-red-200 dark:border-red-700 shadow-sm"
                    role="alert">
                    <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                    </svg>
                    <span class="sr-only">Error</span>
                    <div>{{ session('error') }}</div>
                </div>
            @endif

            {{-- Botón Añadir Bloque --}}
            @if ($isAdmin)
                <div class="flex justify-end pt-4">
                    <x-button wire:click="openCreateModal"
                        class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium text-center text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-300 dark:focus:ring-indigo-800 transition duration-150 ease-in-out">
                        <svg class="w-5 h-5 -ms-1 me-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Añadir Bloque
                    </x-button>
                </div>
            @endif
        </div>

        {{-- Contenedor de los bloques SIN MÁXIMO ANCHO y SIN ESPACIO VERTICAL --}}
        <div class="space-y-0">
            @forelse ($blocks as $block)
                {{-- Contenedor del bloque individual (full width) --}}
                <div wire:key="block-{{ $block->id }}"
                    class="relative group flex flex-col md:flex-row overflow-hidden
                           {{ $block->image_position === 'right' ? 'md:flex-row-reverse' : 'md:flex-row' }}">


                    {{-- *** Columna de Texto (50%) *** --}}
                    <div x-data x-init="$el.classList.add('aos-fade-up-big')" data-aos
                        class="transition-all duration-[1500ms] w-full md:w-1/2 bg-black text-white p-8 md:p-10 lg:p-16 flex items-center justify-center order-2 md:order-none min-h-[300px] md:min-h-0">
                        <div class="text-center max-w-lg">
                            {{-- Controles Admin --}}
                            @if ($isAdmin)
                                <div
                                    class="absolute top-4 right-4 md:relative md:top-auto md:right-auto md:self-end flex space-x-1.5 mb-6 md:mb-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-10">
                                    <button wire:click="openEditModal({{ $block->id }})" title="Editar"
                                        class="p-1.5 bg-blue-600 text-white rounded-full hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-black focus:ring-blue-500 transition duration-150 ease-in-out">
                                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                                        </svg>
                                    </button>
                                    <button wire:click="confirmDelete({{ $block->id }})" title="Eliminar"
                                        class="p-1.5 bg-red-600 text-white rounded-full hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-black focus:ring-red-500 transition duration-150 ease-in-out">
                                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </button>
                                </div>
                            @endif

                            {{-- Título --}}
                            <h3
                                class="text-2xl sm:text-3xl lg:text-4xl font-bold font-sans uppercase tracking-wide text-gray-100 mb-6 antialiased">
                                {{ $block->title }}
                            </h3>

                            {{-- Botón --}}
                            @if ($block->link_url)
                                <a href="{{ $block->link_url }}" target="_blank" rel="noopener noreferrer"
                                    class="inline-flex items-center px-5 py-2 text-sm border border-white text-white hover:bg-white hover:text-black transition rounded">
                                    {{ $block->link_text ?: 'Saber Más' }}
                                    <svg class="w-4 h-4 ml-2 transition-transform duration-150 ease-in-out group-hover/button:translate-x-1"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                    </svg>
                                </a>
                            @endif
                        </div>
                    </div>


                    {{-- *** Columna de Imagen (50%) *** --}}
                    <div x-data x-init="$el.classList.add(
                        '{{ $block->image_position === 'right' ? 'aos-fade-left-big' : 'aos-fade-right-big' }}'
                    )" data-aos
                        class="transition-all duration-[1500ms] w-full md:w-1/2 order-1 md:order-none">
                        @if ($block->image_path)
                            <img src="{{ Storage::disk('info-blocks')->url($block->image_path) }}" alt="{{ $block->title }}"
                                class="block w-full h-80 md:h-full object-cover">
                        @else
                            <div
                                class="w-full h-80 md:h-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                <span class="text-gray-500 italic">Sin imagen</span>
                            </div>
                        @endif
                    </div>

                </div> {{-- Fin del div del bloque --}}
            @empty
                {{-- Mensaje si no hay bloques --}}
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                    @if ($isAdmin)
                        <div class="text-center py-16 px-6 bg-white dark:bg-gray-800 shadow-lg rounded-lg">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                            </svg>
                            <h3 class="mt-2 text-lg font-semibold text-gray-900 dark:text-white">No hay bloques todavía
                            </h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">¡Empieza añadiendo el primer
                                bloque
                                de información!</p>
                            <div class="mt-6">
                                <x-button wire:click="openCreateModal"
                                    class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-center text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-300 dark:focus:ring-indigo-800 transition duration-150 ease-in-out">
                                    <svg class="w-5 h-5 -ms-1 me-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                    Añadir Bloque
                                </x-button>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                            <p>Contenido próximamente.</p>
                        </div>
                    @endif
                </div>
            @endforelse
        </div> {{-- Cierre space-y-0 --}}

        {{-- --------------------- MODAL (Modernized Inputs) --------------------- --}}
        <x-dialog-modal wire:model.live="showModal">
            <x-slot name="title">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    {{ $isEditing ? 'Editar Bloque' : 'Crear Nuevo Bloque' }}
                </h3>
            </x-slot>

            <x-slot name="content">
                <form wire:submit.prevent="saveBlock" id="infoBlockForm" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- Columna 1: Campos de Texto --}}
                        <div class="space-y-5">
                            <div>
                                <x-label for="title" value="{{ __('Título') }}" class="mb-1.5" />
                                <x-input id="title" type="text"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 rounded-md shadow-sm focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:focus:ring-indigo-900"
                                    wire:model="form.title" />
                                <x-input-error for="form.title" class="mt-1.5 text-xs" />
                            </div>
                            <div>
                                <x-label for="description" value="{{ __('Descripción') }}" class="mb-1.5" />
                                <textarea id="description"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 rounded-md shadow-sm focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:focus:ring-indigo-900"
                                    rows="5" wire:model="form.description"></textarea>
                                <x-input-error for="form.description" class="mt-1.5 text-xs" />
                            </div>
                            <div>
                                <x-label for="link_url" value="{{ __('URL Enlace (Opcional)') }}" class="mb-1.5" />
                                <x-input id="link_url" type="url" placeholder="https://ejemplo.com"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 rounded-md shadow-sm focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:focus:ring-indigo-900"
                                    wire:model="form.link_url" />
                                <x-input-error for="form.link_url" class="mt-1.5 text-xs" />
                            </div>
                            <div>
                                <x-label for="link_text" value="{{ __('Texto del Enlace') }}" class="mb-1.5" />
                                <x-input id="link_text" type="text"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 rounded-md shadow-sm focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:focus:ring-indigo-900"
                                    wire:model="form.link_text" />
                                <x-input-error for="form.link_text" class="mt-1.5 text-xs" />
                            </div>
                        </div>

                        {{-- Columna 2: Imagen y Opciones --}}
                        <div class="space-y-5">
                            <div>
                                <x-label for="photo" value="{{ __('Imagen') }}" class="mb-1.5" />
                                <label for="photo" class="block">
                                    <span class="sr-only">Elegir imagen</span>
                                    <input type="file" id="photo" wire:model="photo"
                                        class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 dark:file:bg-gray-700 file:text-indigo-700 dark:file:text-gray-300 hover:file:bg-indigo-100 dark:hover:file:bg-gray-600 transition cursor-pointer" />
                                </label>
                                <x-input-error for="photo" class="mt-1.5 text-xs" />
                                <div wire:loading wire:target="photo"
                                    class="mt-2 text-sm text-indigo-600 dark:text-indigo-400">Cargando...</div>
                                {{-- Previsualización --}}
                                <div class="mt-4">
                                    @if ($photo)
                                        <span
                                            class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Previsualización
                                            Nueva:</span>
                                        <img src="{{ $photo->temporaryUrl() }}"
                                            class="w-full h-auto rounded-md border border-gray-200 dark:border-gray-700 object-contain max-h-40">
                                    @elseif ($current_image_path)
                                        <span
                                            class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Imagen
                                            Actual:</span>
                                        <img src="{{ Storage::disk('info-blocks')->url($current_image_path) }}"
                                            class="w-full h-auto rounded-md border border-gray-200 dark:border-gray-700 object-contain max-h-40">
                                    @else
                                        <div
                                            class="w-full h-32 bg-gray-100 dark:bg-gray-700/50 rounded-md border border-dashed border-gray-300 dark:border-gray-600 flex items-center justify-center">
                                            <span class="text-gray-400 dark:text-gray-500 text-sm italic">Vista
                                                previa</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <x-label for="image_position" value="{{ __('Posición Imagen') }}" class="mb-1.5" />
                                <select id="image_position" wire:model="form.image_position"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 rounded-md shadow-sm focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:focus:ring-indigo-900">
                                    <option value="left">Izquierda (Texto - Imagen)</option>
                                    <option value="right">Derecha (Imagen - Texto)</option>
                                </select>
                                <x-input-error for="form.image_position" class="mt-1.5 text-xs" />
                            </div>
                            <div>
                                <x-label for="order_column" value="{{ __('Orden') }}" class="mb-1.5" />
                                <x-input id="order_column" type="number" step="1" min="0"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 rounded-md shadow-sm focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:focus:ring-indigo-900"
                                    wire:model="form.order_column" />
                                <x-input-error for="form.order_column" class="mt-1.5 text-xs" />
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Los números menores aparecen
                                    primero.</p>
                            </div>
                        </div>
                    </div>
                </form>
            </x-slot>

            <x-slot name="footer">
                <x-secondary-button wire:click="closeModal" wire:loading.attr="disabled">
                    Cancelar
                </x-secondary-button>
                <x-button type="submit" form="infoBlockForm"
                    class="ml-3 bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500" wire:loading.attr="disabled"
                    wire:target="saveBlock, photo">
                    <span wire:loading wire:target="saveBlock, photo" class="inline-flex items-center mr-2">
                        {{-- Changed span to inline-flex for alignment --}}
                        <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24"> {{-- Adjusted margin --}}
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </span>
                    <span wire:loading.remove wire:target="saveBlock, photo">
                        {{ $isEditing ? 'Guardar Cambios' : 'Crear Bloque' }}
                    </span>
                    <span wire:loading wire:target="saveBlock, photo">Guardando...</span>
                </x-button>
            </x-slot>
        </x-dialog-modal>

        {{-- --------------------- MODAL CONFIRMACIÓN DELETE --------------------- --}}
        <x-confirmation-modal wire:model.live="showConfirmDeleteModal">
            <x-slot name="title">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Confirmar Eliminación
                </h3>
            </x-slot>
            <x-slot name="content">
                <p class="text-base text-gray-600 dark:text-gray-300">
                    ¿Estás seguro? Esta acción no se puede deshacer y la imagen asociada (si existe) también será
                    eliminada permanentemente del servidor.
                </p>
            </x-slot>
            <x-slot name="footer">
                <x-secondary-button wire:click="$set('showConfirmDeleteModal', false)" wire:loading.attr="disabled">
                    Cancelar
                </x-secondary-button>
                <x-danger-button class="ml-3" wire:click="deleteBlock" wire:loading.attr="disabled"
                    wire:target="deleteBlock">
                    <span wire:loading wire:target="deleteBlock" class="inline-flex items-center mr-2">
                        {{-- Changed span to inline-flex for alignment --}}
                        <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">{{-- Adjusted margin --}}
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </span>
                    <span wire:loading.remove wire:target="deleteBlock">Eliminar Bloque</span>
                    <span wire:loading wire:target="deleteBlock">Eliminando...</span>
                </x-danger-button>
            </x-slot>
        </x-confirmation-modal>

    </div> {{-- Cierre bg-gray-100 --}}
</div> {{-- Cierre del root div --}}
