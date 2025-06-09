<div class="py-8 md:py-12 bg-black text-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Flash Messages --}}
        @if (session()->has('message'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3500)"
                x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="mb-6 p-4 text-sm text-green-700 bg-green-100 rounded-lg border border-green-300 shadow-sm"
                role="alert">
                {{ session('message') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="mb-6 p-4 text-sm text-red-700 bg-red-100 rounded-lg border border-red-300 shadow-sm"
                role="alert">
                {{ session('error') }}
            </div>
        @endif

        {{-- Section Header --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 md:mb-10">
            <div class="mb-4 sm:mb-0">
                @if ($this->videoSection && $this->videoSection->title)
                    <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight uppercase bg-black px-2 inline-block"
                        style="font-family: 'Arial Black', sans-serif;">
                        {{ $this->videoSection->title }}
                    </h2>
                @endif

                @if ($this->videoSection && $this->videoSection->description)
                    <p class="mt-2 text-base sm:text-lg text-gray-400 max-w-2xl bg-black px-2 inline-block">
                        {{ $this->videoSection->description }}
                    </p>
                @endif
            </div>

            @if ($isAdmin)
                <button wire:click="openManageSectionModal"
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold rounded-md shadow-md transition-colors">
                    Administrar Sección
                </button>
            @endif
        </div>

        {{-- Video Grid --}}
        @if ($videos->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 md:gap-8" wire:sortable="updateVideoOrder">
                @foreach ($videos as $video)
                    <div wire:sortable.item="{{ $video->id }}" wire:key="video-entry-{{ $video->id }}"
                        class="bg-gray-800 rounded-lg shadow-xl overflow-hidden group flex flex-col">
                        <div class="aspect-video relative">
                             @if ($video->video_source_type === 'vimeo' && $video->source_identifier)
                                <iframe
                                    src="https://player.vimeo.com/video/{{ $video->source_identifier }}?title=0&byline=0&portrait=0&autoplay=0&muted=1"
                                    class="w-full h-full" frameborder="0"
                                    allow="autoplay; fullscreen; picture-in-picture" allowfullscreen>
                                </iframe>
                            @elseif($video->video_source_type === 's3' && $video->source_identifier)
                                 @php
                                    $s3Disk = Storage::disk($this->s3Disk);
                                    $fileExists = $s3Disk->exists($video->source_identifier);
                                @endphp
                                @if ($fileExists)
                                    <video controls preload="metadata" class="w-full h-full object-cover">
            <source src="{{ $s3Disk->temporaryUrl($video->source_identifier, now()->addMinutes(5)) }}" type="video/mp4">
                                        Tu navegador no soporta la etiqueta de video.
                                    </video>
                                @else
                                    <div
                                        class="w-full h-full bg-gray-700 flex items-center justify-center text-gray-400 italic">
                                        Video no encontrado en S3
                                    </div>
                                @endif
                            @elseif($video->video_source_type === 'youtube' && $video->source_identifier)
                                <iframe class="w-full h-full"
                                    src="https://www.youtube.com/embed/{{ $video->source_identifier }}"
                                    title="YouTube video player" frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                    allowfullscreen>
                                </iframe>
                            @else
                                <div
                                    class="w-full h-full bg-gray-700 flex items-center justify-center text-gray-400 italic">
                                    Fuente de video no válida.
                                </div>
                            @endif
                        </div>

                        <div class="p-4 md:p-5 flex-grow flex flex-col justify-between bg-black">
                            <div>
                                <h3 class="text-lg md:text-xl font-semibold text-gray-100 mb-1.5 truncate bg-black px-1"
                                    title="{{ $video->entry_title }}">
                                    {{ $video->entry_title }}
                                </h3>
                                @if ($video->entry_description)
                                    <p class="text-sm text-gray-400 line-clamp-2 bg-black px-1"
                                        title="{{ $video->entry_description }}">
                                        {{ $video->entry_description }}
                                    </p>
                                @endif
                            </div>

                            @if ($isAdmin)
                                <div
                                    class="mt-4 pt-3 border-t border-gray-700/50 flex items-center justify-end space-x-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                    <button wire:sortable.handle title="Reordenar"
                                        class="p-1.5 text-gray-400 hover:text-white cursor-grab">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" /></svg>
                                    </button>
                                    <button wire:click="openAddEditVideoModal({{ $video->id }})" title="Editar Video" class="p-1.5 text-blue-400 hover:text-blue-300">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21H3v-3.5L14.732 3.732z" /></svg>
                                    </button>
                                    <button wire:click="deleteVideoEntry({{ $video->id }})" wire:confirm="¿Estás seguro de eliminar este video?" title="Eliminar Video" class="p-1.5 text-red-400 hover:text-red-300">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z" /></svg>
                <h3 class="mt-2 text-lg font-semibold text-white">No hay videos en esta sección</h3>
                @if ($isAdmin)
                    <p class="mt-1 text-sm text-gray-400">Comienza añadiendo videos usando el botón "Administrar Sección".</p>
                @else
                    <p class="mt-1 text-sm text-gray-400">Vuelve más tarde para ver el contenido.</p>
                @endif
            </div>
        @endif

        {{-- Management Modal --}}
        @if ($isAdmin)
            <x-dialog-modal wire:model.live="showManageSectionModal" maxWidth="3xl">
                <x-slot name="title">
                    Administrar Sección de Videos: <span class="font-normal italic">{{ $identifier }}</span>
                </x-slot>

                <x-slot name="content">
                    <div class="space-y-6 text-gray-200">
                        {{-- Edit Section Title and Description --}}
                        <div class="p-4 border border-gray-700 rounded-md bg-gray-800/50 shadow-sm">
                            <h4 class="text-lg font-semibold text-gray-100 mb-3">Detalles de la Sección</h4>
                            <div class="space-y-4">
                                <div>
                                    <label for="sectionTitleModal"
                                        class="block text-sm font-medium text-gray-300 mb-1">Título de la
                                        Sección:</label>
                                    <input type="text" wire:model="sectionTitle" id="sectionTitleModal"
                                        class="w-full p-2.5 rounded bg-gray-700 text-white border border-gray-600 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                    @error('sectionTitle')
                                        <span class="text-red-400 text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label for="sectionDescriptionModal"
                                        class="block text-sm font-medium text-gray-300 mb-1">Descripción de la Sección
                                        (opcional):</label>
                                    <textarea wire:model="sectionDescription" id="sectionDescriptionModal" rows="3"
                                        class="w-full p-2.5 rounded bg-gray-700 text-white border border-gray-600 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                                    @error('sectionDescription')
                                        <span class="text-red-400 text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="text-right">
                                    <x-secondary-button wire:click="saveSectionDetails" wire:loading.attr="disabled"
                                        class="text-xs px-3 py-1.5">
                                        Guardar Detalles
                                    </x-secondary-button>
                                </div>
                            </div>
                        </div>

                        {{-- Add New Video Button --}}
                        <div class="text-right">
                            <x-button wire:click="openAddEditVideoModal()" class="text-sm">
                                Añadir Nuevo Video
                            </x-button>
                        </div>

                        {{-- List of existing videos (simplified for management modal) --}}
                        @if ($videos->isNotEmpty())
                            <div class="mt-4 max-h-72 overflow-y-auto space-y-2">
                                <h4 class="text-md font-semibold text-gray-300 mb-2">Videos Actuales:</h4>
                                @foreach ($videos as $video)
                                    <div
                                        class="flex items-center justify-between p-2.5 bg-gray-700/60 rounded-md hover:bg-gray-700/80 transition-colors">
                                        <div class="flex items-center space-x-3 min-w-0">
                                            <span
                                                class="text-gray-400 text-xs w-6 text-center">{{ $loop->iteration }}.</span>
                                            <span class="text-sm font-medium text-gray-100 truncate"
                                                title="{{ $video->entry_title }}">{{ Illuminate\Support\Str::limit($video->entry_title, 40) }}</span>
                                            <span
                                                class="text-xs text-gray-500">({{ Illuminate\Support\Str::upper($video->video_source_type) }})</span>
                                        </div>
                                        <div class="flex space-x-2 flex-shrink-0">
                                            <button wire:click="openAddEditVideoModal({{ $video->id }})"
                                                title="Editar" class="p-1 text-blue-400 hover:text-blue-300">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21H3v-3.5L14.732 3.732z">
                                                    </path>
                                                </svg>
                                            </button>
                                            <button wire:click="deleteVideoEntry({{ $video->id }})"
                                                wire:confirm="¿Seguro que quieres eliminar este video de la sección?"
                                                title="Eliminar" class="p-1 text-red-400 hover:text-red-300">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500 text-center py-3">No hay videos en esta sección aún.</p>
                        @endif
                    </div>
                </x-slot>

                <x-slot name="footer">
                    <x-secondary-button wire:click="$set('showManageSectionModal', false)"
                        wire:loading.attr="disabled">
                        Cerrar
                    </x-secondary-button>
                </x-slot>
            </x-dialog-modal>
        @endif

        {{-- Add/Edit Video Entry Modal --}}
        @if ($isAdmin)
            <x-dialog-modal wire:model.live="showAddEditVideoModal" maxWidth="xl">
                <x-slot name="title">
                    {{ $editingVideoEntryId ? 'Editar Video' : 'Añadir Nuevo Video' }}
                </x-slot>

                <x-slot name="content">
                    <div class="space-y-5 text-gray-200">
                        {{-- Título y Descripción --}}
                        <div>
                            <label for="entry_title_modal" class="block text-sm font-medium text-gray-300 mb-1">Título del Video:</label>
                            <input type="text" wire:model="entry_title" id="entry_title_modal" placeholder="Ej: Boda de Ana y Juan" class="w-full p-2.5 rounded bg-gray-700 text-white border border-gray-600 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                            @error('entry_title') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="entry_description_modal" class="block text-sm font-medium text-gray-300 mb-1">Descripción Breve (opcional):</label>
                            <textarea wire:model="entry_description" id="entry_description_modal" rows="3" placeholder="Un resumen emotivo del gran día" class="w-full p-2.5 rounded bg-gray-700 text-white border border-gray-600 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                            @error('entry_description') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- Selector de Fuente --}}
                        <div>
                            <label for="video_source_type_modal" class="block text-sm font-medium text-gray-300 mb-1">Fuente del Video:</label>
                            <select wire:model.live="video_source_type" id="video_source_type_modal" class="w-full p-2.5 rounded bg-gray-700 text-white border border-gray-600 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="vimeo">Vimeo ID</option>
                                <option value="youtube">YouTube ID</option>
                                <option value="s3">Subir Archivo a S3</option>
                            </select>
                        </div>

                        {{-- Lógica de Subida de Archivos --}}
                        @if ($video_source_type === 's3')
<div>
    <label for="file_input_for_video" class="block text-sm font-medium text-gray-300 mb-1">
        {{ $editingVideoEntryId && $currentVideoPath ? 'Reemplazar Archivo (opcional):' : 'Archivo de Video (MP4, MOV):' }}
    </label>

    <div x-data="{
            isUploading: false,
            isUploadingSmallFile: false,
            progress: 0,
            handleFileSelect(event) {
                const file = event.target.files[0];
                if (!file) return;

                const isLargeFile = file.size > 100 * 1024 * 1024; // 100 MB

                if (isLargeFile) {
                    // Archivo grande: solo enviamos el NOMBRE al backend
                    this.isUploading = true;
                    this.progress = 0;
                    $wire.call('getPresignedUploadUrl', file.name);
                } else {
                    // Archivo pequeño: usamos el uploader de Livewire
                    this.isUploadingSmallFile = true;
                    @this.upload('newVideoFile', file, (uploadedFilename) => {
                        // Éxito: la subida pequeña terminó
                        this.isUploadingSmallFile = false;
                    }, () => {
                        // Error
                        this.isUploadingSmallFile = false;
                        alert('Error al subir el archivo pequeño.');
                    }, (event) => {
                        // Progreso
                        this.progress = event.detail.progress;
                    })
                }
            }
        }"
         @presigned-url-generated.window="
            const fileInput = document.getElementById('file_input_for_video');
            if (!fileInput.files || fileInput.files.length === 0) return;
            const file = fileInput.files[0];
            const url = $event.detail.url;

            axios.put(url, file, {
                headers: { 'Content-Type': file.type || 'application/octet-stream' },
                onUploadProgress: (progressEvent) => {
                    progress = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                }
            }).then(() => {
                progress = 100;
                $wire.call('handleDirectUploadFinished');
            }).catch((error) => {
                console.error('Error en la subida a S3:', error);
                isUploading = false;
                alert('Error: No se pudo subir el archivo. Revisa la consola.');
            });
         "
    >
        <input type="file" id="file_input_for_video"
               @change="handleFileSelect($event)"
               :disabled="isUploading || isUploadingSmallFile"
               class="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-500 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">

        <div x-show="isUploading" class="mt-2 space-y-1">
            <p class="text-indigo-400 text-xs font-semibold">Subiendo archivo grande a S3...</p>
            <div class="w-full bg-gray-600 rounded-full h-2.5"><div class="bg-indigo-500 h-2.5 rounded-full" :style="`width: ${progress}%`"></div></div>
            <p x-text="`Completado: ${progress}%`" class="text-xs text-gray-300 text-right"></p>
        </div>

        <div x-show="isUploadingSmallFile" class="mt-2 space-y-1">
            <p class="text-indigo-400 text-xs font-semibold">Subiendo archivo pequeño...</p>
             <div class="w-full bg-gray-600 rounded-full h-2.5"><div class="bg-indigo-500 h-2.5 rounded-full" :style="`width: ${progress}%`"></div></div>
            <p x-text="`Completado: ${progress}%`" class="text-xs text-gray-300 text-right"></p>
        </div>
    </div>
    @error('newVideoFile') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
</div>
@endif
                    </div>
                </x-slot>

                <x-slot name="footer">
                    <x-secondary-button wire:click="resetVideoForm(); $set('showAddEditVideoModal', false)" wire:loading.attr="disabled">
                        Cancelar
                    </x-secondary-button>

                    <x-button class="ml-3" wire:click="saveVideoEntry" wire:loading.attr="disabled" wire:target="saveVideoEntry, newVideoFile"
                        x-data x-bind:disabled="$wire.isUploadingLargeFile">
                        <span wire:loading.remove wire:target="saveVideoEntry, handleDirectUploadFinished">
                             {{ $editingVideoEntryId ? 'Actualizar Video' : 'Guardar Video' }}
                        </span>
                        <span wire:loading wire:target="saveVideoEntry, handleDirectUploadFinished">Guardando...</span>
                    </x-button>
                </x-slot>
            </x-dialog-modal>
        @endif
    </div>

    @push('scripts')
        {{-- CDN de Axios. Si ya lo usas en tu proyecto, puedes quitar esta línea. --}}
        <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

        {{-- Tu código para SortableJS. Asegúrate de que la librería SortableJS esté cargada. --}}
        <script>
            document.addEventListener('livewire:init', () => {
                const initSortableVideos = (el) => {
                    if (!el || el.sortableVideoInstance) return;

                    el.sortableVideoInstance = Sortable.create(el, {
                        handle: '[wire\\:sortable\\.handle]',
                        animation: 150,
                        ghostClass: 'bg-gray-700 opacity-60',
                        onEnd: function(evt) {
                            let component = Livewire.find(el.closest('[wire\\:id]').getAttribute('wire:id'));
                            if (component) {
                                let orderedIds = Array.from(evt.to.children).map((item, index) => ({
                                    order: index,
                                    value: item.getAttribute('wire:sortable.item')
                                }));
                                component.call('updateVideoOrder', orderedIds);
                            }
                        }
                    });
                };

                const sortableContainer = document.querySelector('[wire\\:sortable="updateVideoOrder"]');
                if (sortableContainer) {
                    initSortableVideos(sortableContainer);
                }

                Livewire.hook('element.updated', (el, component) => {
                    if (el.hasAttribute('wire:sortable') && el.getAttribute('wire:sortable') === 'updateVideoOrder') {
                        initSortableVideos(el);
                    }
                });
            });
        </script>
    @endpush
</div>
