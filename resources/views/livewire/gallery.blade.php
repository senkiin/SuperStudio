<div class="min-h-screen bg-black text-white">
    <!-- Header -->
    <div class="relative z-10 bg-black/60 backdrop-blur-sm border-b border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-6 sm:pt-24 sm:pb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-light text-white tracking-tight">Galería</h1>
                    <p class="mt-2 sm:mt-3 text-gray-400 font-light text-sm sm:text-base">Colecciones de momentos únicos</p>
                </div>

                @if($showAdminPanel)
                    <div class="mt-4 sm:mt-0">
                        <button wire:click="openAddAlbumModal"
                                class="bg-white hover:bg-gray-100 text-black px-4 py-2 sm:px-5 sm:py-2.5 rounded-full font-medium transition-all duration-300 hover:scale-105 text-sm sm:text-base">
                            <i class="fas fa-plus mr-1 sm:mr-2"></i><span class="hidden sm:inline">Añadir Álbum</span><span class="sm:hidden">Añadir</span>
                        </button>
                    </div>
                @endif
            </div>

            <!-- Search Bar -->
            <div class="mt-6 sm:mt-8">
                <div class="relative max-w-sm sm:max-w-md">
                    <input type="text"
                           wire:model.live.debounce.300ms="search"
                           placeholder="Buscar álbumes..."
                           class="w-full pl-10 sm:pl-12 pr-4 py-3 sm:py-4 bg-gray-900/50 border border-gray-700 rounded-full text-white placeholder-gray-400 focus:ring-2 focus:ring-white/20 focus:border-transparent backdrop-blur-sm text-sm sm:text-base">
                    <div class="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400 text-sm sm:text-base"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Albums Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
        @if($albums->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6 lg:gap-8">
                @foreach($albums as $album)
                    <div class="bg-gray-900/50 backdrop-blur-sm rounded-xl sm:rounded-2xl border border-gray-800 hover:border-gray-700 transition-all duration-300 overflow-hidden group hover:scale-105">
                        <!-- Album Cover -->
                        <div class="relative aspect-square overflow-hidden">
                            @if($album->cover_image)
                                @php
                                    $config = config('filesystems.disks.s3');
                                    $coverUrl = "https://{$config['bucket']}.s3.{$config['region']}.amazonaws.com/{$album->cover_image}";
                                @endphp
                                <img src="{{ $coverUrl }}"
                                     alt="{{ $album->name }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @elseif($album->photos->count() > 0)
                                @php
                                    $config = config('filesystems.disks.s3');
                                    $firstPhotoUrl = "https://{$config['bucket']}.s3.{$config['region']}.amazonaws.com/{$album->photos->first()->file_path}";
                                @endphp
                                <img src="{{ $firstPhotoUrl }}"
                                     alt="{{ $album->name }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-full bg-gray-800 flex items-center justify-center">
                                    <i class="fas fa-images text-gray-500 text-4xl"></i>
                                </div>
                            @endif

                            <!-- Password Lock Icon -->
                            @if($album->password)
                                <div class="absolute top-2 right-2 sm:top-3 sm:right-3">
                                    <div class="bg-black/70 backdrop-blur-sm text-white p-1.5 sm:p-2 rounded-full border border-gray-600">
                                        <i class="fas fa-lock text-xs sm:text-sm"></i>
                                    </div>
                                </div>
                            @endif

                            <!-- Photo Count -->
                            <div class="absolute bottom-2 left-2 sm:bottom-3 sm:left-3">
                                <div class="bg-black/70 backdrop-blur-sm text-white px-2 py-1 sm:px-3 sm:py-2 rounded-full text-xs sm:text-sm border border-gray-600">
                                    <i class="fas fa-camera mr-1"></i>{{ $album->photos_count }}
                                </div>
                            </div>

                            <!-- Admin Controls -->
                            @if($showAdminPanel)
                                <div class="absolute top-2 left-2 sm:top-3 sm:left-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <div class="flex space-x-1 sm:space-x-2">
                                        <button wire:click="openEditAlbumModal({{ $album->id }})"
                                                class="bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white p-1.5 sm:p-2 rounded-full text-xs sm:text-sm border border-white/20">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button wire:click="removeAlbumFromGallery({{ $album->id }})"
                                                wire:confirm="¿Estás seguro de remover este álbum de la galería pública?"
                                                class="bg-red-500/80 hover:bg-red-500 backdrop-blur-sm text-white p-1.5 sm:p-2 rounded-full text-xs sm:text-sm border border-red-400/50">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Album Info -->
                        <div class="p-4 sm:p-6">
                            <h3 class="font-light text-white text-lg sm:text-xl mb-1 sm:mb-2 line-clamp-1">{{ $album->name }}</h3>
                            @if($album->description)
                                <p class="text-gray-400 text-xs sm:text-sm mb-3 sm:mb-4 line-clamp-2">{{ $album->description }}</p>
                            @endif

                            <button wire:click="openAlbum({{ $album->id }})"
                                    class="w-full bg-white hover:bg-gray-100 text-black py-2 sm:py-3 px-3 sm:px-4 rounded-full font-medium transition-all duration-300 hover:scale-105 text-sm sm:text-base">
                                <i class="fas fa-eye mr-1 sm:mr-2"></i>Ver Álbum
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-12">
                {{ $albums->links() }}
            </div>
        @else
            <div class="text-center py-20">
                <i class="fas fa-images text-gray-600 text-8xl mb-6"></i>
                <h3 class="text-2xl font-light text-white mb-3">No hay álbumes disponibles</h3>
                <p class="text-gray-400">No se encontraron álbumes en la galería pública.</p>
            </div>
        @endif
    </div>

    <!-- Add Album Modal -->
    @if($showAddAlbumModal)
        <div class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-gray-900 border border-gray-700 rounded-xl sm:rounded-2xl max-w-sm sm:max-w-md w-full p-6 sm:p-8">
                <div class="flex justify-between items-center mb-4 sm:mb-6">
                    <h3 class="text-lg sm:text-xl font-light text-white">Añadir Álbum</h3>
                    <button wire:click="closeAddAlbumModal" class="text-gray-400 hover:text-white transition-colors">
                        <i class="fas fa-times text-base sm:text-lg"></i>
                    </button>
                </div>

                <form wire:submit.prevent="addAlbumToGallery">
                    <div class="mb-4 sm:mb-6">
                        <label class="block text-xs sm:text-sm font-medium text-gray-300 mb-2 sm:mb-3">Seleccionar Álbum</label>
                        <select wire:model="newAlbumId" class="w-full bg-gray-800 border border-gray-600 rounded-lg sm:rounded-xl px-3 sm:px-4 py-2 sm:py-3 text-white focus:ring-2 focus:ring-white/20 focus:border-transparent text-sm sm:text-base">
                            <option value="">Selecciona un álbum</option>
                            @foreach($this->availableAlbums as $album)
                                <option value="{{ $album->id }}">{{ $album->name }}</option>
                            @endforeach
                        </select>
                        @error('newAlbumId') <span class="text-red-400 text-xs sm:text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-6 sm:mb-8">
                        <label class="block text-xs sm:text-sm font-medium text-gray-300 mb-2 sm:mb-3">Contraseña (opcional)</label>
                        <input type="password"
                               wire:model="newAlbumPassword"
                               placeholder="Dejar vacío para acceso público"
                               class="w-full bg-gray-800 border border-gray-600 rounded-lg sm:rounded-xl px-3 sm:px-4 py-2 sm:py-3 text-white placeholder-gray-400 focus:ring-2 focus:ring-white/20 focus:border-transparent text-sm sm:text-base">
                        @error('newAlbumPassword') <span class="text-red-400 text-xs sm:text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex space-x-3 sm:space-x-4">
                        <button type="button"
                                wire:click="closeAddAlbumModal"
                                class="flex-1 bg-gray-700 hover:bg-gray-600 text-white py-2 sm:py-3 px-3 sm:px-4 rounded-full font-medium transition-all duration-300 text-sm sm:text-base">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="flex-1 bg-white hover:bg-gray-100 text-black py-2 sm:py-3 px-3 sm:px-4 rounded-full font-medium transition-all duration-300 hover:scale-105 text-sm sm:text-base">
                            Añadir
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Edit Album Modal -->
    @if($showEditAlbumModal && $editingAlbum)
        <div class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-gray-900 border border-gray-700 rounded-xl sm:rounded-2xl max-w-sm sm:max-w-md w-full p-6 sm:p-8">
                <div class="flex justify-between items-center mb-4 sm:mb-6">
                    <h3 class="text-lg sm:text-xl font-light text-white">Editar Contraseña</h3>
                    <button wire:click="closeEditAlbumModal" class="text-gray-400 hover:text-white transition-colors">
                        <i class="fas fa-times text-base sm:text-lg"></i>
                    </button>
                </div>

                <div class="mb-4 sm:mb-6">
                    <p class="text-xs sm:text-sm text-gray-400 mb-2">Álbum: <span class="font-medium text-white">{{ $editingAlbum->name }}</span></p>
                </div>

                <form wire:submit.prevent="updateAlbumPassword">
                    <div class="mb-6 sm:mb-8">
                        <label class="block text-xs sm:text-sm font-medium text-gray-300 mb-2 sm:mb-3">Contraseña</label>
                        <input type="password"
                               wire:model="editAlbumPassword"
                               placeholder="Dejar vacío para acceso público"
                               class="w-full bg-gray-800 border border-gray-600 rounded-lg sm:rounded-xl px-3 sm:px-4 py-2 sm:py-3 text-white placeholder-gray-400 focus:ring-2 focus:ring-white/20 focus:border-transparent text-sm sm:text-base">
                        @error('editAlbumPassword') <span class="text-red-400 text-xs sm:text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex space-x-3 sm:space-x-4">
                        <button type="button"
                                wire:click="closeEditAlbumModal"
                                class="flex-1 bg-gray-700 hover:bg-gray-600 text-white py-2 sm:py-3 px-3 sm:px-4 rounded-full font-medium transition-all duration-300 text-sm sm:text-base">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="flex-1 bg-white hover:bg-gray-100 text-black py-2 sm:py-3 px-3 sm:px-4 rounded-full font-medium transition-all duration-300 hover:scale-105 text-sm sm:text-base">
                            Actualizar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Album Modal -->
    @if($showAlbumModal && $selectedAlbum)
        <div class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-gray-900 border border-gray-700 rounded-xl sm:rounded-2xl max-w-lg sm:max-w-2xl w-full p-4 sm:p-6 lg:p-8">
                <div class="flex justify-between items-center mb-4 sm:mb-6">
                    <h3 class="text-lg sm:text-xl font-light text-white truncate pr-4">{{ $selectedAlbum->name }}</h3>
                    <button wire:click="closeAlbumModal" class="text-gray-400 hover:text-white transition-colors flex-shrink-0">
                        <i class="fas fa-times text-base sm:text-lg"></i>
                    </button>
                </div>

                @if($selectedAlbum->password && !$showPhotoViewer)
                    <!-- Password Form -->
                    <div class="text-center py-8 sm:py-12">
                        <div class="mb-6 sm:mb-8">
                            <i class="fas fa-lock text-4xl sm:text-6xl text-gray-500 mb-4 sm:mb-6"></i>
                            <p class="text-gray-400 text-base sm:text-lg">Este álbum está protegido con contraseña</p>
                        </div>

                        <form wire:submit.prevent="verifyPassword">
                            <div class="mb-6 sm:mb-8">
                                <input type="password"
                                       wire:model="albumPassword"
                                       placeholder="Ingresa la contraseña"
                                       class="w-full max-w-xs sm:max-w-sm mx-auto bg-gray-800 border border-gray-600 rounded-lg sm:rounded-xl px-3 sm:px-4 py-2 sm:py-3 text-white placeholder-gray-400 focus:ring-2 focus:ring-white/20 focus:border-transparent text-sm sm:text-base">
                                @error('albumPassword') <span class="text-red-400 text-xs sm:text-sm">{{ $message }}</span> @enderror
                                @if($passwordError)
                                    <span class="text-red-400 text-xs sm:text-sm">{{ $passwordError }}</span>
                                @endif
                            </div>

                            <button type="submit"
                                    class="bg-white hover:bg-gray-100 text-black py-2 sm:py-3 px-6 sm:px-8 rounded-full font-medium transition-all duration-300 hover:scale-105 text-sm sm:text-base">
                                Acceder
                            </button>
                        </form>
                    </div>
                @elseif($showPhotoViewer)
                    <!-- Photo Viewer -->
                    <div class="space-y-4 sm:space-y-6">
                        <!-- Current Photo -->
                        @if($this->currentPhoto)
                            <div class="relative">
                                @php
                                    $config = config('filesystems.disks.s3');
                                    $photoUrl = "https://{$config['bucket']}.s3.{$config['region']}.amazonaws.com/{$this->currentPhoto->file_path}";
                                @endphp
                                <img src="{{ $photoUrl }}"
                                     alt="Foto {{ $currentPhotoIndex + 1 }}"
                                     class="w-full h-64 sm:h-80 lg:h-96 object-contain bg-gray-800 rounded-xl sm:rounded-2xl border border-gray-700">

                                <!-- Navigation Arrows -->
                                <button wire:click="previousPhoto"
                                        @if(!$this->hasPreviousPhoto) disabled @endif
                                        class="absolute left-2 sm:left-4 top-1/2 transform -translate-y-1/2 bg-black/70 backdrop-blur-sm text-white p-2 sm:p-3 rounded-full hover:bg-black/80 disabled:opacity-30 disabled:cursor-not-allowed transition-all duration-300 border border-white/20">
                                    <i class="fas fa-chevron-left text-sm sm:text-base"></i>
                                </button>

                                <button wire:click="nextPhoto"
                                        @if(!$this->hasNextPhoto) disabled @endif
                                        class="absolute right-2 sm:right-4 top-1/2 transform -translate-y-1/2 bg-black/70 backdrop-blur-sm text-white p-2 sm:p-3 rounded-full hover:bg-black/80 disabled:opacity-30 disabled:cursor-not-allowed transition-all duration-300 border border-white/20">
                                    <i class="fas fa-chevron-right text-sm sm:text-base"></i>
                                </button>
                            </div>

                            <!-- Photo Info and Controls -->
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-3 sm:space-y-0">
                                <div class="text-xs sm:text-sm text-gray-400">
                                    Foto {{ $currentPhotoIndex + 1 }} de {{ $selectedAlbum->photos->count() }}
                                </div>

                                <div class="flex space-x-2 sm:space-x-3">
                                    <button wire:click="downloadPhoto({{ $this->currentPhoto->id }})"
                                            class="bg-green-600/80 hover:bg-green-600 backdrop-blur-sm text-white px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm font-medium transition-all duration-300 hover:scale-105 border border-green-400/50">
                                        @if(isset($downloadingPhotos[$this->currentPhoto->id]))
                                            <i class="fas fa-spinner fa-spin mr-1 sm:mr-2"></i><span class="hidden sm:inline">Descargando...</span><span class="sm:hidden">...</span>
                                        @else
                                            <i class="fas fa-download mr-1 sm:mr-2"></i><span class="hidden sm:inline">Descargar</span><span class="sm:hidden">↓</span>
                                        @endif
                                    </button>

                                    <button wire:click="downloadAllPhotos"
                                            class="bg-blue-600/80 hover:bg-blue-600 backdrop-blur-sm text-white px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm font-medium transition-all duration-300 hover:scale-105 border border-blue-400/50">
                                        <i class="fas fa-download mr-1 sm:mr-2"></i><span class="hidden sm:inline">Descargar Todas</span><span class="sm:hidden">Todas</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Thumbnail Grid -->
                            @if($selectedAlbum->photos->count() > 1)
                                <div class="grid grid-cols-4 sm:grid-cols-6 gap-2 sm:gap-3 max-h-24 sm:max-h-32 overflow-y-auto">
                                    @foreach($selectedAlbum->photos as $index => $photo)
                                        @php
                                            $config = config('filesystems.disks.s3');
                                            $thumbUrl = "https://{$config['bucket']}.s3.{$config['region']}.amazonaws.com/{$photo->file_path}";
                                        @endphp
                                        <button wire:click="viewPhoto({{ $photo->id }})"
                                                class="relative aspect-square overflow-hidden rounded-lg sm:rounded-xl {{ $index === $currentPhotoIndex ? 'ring-2 ring-white border-2 border-white' : 'border border-gray-600' }} transition-all duration-300 hover:scale-105">
                                            <img src="{{ $thumbUrl }}"
                                                 alt="Miniatura {{ $index + 1 }}"
                                                 class="w-full h-full object-cover">
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        @endif
                    </div>
                @endif
            </div>

        </div>
    @endif

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="fixed top-16 sm:top-4 right-4 bg-green-600/90 backdrop-blur-sm text-white px-4 sm:px-6 py-2 sm:py-3 rounded-full shadow-lg z-50 border border-green-400/50 text-sm sm:text-base">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="fixed top-16 sm:top-4 right-4 bg-red-600/90 backdrop-blur-sm text-white px-4 sm:px-6 py-2 sm:py-3 rounded-full shadow-lg z-50 border border-red-400/50 text-sm sm:text-base">
            {{ session('error') }}
        </div>
    @endif

    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
            <x-self.superPie></x-self.superPie>

</div>
