<?php

namespace App\Livewire;

use App\Models\Album;
use App\Models\Photo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;

class Gallery extends Component
{
    use WithPagination;

    // --- Propiedades para la gestión de álbumes ---
    public $showAdminPanel = false;
    public $showAddAlbumModal = false;
    public $showEditAlbumModal = false;
    public $editingAlbum = null;

    // --- Propiedades para añadir álbum ---
    public $newAlbumId = '';
    public $newAlbumPassword = '';

    // --- Propiedades para editar álbum ---
    public $editAlbumPassword = '';

    // --- Propiedades para visualización de álbumes ---
    public $selectedAlbum = null;
    public $showAlbumModal = false;
    public $albumPassword = '';
    public $passwordError = '';
    public $currentPhotoIndex = 0;
    public $showPhotoViewer = false;

    // --- Propiedades para búsqueda ---
    public $search = '';

    // --- Propiedades para descarga ---
    public $downloadingPhotos = [];

    protected $rules = [
        'newAlbumId' => 'required|exists:albums,id',
        'newAlbumPassword' => 'nullable|string|max:255',
        'editAlbumPassword' => 'nullable|string|max:255',
        'albumPassword' => 'nullable|string|max:255',
    ];

    public function mount()
    {
        // Verificar si el usuario es admin
        $this->showAdminPanel = Auth::check() && Auth::user()->role === 'admin';
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    // --- Métodos para gestión de álbumes (solo admin) ---

    public function openAddAlbumModal()
    {
        if (!$this->showAdminPanel) return;

        $this->reset(['newAlbumId', 'newAlbumPassword']);
        $this->showAddAlbumModal = true;
    }

    public function closeAddAlbumModal()
    {
        $this->showAddAlbumModal = false;
        $this->reset(['newAlbumId', 'newAlbumPassword']);
    }

    public function addAlbumToGallery()
    {
        if (!$this->showAdminPanel) return;

        $this->validate([
            'newAlbumId' => 'required|exists:albums,id',
            'newAlbumPassword' => 'nullable|string|max:255',
        ]);

        $album = Album::find($this->newAlbumId);

        if ($album) {
            $album->update([
                'is_public_gallery' => true,
                'password' => $this->newAlbumPassword ?: null,
            ]);

            session()->flash('message', 'Álbum añadido a la galería pública exitosamente.');
            $this->closeAddAlbumModal();
        }
    }

    public function openEditAlbumModal($albumId)
    {
        if (!$this->showAdminPanel) return;

        $album = Album::find($albumId);
        if ($album && $album->is_public_gallery) {
            $this->editingAlbum = $album;
            $this->editAlbumPassword = $album->password ?? '';
            $this->showEditAlbumModal = true;
        }
    }

    public function closeEditAlbumModal()
    {
        $this->showEditAlbumModal = false;
        $this->reset(['editingAlbum', 'editAlbumPassword']);
    }

    public function updateAlbumPassword()
    {
        if (!$this->showAdminPanel || !$this->editingAlbum) return;

        $this->validate([
            'editAlbumPassword' => 'nullable|string|max:255',
        ]);

        $this->editingAlbum->update([
            'password' => $this->editAlbumPassword ?: null,
        ]);

        session()->flash('message', 'Contraseña del álbum actualizada exitosamente.');
        $this->closeEditAlbumModal();
    }

    public function removeAlbumFromGallery($albumId)
    {
        if (!$this->showAdminPanel) return;

        $album = Album::find($albumId);
        if ($album) {
            $album->update([
                'is_public_gallery' => false,
                'password' => null,
            ]);

            session()->flash('message', 'Álbum removido de la galería pública.');
        }
    }

    // --- Métodos para visualización de álbumes ---

    public function openAlbum($albumId)
    {
        $album = Album::with('photos')->find($albumId);

        if (!$album || !$album->is_public_gallery) {
            session()->flash('error', 'Álbum no encontrado o no disponible.');
            return;
        }

        $this->selectedAlbum = $album;
        $this->albumPassword = '';
        $this->passwordError = '';
        $this->showAlbumModal = true;
    }

    public function closeAlbumModal()
    {
        $this->showAlbumModal = false;
        $this->reset(['selectedAlbum', 'albumPassword', 'passwordError', 'currentPhotoIndex', 'showPhotoViewer']);
    }

    public function verifyPassword()
    {
        if (!$this->selectedAlbum) return;

        $this->passwordError = '';

        // Asegurar que el álbum tenga las fotos cargadas
        if (!$this->selectedAlbum->relationLoaded('photos')) {
            $this->selectedAlbum->load('photos');
        }

        // Si no tiene contraseña, permitir acceso directo
        if (empty($this->selectedAlbum->password)) {
            $this->showAlbumPhotos();
            return;
        }

        // Verificar contraseña
        if ($this->albumPassword === $this->selectedAlbum->password) {
            $this->showAlbumPhotos();
        } else {
            $this->passwordError = 'Contraseña incorrecta.';
        }
    }

    private function showAlbumPhotos()
    {
        // Asegurar que el álbum tenga las fotos cargadas
        if (!$this->selectedAlbum->relationLoaded('photos')) {
            $this->selectedAlbum->load('photos');
        }

        // Verificar que hay fotos disponibles
        if ($this->selectedAlbum->photos->count() > 0) {
            $this->currentPhotoIndex = 0;
            $this->showPhotoViewer = true;
        } else {
            session()->flash('error', 'No hay fotos disponibles en este álbum.');
        }
    }

    public function viewPhoto($photoId)
    {
        if (!$this->selectedAlbum) return;

        $photos = $this->selectedAlbum->photos;
        $index = $photos->search(function ($photo) use ($photoId) {
            return $photo->id == $photoId;
        });

        if ($index !== false) {
            $this->currentPhotoIndex = $index;
        }
    }

    public function nextPhoto()
    {
        if (!$this->selectedAlbum) return;

        $photos = $this->selectedAlbum->photos;
        if ($this->currentPhotoIndex < $photos->count() - 1) {
            $this->currentPhotoIndex++;
        }
    }

    public function previousPhoto()
    {
        if ($this->currentPhotoIndex > 0) {
            $this->currentPhotoIndex--;
        }
    }

    public function closePhotoViewer()
    {
        $this->showPhotoViewer = false;
        $this->currentPhotoIndex = 0;
    }

    // --- Métodos para descarga ---

    public function downloadPhoto($photoId)
    {
        if (!$this->selectedAlbum) return;

        $photo = Photo::find($photoId);
        if (!$photo || $photo->album_id !== $this->selectedAlbum->id) {
            session()->flash('error', 'Foto no encontrada.');
            return;
        }

        $this->downloadingPhotos[$photoId] = true;

        try {
            $filePath = $photo->file_path;

            if (Storage::disk('albums')->exists($filePath)) {
                // Crear archivo temporal con la foto original
                $extension = pathinfo($filePath, PATHINFO_EXTENSION);
                $fileName = $this->selectedAlbum->name . '_' . $photo->id . '.' . $extension;
                $tempFilePath = storage_path('app/temp/' . $fileName);

                // Crear directorio temp si no existe
                if (!file_exists(storage_path('app/temp'))) {
                    mkdir(storage_path('app/temp'), 0755, true);
                }

                // Obtener el contenido del archivo desde S3
                $fileContent = Storage::disk('albums')->get($filePath);
                file_put_contents($tempFilePath, $fileContent);

                // Descargar el archivo original
                return response()->download($tempFilePath, $fileName)->deleteFileAfterSend(true);

            } else {
                session()->flash('error', 'Archivo no encontrado.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error al descargar la foto: ' . $e->getMessage());
        } finally {
            unset($this->downloadingPhotos[$photoId]);
        }
    }

    public function downloadAllPhotos()
    {
        if (!$this->selectedAlbum) return;

        try {
            $photos = $this->selectedAlbum->photos;
            if ($photos->count() === 0) {
                session()->flash('error', 'No hay fotos para descargar.');
                return;
            }

            // Crear un ZIP temporal
            $zipFileName = $this->selectedAlbum->name . '_todas_las_fotos.zip';
            $tempZipPath = storage_path('app/temp/' . $zipFileName);

            // Crear directorio temp si no existe
            if (!file_exists(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0755, true);
            }

            $zip = new \ZipArchive();
            if ($zip->open($tempZipPath, \ZipArchive::CREATE) !== TRUE) {
                session()->flash('error', 'No se pudo crear el archivo ZIP.');
                return;
            }

            $addedFiles = 0;
            foreach ($photos as $photo) {
                $filePath = $photo->file_path;
                if (Storage::disk('albums')->exists($filePath)) {
                    $extension = pathinfo($filePath, PATHINFO_EXTENSION);
                    $fileName = $this->selectedAlbum->name . '_' . $photo->id . '.' . $extension;

                    // Obtener el contenido del archivo desde S3
                    $fileContent = Storage::disk('albums')->get($filePath);
                    $zip->addFromString($fileName, $fileContent);
                    $addedFiles++;
                }
            }

            $zip->close();

            if ($addedFiles === 0) {
                session()->flash('error', 'No se encontraron archivos para descargar.');
                unlink($tempZipPath);
                return;
            }

            // Descargar el ZIP
            return response()->download($tempZipPath, $zipFileName)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            session()->flash('error', 'Error al crear el archivo ZIP: ' . $e->getMessage());
        }
    }

    // --- Propiedades computadas ---

    public function getAvailableAlbumsProperty()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return collect();
        }

        return Album::where('is_public_gallery', false)
            ->where('user_id', Auth::id())
            ->orderBy('name')
            ->get();
    }

    public function getCurrentPhotoProperty()
    {
        if (!$this->selectedAlbum || !$this->showPhotoViewer) {
            return null;
        }

        // Asegurar que las fotos estén cargadas
        if (!$this->selectedAlbum->relationLoaded('photos')) {
            $this->selectedAlbum->load('photos');
        }

        $photos = $this->selectedAlbum->photos;

        if ($photos->count() === 0) {
            return null;
        }

        return $photos->get($this->currentPhotoIndex);
    }

    public function getHasNextPhotoProperty()
    {
        if (!$this->selectedAlbum) return false;

        $photos = $this->selectedAlbum->photos;
        return $this->currentPhotoIndex < $photos->count() - 1;
    }

    public function getHasPreviousPhotoProperty()
    {
        return $this->currentPhotoIndex > 0;
    }

    public function render()
    {
        $query = Album::where('is_public_gallery', true)
            ->with(['photos', 'user'])
            ->withCount('photos');

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        $albums = $query->orderBy('created_at', 'desc')->paginate(12);

        return view('livewire.gallery', [
            'albums' => $albums,
            'availableAlbums' => $this->availableAlbums,
        ]);
    }
}
