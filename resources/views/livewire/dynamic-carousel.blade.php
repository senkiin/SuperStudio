<div>
    @if ($slides->isNotEmpty())
        <div
            x-data="{
                currentSlide: @entangle('currentSlideIndex').live,
                totalSlides: {{ $slides->count() }},
                autoplay: true,
                autoplayInterval: 7000, // Milisegundos
                intervalId: null,
                progress: 0,
                next() {
                    this.currentSlide = (this.currentSlide + 1) % this.totalSlides;
                    this.$wire.goToSlide(this.currentSlide);
                    if (this.autoplay) this.startAutoplay();
                },
                prev() {
                    this.currentSlide = (this.currentSlide - 1 + this.totalSlides) % this.totalSlides;
                    this.$wire.goToSlide(this.currentSlide);
                    if (this.autoplay) this.startAutoplay();
                },
                goTo(index) {
                    this.currentSlide = index;
                    this.$wire.goToSlide(index);
                    if (this.autoplay) this.startAutoplay();
                },
                init() {
                    this.startAutoplay();
                    this.$watch('currentSlide', () => { // Cuando Livewire cambia el slide, reiniciar autoplay
                        if (this.autoplay) this.startAutoplay();
                    });
                    // Pausar en hover
                    this.$el.addEventListener('mouseenter', () => this.stopAutoplay());
                    this.$el.addEventListener('mouseleave', () => this.startAutoplay());
                }
            }"
            x-init="init()"
            class="relative w-full h-[60vh] sm:h-[70vh] md:h-[85vh] lg:h-screen overflow-hidden shadow-2xl"
            role="region"
            aria-roledescription="carousel"
            aria-label="Destacados"
        >
            {{-- Slides --}}
            @foreach ($slides as $index => $slide)
                <div
                    x-show="currentSlide === {{ $index }}"
                    x-transition:enter="transition ease-in-out duration-1000"
                    x-transition:enter-start="opacity-0 transform scale-105"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in-out duration-1000"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                    class="absolute inset-0 w-full h-full"
                    role="group"
                    aria-roledescription="slide"
                    :aria-label="'Slide ' + ({{ $index }} + 1) + ' de ' + totalSlides"
                >
                    {{-- Imagen de Fondo --}}
                    <img src="{{ Storage::disk('s3')->url($slide->background_image_path) }}"
     alt="{{ $slide->title }} fondo"
                         class="w-full h-full object-cover">
                    {{-- Overlay Oscuro --}}
                    <div class="absolute inset-0 bg-black opacity-40"></div>

                    {{-- Contenido de Texto --}}
                    <div class="absolute inset-0 flex flex-col items-center justify-center text-center p-4 md:p-8 z-10">
                        <div class="{{ $slide->text_animation ?: 'fade-in-up' }}" style="animation-delay: 0.3s; color: {{ $slide->text_color ?? '#FFFFFF' }};">
                            <h2 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold drop-shadow-md leading-tight">
                                {{ $slide->title }}
                            </h2>
                            @if ($slide->subtitle)
                                <p class="mt-4 text-lg sm:text-xl md:text-2xl max-w-2xl mx-auto drop-shadow-sm">
                                    {{ $slide->subtitle }}
                                </p>
                            @endif
                            @if ($slide->button_text && $slide->button_link)
                                <a href="{{ $slide->button_link }}"
                                   class="mt-8 inline-block bg-indigo-600 hover:bg-indigo-500 text-white font-semibold px-8 py-3 rounded-lg shadow-lg transition-transform transform hover:scale-105">
                                    {{ $slide->button_text }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Controles de Navegación (Opcional) --}}
            @if($slides->count() > 1)
                <button @click="prev()" title="Anterior" aria-label="Slide anterior"
                        class="absolute top-1/2 left-4 transform -translate-y-1/2 z-20 p-3 bg-black/30 hover:bg-black/50 text-white rounded-full focus:outline-none focus:ring-2 focus:ring-white focus:ring-opacity-50 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                </button>
                <button @click="next()" title="Siguiente" aria-label="Siguiente slide"
                        class="absolute top-1/2 right-4 transform -translate-y-1/2 z-20 p-3 bg-black/30 hover:bg-black/50 text-white rounded-full focus:outline-none focus:ring-2 focus:ring-white focus:ring-opacity-50 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </button>

                {{-- Indicadores de Puntos (Dots) --}}
                <div class="absolute bottom-6 left-1/2 transform -translate-x-1/2 z-20 flex space-x-2">
                    @foreach ($slides as $index => $slide)
                        <button @click="goTo({{ $index }})"
                                :class="{ 'bg-white': currentSlide === {{ $index }}, 'bg-white/50 hover:bg-white/75': currentSlide !== {{ $index }} }"
                                class="w-3 h-3 rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-black/50"
                                :aria-label="'Ir al slide ' + ({{ $index }} + 1)"></button>
                    @endforeach
                </div>

                {{-- Barra de Progreso del Autoplay --}}
                <div class="absolute bottom-0 left-0 w-full h-1 bg-black/30 z-20">
                    <div class="h-full bg-indigo-500 transition-all duration-100 ease-linear" :style="`width: ${progress}%`"></div>
                </div>
            @endif
        </div>
    @else
        @if($isAdmin)
            <div class="text-center py-10 bg-gray-800 text-gray-400">
                El carrusel está vacío. <button wire:click="openManageSlidesModal" class="text-indigo-400 hover:underline">Añade algunos slides.</button>
            </div>
        @else
            {{-- No mostrar nada si no hay slides y no es admin, o un mensaje discreto --}}
        @endif
    @endif

    {{-- Botón de Administración --}}
    @if ($isAdmin)
        <div class="text-center my-6">
            <button wire:click="openManageSlidesModal"
                class="px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-md shadow-md transition-colors">
                Administrar Slides del Carrusel
            </button>
        </div>
    @endif

    {{-- Modal de Administración de Slides --}}
    @if ($isAdmin)
        <x-dialog-modal wire:model.live="showManageSlidesModal" maxWidth="4xl">
            <x-slot name="title">
                Administrar Slides del Carrusel
            </x-slot>

            <x-slot name="content">
                @if (session()->has('modal_carousel_message'))
                    <div class="mb-4 p-3 bg-green-600 text-white rounded-md text-sm">{{ session('modal_carousel_message') }}</div>
                @endif
                 @if (session()->has('modal_carousel_error'))
                    <div class="mb-4 p-3 bg-red-600 text-white rounded-md text-sm">{{ session('modal_carousel_error') }}</div>
                @endif

                <div class="space-y-6 text-gray-200">
                    {{-- Formulario para Añadir/Editar Slide --}}
                    <form wire:submit.prevent="saveSlide" class="p-4 border border-gray-700 rounded-md bg-gray-800 shadow-sm space-y-4">
                        <h4 class="text-lg font-semibold text-gray-100 mb-3">{{ $editingSlideId ? 'Editar Slide' : 'Añadir Nuevo Slide' }}</h4>
                        <div>
                            <label for="slideTitle" class="block text-sm font-medium mb-1">Título:</label>
                            <input type="text" wire:model.defer="slideTitle" id="slideTitle" class="w-full p-2.5 rounded bg-gray-700 text-white border border-gray-600 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                            @error('slideTitle') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="slideSubtitle" class="block text-sm font-medium mb-1">Subtítulo (opcional):</label>
                            <textarea wire:model.defer="slideSubtitle" id="slideSubtitle" rows="3" class="w-full p-2.5 rounded bg-gray-700 text-white border border-gray-600 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                            @error('slideSubtitle') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="newSlideImage" class="block text-sm font-medium mb-1">Imagen de Fondo:</label>
                            <input type="file" wire:model="newSlideImage" id="newSlideImage-{{ $this->getId() }}" class="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-500 cursor-pointer">
                            <div wire:loading wire:target="newSlideImage" class="text-indigo-400 text-xs mt-1">Cargando imagen...</div>
                            @error('newSlideImage') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                            @if ($newSlideImage)
                                <img src="{{ $newSlideImage->temporaryUrl() }}" alt="Previsualización" class="mt-2 rounded max-h-32 object-contain">
                            @elseif ($existingSlideImagePreview)
                                <img src="{{ Storage::disk('s3')->url($existingSlideImagePreview) }}" alt="Imagen actual" class="mt-2 rounded max-h-32 object-contain">
                            @endif
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="slideTextColor" class="block text-sm font-medium mb-1">Color del Texto (ej: #FFFFFF o text-white):</label>
                                <input type="text" wire:model.defer="slideTextColor" id="slideTextColor" class="w-full p-2.5 rounded bg-gray-700 text-white border border-gray-600 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                @error('slideTextColor') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="slideTextAnimation" class="block text-sm font-medium mb-1">Animación del Texto (Tailwind/CSS class):</label>
                                <input type="text" wire:model.defer="slideTextAnimation" id="slideTextAnimation" placeholder="Ej: fade-in-up" class="w-full p-2.5 rounded bg-gray-700 text-white border border-gray-600 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                @error('slideTextAnimation') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                         <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="slideButtonText" class="block text-sm font-medium mb-1">Texto del Botón (opcional):</label>
                                <input type="text" wire:model.defer="slideButtonText" id="slideButtonText" class="w-full p-2.5 rounded bg-gray-700 text-white border border-gray-600 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                @error('slideButtonText') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="slideButtonLink" class="block text-sm font-medium mb-1">Enlace del Botón (opcional, URL completa):</label>
                                <input type="url" wire:model.defer="slideButtonLink" id="slideButtonLink" placeholder="https://ejemplo.com/pagina" class="w-full p-2.5 rounded bg-gray-700 text-white border border-gray-600 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                @error('slideButtonLink') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-end">
                            <div>
                                <label for="slideOrder" class="block text-sm font-medium mb-1">Orden:</label>
                                <input type="number" wire:model.defer="slideOrder" id="slideOrder" class="w-full p-2.5 rounded bg-gray-700 text-white border border-gray-600 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                @error('slideOrder') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" wire:model.defer="slideIsActive" id="slideIsActive" class="h-4 w-4 text-indigo-600 border-gray-500 rounded focus:ring-indigo-500">
                                <label for="slideIsActive" class="ml-2 text-sm font-medium">Activo</label>
                            </div>
                        </div>
                        <div class="flex justify-end space-x-3 pt-3">
                            @if($editingSlideId)
                            <x-secondary-button type="button" wire:click="resetFormFields" class="text-xs">Cancelar Edición</x-secondary-button>
                            @endif
                            <x-button type="submit" wire:loading.attr="disabled" wire:target="saveSlide, newSlideImage" class="text-sm px-5 py-2.5">
                                {{ $editingSlideId ? 'Actualizar Slide' : 'Guardar Nuevo Slide' }}
                            </x-button>
                        </div>
                    </form>

                    {{-- Lista de Slides Existentes para Administrar --}}
                    <div class="mt-6 pt-6 border-t border-gray-700">
                        <h4 class="text-lg font-semibold text-gray-100 mb-3">Slides Existentes</h4>
                        @if ($allSlidesForManagement->isNotEmpty())
                            <ul {{-- wire:sortable="updateSlideOrder" --}} class="space-y-3 max-h-96 overflow-y-auto pr-2">
                                @foreach ($allSlidesForManagement as $slideItem)
                                    <li wire:key="slide-manage-{{ $slideItem->id }}" {{-- wire:sortable.item="{{ $slideItem->id }}" --}}
                                        class="p-3 bg-gray-700/70 rounded-md shadow flex items-center justify-between hover:bg-gray-700">
                                        <div class="flex items-center space-x-3">
                                            {{-- <span wire:sortable.handle class="cursor-move text-gray-400 hover:text-white pr-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m4 6H4" /></svg>
                                            </span> --}}
                                            <img src="{{ Storage::disk('s3')->url($slideItem->background_image_path) }}" alt="" class="w-20 h-12 object-cover rounded-sm">
                                            <div>
                                                <span class="font-medium text-gray-100 block">{{ $slideItem->title }}</span>
                                                <span class="text-xs text-gray-400">Orden: {{ $slideItem->order }} - {{ $slideItem->is_active ? 'Activo' : 'Inactivo' }}</span>
                                            </div>
                                        </div>
                                        <div class="space-x-2 flex-shrink-0">
                                            <button wire:click="editSlide({{ $slideItem->id }})" class="text-xs px-2.5 py-1 bg-blue-600 hover:bg-blue-500 rounded">Editar</button>
                                            <button wire:click="deleteSlide({{ $slideItem->id }})" wire:confirm="¿Seguro que quieres eliminar este slide?" class="text-xs px-2.5 py-1 bg-red-600 hover:bg-red-500 rounded">Eliminar</button>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-sm text-gray-500">No hay slides creados todavía.</p>
                        @endif
                         <div class="mt-4 text-right">
                            <x-secondary-button wire:click="createNewSlide" class="text-xs">
                                Añadir Nuevo Slide (Limpiar Formulario)
                            </x-secondary-button>
                        </div>
                    </div>
                </div>
            </x-slot>

            <x-slot name="footer">
                <x-secondary-button wire:click="closeManageSlidesModal" wire:loading.attr="disabled">
                    Cerrar Administración
                </x-secondary-button>
            </x-slot>
        </x-dialog-modal>
    @endif

    {{-- Mensajes Flash generales de la página (fuera del modal) --}}
    @if (session()->has('carousel_message')) {{-- Usar una key de sesión diferente --}}
        <div x-data="{ showFlashCarousel: true }" x-init="setTimeout(() => showFlashCarousel = false, 3500)" x-show="showFlashCarousel"
            x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed bottom-5 right-5 bg-green-600 text-white py-2.5 px-5 rounded-lg shadow-xl z-[101] text-sm">
            {{ session('carousel_message') }}
        </div>
    @endif
     @if (session()->has('carousel_error'))
        <div x-data="{ showFlashCarousel: true }" x-init="setTimeout(() => showFlashCarousel = false, 3500)" x-show="showFlashCarousel"
            x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed bottom-5 right-5 bg-red-600 text-white py-2.5 px-5 rounded-lg shadow-xl z-[101] text-sm">
            {{ session('carousel_error') }}
        </div>
    @endif

</div>

@push('styles')
<style>
    /* Animaciones de Texto (Ejemplos - puedes expandir y personalizar) */
    /* Estas clases se aplicarían al contenedor del texto del slide */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translate3d(0, 40px, 0); }
        to { opacity: 1; transform: translate3d(0, 0, 0); }
    }
    .fade-in-up { animation: fadeInUp 1s ease-out forwards; opacity: 0; }

    @keyframes fadeInLeft {
        from { opacity: 0; transform: translate3d(-40px, 0, 0); }
        to { opacity: 1; transform: translate3d(0, 0, 0); }
    }
    .fade-in-left { animation: fadeInLeft 1s ease-out forwards; opacity: 0; }

    @keyframes fadeInRight {
        from { opacity: 0; transform: translate3d(40px, 0, 0); }
        to { opacity: 1; transform: translate3d(0, 0, 0); }
    }
    .fade-in-right { animation: fadeInRight 1s ease-out forwards; opacity: 0; }

    /* Para que las animaciones se reinicien cuando cambia el slide con Alpine.js,
       Livewire podría re-renderizar el DOM, lo que naturalmente reiniciaría las animaciones CSS.
       Si usas x-show, la animación se aplicará cuando el elemento se muestre.
       El `animation-delay` en el style inline ayuda a secuenciar. */
</style>
@endpush

@push('scripts')
<script>
    // Código para SortableJS (si decides implementarlo para reordenar los slides en el modal)
    // document.addEventListener('livewire:init', () => {
    //     const initSortableSlides = (el) => {
    //         if (el.sortableInstanceSlides) el.sortableInstanceSlides.destroy();
    //         el.sortableInstanceSlides = Sortable.create(el, {
    //             animation: 150,
    //             handle: '[wire\\:sortable\\.handle]', // Si usas un handle específico
    //             ghostClass: 'bg-gray-600 opacity-50',
    //             onEnd: function (evt) {
    //                 let order = Array.from(evt.target.children).map((child, index) => {
    //                     if (child.hasAttribute('wire:sortable.item')) {
    //                         return {
    //                             value: child.getAttribute('wire:sortable.item'),
    //                             order: index // SortableJS es 0-indexed, ajusta en el backend si guardas 1-indexed
    //                         };
    //                     }
    //                     return null;
    //                 }).filter(item => item !== null);

    //                 if (order.length > 0) {
    //                     let component = Livewire.find(el.closest('[wire\\:id]').getAttribute('wire:id'));
    //                     if (component) {
    //                         component.call('updateSlideOrder', order);
    //                     }
    //                 }
    //             }
    //         });
    //     };

    //     // Hook para reinicializar si el DOM de la lista de slides se actualiza
    //     Livewire.hook('element.updated', (el, component) => {
    //         if (component.name === @json(Str::kebab(class_basename($this))) && el.hasAttribute('wire:sortable') && el.getAttribute('wire:sortable') === 'updateSlideOrder') {
    //             initSortableSlides(el);
    //         }
    //     });

    //     // Para la carga inicial, si el modal está abierto por defecto o se renderiza condicionalmente
    //     // y el elemento ya existe.
    //     const manageModalEl = document.querySelector('[wire\\:id] [wire\\:sortable="updateSlideOrder"]');
    //     if(manageModalEl) {
    //         initSortableSlides(manageModalEl);
    //     }
    // });
</script>
@endpush
