{{-- resources/views/livewire/homepage/content-cards.blade.php --}}
<div x-data="{ isAdmin: @js($isAdmin) }" class=" bg-black dark:bg-black">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Flash Messages (Copied from previous component) --}}
        @if (session()->has('message'))
            <div x-data="{ show: true }" x-show="show" x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-init="setTimeout(() => show = false, 3500)"
                class="flex items-center p-4 mb-6 text-sm font-medium text-green-800 rounded-lg bg-green-100 dark:bg-gray-800 dark:text-green-400 border border-green-200 dark:border-green-700 shadow-sm"
                role="alert">
                {{-- Icon + Text --}}
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
                class="flex items-center p-4 mb-6 text-sm font-medium text-red-800 rounded-lg bg-red-100 dark:bg-gray-800 dark:text-red-400 border border-red-200 dark:border-red-700 shadow-sm"
                role="alert">
                {{-- Icon + Text --}}
                <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                </svg>
                <span class="sr-only">Error</span>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        {{-- Section Header & Add Button --}}
        <div :class="isAdmin ? 'flex justify-between items-center mb-8 mt-16' : 'text-center mb-8 mt-16'">
            <h2 class="text-4xl lg:text-4xl font-extrabold tracking-wide uppercase text-gray-900 dark:text-white">
                Nuestros Servicios Destacados
            </h2>

            {{-- Mostrar botón solo si es Admin --}}
            <template x-if="isAdmin">
                <x-button wire:click="openCreateModal" class="ml-4">
                    <svg class="w-5 h-5 -ms-1 me-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Añadir Tarjeta
                </x-button>
            </template>
        </div>


        {{-- Grid Container (Sortable Target) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8" x-init="() => {
            if (isAdmin) {
                Sortable.create($el, {
                    handle: '.card-drag-handle', // Use a specific handle class
                    animation: 150,
                    ghostClass: 'bg-blue-100 opacity-50', // Tailwind classes for ghost
                    // dragClass: 'sortable-drag', // Optional: class for element being dragged
                    onEnd: (evt) => {
                        let items = Array.from(evt.to.children).map((item, index) => {
                            return {
                                order: index,
                                value: item.getAttribute('wire:key').replace('card-', '')
                            }
                        });
                        // console.log('New Order:', items); // Debugging
                        $wire.call('updateCardOrder', items);
                    }
                });
            }
        }">

            {{-- Card Loop --}}
            @forelse ($cards as $card)
                <div wire:key="card-{{ $card->id }}"
                    class="relative group/card bg-gray-800 aspect-square overflow-hidden shadow-md transition transform hover:scale-110 hover:shadow-2xl duration-300">

                    {{-- Imagen de Fondo --}}
                    @if ($card->image_path && Storage::disk('public')->exists($card->image_path))
                        <img src="{{ Storage::url($card->image_path) }}" alt="{{ $card->title }}"
                            class="absolute inset-0 w-full h-full object-cover object-center" />
                    @else
                        <div class="absolute inset-0 bg-gray-300 dark:bg-gray-700 flex items-center justify-center">
                            <span class="text-gray-500 dark:text-gray-400 text-sm">Sin Imagen</span>
                        </div>
                    @endif

                    {{-- Overlay oscuro, SIN desenfoque --}}
                    <div class="absolute inset-0 bg-black/40"></div>

                    {{-- Contenido centrado --}}
                    <div class="relative z-10 flex flex-col items-center justify-center text-center h-full p-6">

                        {{-- Botones Admin en Hover --}}
                        @if ($isAdmin)
                            <div
                                class="absolute top-2 right-2 flex gap-2 opacity-0 group-hover/card:opacity-100 transition duration-300">
                                {{-- Drag Handle --}}
                                <div class="card-drag-handle p-1.5 bg-gray-500 text-white cursor-move hover:bg-gray-600"
                                    title="Arrastrar para reordenar">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3.75 9h16.5m-16.5 6.75h16.5" />
                                    </svg>
                                </div>
                                {{-- Editar --}}
                                <button wire:click="openEditModal({{ $card->id }})" title="Editar"
                                    class="p-1.5 bg-blue-600 hover:bg-blue-700 text-white">
                                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19.5 7.125L16.863 4.487" />
                                    </svg>
                                </button>
                                {{-- Eliminar --}}
                                <button wire:click="confirmDelete({{ $card->id }})" title="Eliminar"
                                    class="p-1.5 bg-red-600 hover:bg-red-700 text-white">
                                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166M18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397M6.25 5.355a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916" />
                                    </svg>
                                </button>
                            </div>
                        @endif

                        {{-- Título --}}
                        <h3 class="text-2xl font-bold text-white mb-4">
                            {{ $card->title }}
                        </h3>

                        {{-- Botón --}}
                        @if ($card->link_url)
                            <a href="{{ $card->link_url }}" target="_blank" rel="noopener noreferrer"
                                class="inline-block text-white border border-white px-4 py-1.5 text-sm rounded transition duration-300 hover:bg-white hover:text-black">
                                {{ $card->link_text ?: 'Saber Más' }}
                            </a>
                        @endif
                    </div>
                </div>



            @empty
                {{-- Empty State --}}
                <div
                    class="md:col-span-2 lg:col-span-3 text-center py-12 px-6 bg-white dark:bg-gray-800 shadow-md rounded-lg">
                    <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                    </svg>
                    <h3 class="mt-2 text-md font-semibold text-gray-900 dark:text-white">
                        @if ($isAdmin)
                            Aún no hay tarjetas de contenido.
                        @else
                            No hay contenido para mostrar en este momento.
                        @endif
                    </h3>
                    @if ($isAdmin)
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">¡Añade la primera tarjeta para
                            empezar!</p>
                        <div class="mt-6">
                            <x-button wire:click="openCreateModal">
                                <svg class="w-5 h-5 -ms-1 me-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                                Añadir Tarjeta
                            </x-button>
                        </div>
                    @endif
                </div>
            @endforelse

        </div> {{-- End Grid --}}

    </div> {{-- End max-w-7xl --}}

    {{-- --------------------- MODAL Create/Edit Card --------------------- --}}
    <x-dialog-modal wire:model.live="showModal">
        <x-slot name="title">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                {{ $isEditing ? 'Editar Tarjeta' : 'Crear Nueva Tarjeta' }}
            </h3>
        </x-slot>

        <x-slot name="content">
            <form wire:submit.prevent="saveCard" id="contentCardForm" class="space-y-6">
                {{-- Fields are similar to InfoBlock modal, adjust labels/models --}}
                <div>
                    <x-label for="card_title" value="{{ __('Título') }}" class="mb-1.5" />
                    <x-input id="card_title" type="text" class="mt-1 block w-full" wire:model="form.title" />
                    <x-input-error for="form.title" class="mt-1.5 text-xs" />
                </div>
                <div>
                    <x-label for="card_description" value="{{ __('Descripción (Opcional)') }}" class="mb-1.5" />
                    <textarea id="card_description"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 rounded-md shadow-sm focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:focus:ring-indigo-900"
                        rows="4" wire:model="form.description"></textarea>
                    <x-input-error for="form.description" class="mt-1.5 text-xs" />
                </div>
                <div>
                    <x-label for="card_link_url" value="{{ __('URL Enlace (Opcional)') }}" class="mb-1.5" />
                    <x-input id="card_link_url" type="url" placeholder="https://ejemplo.com"
                        class="mt-1 block w-full" wire:model="form.link_url" />
                    <x-input-error for="form.link_url" class="mt-1.5 text-xs" />
                </div>
                <div>
                    <x-label for="card_link_text" value="{{ __('Texto del Enlace') }}" class="mb-1.5" />
                    <x-input id="card_link_text" type="text" class="mt-1 block w-full"
                        wire:model="form.link_text" />
                    <x-input-error for="form.link_text" class="mt-1.5 text-xs" />
                </div>
                <div>
                    <x-label for="card_photo" value="{{ __('Imagen') }}" class="mb-1.5" />
                    <input type="file" id="card_photo" wire:model="photo"
                        class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 dark:file:bg-gray-700 file:text-indigo-700 dark:file:text-gray-300 hover:file:bg-indigo-100 dark:hover:file:bg-gray-600 transition cursor-pointer" />
                    <x-input-error for="photo" class="mt-1.5 text-xs" /> {{-- Error for component's photo property --}}
                    <div wire:loading wire:target="photo" class="mt-2 text-sm text-indigo-600 dark:text-indigo-400">
                        Cargando...</div>
                    {{-- Preview --}}
                    <div class="mt-4">
                        @if ($photo)
                            <span
                                class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Previsualización
                                Nueva:</span>
                            <img src="{{ $photo->temporaryUrl() }}"
                                class="w-full h-auto rounded-md border border-gray-200 dark:border-gray-700 object-contain max-h-40">
                        @elseif ($current_image_path)
                            <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Imagen
                                Actual:</span>
                            <img src="{{ Storage::url($current_image_path) }}"
                                class="w-full h-auto rounded-md border border-gray-200 dark:border-gray-700 object-contain max-h-40">
                        @else
                            <div
                                class="w-full h-32 bg-gray-100 dark:bg-gray-700/50 rounded-md border border-dashed border-gray-300 dark:border-gray-600 flex items-center justify-center">
                                <span class="text-gray-400 dark:text-gray-500 text-sm italic">Vista previa</span>
                            </div>
                        @endif
                    </div>
                </div>
                <div>
                    <x-label for="card_order_column" value="{{ __('Orden (Opcional)') }}" class="mb-1.5" />
                    <x-input id="card_order_column" type="number" step="1" min="0"
                        class="mt-1 block w-full" wire:model="form.order_column" />
                    <x-input-error for="form.order_column" class="mt-1.5 text-xs" />
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Se ordenará automáticamente si se deja en
                        0. Los números menores aparecen primero.</p>
                </div>
            </form>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="closeModal" wire:loading.attr="disabled">
                Cancelar
            </x-secondary-button>
            <x-button type="submit" form="contentCardForm" class="ml-3" wire:loading.attr="disabled"
                wire:target="saveCard, photo">
                <span wire:loading wire:target="saveCard, photo" class="inline-flex items-center mr-2">
                    <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                </span>
                <span wire:loading.remove wire:target="saveCard, photo">
                    {{ $isEditing ? 'Guardar Cambios' : 'Crear Tarjeta' }}
                </span>
                <span wire:loading wire:target="saveCard, photo">Guardando...</span>
            </x-button>
        </x-slot>
    </x-dialog-modal>

    {{-- --------------------- MODAL CONFIRMACIÓN DELETE Card --------------------- --}}
    <x-confirmation-modal wire:model.live="showConfirmDeleteModal">
        <x-slot name="title">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                Confirmar Eliminación
            </h3>
        </x-slot>
        <x-slot name="content">
            <p class="text-base text-gray-600 dark:text-gray-300">
                ¿Estás seguro de que deseas eliminar esta tarjeta? La imagen asociada también será eliminada
                permanentemente. Esta acción no se puede deshacer.
            </p>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showConfirmDeleteModal', false)" wire:loading.attr="disabled">
                Cancelar
            </x-secondary-button>
            <x-danger-button class="ml-3" wire:click="deleteCard" wire:loading.attr="disabled"
                wire:target="deleteCard">
                <span wire:loading wire:target="deleteCard" class="inline-flex items-center mr-2">
                    <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                </span>
                <span wire:loading.remove wire:target="deleteCard">Eliminar Tarjeta</span>
                <span wire:loading wire:target="deleteCard">Eliminando...</span>
            </x-danger-button>
        </x-slot>
    </x-confirmation-modal>

</div> {{-- End Root div --}}
