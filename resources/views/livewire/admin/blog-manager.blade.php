<div class="bg-black min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-12">

        {{-- INICIO: Cabecera integrada en el componente --}}
        <header class="mb-8 mt-12">
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-2xl text-gray-200 leading-tight">
                    Gestión de Posts del Blog
                </h2>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.blog.categories.manager') }}" class="text-sm text-indigo-400 hover:text-indigo-300 underline transition-colors duration-200">
                        Gestionar Categorías
                    </a>
                    <x-button wire:click="openModalToCreate">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        Crear Post
                    </x-button>
                </div>
            </div>
            <p class="mt-1 text-sm text-gray-500">
                Crea, visualiza, edita y elimina las entradas de tu blog.
            </p>
        </header>
        {{-- FIN: Cabecera --}}


        {{-- Alertas de Sesión --}}
        @if (session()->has('message'))
            <div class="bg-green-500/10 border border-green-500/20 text-green-300 px-4 py-3 rounded-lg relative mb-6" role="alert">
                <strong class="font-bold">¡Éxito!</strong>
                <span class="block sm:inline ml-2">{{ session('message') }}</span>
            </div>
        @endif

        {{-- Contenedor de la Tabla --}}
        <div class="bg-gray-900/50 backdrop-blur-sm shadow-2xl rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-800">
                    <thead class="bg-gray-800/50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Título</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Categoría</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-400 uppercase tracking-wider">Estado</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Publicado</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-gray-900/80 divide-y divide-gray-800">
                        @forelse($posts as $post)
                            <tr class="hover:bg-gray-800/60 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded-md object-cover" src="{{ $post->first_image_url }}" alt="Imagen de {{ $post->title }}">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-100">{{ $post->title }}</div>
                                            <div class="text-sm text-gray-500">por {{ $post->author->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">{{ $post->category->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span @class([
                                        'px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full',
                                        'bg-green-500/20 text-green-300' => $post->status == 'published',
                                        'bg-yellow-500/20 text-yellow-300' => $post->status == 'draft',
                                    ])>
                                        {{ ucfirst($post->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                    {{ $post->published_at ? $post->published_at->format('d/m/Y') : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right space-x-4">
                                    <a href="{{ route('blog.show', $post->slug) }}" target="_blank" class="text-gray-400 hover:text-white transition-colors" title="Ver Post">Ver</a>
                                    <button wire:click="openModalToEdit({{ $post->id }})" class="text-blue-400 hover:text-blue-300 transition-colors">Editar</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        <p>Aún no has creado ningún post.</p>
                                        <p class="text-xs mt-1">Haz clic en "Crear Post" para empezar.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            <div class="mt-8">
                {{ $posts->links() }}
            </div>
        </div>
    </div>

    {{-- Modal para Crear/Editar Post (sin cambios en la estructura, solo en el contenido) --}}
    <x-dialog-modal wire:model.live="showModal">
        <x-slot name="title">
            <span class="text-gray-200">{{ $editingPost ? 'Editar Post' : 'Crear Nuevo Post' }}</span>
        </x-slot>

        <x-slot name="content">
             <div class="space-y-6">
                {{-- Título --}}
                <div>
                    <x-label for="title" value="Título" class="text-gray-400" />
                    <x-input id="title" type="text" class="mt-1 block w-full bg-gray-800 border-gray-600 text-gray-200 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" wire:model.live.debounce.500ms="title" />
                    <x-input-error for="title" class="mt-2" />
                </div>

                {{-- Slug --}}
                <div>
                    <x-label for="slug" value="Slug (URL Amigable)" class="text-gray-400" />
                    <x-input id="slug" type="text" class="mt-1 block w-full bg-gray-700 border-gray-600 text-gray-400 rounded-md shadow-sm cursor-not-allowed" wire:model="slug" readonly />
                    <x-input-error for="slug" class="mt-2" />
                </div>

                {{-- Contenido (Trix Editor) --}}
                <div wire:ignore>
                    <x-label for="content" value="Contenido" class="text-gray-400" />
                    <input id="content" type="hidden" name="content" value="{{ $content }}">
                    <trix-editor input="content" wire:model.lazy="content" class="mt-1 trix-content-dark"></trix-editor>
                </div>
                <x-input-error for="content" class="mt-2" />

                {{-- Categoría y Estado (en la misma fila) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-label for="blog_category_id" value="Categoría" class="text-gray-400" />
                        <select id="blog_category_id" wire:model="blog_category_id" class="mt-1 block w-full bg-gray-800 border-gray-600 text-gray-200 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">-- Selecciona una categoría --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error for="blog_category_id" class="mt-2" />
                    </div>
                    <div>
                        <x-label for="status" value="Estado" class="text-gray-400" />
                        <select id="status" wire:model="status" class="mt-1 block w-full bg-gray-800 border-gray-600 text-gray-200 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="draft">Borrador</option>
                            <option value="published">Publicado</option>
                        </select>
                        <x-input-error for="status" class="mt-2" />
                    </div>
                </div>

                {{-- Galería de imágenes existentes --}}
                @if (count($existing_images) > 0)
                    <div class="mt-4">
                        <x-label value="Imágenes Actuales" class="text-gray-400 mb-2"/>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                            @foreach ($existing_images as $image)
                                <div class="relative group">
                                    <img src="{{ Storage::disk('blog-media')->url($image->image_path) }}" class="h-24 w-full object-cover rounded-md shadow">
                                    <button wire:click="deleteImage({{ $image->id }})" wire:confirm="¿Estás seguro de que quieres eliminar esta imagen?"
                                            class="absolute top-1 right-1 bg-red-600 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity focus:outline-none focus:ring-2 focus:ring-red-500">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Campo para subir imágenes --}}
                <div>
                    <x-label for="new_images" value="Añadir Nuevas Imágenes" class="text-gray-400" />
                    <input id="new_images" type="file" class="mt-1 block w-full text-sm text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-600/20 file:text-indigo-300 hover:file:bg-indigo-600/30" wire:model="new_images" multiple />
                    <div wire:loading wire:target="new_images" class="mt-2 text-sm text-gray-500">Cargando imágenes...</div>
                    <x-input-error for="new_images.*" class="mt-2" />
                </div>

                {{-- Previsualización de imágenes nuevas --}}
                @if ($new_images)
                    <div class="mt-4">
                        <x-label value="Previsualización" class="text-gray-400 mb-2"/>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                            @foreach ($new_images as $image)
                                <img src="{{ $image->temporaryUrl() }}" class="h-24 w-full object-cover rounded-md shadow">
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Campo de Video --}}
                <div>
                    <x-label for="video_url" value="ID de Vimeo o URL de YouTube (Opcional)" class="text-gray-400" />
                    <div class="flex items-center space-x-2 mt-1">
                        <x-input id="video_url" type="text" class="block w-full bg-gray-800 border-gray-600 text-gray-200 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" wire:model.defer="video_url" placeholder="Ej: 941076249 o URL de YouTube" />
                        <x-secondary-button wire:click="$set('showVideoGalleryModal', true)">Galería</x-secondary-button>
                    </div>
                    <x-input-error for="video_url" class="mt-2" />
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="closeModal">Cancelar</x-secondary-button>
            <x-button class="ml-3" wire:click="savePost" wire:loading.attr="disabled">Guardar Post</x-button>
        </x-slot>
    </x-dialog-modal>

    {{-- Modal para Galería de Vídeos --}}
    <x-dialog-modal wire:model.live="showVideoGalleryModal">
        <x-slot name="title">
            <span class="text-gray-200">Seleccionar Vídeo de la Galería</span>
        </x-slot>
        <x-slot name="content">
            @livewire('admin.video-selector')
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showVideoGalleryModal', false)">
                Cerrar
            </x-secondary-button>
        </x-slot>
    </x-dialog-modal>

</div>

@push('styles')
<style>
/* Estilos para el editor Trix en modo oscuro */
.trix-content-dark.trix-container {
    background-color: #1f2937; /* gray-800 */
    border-color: #4b5563; /* gray-600 */
}
.trix-content-dark .trix-content {
    color: #d1d5db; /* gray-300 */
}
.trix-content-dark .trix-toolbar {
    background-color: #374151; /* gray-700 */
}
.trix-content-dark .trix-toolbar .trix-button-group {
    border-color: #4b5563; /* gray-600 */
}
.trix-content-dark .trix-toolbar .trix-button {
    color: #d1d5db; /* gray-300 */
}
.trix-content-dark .trix-toolbar .trix-button:not(:disabled):hover {
    background-color: #4b5563; /* gray-600 */
    color: #fff;
}
.trix-content-dark .trix-toolbar .trix-button.trix-active {
    background-color: #111827; /* gray-900 */
    color: #fff;
}
</style>
@endpush
