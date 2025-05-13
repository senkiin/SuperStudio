<div>
    <header class="relative text-white bg-gray-900">

        <div class="relative h-[55vh] md:h-[65vh] bg-cover bg-center group"
            style="background-image: url('{{ $settings && $settings->background_image_url ? Storage::url($settings->background_image_url) : '/ruta/por/defecto/tu-imagen-de-leopardo.jpg' }}');">
            <div class="absolute inset-0 bg-black opacity-40"></div>

            <div
                class="relative z-10 flex flex-col items-center justify-center h-full text-center px-4 -mt-16 md:-mt-20">
                @if ($isAdmin)
                    <button wire:click="openEditModal" title="Editar Cabecera"
                        class="p-2 bg-blue-600 hover:bg-blue-700 text-white rounded-full shadow-lg transition transform hover:scale-110">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                        </svg>
                    </button>
                @endif
                <div x-data x-intersect="$el.classList.add('aos-animate')"
                    class="relative z-10 flex flex-col items-center justify-center h-full text-center px-4 -mt-16 md:-mt-20 fade-up">
                    <h1
                        class="text-4xl sm:text-5xl md:text-6xl font-bold tracking-tight leading-tight mb-4 drop-shadow-md">
                        {{ $settings->hero_title ?? 'Título Hero por Defecto' }}
                    </h1>
                    <p class="text-lg sm:text-xl md:text-2xl text-gray-200 max-w-3xl drop-shadow-md fade-up" x-data
                        x-intersect="$el.classList.add('aos-animate')">
                        {{ $settings->hero_subtitle ?? 'Subtítulo Hero por Defecto' }}
                    </p>
                </div>
            </div>

        </div>

    </header>

    @if ($showEditModal && Auth::user()->role == 'admin')
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50 p-4"
            wire:transition.opacity>
            <div class="bg-gray-800 p-6 md:p-8 rounded-lg shadow-2xl w-full max-w-lg transform transition-all"
                @click.away="closeEditModal()">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-semibold text-white">Editar Cabecera</h3>
                    <button wire:click="closeEditModal" class="text-gray-400 hover:text-gray-200 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-7 h-7">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="save">
                    <div class="space-y-6">
                        <div>
                            <label for="modalHeroTitle" class="block text-sm font-medium text-gray-300 mb-1">Título
                                Principal:</label>
                            <input type="text" wire:model.defer="heroTitle" id="modalHeroTitle"
                                class="w-full p-3 rounded bg-gray-700 text-white border border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-500">
                            @error('heroTitle')
                                <span class="text-red-400 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label for="modalHeroSubtitle"
                                class="block text-sm font-medium text-gray-300 mb-1">Subtítulo:</label>
                            <textarea wire:model.defer="heroSubtitle" id="modalHeroSubtitle" rows="4"
                                class="w-full p-3 rounded bg-gray-700 text-white border border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-500"></textarea>
                            @error('heroSubtitle')
                                <span class="text-red-400 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label for="modalNewBackgroundImage"
                                class="block text-sm font-medium text-gray-300 mb-1">Imagen de Fondo:</label>
                            <input type="file" wire:model="newBackgroundImage" id="modalNewBackgroundImage"
                                class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-500 file:text-white hover:file:bg-blue-600 cursor-pointer">
                            <div wire:loading wire:target="newBackgroundImage" class="text-blue-400 text-xs mt-1">
                                Subiendo imagen...</div>
                            @error('newBackgroundImage')
                                <span class="text-red-400 text-xs mt-1">{{ $message }}</span>
                            @enderror

                            @if ($newBackgroundImage)
                                <p class="text-xs text-gray-400 mt-2">Previsualización de la nueva imagen:</p>
                                <img src="{{ $newBackgroundImage->temporaryUrl() }}" alt="Previsualización"
                                    class="mt-2 rounded max-h-40 object-contain">
                            @elseif ($existingBackgroundImageUrl)
                                <p class="text-xs text-gray-400 mt-2">Imagen actual:</p>
                                <img src="{{ Storage::url($existingBackgroundImageUrl) }}" alt="Imagen actual"
                                    class="mt-2 rounded max-h-40 object-contain">
                            @else
                                <p class="text-xs text-gray-400 mt-2">No hay imagen de fondo configurada.</p>
                            @endif
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end space-x-3">
                        <button type="button" wire:click="closeEditModal"
                            class="px-5 py-2.5 text-sm font-medium text-gray-300 bg-gray-600 hover:bg-gray-500 rounded-lg focus:outline-none focus:ring-4 focus:ring-gray-700 transition">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg focus:outline-none focus:ring-4 focus:ring-blue-800 transition">
                            Guardar Cambios
                            <div wire:loading wire:target="save" class="inline-block ml-2">
                                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                            </div>
                        </button>
                    </div>
                </form>
                <div wire:loading wire:target="save" class="text-sm text-blue-300">Guardando...</div>
            </div>
        </div>
    @endif

    @if (session()->has('message'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-y-0"
            x-transition:leave-end="opacity-0 transform translate-y-2"
            class="fixed bottom-5 right-5 bg-green-600 text-white py-3 px-6 rounded-lg shadow-xl z-50 text-sm">
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-y-0"
            x-transition:leave-end="opacity-0 transform translate-y-2"
            class="fixed bottom-5 right-5 bg-red-600 text-white py-3 px-6 rounded-lg shadow-xl z-50 text-sm">
            {{ session('error') }}
        </div>
    @endif


    <section class="py-12 bg-black text-white">
        <div class="container mx-auto px-6">

            {{-- Título --}}
            <h2 x-data x-intersect="$el.classList.add('aos-animate')"
                class="text-3xl font-semibold mb-4 text-center fade-up">
                Nuestras Comuniones
            </h2>

            {{-- Párrafo --}}
            <p x-data x-intersect="$el.classList.add('aos-animate')"
                class="text-gray-400 text-center max-w-3xl mx-auto fade-up">
                Más de 1000 comuniones documentadas.
            </p>


        </div>
    </section>

</div>
