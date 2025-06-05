<div>
    <button wire:click="toggleModal" title="{{ $fixedPurpose }}"
        class="fixed bottom-6 right-6 bg-slate-700 hover:bg-slate-800 text-white w-16 h-16 rounded-full shadow-xl flex items-center justify-center z-[1000] transition-all duration-300 ease-in-out transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-slate-400 focus:ring-opacity-75">
        {{-- Icono de sobre --}}
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="w-7 h-7">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 8.25v9a2.25 2.25 0 01-2.25 2.25H4.5a2.25
                 2.25 0 01-2.25-2.25v-9a2.25 2.25 0 012.25-2.25h15a2.25
                 2.25 0 012.25 2.25z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75l8.25 5.25L20.25 6.75" />
        </svg>
    </button>

    {{-- Overlay y Contenedor del Modal (igual que en la respuesta 28) --}}
    <div class="fixed inset-0 bg-gray-800 bg-opacity-75 backdrop-blur-sm flex items-center justify-center z-[1001] px-4 py-6"
        x-data="{ openModal: @entangle('showModal') }" x-show="openModal" x-on:keydown.escape.window="openModal = false"
        x-on:click.self="openModal = false" x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" style="display: none;" role="dialog" aria-modal="true"
        aria-labelledby="modal-title-contact-fab" wire:ignore.self>

        {{-- Contenido del Modal --}}
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg max-h-[90vh] flex flex-col transform transition-all"
            x-show="openModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">

            @if ($formSubmitted)
                <div class="text-center p-6 sm:p-10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-green-500 mx-auto mb-4" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-xl font-semibold text-slate-800 mb-2">¡Solicitud Enviada!</h3>
                    {{-- Mensaje de éxito adaptado --}}
                    <p class="text-slate-600">Gracias por tu interés. Hemos recibido tu solicitud de información y nos
                        pondremos en contacto contigo pronto.</p>
                    <button wire:click="toggleModal"
                        class="mt-6 w-full sm:w-auto inline-flex justify-center items-center px-6 py-2.5 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-slate-600 hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors">
                        Cerrar
                    </button>
                </div>
            @else
                {{-- Header del Modal --}}
                <div class="px-6 py-4 sm:px-7 border-b border-slate-200">
                    <div class="flex justify-between items-center">
                        {{-- Título del modal usa la variable $fixedPurpose --}}
                        <h2 class="text-lg font-semibold text-slate-800" id="modal-title-contact-fab">
                            {{ $fixedPurpose }}</h2>
                        <button wire:click="toggleModal" title="Cerrar modal"
                            class="text-slate-400 hover:text-slate-500 transition-colors rounded-full p-1 -mr-2 focus:outline-none focus:ring-2 focus:ring-slate-500">
                            <span class="sr-only">Cerrar</span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Cuerpo del Modal (con scroll) --}}
                <div class="px-6 py-5 sm:px-7 sm:py-6 flex-grow overflow-y-auto">
                    @if (session()->has('fab_error'))
                        <div class="mb-4 p-3 bg-red-100 border border-red-300 text-red-700 rounded-md text-sm">
                            {{ session('fab_error') }}
                        </div>
                    @endif
                    <form wire:submit.prevent="submitForm" class="space-y-4">
                        @csrf
                        <div>
                            <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Nombre:</label>
                            <input type="text" id="name" wire:model.defer="name"
                                class="w-full px-3.5 py-2 border border-slate-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-slate-500 sm:text-sm @error('name') border-red-500 ring-red-500 @enderror"
                                placeholder="Tu nombre completo">
                            @error('name')
                                <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Correo
                                Electrónico:</label>
                            <input type="email" id="email" wire:model.defer="email"
                                class="w-full px-3.5 py-2 border border-slate-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-slate-500 sm:text-sm @error('email') border-red-500 ring-red-500 @enderror"
                                placeholder="tu@email.com">
                            @error('email')
                                <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- NUEVO CAMPO DE TELÉFONO --}}
                        <div>
                            <label for="phone" class="block text-sm font-medium text-slate-700 mb-1">Teléfono
                                (Opcional):</label>
                            <input type="tel" id="phone" wire:model.defer="phone"
                                class="w-full px-3.5 py-2 border border-slate-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-slate-500 sm:text-sm @error('phone') border-red-500 ring-red-500 @enderror"
                                placeholder="Tu número de teléfono">
                            @error('phone')
                                <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- CAMPO CATEGORÍA ELIMINADO DE LA VISTA --}}

                        <div>
                            <label for="description"
                                class="block text-sm font-medium text-slate-700 mb-1">Mensaje:</label>
                            {{-- Label de descripción cambiado a "Mensaje" --}}
                            <textarea id="description" wire:model.defer="description" rows="4"
                                class="w-full px-3.5 py-2 border border-slate-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-slate-500 sm:text-sm @error('description') border-red-500 ring-red-500 @enderror"
                                placeholder="Escribe aquí tu consulta o qué información necesitas..."></textarea>
                            @error('description')
                                <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </form>
                </div>

                {{-- Footer del Modal (igual que en la respuesta 28) --}}
                <div class="px-6 py-4 sm:px-7 bg-slate-50 border-t border-slate-200 rounded-b-xl">
                    {{-- ... (botones de Enviar y Cancelar igual que antes) ... --}}
                    <div class="flex flex-col-reverse sm:flex-row sm:justify-end sm:space-x-3 sm:space-x-reverse">
                        <button wire:click="submitForm" wire:loading.attr="disabled" wire:target="submitForm"
                            type="submit"
                            class="w-full sm:w-auto inline-flex justify-center items-center px-5 py-2.5 border border-transparent text-sm font-semibold rounded-lg shadow-sm text-white bg-slate-700 hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors disabled:opacity-60 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="submitForm">Enviar Solicitud</span>
                            <span wire:loading wire:target="submitForm" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2.5 h-5 w-5 text-white"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                Enviando...
                            </span>
                        </button>
                        <button wire:click="toggleModal" type="button"
                            class="mt-3 w-full sm:mt-0 sm:w-auto inline-flex justify-center items-center px-5 py-2.5 border border-slate-300 text-sm font-semibold rounded-lg shadow-sm text-slate-700 bg-white hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors">
                            Cancelar
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
