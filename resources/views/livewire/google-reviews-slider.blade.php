{{-- Archivo: resources/views/livewire/google-reviews-slider.blade.php --}}
{{-- Requires Swiper.js library (JS and CSS) to be loaded --}}
<div x-data="reviewSlider()" class="py-12 bg-black text-white"> {{-- Changed background to black and default text to white --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Flash Messages --}}
        {{-- ... (keep existing flash message code) ... --}}
        @if (session()->has('message'))
            <div x-data="{ show: true }" x-show="show" x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-init="setTimeout(() => show = false, 3500)"
                class="flex items-center p-4 mb-6 text-sm font-medium text-green-800 rounded-lg bg-green-100 dark:bg-gray-800 dark:text-green-400 border border-green-200 dark:border-green-700 shadow-sm"
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
                class="flex items-center p-4 mb-6 text-sm font-medium text-red-800 rounded-lg bg-red-100 dark:bg-gray-800 dark:text-red-400 border border-red-200 dark:border-red-700 shadow-sm"
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


        {{-- Section Header & Sort Dropdown --}}
        <div class="flex flex-row flex-wrap justify-between items-center mb-6 gap-4">
            <h2 x-data x-init="$el.classList.add('title-animate-down')" data-aos
                class="title-animate-down text-4xl lg:text-3, xl font-extrabold tracking-wide uppercase text-gray-900 dark:text-white">
                Qué dicen nuestros clientes
            </h2>
            @auth
                @if (Auth::user()->role === 'admin')
                    <div class="mb-6">
                        <button wire:click="fetchReviewsFromAdmin"
                            class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-500 text-white text-sm font-semibold rounded-md transition">
                            Actualizar reseñas
                        </button>
                    </div>
                @endif
            @endauth
            {{-- Sort Dropdown - Keep as is, maybe adjust colors if needed --}}
            <div class="relative" x-data="{ open: false }" @click.away="open = false">
                <button @click="open = !open"
                    class="inline-flex items-center px-3 py-2 border border-gray-600 text-sm leading-4 font-medium rounded-md text-gray-300 bg-gray-800 hover:text-white focus:outline-none focus:border-blue-500 focus:ring focus:ring-blue-400 focus:ring-opacity-50 active:bg-gray-700 transition ease-in-out duration-150">
                    <span>Ordenar por: {{ $sortOptions[$sortBy] ?? 'Seleccionar' }}</span>
                    <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M8.25 15 12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                    </svg>
                </button>

                <div x-show="open" x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="transform opacity-0 scale-95"
                    x-transition:enter-end="transform opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="transform opacity-100 scale-100"
                    x-transition:leave-end="transform opacity-0 scale-95"
                    class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-gray-700 ring-1 ring-black ring-opacity-5 z-50"
                    style="display: none;">
                    <div class="py-1">
                        @foreach ($sortOptions as $key => $label)
                            <button wire:click="setSort('{{ $key }}')" @click="open = false"
                                class="block w-full text-start px-4 py-2 text-sm leading-5 text-gray-300 hover:bg-gray-600 focus:outline-none focus:bg-gray-600 transition duration-150 ease-in-out">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Overall Rating and Review Button --}}
        {{-- Overall Rating and Review Button (Versión Rectangular y Compacta) --}}
        @if (!is_null($averageRating) && $totalReviewsCount > 0)
            <div x-data x-init="$el.classList.add('google-rating-bar')" data-aos
                class="google-rating-bar flex flex-col md:flex-row items-center justify-between bg-gray-800 p-4 shadow-md mb-6 space-y-3 md:space-y-0">

                {{-- Left side: Stars and Rating --}}
                <div class="flex flex-col md:flex-row items-center space-y-2 md:space-y-0 md:space-x-3">

                    {{-- Google Logo and Text --}}
                    <div class="flex items-center space-x-2">
                         @php
                                                        $url = Storage::disk('logos')->temporaryUrl(
                                                    'logoGoogle.png',
                                                    now()->addMinutes(30)
                                                ); @endphp
                        <img src="{{ $url }}" class="h-5 w-auto">
                        <span class="text-white text-base font-semibold">Excelente en Google</span>
                    </div>

                    {{-- Stars and Rating --}}
                    <div class="flex items-center space-x-2">
                        {{-- Stars --}}
                        <div class="flex space-x-1">
                            @for ($i = 0; $i < 5; $i++)
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-400"
                                    fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.966a1 1 0 00.95.69h4.18c.969 0 1.371 1.24.588 1.81l-3.388 2.46a1 1 0 00-.364 1.118l1.286 3.966c.3.921-.755 1.688-1.54 1.118l-3.388-2.46a1 1 0 00-1.176 0l-3.388 2.46c-.784.57-1.838-.197-1.539-1.118l1.286-3.966a1 1 0 00-.364-1.118l-3.388-2.46c-.783-.57-.38-1.81.588-1.81h4.18a1 1 0 00.95-.69l1.286-3.966z" />
                                </svg>
                            @endfor
                        </div>

                        {{-- Average Rating --}}
                        <span class="text-white text-lg font-bold">{{ number_format($averageRating, 1) }} de 5</span>
                    </div>

                </div>

                {{-- Right side: Button --}}
                <div class="w-full md:w-auto">
                    <a href="https://search.google.com/local/writereview?placeid=..." x-data x-init="$el.classList.add('google-btn-animate')"
                        data-aos
                        class="inline-flex google-btn-animate w-full md:w-auto justify-center items-center px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white font-semibold transition">
                        Califícanos en Google
                    </a>
                </div>

            </div>
        @endif





        {{-- Loading Indicator (Positioned outside the slider) --}}
        <div wire:loading wire:target="reviews,setSort" class="w-full text-center py-12">
            <svg class="animate-spin mx-auto h-8 w-8 text-indigo-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
            </svg>
            <p class="mt-2 text-sm text-gray-400">Cargando reseñas...</p>
        </div>

        {{-- Swiper Slider Container --}}
        {{-- Show only when NOT loading AND reviews exist --}}
        <div wire:loading.remove wire:target="reviews,setSort" x-show="!loading && reviewsExist" x-cloak>
            <div class="overflow-x-auto pb-6 -mx-2 sm:-mx-4 lg:-mx-6">
                <div class="flex px-2 sm:px-4 lg:px-6 gap-4 snap-x snap-mandatory overflow-x-auto scroll-smooth">

                    @forelse ($this->reviews as $review)
                        <a href="{{ $review->author_url }}" target="_blank" rel="noopener noreferrer" x-data
                            x-init="$el.classList.add('review-animate-up')" data-aos @touchstart="touchStart($event)"
                            @touchend="touchEnd($event)"
                            class="review-card flex-shrink-0 w-52 h-52 sm:w-56 sm:h-56 md:w-60 md:h-60 lg:w-64 lg:h-64 bg-gray-800 shadow-md hover:shadow-lg transition-all duration-300 snap-start flex flex-col justify-between p-3 group hover:-translate-y-1 hover:scale-105">

                            {{-- Top: Avatar, Name, Date --}}
                            <div>
                                <div class="flex items-center space-x-2 mb-2">
                                    @if ($review->profile_photo_url)
                                        <img src="{{ $review->profile_photo_url }}" alt="{{ $review->author_name }}"
                                            class="w-9 h-9 object-cover">
                                    @else
                                        <div
                                            class="w-9 h-9 bg-gray-700 flex items-center justify-center text-gray-400 font-bold text-base">
                                            {{ strtoupper(substr($review->author_name, 0, 1)) }}
                                        </div>
                                    @endif

                                    <div class="min-w-0">
                                        <div class="flex items-center space-x-2 truncate">
                                            <span
                            
                                                class="font-semibold text-white text-sm truncate">{{ $review->author_name }}</span>
                                            <img src="{{ $url }}" class="w-4 h-4" alt="Google">
                                            <svg class="w-4 h-4 text-blue-400" xmlns="http://www.w3.org/2000/svg"
                                                fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
                                            </svg>
                                        </div>
                                        <p class="text-xs text-gray-400 truncate">
                                            {{ $review->relative_time_description }}
                                        </p>
                                    </div>
                                </div>

                                {{-- Stars --}}
                                <div class="flex items-center mb-2">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-600' }}"
                                            fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    @endfor
                                </div>

                                {{-- Review Text --}}
                                @if ($review->text)
                                    <p class="text-sm text-gray-300 font-bold line-clamp-3">
                                        {{ $review->text }}
                                    </p>
                                @else
                                    <p class="text-sm text-gray-500 italic font-bold">(Sin comentario)</p>
                                @endif
                            </div>

                            {{-- Leer Más --}}
                            <div class="pt-1">
                                <span class="text-blue-400 text-xs font-semibold group-hover:underline">Leer más</span>
                            </div>

                        </a>
                    @empty
                        <div class="text-center text-gray-400 w-full py-12">
                            No hay reseñas disponibles.
                        </div>
                    @endforelse

                </div>
            </div>
        </div>







        {{-- Empty State (visible only when not loading and no reviews) --}}
        <div wire:loading.remove wire:target="reviews,setSort" x-show="!loading && !reviewsExist" x-cloak>
            <div class="text-center py-12 px-6 bg-gray-800 shadow-md rounded-lg"> {{-- Adjusted for dark theme --}}
                <svg class="mx-auto h-10 w-10 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                </svg>
                <h3 class="mt-2 text-md font-semibold text-white"> {{-- Adjusted text color --}}
                    No hay reseñas para mostrar.
                </h3>
                {{-- Optional: Add the "Rate us" button here too if desired --}}
                @if ($googleReviewLink && $googleReviewLink !== '#')
                    <div class="mt-4">
                        <a href="{{ $googleReviewLink }}" target="_blank" rel="noopener noreferrer"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 disabled:opacity-25 transition">
                            Sé el primero en calificarnos
                        </a>
                    </div>
                @endif
            </div>
        </div>

    </div> {{-- End max-w-7xl --}}
</div> {{-- End Root div --}}

@push('scripts')
    <script>
        function touchScrollHandler() {
            let touchStartX = 0;
            let touchEndX = 0;

            return {
                touchStart(event) {
                    touchStartX = event.changedTouches[0].screenX;
                },
                touchEnd(event) {
                    touchEndX = event.changedTouches[0].screenX;

                    if (Math.abs(touchStartX - touchEndX) < 10) {
                        // Si no hubo scroll (menos de 10px), interpretamos que sí quiere clickar
                        event.target.closest('a').click();
                    }
                    // Si hubo swipe, NO hacemos nada (no abrimos enlace)
                }
            }
        }

        function reviewSlider() {
            return {
                swiperInstance: null,
                reviewsExist: {{ $this->reviews->count() > 0 ? 'true' : 'false' }}, // Initial check if reviews exist

                initSwiper() {
                    // Destroy previous instance if it exists (useful for Livewire updates)
                    if (this.swiperInstance) {
                        this.swiperInstance.destroy(true, true);
                        this.swiperInstance = null;
                    }

                    // Only init if reviews exist
                    if (this.reviewsExist && this.$refs.swiperContainer) {
                        this.swiperInstance = new Swiper(this.$refs.swiperContainer, {
                            // Optional parameters
                            loop: false, // Set to true if you want infinite loop
                            slidesPerView: 1.2, // Show parts of next/prev slides
                            spaceBetween: 15, // Space between slides
                            centeredSlides: false, // Don't center the active slide initially
                            pagination: {
                                el: '.swiper-pagination',
                                clickable: true,
                            },
                            navigation: {
                                nextEl: '.swiper-button-next',
                                prevEl: '.swiper-button-prev',
                            },
                            // Responsive breakpoints
                            breakpoints: {
                                // when window width is >= 640px
                                640: {
                                    slidesPerView: 2.3,
                                    spaceBetween: 20
                                },
                                // when window width is >= 768px
                                768: {
                                    slidesPerView: 3.3,
                                    spaceBetween: 25
                                },
                                // when window width is >= 1024px
                                1024: {
                                    slidesPerView: 4.3, // Adjust based on desired look
                                    spaceBetween: 30
                                }
                            },
                            // Handle Livewire updates potentially messing with Swiper
                            observer: true, // Re-init Swiper on DOM changes within container
                            observeParents: true, // Re-init Swiper on DOM changes within parent
                        });
                    }
                },

                init() {
                    // Initial setup
                    this.loading = false; // Assume loading finishes quickly or handle with wire:loading elsewhere

                    // Use Livewire hooks to re-initialize Swiper after updates
                    Livewire.hook('element.updated', (el, component) => {
                        // Check if the updated element is within this component
                        if (component.id === @this.__livewireId) {
                            this.reviewsExist =
                                {{ $this->reviews->count() > 0 ? 'true' : 'false' }}; // Re-check review count
                            this.$nextTick(() => this.initSwiper()); // Re-initialize Swiper on next tick
                        }
                    });

                    // Initial Swiper initialization
                    this.$nextTick(() => this.initSwiper());

                    // Watch for changes in review count (alternative way)
                    // this.$watch('reviewsExist', (value) => {
                    //     if (value) {
                    //         this.$nextTick(() => this.initSwiper());
                    //     } else if (this.swiperInstance) {
                    //         this.swiperInstance.destroy(true, true);
                    //         this.swiperInstance = null;
                    //     }
                    // });
                }
            }
        }
    </script>
@endpush
