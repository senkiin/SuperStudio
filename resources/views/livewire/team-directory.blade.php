<div class="py-16 sm:py-24 bg-gradient-to-b from-black via-gray-950 to-black">
    <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header Section --}}
        <div class="text-center mb-16">
            <p class="text-indigo-400 font-semibold text-sm uppercase tracking-wide mb-3">Conoce a los Expertos</p>
            <h2 class="text-4xl sm:text-5xl font-bold text-white mb-4">Nuestro Equipo Profesional</h2>
            <p class="text-gray-400 text-lg max-w-2xl mx-auto">
                Un equipo apasionado dedicado a capturar tus momentos más especiales con creatividad y profesionalismo
            </p>

            @auth
                @if (auth()->user()->role === 'admin')
                    <button wire:click="create"
                        class="mt-8 inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold py-3 px-6 rounded-xl shadow-lg shadow-indigo-500/20 transition-all duration-300 transform hover:scale-105 hover:shadow-indigo-500/40">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        Añadir Miembro
                    </button>
                @endif
            @endauth
        </div>

        @if (session()->has('message'))
            <div class="mb-8 bg-gradient-to-r from-green-500 to-emerald-600 text-white p-4 rounded-xl shadow-lg"
                x-data="{ show: true }"
                x-show="show"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-y-2"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                x-init="setTimeout(() => show = false, 3000)">
                <div class="flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('message') }}
                </div>
            </div>
        @endif

        {{-- Team Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 lg:gap-10">
            @forelse ($employees as $employee)
                <div class="group relative">
                    {{-- Card Container --}}
                    <div class="relative bg-gradient-to-br from-gray-900 to-gray-950 rounded-2xl overflow-hidden shadow-xl hover:shadow-2xl hover:shadow-indigo-500/20 transition-all duration-500 transform hover:-translate-y-2">

                        {{-- Image Section --}}
                        <div class="relative h-80 overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/50 to-transparent z-10"></div>
                            <img class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700"
                                src="{{ $employee->image_path ? Illuminate\Support\Facades\Storage::disk('empleados')->url($employee->image_path) : 'https://via.placeholder.com/400x500.png/1a202c/ffffff?text=Sin+Foto' }}"
                                alt="Foto de {{ $employee->name }}">

                            {{-- Position Badge --}}
                            <div class="absolute top-4 left-4 z-20">
                                <span class="inline-flex items-center px-4 py-2 bg-indigo-600/90 backdrop-blur-sm text-white text-sm font-semibold rounded-full shadow-lg">
                                    {{ $employee->position }}
                                </span>
                            </div>
                        </div>

                        {{-- Content Section --}}
                        <div class="relative z-20 -mt-16 px-6 pb-6">
                            {{-- Name Card --}}
                            <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl p-5 shadow-xl border border-gray-700/50 mb-4">
                                <h3 class="text-2xl font-bold text-white mb-1 tracking-tight">{{ $employee->name }}</h3>
                                <div class="flex items-center gap-2 text-indigo-400 text-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="font-medium">Foto Valera</span>
                                </div>
                            </div>

                            {{-- Description Quote --}}
                            <div class="relative">
                                <div class="absolute -left-2 -top-2 text-5xl text-indigo-500/20 font-serif leading-none">"</div>
                                <p class="text-gray-300 text-sm leading-relaxed pl-6 pr-2 italic">
                                    {{ $employee->description }}
                                </p>
                                <div class="absolute -right-2 -bottom-2 text-5xl text-indigo-500/20 font-serif leading-none">"</div>
                            </div>
                        </div>

                        {{-- Decorative Bottom Bar --}}
                        <div class="h-1 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500"></div>


                        {{-- Admin Controls --}}
                        @auth
                            @if (auth()->user()->role === 'admin')
                                <div class="absolute top-6 right-6 z-30 flex gap-2 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-2 group-hover:translate-y-0">
                                    <button wire:click="edit({{ $employee->id }})"
                                        class="bg-blue-600/90 hover:bg-blue-500 backdrop-blur-sm text-white p-3 rounded-full shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-110">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" />
                                            <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                    <button wire:click="confirmDelete({{ $employee->id }})"
                                        class="bg-red-600/90 hover:bg-red-500 backdrop-blur-sm text-white p-3 rounded-full shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-110">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm4 0a1 1 0 012 0v6a1 1 0 11-2 0V8z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            @endif
                        @endauth
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="text-center py-20">
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-800 rounded-full mb-6">
                            <svg class="w-10 h-10 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-2">No hay miembros del equipo</h3>
                        <p class="text-gray-400 mb-6">Aún no se han agregado miembros al equipo.</p>
                        @auth
                            @if (auth()->user()->role === 'admin')
                                <button wire:click="create"
                                    class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold py-3 px-6 rounded-xl shadow-lg transition-all duration-300 transform hover:scale-105">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                    </svg>
                                    Añadir Primer Miembro
                                </button>
                            @endif
                        @endauth
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Modal Form --}}
        <x-dialog-modal wire:model.live="isModalOpen">
            <x-slot name="title">
                <div class="flex items-center gap-3">
                    <div class="flex items-center justify-center w-10 h-10 bg-indigo-500/10 rounded-lg">
                        <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <span class="text-xl font-bold text-white">
                        {{ $selectedEmployeeId ? 'Editar Miembro del Equipo' : 'Añadir Nuevo Miembro' }}
                    </span>
                </div>
            </x-slot>

            <x-slot name="content">
                <div class="space-y-6">
                    {{-- Name Field --}}
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-300 mb-2">
                            Nombre Completo
                            <span class="text-red-400">*</span>
                        </label>
                        <input type="text" wire:model="name" id="name"
                            class="block w-full bg-gray-800/50 border border-gray-700 rounded-lg px-4 py-3 text-white placeholder-gray-500 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                            placeholder="Ej: Juan Pérez">
                        @error('name')
                            <p class="mt-2 text-sm text-red-400 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Position Field --}}
                    <div>
                        <label for="position" class="block text-sm font-semibold text-gray-300 mb-2">
                            Cargo / Posición
                            <span class="text-red-400">*</span>
                        </label>
                        <input type="text" wire:model="position" id="position"
                            class="block w-full bg-gray-800/50 border border-gray-700 rounded-lg px-4 py-3 text-white placeholder-gray-500 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                            placeholder="Ej: Fotógrafo Principal">
                        @error('position')
                            <p class="mt-2 text-sm text-red-400 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Description Field --}}
                    <div>
                        <label for="description" class="block text-sm font-semibold text-gray-300 mb-2">
                            Descripción / Cita
                            <span class="text-red-400">*</span>
                        </label>
                        <textarea wire:model="description" id="description" rows="4"
                            class="block w-full bg-gray-800/50 border border-gray-700 rounded-lg px-4 py-3 text-white placeholder-gray-500 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all resize-none"
                            placeholder="Breve descripción o cita inspiradora..."></textarea>
                        @error('description')
                            <p class="mt-2 text-sm text-red-400 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Photo Upload Field --}}
                    <div>
                        <label for="photo" class="block text-sm font-semibold text-gray-300 mb-2">
                            Fotografía Profesional
                        </label>
                        <div class="mt-2">
                            <label for="photo" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-700 border-dashed rounded-lg cursor-pointer bg-gray-800/30 hover:bg-gray-800/50 transition-all">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <svg class="w-10 h-10 mb-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    <p class="mb-2 text-sm text-gray-400">
                                        <span class="font-semibold">Click para subir</span> o arrastra la imagen
                                    </p>
                                    <p class="text-xs text-gray-500">PNG, JPG (MAX. 20MB)</p>
                                </div>
                                <input type="file" wire:model="photo" id="photo" class="hidden">
                            </label>
                        </div>

                        @error('photo')
                            <p class="mt-2 text-sm text-red-400 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror

                        <div wire:loading wire:target="photo" class="mt-4 flex items-center gap-2 text-indigo-400 text-sm">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Cargando imagen...
                        </div>

                        {{-- Photo Preview --}}
                        @if ($photo || $existingPhoto)
                            <div class="mt-4 flex items-center gap-4 p-4 bg-gray-800/30 rounded-lg border border-gray-700">
                                @if ($photo)
                                    <img src="{{ $photo->temporaryUrl() }}" class="h-24 w-24 rounded-lg object-cover shadow-lg ring-2 ring-indigo-500">
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-white">Nueva foto</p>
                                        <p class="text-xs text-gray-400 mt-1">Previsualización de la imagen seleccionada</p>
                                    </div>
                                @elseif ($existingPhoto)
                                    <img src="{{ Illuminate\Support\Facades\Storage::disk('empleados')->url($existingPhoto) }}"
                                        class="h-24 w-24 rounded-lg object-cover shadow-lg">
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-white">Foto actual</p>
                                        <p class="text-xs text-gray-400 mt-1">Sube una nueva imagen para reemplazarla</p>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </x-slot>

            <x-slot name="footer">
                <div class="flex items-center justify-end gap-3">
                    <button wire:click="closeModal"
                        class="px-6 py-2.5 bg-gray-700 hover:bg-gray-600 text-white font-semibold rounded-lg transition-all duration-300 transform hover:scale-105">
                        Cancelar
                    </button>
                    <button wire:click.prevent="store"
                        class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-semibold rounded-lg shadow-lg shadow-indigo-500/20 transition-all duration-300 transform hover:scale-105 hover:shadow-indigo-500/40">
                        {{ $selectedEmployeeId ? 'Actualizar' : 'Guardar' }}
                    </button>
                </div>
            </x-slot>
        </x-dialog-modal>

    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('livewire:initialized', () => {
                @this.on('show-delete-confirmation', (event) => {
                    const employeeId = event[0];
                    Swal.fire({
                        title: '¿Eliminar miembro del equipo?',
                        html: '<p class="text-gray-400 mt-2">Esta acción no se puede deshacer. La información del miembro se eliminará permanentemente.</p>',
                        icon: 'warning',
                        iconColor: '#f59e0b',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: '<span class="font-semibold">Sí, eliminar</span>',
                        cancelButtonText: '<span class="font-semibold">Cancelar</span>',
                        background: '#111827',
                        color: '#ffffff',
                        backdrop: `rgba(0,0,0,0.8)`,
                        customClass: {
                            popup: 'rounded-2xl border border-gray-800 shadow-2xl',
                            title: 'text-2xl font-bold',
                            htmlContainer: 'text-sm',
                            confirmButton: 'rounded-lg px-6 py-3 font-semibold shadow-lg hover:scale-105 transition-transform',
                            cancelButton: 'rounded-lg px-6 py-3 font-semibold hover:scale-105 transition-transform'
                        },
                        buttonsStyling: true,
                        showClass: {
                            popup: 'animate__animated animate__fadeIn animate__faster'
                        },
                        hideClass: {
                            popup: 'animate__animated animate__fadeOut animate__faster'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            @this.call('delete', employeeId);
                            Swal.fire({
                                title: '¡Eliminado!',
                                text: 'El miembro ha sido eliminado del equipo.',
                                icon: 'success',
                                iconColor: '#10b981',
                                background: '#111827',
                                color: '#ffffff',
                                confirmButtonColor: '#10b981',
                                confirmButtonText: 'Entendido',
                                customClass: {
                                    popup: 'rounded-2xl border border-gray-800 shadow-2xl',
                                    confirmButton: 'rounded-lg px-6 py-3 font-semibold shadow-lg hover:scale-105 transition-transform'
                                },
                                timer: 2000,
                                timerProgressBar: true
                            });
                        }
                    })
                });
            });
        </script>
    @endpush
</div>
