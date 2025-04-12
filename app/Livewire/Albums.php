<?php

namespace App\Livewire;

use App\Models\Album;
use App\Models\Photo; // Importar Photo
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // Importar Storage
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Livewire\WithPagination;

class Albums extends Component
{
    use WithPagination;
    use WithFileUploads;


    // Propiedades existentes
    public string $cadena = '';
    public string $campo = 'created_at';
    public string $order = 'desc';
    public bool $showModal = false;
    public ?Album $selectedAlbum = null;

    // --- NUEVAS PROPIEDADES ---
    public array $selectedPhotos = []; // Guarda los IDs de las fotos seleccionadas para borrar
     // --- NUEVA PROPIEDAD PARA SUBIDA DE ARCHIVOS ---
     #[Validate(['uploadedPhotos.*' => 'image|max:100240'])] // Ejemplo validación: imagen, max 10MB por archivo
     public $uploadedPhotos = []; // Guarda temporalmente los archivos a subir
      // --- NUEVA PROPIEDAD PARA MODO SELECCIÓN ---
    public bool $selectionMode = false; // Controla si estamos en modo de selección

     // --- NUEVO MÉTODO PARA ACTIVAR/DESACTIVAR MODO SELECCIÓN ---
     public function toggleSelectionMode()
     {
         $this->selectionMode = !$this->selectionMode;
         // Si desactivamos el modo selección, limpiamos las fotos seleccionadas
         if (!$this->selectionMode) {
             $this->reset('selectedPhotos');
         }
     }

     // --- MODIFICAR toggleSelection PARA QUE SOLO FUNCIONE EN MODO SELECCIÓN ---
     public function toggleSelection(int $photoId)
     {
         // Solo permite seleccionar/deseleccionar si el modo está activo
         if (!$this->selectionMode) {
             return; // No hacer nada si no estamos en modo selección
         }

         if (in_array($photoId, $this->selectedPhotos)) {
             $this->selectedPhotos = array_diff($this->selectedPhotos, [$photoId]);
         } else {
             $this->selectedPhotos[] = $photoId;
         }
         $this->selectedPhotos = array_values($this->selectedPhotos);
     }

    // --- NUEVO MÉTODO PARA GUARDAR FOTOS SUBIDAS ---
    public function savePhotos()
    {
        // Validar los archivos usando las reglas definidas en la propiedad
        $this->validate();

        // Verificar permisos (solo admin o dueño del álbum)
        $user = Auth::user();
        if (!$user || ($user->role !== 'admin' && $this->selectedAlbum?->user_id !== $user->id)) {
            session()->flash('error', 'No tienes permiso para añadir fotos a este álbum.');
            $this->reset('uploadedPhotos'); // Limpiar la subida
            return;
        }

        if ($this->selectedAlbum && !empty($this->uploadedPhotos)) {
            foreach ($this->uploadedPhotos as $photoFile) {
                // 1. Guardar el archivo original (ej. en disco 'public')
                // Crear una subcarpeta por álbum es buena idea
                $path = $photoFile->store("albums/{$this->selectedAlbum->id}/photos", 'public');

                // 2. (Opcional pero recomendado) Crear Thumbnail
                //    Necesitas 'intervention/image' (composer require intervention/image)
                //    Esta parte puede ser más compleja (manejo de errores, drivers GD/Imagick)
                $thumbnailPath = null;
                /* // Ejemplo básico con Intervention Image (requiere instalación y configuración)
                try {
                    $manager = new \Intervention\Image\Drivers\Gd\Driver(); // O ImagickDriver
                    $image = $manager->read(Storage::disk('public')->path($path));
                    $thumbFilename = 'thumb_' . basename($path);
                    $thumbnailPath = "albums/{$this->selectedAlbum->id}/thumbnails/{$thumbFilename}";

                    $image->scaleDown(width: 400); // Redimensiona a 400px de ancho máx.
                    $image->save(Storage::disk('public')->path($thumbnailPath));
                 } catch (\Exception $e) {
                     // Log error, thumbnailPath seguirá null
                     \Log::error("Error creando thumbnail para {$path}: " . $e->getMessage());
                     $thumbnailPath = null; // Asegura que es null si falla
                 }
                */

                // 3. Crear registro en la base de datos
                Photo::create([
                    'album_id' => $this->selectedAlbum->id,
                    'file_path' => $path,
                    'thumbnail_path' => $thumbnailPath, // Será null si no se generó
                    'uploaded_by' => $user->id,
                    'like' => false, // Valor inicial por defecto
                ]);
            }

            $this->reset('uploadedPhotos'); // Limpiar los archivos temporales
            session()->flash('message', 'Fotos añadidas correctamente.');
             // Forzar re-render para actualizar la galería modal inmediatamente
             // (Podría no ser estrictamente necesario si Livewire detecta el cambio en la relación)
             $this->selectedAlbum->refresh(); // Recarga el álbum y sus relaciones
             $this->resetPage('photosPage'); // Ir a la primera página de fotos
        }
    }

    // --- MÉTODO PARA LIKE/UNLIKE ---
    public function toggleLike(int $photoId)
    {
        // Verificar si el usuario tiene permiso para dar like (opcional)
        // if (!auth()->user()->can('like photos')) return;

        $photo = Photo::find($photoId);

        // Asegurarse que la foto pertenece al álbum actual mostrado en el modal
        if ($photo && $this->selectedAlbum && $photo->album_id === $this->selectedAlbum->id) {
            $photo->like = !$photo->like; // Cambia el valor booleano
            $photo->save();
            // Livewire refrescará la vista y mostrará el cambio del icono
        }
    }

    // --- MÉTODO PARA BORRAR FOTOS SELECCIONADAS ---
    public function deleteSelectedPhotos()
    {
        if (empty($this->selectedPhotos)) {
            return;
        }

        // --- ¡IMPORTANTE! Comprobación de Permisos ---
        // Asegúrate que el usuario actual puede borrar fotos de ESTE álbum
        // Ejemplo: Solo el dueño del álbum o un admin puede borrar
        $user = Auth::user();
        if (!$user || ($user->role !== 'admin' && $this->selectedAlbum?->user_id !== $user->id)) {
             // Mostrar un mensaje de error al usuario
             session()->flash('error', 'No tienes permiso para borrar estas fotos.');
             // O usar this->addError(...)
             return;
        }
        // --- Fin Comprobación de Permisos ---

        // Obtener las fotos a borrar (volviendo a comprobar que pertenecen al álbum actual por seguridad)
        $photosToDelete = Photo::whereIn('id', $this->selectedPhotos)
                               ->where('album_id', $this->selectedAlbum?->id)
                               ->get();

        if ($photosToDelete->isEmpty()) {
             $this->selectedPhotos = [];
             return;
        }

        // 1. Borrar los archivos físicos del almacenamiento (¡MUY IMPORTANTE!)
        $pathsToDelete = $photosToDelete->pluck('file_path')->filter()->toArray();
        $thumbPathsToDelete = $photosToDelete->pluck('thumbnail_path')->filter()->toArray();

        // Asegúrate de usar el disco correcto si no es el default ('public' o 's3')
        Storage::delete($pathsToDelete);
        Storage::delete($thumbPathsToDelete);

        // 2. Borrar los registros de la base de datos
        Photo::destroy($this->selectedPhotos);

        // 3. Limpiar la selección
        $this->selectedPhotos = [];

        // 4. Mostrar mensaje de éxito (opcional)
        session()->flash('message', 'Fotos eliminadas correctamente.');

        // 5. Refrescar (Livewire lo hará, pero si la paginación cambia mucho, puedes forzar)
        // $this->resetPage('photosPage'); // Puede ser necesario si borras muchos items de una página
    }


    public function openModal(int $albumId)
    {
        $this->selectedAlbum = Album::find($albumId);
        $this->selectedPhotos = [];
        $this->reset('uploadedPhotos'); // Limpiar subidas previas al abrir
        $this->showModal = true;
        $this->selectionMode = false; // <-- Resetear modo selección al abrir
        $this->resetPage('photosPage');
    }

     public function closeModal()
     {
         $this->showModal = false;
         $this->selectedAlbum = null;
         $this->selectedPhotos = [];
         $this->selectionMode = false; // <-- Resetear modo selección al abrir
         $this->reset('uploadedPhotos'); // Limpiar subidas al cerrar
     }


    // ... (updatingCadena, sortBy sin cambios) ...

    // Modificar render para pasar selectedPhotos count (opcional, para UI)
    public function render()
    {
         // ... (Lógica para obtener $albums y $photosInModal como antes) ...
        $user = Auth::user();
        $query = Album::query();
        if ($user) {
            if (! $user == 'admin') {
                $query->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                       ->orWhere('type', 'public');
                });
            }
        } else {
            $query->where('type', 'public');
        }
        if (!empty($this->cadena)) {
             $query->where(function ($q) {
                 $searchTerm = '%' . $this->cadena . '%';
                 $q->where('name', 'like', $searchTerm)
                   ->orWhere('description', 'like', $searchTerm);
             });
        }
         $query->with(['user']);
         $allowedSorts = ['name', 'created_at', 'type'];
         $sortField = in_array($this->campo, $allowedSorts) ? $this->campo : 'created_at';
         $sortDirection = $this->order === 'asc' ? 'asc' : 'desc';
         $albums = $query->orderBy($sortField, $sortDirection)->paginate(12, ['*'], 'albumsPage');

        $photosInModal = null;
        if ($this->showModal && $this->selectedAlbum) {
            $photosInModal = $this->selectedAlbum
                                 ->photos()
                                 ->orderBy('id')
                                 ->paginate(9, ['*'], 'photosPage');
        }

        return view('livewire.albums', [
            'albums' => $albums,
            'photosInModal' => $photosInModal,
            // 'selectedPhotosCount' => count($this->selectedPhotos) // Podrías pasar el conteo si lo usas en la vista
        ]);
    }
}
