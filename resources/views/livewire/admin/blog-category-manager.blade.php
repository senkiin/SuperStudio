<div class="bg-black min-h-screen">



    <div class="py-12">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <header class="mt-12 mb-8">
                <div class="flex justify-between items-center">
                    <h2 class="font-semibold text-2xl text-gray-200 leading-tight">
                        Gestión de Categorías
                    </h2>
                    <x-button wire:click="openModalToCreate">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Crear Categoría
                    </x-button>
                </div>
                <p class="mt-1 text-sm text-gray-500">
                    Añade, edita o elimina las categorías para los posts de tu blog.
                </p>
            </header>
            {{-- Alertas de Sesión --}}
            @if (session()->has('message'))
                <div class="bg-green-500/10 border border-green-500/20 text-green-300 px-4 py-3 rounded-lg relative mb-6"
                    role="alert">
                    <strong class="font-bold">¡Éxito!</strong>
                    <span class="block sm:inline ml-2">{{ session('message') }}</span>
                </div>
            @endif
            @if (session()->has('error'))
                <div class="bg-red-500/10 border border-red-500/20 text-red-300 px-4 py-3 rounded-lg relative mb-6"
                    role="alert">
                    <strong class="font-bold">¡Error!</strong>
                    <span class="block sm:inline ml-2">{{ session('error') }}</span>
                </div>
            @endif

            <div class="flex justify-start mb-6">
                <a href="{{ route('admin.blog.manager') }}"
                    class="inline-flex items-center text-sm text-gray-400 hover:text-indigo-400 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Volver a los Posts
                </a>
            </div>

            {{-- Contenedor de la Tabla --}}
            <div class="bg-gray-900/50 backdrop-blur-sm shadow-2xl rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-800">
                        <thead class="bg-gray-800/50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                    Nombre</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                    Slug</th>
                                <th scope="col"
                                    class="px-6 py-3 text-center text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                    Nº Posts</th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                    Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-900/80 divide-y divide-gray-800">
                            @forelse($categories as $category)
                                <tr class="hover:bg-gray-800/60 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-100">
                                        {{ $category->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400 font-mono">
                                        {{ $category->slug }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                        <a href="{{ route('blog.index', ['category' => $category->id]) }}"
                                            target="_blank"
                                            class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-indigo-500/20 text-indigo-300 hover:bg-indigo-500/40 transition">
                                            {{ $category->posts_count }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right space-x-4">
                                        <button wire:click="openModalToEdit({{ $category->id }})"
                                            class="text-blue-400 hover:text-blue-300 transition-colors">Editar</button>
                                        <button wire:click="deleteCategory({{ $category->id }})"
                                            wire:confirm="¿Estás seguro?\n\nNo podrás deshacer esta acción."
                                            class="text-red-500 hover:text-red-400 transition-colors">Eliminar</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-16 text-center text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-600 mb-2" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 7h.01M7 3h5a2 2 0 012 2v5a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2zm0 0v11a2 2 0 002 2h5">
                                                </path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M14 3h5a2 2 0 012 2v5a2 2 0 01-2 2h-5a2 2 0 01-2-2V5a2 2 0 012-2zM14 11h5a2 2 0 012 2v5a2 2 0 01-2 2h-5a2 2 0 01-2-2v-5a2 2 0 012-2z">
                                                </path>
                                            </svg>
                                            <p>No se encontraron categorías.</p>
                                            <p class="text-xs mt-1">Crea la primera usando el botón de la esquina
                                                superior derecha.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Paginación (si es necesaria) --}}
            <div class="mt-8">
                {{ $categories->links() }}
            </div>
        </div>
    </div>

    {{-- Modal para Crear/Editar Categoría --}}
    <x-dialog-modal wire:model.live="showModal">
        <x-slot name="title">
            <span class="text-gray-200">{{ $editingCategory ? 'Editar Categoría' : 'Crear Nueva Categoría' }}</span>
        </x-slot>

        <x-slot name="content">
            <div class="space-y-4">
                {{-- Nombre --}}
                <div>
                    <x-label for="name" value="Nombre" class="text-gray-400" />
                    <x-input id="name" type="text"
                        class="mt-1 block w-full bg-gray-800 border-gray-600 text-gray-200 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        wire:model.live.debounce.500ms="name" />
                    <x-input-error for="name" class="mt-2" />
                </div>

                {{-- Slug --}}
                <div>
                    <x-label for="slug" value="Slug (URL Amigable)" class="text-gray-400" />
                    <x-input id="slug" type="text"
                        class="mt-1 block w-full bg-gray-700 border-gray-600 text-gray-400 rounded-md shadow-sm cursor-not-allowed"
                        wire:model="slug" readonly />
                    <x-input-error for="slug" class="mt-2" />
                </div>

                {{-- Descripción --}}
                <div>
                    <x-label for="description" value="Descripción (Opcional)" class="text-gray-400" />
                    <textarea id="description" wire:model.defer="description" rows="3"
                        class="mt-1 block w-full bg-gray-800 border-gray-600 text-gray-200 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    <x-input-error for="description" class="mt-2" />
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="closeModal">Cancelar</x-secondary-button>
            <x-button class="ml-3" wire:click="saveCategory" wire:loading.attr="disabled">Guardar</x-button>
        </x-slot>
    </x-dialog-modal>
</div>
