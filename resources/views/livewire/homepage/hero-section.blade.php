{{-- resources/views/livewire/homepage/hero-section.blade.php --}}
<div x-data="{ isAdmin: @js($isAdmin) }"> {{-- Añadido x-data --}}

    {{-- Flash messages para el modal --}}
    @if (session()->has('message'))
        <div class="fixed top-5 right-5 z-[100] w-auto max-w-sm" x-data="{ show: true }" x-show="show"
            x-transition:enter="transform ease-out duration-300 transition"
            x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
            x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
            x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" x-init="setTimeout(() => show = false, 3500)">
            <div class="flex items-center p-4 text-sm font-medium text-green-800 rounded-lg bg-green-100 dark:bg-gray-800 dark:text-green-400 border border-green-200 dark:border-green-700 shadow-lg"
                role="alert">
                <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                </svg>
                <span class="sr-only">Info</span>
                <div>{{ session('message') }}</div>
            </div>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="fixed top-5 right-5 z-[100] w-auto max-w-sm" x-data="{ show: true }" x-show="show"
            x-transition:enter="transform ease-out duration-300 transition"
            x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
            x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
            x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" x-init="setTimeout(() => show = false, 3500)">
            <div class="flex items-center p-4 text-sm font-medium text-red-800 rounded-lg bg-red-100 dark:bg-gray-800 dark:text-red-400 border border-red-200 dark:border-red-700 shadow-lg"
                role="alert">
                <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                </svg>
                <span class="sr-only">Error</span>
                <div>{{ session('error') }}</div>
            </div>
        </div>
    @endif


    {{-- El @if principal ahora envuelve toda la sección y el botón de crear --}}
    <div class="relative"> {{-- Contenedor relativo para posicionar botones admin --}}

        {{-- Botones Admin --}}
        <template x-if="isAdmin">
            <div class="absolute top-4 right-4 z-30 flex space-x-2">
                {{-- Botón Editar (solo si hay un bloque hero visible) --}}
                @if ($heroBlock)
                    <button wire:click="openEditModal()" title="Editar Hero Section"
                        class="p-2 bg-blue-600/80 text-white rounded-full hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-blue-500 transition">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                        </svg>
                    </button>
                @endif
                {{-- Botón Crear Nuevo --}}
                <button wire:click="openCreateModal()" title="Crear Nuevo Hero Block"
                    class="p-2 bg-green-600/80 text-white rounded-full hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-green-500 transition">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </button>
            </div>
        </template>

        {{-- Sección Hero (como antes) --}}
        @if ($heroBlock)
            <section
                class="relative bg-gray-900 text-white overflow-hidden min-h-[60vh] md:min-h-[75vh] flex items-center justify-center">
                {{-- Imagen de Fondo --}}
                @if ($heroBlock->image_path)
    <img
        x-data
        x-init="$el.classList.add('aos-hero-bg')"
        data-aos
        src="{{ Storage::disk('hero-home')->url($heroBlock->image_path) }}"
        alt="{{ $heroBlock->title }}"
        loading="lazy"
        class="absolute inset-0 w-full h-full object-cover object-center z-0"
    />
@else
    <div class="absolute inset-0 w-full h-full bg-gradient-to-br from-gray-700 to-gray-900 z-0"></div>
@endif
                {{-- Overlay Oscuro --}}
                <div class="absolute inset-0 bg-black/60 z-10"></div>
                {{-- Contenido Centrado --}}
                <div
                x-data
                x-init="$el.classList.add('aos-hero-text')"
                data-aos
                class="relative z-20 container mx-auto px-4 py-16 text-center max-w-4xl"
            >
                                <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold mb-4 leading-tight drop-shadow-lg">
                        {{ $heroBlock->title }}
                    </h1>
                    @if ($heroBlock->description)
                        <p class="text-lg md:text-xl text-gray-200 mb-8 max-w-2xl mx-auto leading-relaxed">
                            {{ $heroBlock->description }}
                        </p>
                    @endif
                    @if ($heroBlock->link_url)
                        <a href="{{ $heroBlock->link_url }}" target="_blank" rel="noopener noreferrer"
                            class="inline-block bg-white text-gray-900 hover:bg-gray-200 text-base md:text-lg font-semibold px-8 py-3 rounded-md shadow-lg transition duration-300 ease-in-out transform hover:scale-105">
                            {{ $heroBlock->link_text ?: 'Ver Más' }}
                        </a>
                    @endif
                </div>
            </section>
        @else
            {{-- Mensaje si NO hay NINGÚN hero block activo --}}
            <div class="min-h-[50vh] bg-gray-200 dark:bg-gray-800 flex items-center justify-center text-center px-4">
                @if (Auth::check() && Auth::user()->role === 'admin')
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 mb-4">No hay ninguna sección Hero activa.</p>
                        <x-button wire:click="openCreateModal">
                            Crear Primera Sección Hero
                        </x-button>
                    </div>
                @else
                    <p class="text-gray-500 dark:text-gray-400">Contenido principal próximamente.</p>
                @endif
            </div>
        @endif

    </div>

    {{-- --------------------- MODAL Create/Edit Hero Block --------------------- --}}
    <x-dialog-modal wire:model.live="showModal">
        <x-slot name="title">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                {{ $isEditing ? 'Editar Sección Hero' : 'Crear Nueva Sección Hero' }}
            </h3>
        </x-slot>

        <x-slot name="content">
            <form wire:submit.prevent="saveHeroBlock" id="heroBlockForm" class="space-y-6">
                <div>
                    <x-label for="hero_title" value="{{ __('Título Principal') }}" />
                    <x-input id="hero_title" type="text" class="mt-1 block w-full" wire:model="form.title" />
                    <x-input-error for="form.title" class="mt-1.5 text-xs" />
                </div>
                <div>
                    <x-label for="hero_description" value="{{ __('Descripción') }}" />
                    <textarea id="hero_description" class="mt-1 block w-full" rows="4" wire:model="form.description"></textarea>
                    <x-input-error for="form.description" class="mt-1.5 text-xs" />
                </div>
                <div>
                    <x-label for="hero_link_url" value="{{ __('URL del Botón (Opcional)') }}" />
                    <x-input id="hero_link_url" type="url" placeholder="https://ejemplo.com"
                        class="mt-1 block w-full" wire:model="form.link_url" />
                    <x-input-error for="form.link_url" class="mt-1.5 text-xs" />
                </div>
                <div>
                    <x-label for="hero_link_text" value="{{ __('Texto del Botón') }}" />
                    <x-input id="hero_link_text" type="text" class="mt-1 block w-full"
                        wire:model="form.link_text" />
                    <x-input-error for="form.link_text" class="mt-1.5 text-xs" />
                </div>
                <div>
                    <x-label for="hero_photo" value="{{ __('Imagen de Fondo') }}" />
                    <input type="file" id="hero_photo" wire:model="photo" class="mt-1 block w-full text-sm" />
                    <x-input-error for="photo" class="mt-1.5 text-xs" />
                    <div wire:loading wire:target="photo" class="mt-2 text-sm text-indigo-600 dark:text-indigo-400">
                        Cargando...</div>
                    {{-- Preview --}}
                    <div class="mt-4">
                        @if ($photo)
                            <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Nueva:</span>
                            <img src="{{ $photo->temporaryUrl() }}"
                                class="w-auto h-auto rounded border object-contain max-h-40">
                        @elseif ($current_image_path)
                            <span
                                class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Actual:</span>
                            <img src="{{ Storage::disk('hero-home')->url($heroBlock->image_path) }}"
                                class="w-auto h-auto rounded border object-contain max-h-40">
                        @endif
                    </div>
                </div>
                {{-- Checkbox para Activo/Inactivo --}}
                <div class="flex items-center">
                    <x-checkbox id="hero_is_active" wire:model="form.is_active" />
                    <x-label for="hero_is_active" value="{{ __('Activo (Mostrar este bloque)') }}" class="ml-2" />
                    <x-input-error for="form.is_active" class="mt-1.5 text-xs ml-2" />
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Nota: Solo se mostrará el bloque activo más reciente. Marcar este como activo puede desactivar otros
                    automáticamente si has añadido esa lógica.
                </p>
            </form>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="closeModal" wire:loading.attr="disabled">
                Cancelar
            </x-secondary-button>
            <x-button type="submit" form="heroBlockForm" class="ml-3" wire:loading.attr="disabled"
                wire:target="saveHeroBlock, photo">
                <span wire:loading wire:target="saveHeroBlock, photo" class="inline-flex items-center mr-2"><svg
                        class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg></span>
                <span wire:loading.remove
                    wire:target="saveHeroBlock, photo">{{ $isEditing ? 'Guardar Cambios' : 'Crear Bloque' }}</span>
                <span wire:loading wire:target="saveHeroBlock, photo">Guardando...</span>
            </x-button>
        </x-slot>
    </x-dialog-modal>

</div>
