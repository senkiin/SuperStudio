  {{-- @php
    use Illuminate\Support\Facades\Storage;

    $disk   = Storage::disk('favicons');
    $expires = now()->addMinutes(60); // tiempo de validez de la URL

    $fondo   = $disk->temporaryUrl('175227.jpg',   $expires);
style="background-image: url('{{ $fondo }}')"";
@endphp --}}

<div class=" text-gray-900 sm:p-6 flex items-center justify-center bg-black">
    <div class="w-full max-w-7xl mx-auto">

        <div class="flex justify-between items-center mb-8 px-2 sm:px-0">
            <h1 class="text-3xl sm:text-4xl font-bold text-gray-200">Nuestro Equipo</h1>
            @auth
                @if (auth()->user()->role === 'admin')
                    <button wire:click="create"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-transform transform hover:scale-105">
                        Añadir Empleado
                    </button>
                @endif
            @endauth
        </div>

        @if (session()->has('message'))
            <div class="bg-green-500 text-white p-4 rounded-lg mb-6" x-data="{ show: true }" x-show="show"
                x-init="setTimeout(() => show = false, 3000)">
                {{ session('message') }}
            </div>
        @endif

        {{-- CAMBIO: Reducido el espacio entre tarjetas de gap-8 a gap-6 --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @forelse ($employees as $employee)
                {{-- Se añade la clase 'group' para que los botones aparezcan al pasar el cursor --}}
                <div
                    class="bg-[#F3EFEA] font-sans rounded-lg shadow-lg transform transition-transform hover:scale-105 relative overflow-hidden group">

                    <div class="flex">
                        <div class="w-1/3 flex-shrink-0">
                            <img class="w-full h-full object-cover"
                                src="{{ $employee->image_path ? Illuminate\Support\Facades\Storage::disk('empleados')->url($employee->image_path) : 'https://via.placeholder.com/300x400.png/1a202c/ffffff?text=Sin+Foto' }}"
                                alt="Foto de {{ $employee->name }}">
                        </div>

                        {{-- CAMBIO: Reducido el padding de p-4 a p-3 para un look más compacto --}}
                        <div class="w-2/3 p-3 flex flex-col justify-between">
                            <div class="text-right">
                                <h2 class="text-xl font-bold text-purple-900">Fotovalera</h2>
                                <p class="text-xs text-purple-700 tracking-wider">"Donde simpre se crean recuerdos"</p>
                            </div>

                            <div class="text-left my-3">
                                <p class="text-sm font-semibold text-gray-600 uppercase">{{ $employee->position }}</p>
                                <h3 class="text-2xl font-bold text-black uppercase tracking-wide">{{ $employee->name }}
                                </h3>
                            </div>

                            <div class="bg-yellow-300 p-2 text-center">
                                <p class="text-sm font-medium italic text-gray-800">"{{ $employee->description }}"</p>
                            </div>

                            <div class="flex justify-between items-center mt-2">
                                <div class="w-2/3 h-6 bg-gray-300 flex items-center">
                                    <svg class="w-full h-px" viewBox="0 0 100 1" preserveAspectRatio="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <line x1="0" y1="0.5" x2="100" y2="0.5" stroke="black"
                                            stroke-width="3" stroke-dasharray="1,3" />
                                    </svg>
                                </div>
                                <p class="text-sm font-mono font-bold">ESP
                                </p>
                            </div>
                        </div>
                    </div>




                    @auth
                        @if (auth()->user()->role === 'admin')
                            <div
                                class="absolute top-2 right-2 flex flex-col space-y-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <button wire:click="edit({{ $employee->id }})"
                                    class="bg-blue-600 hover:bg-blue-700 text-white p-2 rounded-full shadow-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path
                                            d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" />
                                        <path fill-rule="evenodd"
                                            d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <button wire:click="confirmDelete({{ $employee->id }})"
                                    class="bg-red-600 hover:bg-red-700 text-white p-2 rounded-full shadow-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm4 0a1 1 0 012 0v6a1 1 0 11-2 0V8z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        @endif
                    @endauth
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <p class="text-gray-200 text-lg">Aún no hay empleados para mostrar.</p>
                </div>
            @endforelse
        </div>

        <x-dialog-modal wire:model.live="isModalOpen">
            <x-slot name="title">
                <span class="text-white">{{ $selectedEmployeeId ? 'Editar Empleado' : 'Añadir Nuevo Empleado' }}</span>
            </x-slot>
            <x-slot name="content">
                <div class="space-y-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-300">Nombre Completo</label>
                        <input type="text" wire:model="name" id="name"
                            class="mt-1 block w-full bg-gray-800 border-gray-600 rounded-md shadow-sm text-white focus:ring-indigo-500 focus:border-indigo-500">
                        @error('name')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label for="position" class="block text-sm font-medium text-gray-300">Cargo</label>
                        <input type="text" wire:model="position" id="position"
                            class="mt-1 block w-full bg-gray-800 border-gray-600 rounded-md shadow-sm text-white focus:ring-indigo-500 focus:border-indigo-500">
                        @error('position')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-300">Descripción /
                            Cita</label>
                        <textarea wire:model="description" id="description" rows="3"
                            class="mt-1 block w-full bg-gray-800 border-gray-600 rounded-md shadow-sm text-white focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        @error('description')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label for="photo" class="block text-sm font-medium text-gray-300">Foto</label>
                        <input type="file" wire:model="photo" id="photo"
                            class="mt-1 block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                        @error('photo')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                        <div wire:loading wire:target="photo" class="text-indigo-400 text-sm mt-2">Cargando...</div>
                        <div class="mt-4 flex items-center space-x-4">
                            @if ($photo)
                                <img src="{{ $photo->temporaryUrl() }}" class="h-20 w-20 rounded-full object-cover">
                                <span class="text-gray-400">Previsualización nueva</span>
                            @elseif ($existingPhoto)
                                <img src="{{ Illuminate\Support\Facades\Storage::disk('empleados')->url($existingPhoto) }}"
                                    class="h-20 w-20 rounded-full object-cover">
                                <span class="text-gray-400">Foto actual</span>
                            @endif
                        </div>
                    </div>
                </div>
            </x-slot>
            <x-slot name="footer">
                <button wire:click="closeModal"
                    class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg">
                    Cancelar
                </button>
                <button wire:click.prevent="store"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg ml-4">
                    Guardar Cambios
                </button>
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
                        title: '¿Estás seguro?',
                        text: "¡La información del empleado se eliminará permanentemente!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#e11d48',
                        cancelButtonColor: '#3b82f6',
                        confirmButtonText: 'Sí, ¡eliminar!',
                        cancelButtonText: 'Cancelar',
                        background: '#1f2937',
                        color: '#ffffff'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            @this.call('delete', employeeId);
                        }
                    })
                });
            });
        </script>
    @endpush
</div>
