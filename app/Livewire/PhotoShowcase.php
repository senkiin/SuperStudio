<?php

namespace App\Livewire;

use App\Models\CuratedGallery;
use App\Models\Photo;
use App\Models\Album; // Asegúrate que este modelo exista si lo usas
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str; // Para Str::slug
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Collection; // Usar la colección de Laravel

class PhotoShowcase extends Component
{
    use WithFileUploads, WithPagination;

    public ?CuratedGallery $gallery = null;
    public string $galleryIdentifier = 'default_showcase';

    // Tipado explícito con la colección de Laravel
    public Collection $photosToDisplay;
    public Collection $photosForCustomLightbox;

    public bool $isAdmin = false;
    public bool $showManageModal = false;

    // Para subir nuevas fotos
    public $newPhotos = [];

    // Para seleccionar desde álbumes existentes
    public ?Collection $albums = null; // Tipado como Collection
    public $selectedAlbumId = null;
    public Collection $photosInSelectedAlbum; // Tipado como Collection
    public $selectedPhotosFromAlbum = [];

    // Para seleccionar fotos con "me gusta"
    public Collection $likedPhotos; // Tipado como Collection
    public $selectedLikedPhotos = [];

    // Para búsqueda general de fotos existentes a añadir
    public string $photoSearchQuery = '';
    public $searchedPhotos = null; // Se maneja con paginación, no necesita ser Collection aquí
    public $selectedExistingPhotos = [];

    // --- Propiedades para el Lightbox ---
    public bool $showCustomLightbox = false;
    public ?Photo $currentLightboxPhoto = null;
    public int $currentLightboxPhotoIndex = 0;
    // --- Fin Propiedades Lightbox ---

    protected $paginationTheme = 'tailwind';

    protected function rules()
    {
        return [
            'newPhotos.*' => 'nullable|image|max:2048|mimes:jpeg,png,jpg,webp',
            'selectedPhotosFromAlbum' => 'array',
            'selectedLikedPhotos' => 'array',
            'selectedExistingPhotos' => 'array',
        ];
    }

     protected $messages = [
        'newPhotos.*.image' => 'Cada archivo debe ser una imagen.',
        'newPhotos.*.max' => 'Cada imagen no debe superar los 2MB.',
        'newPhotos.*.mimes' => 'Formato de imagen no válido. Usar: jpeg, png, jpg, webp.',
    ];


    public function mount(string $identifier = 'default_showcase')
    {
        $this->galleryIdentifier = $identifier;
        $this->isAdmin = Auth::check() && Auth::user()->role === 'admin';
        $this->gallery = CuratedGallery::firstOrCreate(
            ['identifier' => $this->galleryIdentifier],
            ['title' => 'Galería Destacada']
        );

        // Inicializar como colecciones de Laravel vacías
        $this->photosToDisplay = new Collection();
        $this->photosForCustomLightbox = new Collection();
        $this->photosInSelectedAlbum = new Collection();
        $this->likedPhotos = new Collection();


        $this->loadPhotosToDisplay();

        if ($this->isAdmin) {
            if (class_exists(Album::class)) {
                $this->albums = Album::orderBy('name')->get();
            } else {
                $this->albums = new Collection();
            }
            // Asumiendo que Photo tiene un scope o una forma de obtener los 'liked'
            // o un campo 'is_liked' como se usó antes
            if (method_exists(Photo::class, 'where')) { // Simple check
                 $this->likedPhotos = Photo::where('is_liked', true)->orderBy('created_at', 'desc')->get();
            } else {
                 $this->likedPhotos = new Collection();
            }
        }
    }

    public function loadPhotosToDisplay()
    {
        if ($this->gallery) {
            $this->gallery->refresh();
            $this->photosToDisplay = $this->gallery->photos()->get(); // photos() es la relación en CuratedGallery
            $this->photosForCustomLightbox = $this->photosToDisplay;
        } else {
            // Manejar el caso en que $gallery no se haya podido cargar o crear
            $this->photosToDisplay = new Collection();
            $this->photosForCustomLightbox = new Collection();
        }
    }

    public function openManageModal()
    {
        if (!$this->isAdmin) return;
        $this->resetInputFields(); // Asegúrate de que esto también resetea los errores de validación
        $this->showManageModal = true;
    }

    public function closeManageModal()
    {
        $this->showManageModal = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->newPhotos = [];
        $this->selectedAlbumId = null;
        $this->photosInSelectedAlbum = new Collection(); // Resetear a Collection vacía
        $this->selectedPhotosFromAlbum = [];
        $this->selectedLikedPhotos = [];
        $this->photoSearchQuery = '';
        $this->searchedPhotos = null; // La paginación se reseteará sola
        $this->selectedExistingPhotos = [];
        $this->resetErrorBag(); // Limpiar errores de validación
        $this->resetValidation(); // Limpiar mensajes de error explícitamente
    }

    public function updatedSelectedAlbumId($albumId)
    {
        if ($albumId && class_exists(Album::class)) {
            $album = Album::find($albumId);
            // Asumiendo relación 'photos' en el modelo Album
            $this->photosInSelectedAlbum = $album ? $album->photos()->get() : new Collection();
        } else {
            $this->photosInSelectedAlbum = new Collection();
        }
        $this->selectedPhotosFromAlbum = [];
    }

    public function searchExistingPhotos()
    {
        if (strlen($this->photoSearchQuery) >= 3 && method_exists(Photo::class, 'where')) {
            $this->searchedPhotos = Photo::where('filename', 'like', '%' . $this->photoSearchQuery . '%')
                // ->orWhere('description', 'like', '%' . $this->photoSearchQuery . '%')
                ->orderBy('created_at', 'desc')
                ->paginate(5, ['*'], 'searchedPhotosPage');
        } else {
            $this->searchedPhotos = null;
        }
         $this->resetPage('searchedPhotosPage'); // Resetea la página de la paginación específica
    }

    public function updatedPhotoSearchQuery() {
        $this->resetPage('searchedPhotosPage');
        if (strlen($this->photoSearchQuery) < 3) {
             $this->searchedPhotos = null;
        }
    }

    // --- Métodos para el Lightbox ---
    public function openCustomLightbox(int $photoId)
    {
        $this->currentLightboxPhoto = Photo::find($photoId);
        if (!$this->currentLightboxPhoto) {
            session()->flash('error', 'Foto no encontrada para previsualizar.');
            return;
        }

        // Asegurarse de que photosForCustomLightbox está poblada y es la correcta
        if ($this->photosForCustomLightbox->isEmpty() && $this->photosToDisplay->isNotEmpty()) {
            $this->photosForCustomLightbox = $this->photosToDisplay;
        } elseif ($this->photosForCustomLightbox->isEmpty() && $this->photosToDisplay->isEmpty()) {
            // Si ambas están vacías pero tenemos una foto actual (caso muy raro), inicializar con ella
            $this->photosForCustomLightbox = new Collection([$this->currentLightboxPhoto]);
        }


        $this->currentLightboxPhotoIndex = $this->photosForCustomLightbox->search(function ($photo) {
            return $photo->id === $this->currentLightboxPhoto->id;
        });

        if ($this->currentLightboxPhotoIndex === false) {
            // Si no se encuentra, podría ser una desincronización. Intentar añadirla y buscar de nuevo.
            if (!$this->photosForCustomLightbox->contains('id', $this->currentLightboxPhoto->id)) {
                 $this->photosForCustomLightbox->prepend($this->currentLightboxPhoto);
                 // Re-buscar el índice después de añadirla
                 $this->currentLightboxPhotoIndex = $this->photosForCustomLightbox->search(function ($photo) {
                    return $photo->id === $this->currentLightboxPhoto->id;
                 });
            }
             // Si aún así no se encuentra (extremadamente improbable si se añadió), default a 0
             if ($this->currentLightboxPhotoIndex === false) {
                 $this->currentLightboxPhotoIndex = 0;
             }
        }
        $this->showCustomLightbox = true;
    }

    public function closeCustomLightbox()
    {
        $this->showCustomLightbox = false;
        $this->currentLightboxPhoto = null;
    }

    public function nextPhotoInLightbox()
    {
        if ($this->photosForCustomLightbox->isEmpty()){
            $this->closeCustomLightbox();
            return;
        }
        if ($this->currentLightboxPhotoIndex < ($this->photosForCustomLightbox->count() - 1)) {
            $this->currentLightboxPhotoIndex++;
            $this->currentLightboxPhoto = $this->photosForCustomLightbox->get($this->currentLightboxPhotoIndex);
        } else {
            // Opcional: Ir a la primera si está en la última
            // $this->currentLightboxPhotoIndex = 0;
            // $this->currentLightboxPhoto = $this->photosForCustomLightbox->get($this->currentLightboxPhotoIndex);
        }
    }

    public function previousPhotoInLightbox()
    {
        if ($this->photosForCustomLightbox->isEmpty()){
            $this->closeCustomLightbox();
            return;
        }
        if ($this->currentLightboxPhotoIndex > 0) {
            $this->currentLightboxPhotoIndex--;
            $this->currentLightboxPhoto = $this->photosForCustomLightbox->get($this->currentLightboxPhotoIndex);
        } else {
            // Opcional: Ir a la última si está en la primera
            // $this->currentLightboxPhotoIndex = $this->photosForCustomLightbox->count() - 1;
            // $this->currentLightboxPhoto = $this->photosForCustomLightbox->get($this->currentLightboxPhotoIndex);
        }
    }
    // --- Fin Métodos Lightbox ---

    public function addUploadedPhotos()
    {
        if (!$this->isAdmin) return;
        $this->validateOnly('newPhotos.*'); // Validar solo este campo

        if (empty($this->newPhotos)) {
            session()->flash('error', 'No seleccionaste ninguna foto para subir.');
            return;
        }

        foreach ($this->newPhotos as $photoFile) {
            $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
            $filename = Str::slug($originalFilename) . '-' . uniqid() . '.' . $photoFile->extension();
            $path = $photoFile->storeAs('showcase_uploads', $filename, 'public');

            $photo = Photo::create([
                'file_path' => $path,
                'thumbnail_path' => $path, // Idealmente, generar un thumbnail aquí
                'filename' => $photoFile->getClientOriginalName(),
                'album_id' => null,
                'uploaded_by' => Auth::id(), // Asumiendo que este campo se llama usuario_id
                'is_liked' => false, // O el valor por defecto que tengas
            ]);

            // Añadir a la galería con orden
            $currentMaxOrder = $this->gallery->photos()->max('order') ?? 0;
            $this->gallery->photos()->syncWithoutDetaching([$photo->id => ['order' => $currentMaxOrder + 1]]);
        }
        $this->newPhotos = []; // Limpiar input
        $this->loadPhotosToDisplay();
        session()->flash('message', count($this->newPhotos) . ' nuevas fotos subidas y añadidas.');
    }

    public function addSelectedPhotos()
    {
        if (!$this->isAdmin) return;

        // Validar que al menos una de las listas de selección tiene algo
        // (No es estrictamente necesario si se maneja abajo, pero puede ser una validación temprana)

        $allSelectedIds = array_unique(array_filter(array_merge(
            array_map('intval', $this->selectedPhotosFromAlbum),
            array_map('intval', $this->selectedLikedPhotos),
            array_map('intval', $this->selectedExistingPhotos)
        )));

        if (!empty($allSelectedIds)) {
            $currentMaxOrder = $this->gallery->photos()->max('order') ?? 0;
            $syncData = [];
            foreach ($allSelectedIds as $index => $photoId) {
                // Evitar añadir fotos que ya están en la galería
                if (!$this->gallery->photos()->where('photo_id', $photoId)->exists()) {
                    $syncData[$photoId] = ['order' => $currentMaxOrder + count($syncData) + 1];
                }
            }
            if(!empty($syncData)){
                $this->gallery->photos()->attach($syncData); // Usar attach en lugar de syncWithoutDetaching para nuevas
            }

            $this->loadPhotosToDisplay();
            $this->resetInputFields();
            session()->flash('message', count($syncData) . ' fotos seleccionadas añadidas a la galería.');
        } else {
            session()->flash('error', 'No se seleccionaron fotos nuevas para añadir.');
        }
    }

    public function removePhotoFromShowcase(int $photoId)
    {
        if (!$this->isAdmin) return;
        $this->gallery->photos()->detach($photoId);
        $this->loadPhotosToDisplay();
        session()->flash('message', 'Foto eliminada de la galería.');
    }

    public function updatePhotoOrder($orderedIdsWithInfo) // Recibe un array de arrays de SortableJS
    {
        if (!$this->isAdmin) return;
        foreach ($orderedIdsWithInfo as $item) {
            // $item['order'] es la nueva posición (1-based)
            // $item['value'] es el photo_id
            $this->gallery->photos()->updateExistingPivot($item['value'], ['order' => $item['order']]);
        }
        $this->loadPhotosToDisplay(); // Recargar para reflejar el nuevo orden
        session()->flash('message', 'Orden de fotos actualizado.');
    }

    public function render()
    {
        $photosForSearchModal = null;
        if ($this->isAdmin) { // Solo buscar si es admin y el modal podría mostrarse
            if (strlen($this->photoSearchQuery) >= 3) {
                // Solo llamar a searchExistingPhotos si el query es suficientemente largo
                // y la paginación ya se maneja dentro de ese método.
                if ($this->searchedPhotos === null || ($this->searchedPhotos && $this->searchedPhotos->currentPage() === 1 && $this->photoSearchQuery !== $this->searchedPhotos->first()?->search_query_that_generated_it) ) {
                     // Evitar re-búsquedas innecesarias si la paginación se encarga.
                     // Esto es complejo de manejar perfectamente sin un estado más robusto para la búsqueda.
                     // Por ahora, confiamos en que $this->resetPage lo maneja.
                }
                $this->searchExistingPhotos(); // Se llama aquí para que la paginación funcione en render
            } elseif (strlen($this->photoSearchQuery) === 0) {
                $this->searchedPhotos = null; // Limpiar si la búsqueda se borra
            }
        }
        $photosForSearchModal = $this->searchedPhotos;

        return view('livewire.photo-showcase', [
            'photosForSearchModal' => $photosForSearchModal
        ]);
    }
}
