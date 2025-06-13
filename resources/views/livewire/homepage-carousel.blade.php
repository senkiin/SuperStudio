{{-- resources/views/livewire/homepage-carousel.blade.php --}}
<div class="relative h-screen w-full overflow-hidden" {{-- Lógica Alpine para controlar el carrusel --}} x-data="{
    activeIndex: 0,
    images: {{ $images->count() > 0 ? $images->count() : 0 }},
    interval: null,
    autoplay: true,
    autoplayDelay: 7000,

    next() {
        if (this.images === 0) return;
        this.activeIndex = (this.activeIndex + 1) % this.images;
        this.resetInterval();
    },
    prev() {
        if (this.images === 0) return;
        this.activeIndex = (this.activeIndex - 1 + this.images) % this.images;
        this.resetInterval();
    },
    goTo(index) {
        if (this.images === 0) return;
        this.activeIndex = index;
        this.resetInterval();
    },
    startAutoplay() {
        if (!this.autoplay || this.images === 0) return;
        clearInterval(this.interval);
        this.interval = setInterval(() => {
            this.next();
        }, this.autoplayDelay);
    },
    stopAutoplay() {
        clearInterval(this.interval);
    },
    resetInterval() {
        if (this.autoplay && this.images > 0) {
            this.startAutoplay();
        }
    },
    init() {
        this.startAutoplay();
        if (this.images > 0) {
            this.$el.addEventListener('mouseenter', () => this.stopAutoplay());
            this.$el.addEventListener('mouseleave', () => this.startAutoplay());
        }
    }
}"
    x-init="init()">

    {{-- Contenedor de Imágenes del Carrusel --}}
    @if ($images->count() > 0)
        @foreach ($images as $index => $image)
            <div x-show="activeIndex === {{ $index }}" x-transition:enter="transition ease-in-out duration-1000"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in-out duration-1000" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" class="absolute inset-0">

                {{-- Enlace Condicional: Envuelve si hay link_url. El title ya está como pediste. --}}
                @if ($image->link_url)
                    <a href="{{ $image->link_url }}" target="_blank" rel="noopener noreferrer"
                        class="block w-full h-full group" title="Ir a: {{ $image->caption ?? $image->link_url }}">
                @endif

                {{-- Contenedor interno --}}
                <div class="relative w-full h-full">
                    {{-- Imagen de fondo --}}
                    <img src="{{ $image->image_url }}" alt="{{ $image->caption }}"
                        class="w-full h-full object-cover">

                    {{-- Overlay oscuro --}}
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/30 to-transparent opacity-80 {{ $image->link_url ? 'group-hover:opacity-90 transition-opacity' : '' }}">
                    </div>

                    {{-- Texto (Caption) Superpuesto y Centrado --}}
                    @if ($image->caption)
                        {{-- Este div ya centra el contenido horizontalmente (items-center) y lo alinea abajo (justify-end) --}}
                        <div
                            class="absolute inset-x-0 bottom-0 z-10 flex flex-col items-center justify-end pb-20 md:pb-24 lg:pb-32 text-center text-white px-4 pointer-events-none">
                            <h1 class="text-3xl sm:text-4xl md:text-5xl font-semibold tracking-wide drop-shadow-md animate-fade-in-up"
                                style="animation-delay: 0.3s; opacity:0; animation-fill-mode: forwards;">
                                {{ $image->caption }} {{-- Muestra el título --}}
                            </h1>
                            {{-- Indicador visual si es un enlace (centrado) --}}
                            @if ($image->link_url)
                                <span class="mt-3 text-sm opacity-80 group-hover:opacity-100 transition-opacity">
                                    (Haz clic para saber más)
                                </span>
                            @endif
                        </div>
                    @endif
                </div> {{-- Fin contenedor interno --}}

                {{-- Cierre del Enlace Condicional --}}
                @if ($image->link_url)
                    </a>
                @endif

            </div>
        @endforeach
    @else
        {{-- Fallback si NO hay imágenes --}}
        <div class="absolute inset-0 flex items-center justify-center bg-gray-200">
            {{-- ... (código de fallback sin cambios) ... --}}
            <div class="relative w-full h-full">
                <img src="{{ asset('images/placeholder-fallback.jpg') }}" alt="Sin imágenes"
                    class="w-full h-full object-cover opacity-50">
                <p class="absolute inset-0 flex items-center justify-center text-gray-500 text-xl font-semibold z-10">El
                    carrusel está vacío.</p>
                @auth
                    @if (Auth::user()->role === 'admin')
                        <div class="absolute bottom-4 right-4 z-30 bg-gray-900/50 p-3 rounded-md backdrop-blur-sm">
                            <p class="text-white font-semibold mb-1 text-sm">Opciones de Admin:</p>
                            <button wire:click="$dispatch('openCarouselModal')" type="button"
                                class="text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 px-3 py-1.5 rounded-md shadow focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-gray-900/50">
                                Gestionar Carrusel
                            </button>
                        </div>
                    @endif
                @endauth
            </div>
        </div>
    @endif



    {{-- Controles de Navegación (sin cambios) --}}
    @if ($images->count() > 1)
        {{-- ... (botones prev/next y puntos indicadores sin cambios) ... --}}
        <button @click="prev()"
            class="absolute left-4 top-1/2 -translate-y-1/2 z-20 p-3 bg-black/40 hover:bg-black/60 text-white rounded-full shadow-lg transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-white/60 group"
            aria-label="Imagen anterior">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 group-hover:-translate-x-0.5 transition-transform"
                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </button>

        <!-- Flecha derecha -->
        <button @click="next()"
            class="absolute right-4 top-1/2 -translate-y-1/2 z-20 p-3 bg-black/40 hover:bg-black/60 text-white rounded-full shadow-lg transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-white/60 group"
            aria-label="Siguiente imagen">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 group-hover:translate-x-0.5 transition-transform"
                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </button>
        {{-- *** Puntos Indicadores *** --}}
        <div class="absolute bottom-5 left-1/2 -translate-x-1/2 z-20 flex space-x-2">
            {{-- Itera sobre las imágenes para crear un punto por cada una --}}
            @foreach ($images as $index => $image)
                <button @click="goTo({{ $index }})" :aria-label="'Ir a imagen ' + ({{ $index }} + 1)"
                    class="w-2.5 h-2.5 rounded-full transition-all duration-300 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-black/50 focus:ring-white"
                    {{-- Clases condicionales para el punto activo/inactivo --}}
                    :class="{
                        'bg-white opacity-100 scale-110': activeIndex === {{ $index }},
                        'bg-white/60 opacity-60 hover:opacity-80 hover:bg-white/80': activeIndex !==
                            {{ $index }}
                    }"></button>
            @endforeach
        </div>
    @endif

    {{-- Sección Admin (sin cambios) --}}
    @auth
        @if (Auth::user()->role === 'admin')
            <div class="absolute bottom-4 right-4 z-30 bg-gray-900/50 p-3 rounded-md backdrop-blur-sm">
                {{-- ... (botón admin sin cambios) ... --}}
                <p class="text-white font-semibold mb-1 text-sm">Opciones de Admin:</p>
                <button wire:click="$dispatch('openCarouselModal')" type="button"
                    class="text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 px-3 py-1.5 rounded-md shadow focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-gray-900/50">
                    Gestionar Carrusel
                </button>
            </div>
        @endif
    @endauth

</div>

{{-- Estilos CSS para Animaciones (sin cambios) --}}
@push('styles')
    <style>
        /* ... (keyframes y clases de animación como antes) ... */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-down {
            animation: fadeInDown 1s ease-out forwards;
        }

        .animate-fade-in-up {
            animation: fadeInUp 1s ease-out forwards;
        }

        .animation-delay-500 {
            animation-delay: 0.5s;
            opacity: 0;
            animation-fill-mode: forwards;
        }

        @keyframes bounce {

            0%,
            100% {
                transform: translateY(-25%);
                animation-timing-function: cubic-bezier(0.8, 0, 1, 1);
            }

            50% {
                transform: translateY(0);
                animation-timing-function: cubic-bezier(0, 0, 0.2, 1);
            }
        }

        .animate-bounce {
            animation: bounce 1s infinite;
        }
    </style>
@endpush
