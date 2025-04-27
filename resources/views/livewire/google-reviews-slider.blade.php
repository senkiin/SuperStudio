{{-- resources/views/livewire/google-reviews-slider.blade.php --}}
{{-- MODIFICADO: Añadida comprobación @if($review) dentro del bucle --}}
<div x-data="{ isAdmin: @js($isAdmin) }" class="py-12 bg-gray-50 dark:bg-gray-800/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Flash Messages --}}
        @if (session()->has('message'))
            <div x-data="{ show: true }" x-show="show" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-init="setTimeout(() => show = false, 3500)"
                 class="flex items-center p-4 mb-6 text-sm font-medium text-green-800 rounded-lg bg-green-100 dark:bg-gray-800 dark:text-green-400 border border-green-200 dark:border-green-700 shadow-sm" role="alert">
                 <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/></svg>
                 <span class="sr-only">Info</span><div>{{ session('message') }}</div>
            </div>
        @endif
        @if (session()->has('error'))
             <div x-data="{ show: true }" x-show="show" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-init="setTimeout(() => show = false, 3500)"
                 class="flex items-center p-4 mb-6 text-sm font-medium text-red-800 rounded-lg bg-red-100 dark:bg-gray-800 dark:text-red-400 border border-red-200 dark:border-red-700 shadow-sm" role="alert">
                 <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/></svg>
                 <span class="sr-only">Error</span><div>{{ session('error') }}</div>
            </div>
        @endif

        {{-- Section Header --}}
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">
                Reseñas de Google
            </h2>
            {{-- Aquí podrías añadir un dropdown para $sortOptions si quieres --}}
             <div class="relative" x-data="{ open: false }" @click.away="open = false">
                <button @click="open = !open" class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 active:bg-gray-50 dark:active:bg-gray-700 active:text-gray-800 dark:active:text-gray-200 transition ease-in-out duration-150">
                    <span>Ordenar por: {{ $sortOptions[$sortBy] ?? 'Seleccionar' }}</span>
                    <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15 12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                    </svg>
                </button>
                <div x-show="open"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-700 ring-1 ring-black ring-opacity-5 z-50"
                     style="display: none;">
                    <div class="py-1">
                        @foreach($sortOptions as $key => $label)
                            <button wire:click="setSort('{{ $key }}')" @click="open = false"
                               class="block w-full text-start px-4 py-2 text-sm leading-5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-600 transition duration-150 ease-in-out">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Grid Container --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 lg:gap-8">

             {{-- Card Loop - Adaptado para Reseñas --}}
             @forelse ($this->reviews as $review)
                 {{-- ! Añadido chequeo para evitar error si $review es null ! --}}
                 @if($review)
                     <div wire:key="review-{{ $review->id }}"
                          class="relative group/card flex flex-col
                                 bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden
                                 transition-all duration-300 ease-in-out hover:shadow-xl">

                         {{-- Contenido de la Reseña --}}
                         <div class="flex-grow p-4 md:p-6">
                             {{-- Autor y Fecha --}}
                             <div class="flex items-center mb-3">
                                 @if($review->profile_photo_url)
                                     <img src="{{ $review->profile_photo_url }}" alt="{{ $review->author_name }}" class="w-10 h-10 rounded-full mr-3 object-cover border border-gray-200 dark:border-gray-700">
                                 @else
                                     {{-- Placeholder si no hay foto --}}
                                     <span class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400 mr-3 text-lg font-semibold">
                                         {{-- Asegurarse que author_name no sea null aquí también --}}
                                         {{ $review->author_name ? strtoupper(substr($review->author_name, 0, 1)) : '?' }}
                                     </span>
                                 @endif
                                 <div>
                                     <p class="font-semibold text-gray-900 dark:text-white">{{ $review->author_name }}</p>
                                     <p class="text-xs text-gray-500 dark:text-gray-400">{{ $review->relative_time_description }}</p>
                                 </div>
                             </div>

                             {{-- Estrellas --}}
                             <div class="flex items-center mb-2">
                                 @if(!is_null($review->rating)) {{-- Chequeo por si rating fuera null --}}
                                     @for ($i = 1; $i <= 5; $i++)
                                         <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                             <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                         </svg>
                                     @endfor
                                     <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">({{ $review->rating }})</span>
                                 @else
                                      <span class="ml-2 text-xs text-gray-400 dark:text-gray-500 italic">(Sin valoración)</span>
                                 @endif
                             </div>

                             {{-- Texto de la Reseña --}}
                             @if($review->text)
                                 <p class="text-sm text-gray-600 dark:text-gray-300 line-clamp-5">
                                     {{ $review->text }}
                                 </p>
                             @else
                                  <p class="text-sm text-gray-400 dark:text-gray-500 italic">(Sin comentario)</p>
                             @endif
                         </div>

                         {{-- Enlace a Google (Opcional) --}}
                         @if($review->author_url)
                             <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-3 bg-gray-50 dark:bg-gray-800/50">
                                 <a href="{{ $review->author_url }}" target="_blank" rel="noopener noreferrer"
                                    class="text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 inline-flex items-center">
                                     Ver en Google
                                     <svg class="w-3 h-3 ml-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                 </a>
                             </div>
                         @endif

                     </div> {{-- Fin del div de la reseña --}}
                 @endif {{-- Fin del @if($review) --}}
             @empty
                 {{-- Empty State --}}
                 <div class="sm:col-span-2 md:col-span-3 lg:col-span-4 text-center py-12 px-6 bg-white dark:bg-gray-800 shadow-md rounded-lg">
                     <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" /></svg>
                     <h3 class="mt-2 text-md font-semibold text-gray-900 dark:text-white">
                         No hay reseñas para mostrar.
                     </h3>
                 </div>
             @endforelse

        </div> {{-- End Grid --}}

    </div> {{-- End max-w-7xl --}}

</div> {{-- End Root div --}}
