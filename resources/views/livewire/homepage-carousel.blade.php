{{-- resources/views/livewire/homepage-carousel.blade.php --}}
<div class="relative h-screen w-full overflow-hidden"
    x-data="{
        activeIndex: 0, // Índice de la imagen activa
        images: {{ $images->count() > 0 ? $images->count() : 1 }}, // Número total de imágenes
        interval: null, // Para el temporizador de cambio automático
        autoplay: true, // Activar/desactivar cambio automático
        autoplayDelay: 7000, // Milisegundos entre cambios (7 segundos)

        next() {
            this.activeIndex = (this.activeIndex + 1) % this.images;
            this.resetInterval();
        },
        prev() {
            this.activeIndex = (this.activeIndex - 1 + this.images) % this.images;
            this.resetInterval();
        },
        goTo(index) {
            this.activeIndex = index;
            this.resetInterval();
        },
        startAutoplay() {
            if(!this.autoplay) return;
            clearInterval(this.interval); // Limpiar intervalo anterior
            this.interval = setInterval(() => {
                this.next();
            }, this.autoplayDelay);
        },
        stopAutoplay() {
             clearInterval(this.interval);
        },
        resetInterval() {
            if (this.autoplay) {
                this.startAutoplay();
            }
        },
        init() { // Inicializar Alpine
            this.startAutoplay(); // Iniciar autoplay al cargar
            // Pausar al pasar el ratón por encima
            this.$el.addEventListener('mouseenter', () => this.stopAutoplay());
            this.$el.addEventListener('mouseleave', () => this.startAutoplay());
        }
    }"
    x-init="init()">

    {{-- Contenedor de Imágenes del Carrusel --}}
    @foreach ($images as $index => $image)
        <div x-show="activeIndex === {{ $index }}"
             x-transition:enter="transition ease-in-out duration-1000"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in-out duration-1000"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="absolute inset-0">

             {{-- Imagen de fondo --}}
             {{-- Usamos getImageUrlAttribute() del modelo --}}
             <img src="{{ $image->imageUrl ?? asset('images/placeholder-fallback.jpg') }}"
                  alt="{{ $image->caption ?? 'Imagen del carrusel ' . ($index + 1) }}"
                  class="w-full h-full object-cover">

             {{-- Overlay oscuro para contraste --}}
             <div class="absolute inset-0 bg-black opacity-40"></div>
        </div>
    @endforeach

    {{-- Contenido Superpuesto (Eslogan, Botón Scroll) --}}
    <div class="absolute inset-0 z-10 flex flex-col items-center justify-center text-center text-white px-4">
        {{-- Eslogan (puede venir del componente o estar fijo) --}}
        <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-bold uppercase tracking-wider drop-shadow-md mb-6 animate-fade-in-down"
            x-text="$wire.slogan"> {{-- Usa la propiedad del componente --}}
            {{-- IGNITING PASSION --}} {{-- Fallback si JS falla --}}
        </h1>

         {{-- Botón de Scroll (o llamada a la acción) --}}
        {{-- Asegúrate de tener un elemento con id="main-content" más abajo --}}
        <button
            @click="document.getElementById('main-content')?.scrollIntoView({ behavior: 'smooth' })"
            class="mt-8 border border-white/80 rounded-full w-10 h-16 flex items-center justify-center hover:bg-white/20 transition animate-fade-in-up animation-delay-500"
            aria-label="Desplazarse hacia abajo">
            {{-- Flecha animada simple --}}
            <svg class="w-4 h-4 text-white animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </button>
    </div>

    {{-- Controles de Navegación (Opcional) --}}
    @if ($images->count() > 1)
        {{-- Botón Anterior --}}
        <button @click="prev()"
                class="absolute left-4 top-1/2 -translate-y-1/2 z-20 p-2 bg-black/30 rounded-full text-white hover:bg-black/50 transition focus:outline-none focus:ring-2 focus:ring-white/50"
                aria-label="Imagen anterior">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </button>
        {{-- Botón Siguiente --}}
        <button @click="next()"
                class="absolute right-4 top-1/2 -translate-y-1/2 z-20 p-2 bg-black/30 rounded-full text-white hover:bg-black/50 transition focus:outline-none focus:ring-2 focus:ring-white/50"
                aria-label="Siguiente imagen">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </button>

        {{-- Indicadores de Puntos (Opcional) --}}
        <div class="absolute bottom-5 left-1/2 -translate-x-1/2 z-20 flex space-x-2">
            @foreach ($images as $index => $image)
                <button @click="goTo({{ $index }})"
                        :class="{ 'bg-white': activeIndex === {{ $index }}, 'bg-white/40': activeIndex !== {{ $index }} }"
                        class="w-2.5 h-2.5 rounded-full hover:bg-white/70 transition"
                        aria-label="Ir a la imagen {{ $index + 1 }}">
                </button>
            @endforeach
        </div>
    @endif

    {{-- Sección Admin (visible solo si es admin) --}}
    @auth
        @if(Auth::user()->role === 'admin')
            <div class="absolute bottom-4 right-4 z-30 bg-white/80 backdrop-blur-sm text-black p-3 rounded-lg shadow text-xs">
                <p class="font-semibold mb-1">Opciones de Admin:</p>
                <a href="{{ route('admin.homepage.carousel') }}" {{-- Ruta a crear --}}
                   class="text-blue-600 hover:underline block">Gestionar Carrusel</a>
            </div>
        @endif
    @endauth
</div>

{{-- Añadimos algo de CSS para las animaciones si no usas una librería externa --}}
@push('styles')
<style>
    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-down { animation: fadeInDown 1s ease-out forwards; }
    .animate-fade-in-up { animation: fadeInUp 1s ease-out forwards; }
    .animation-delay-500 { animation-delay: 0.5s; opacity:0; animation-fill-mode: forwards;} /* Oculta hasta que empiece */
    /* Simple animación bounce para la flecha */
     @keyframes bounce {
         0%, 100% { transform: translateY(-25%); animation-timing-function: cubic-bezier(0.8,0,1,1); }
         50% { transform: translateY(0); animation-timing-function: cubic-bezier(0,0,0.2,1); }
     }
    .animate-bounce { animation: bounce 1s infinite; }
</style>
@endpush

{{-- Asegúrate de tener @stack('styles') en tu layout principal (layouts/app.blade.php) dentro del <head> --}}
