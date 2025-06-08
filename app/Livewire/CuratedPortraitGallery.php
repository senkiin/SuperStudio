<?php

namespace App\Livewire;

use App\Models\GridGallery as PortraitCollection; // Alias para GridGallery, usado como la configuración de esta galería específica.
use App\Models\Photo; // Modelo Eloquent para las fotos.
use App\Models\Album; // Modelo Eloquent para los álbumes (usado para seleccionar fotos desde álbumes).
use Illuminate\Support\Facades\Auth; // Fachada para acceder a la información del usuario autenticado.
use Illuminate\Support\Facades\Storage; // Fachada para interactuar con el sistema de archivos (ej. S3).
use Illuminate\Support\Str; // Helper de Laravel para manipulación de strings (ej. para generar nombres de archivo).
use Livewire\Component; // Clase base para todos los componentes Livewire.
use Livewire\WithFileUploads; // Trait para habilitar la subida de archivos.
use Livewire\WithPagination; // Trait para habilitar la paginación de forma sencilla.
use Illuminate\Support\Collection; // Clase Collection de Laravel para un manejo de arrays más fluido.
use Illuminate\Support\Facades\Log;  // Fachada para registrar logs, útil para depuración.

class CuratedPortraitGallery extends Component
{
    use WithFileUploads, WithPagination; // Habilita la subida de archivos y la paginación.

    // --- Disco S3 donde guardar y leer fotos ----
    public string $disk = 'albums'; // Define el disco S3 por defecto. Asegúrate que esté configurado en filesystems.php.

    public ?PortraitCollection $collectionConfig = null; // Almacena la configuración de la galería (modelo PortraitCollection).
    public string $identifier = 'default_portrait_gallery'; // Identificador único para esta instancia de galería.
    public ?string $galleryTitle = ''; // Título público de la galería.
    public ?string $galleryDescription = ''; // Descripción pública de la galería.

    public Collection $photosForDisplay; // Colección de objetos Photo que se mostrarán en la galería.

    public bool $isAdmin = false; // Indica si el usuario actual es administrador.
    public bool $showManagerModal = false; // Controla la visibilidad del modal de gestión para el admin.

    // --- Lightbox ---
    public bool $showCustomLightbox = false; // Controla la visibilidad del modal del lightbox para previsualizar fotos.
    public ?Photo $currentLightboxPhoto = null; // Almacena el objeto Photo de la imagen actual en el lightbox.
    public int $currentLightboxPhotoIndex = 0;    // Índice de la foto actual dentro de la colección $photosForDisplay.

    // --- Modal de gestión (propiedades para el formulario dentro del modal) ---
    public $newPhotosToUploadModal = []; // Array para almacenar archivos de fotos subidos temporalmente en el modal.
    public $selectedAlbumIdModal = null; // ID del álbum seleccionado en el modal para cargar sus fotos.
    public Collection $photosFromAlbumModal; // Colección de fotos del álbum seleccionado en el modal.
    public Collection $likedPhotosForUserModal; // Colección de fotos favoritas del usuario (admin) para seleccionar en el modal.
    public string $searchQueryModal = ''; // Término de búsqueda para filtrar fotos en el modal.
    public $searchedPhotosModalPaginator = null; // Paginador para los resultados de búsqueda de fotos en el modal.
    public $selectedPhotosFromAlbumModalArray = []; // IDs de fotos seleccionadas desde un álbum en el modal.
    public $selectedLikedPhotosModalArray = []; // IDs de fotos favoritas seleccionadas en el modal.
    public $selectedExistingPhotosModalArray = []; // IDs de fotos seleccionadas desde la búsqueda en el modal.
    public ?Collection $allAlbumsModalCollection = null; // Colección de todos los álbumes disponibles para el selector en el modal.
    public string $editableCollectionTitleModal = ''; // Título de la galería, editable en el modal.
    public string $editableCollectionDescriptionModal = ''; // Descripción de la galería, editable en el modal.

    protected $paginationTheme = 'tailwind'; // Especifica que se usarán las vistas de paginación de Tailwind CSS.

    /**
     * Define las reglas de validación para los campos del modal de gestión.
     * @return array
     */
    protected function rules()
    {
        return [
            // Reglas para la subida de nuevas fotos en el modal.
            'newPhotosToUploadModal.*' => 'nullable|image|max:2048|mimes:jpeg,png,jpg,webp',
            // Reglas para los arrays de IDs de fotos seleccionadas.
            'selectedPhotosFromAlbumModalArray' => 'array',
            'selectedLikedPhotosModalArray'    => 'array',
            'selectedExistingPhotosModalArray' => 'array',
            // Reglas para el título y descripción editables de la galería.
            'editableCollectionTitleModal'     => 'nullable|string|max:255',
            'editableCollectionDescriptionModal' => 'nullable|string|max:5000',
        ];
    }

    /**
     * Define mensajes de error personalizados para las reglas de validación.
     * @var array
     */
    protected $messages = [
        'newPhotosToUploadModal.*.image' => 'Cada archivo debe ser una imagen.',
        'newPhotosToUploadModal.*.max'   => 'Cada imagen no debe superar los 2MB.',
    ];

    /**
     * Método `mount`: Se ejecuta una vez cuando el componente se inicializa.
     * Carga o crea la configuración de la galería y prepara datos iniciales.
     *
     * @param string $identifier Identificador único para esta instancia de galería.
     * @param string|null $defaultTitle Título por defecto si no hay configuración guardada.
     * @param string|null $defaultDescription Descripción por defecto.
     */
    public function mount(string $identifier, ?string $defaultTitle = "Retratos Seleccionados", ?string $defaultDescription = "")
    {
        $this->identifier = $identifier; // Asigna el identificador.
        $this->isAdmin   = Auth::check() && Auth::user()->role === 'admin'; // Verifica si es admin.

        // Carga la configuración de la galería (PortraitCollection) o la crea si no existe.
        $this->collectionConfig = PortraitCollection::firstOrCreate(
            ['identifier' => $this->identifier], // Busca por este identificador.
            // Valores por defecto si se crea una nueva configuración:
            ['title' => $defaultTitle, 'description' => $defaultDescription]
        );

        // Inicializa las propiedades del componente con los valores de la configuración.
        $this->galleryTitle       = $this->collectionConfig->title;
        $this->galleryDescription = $this->collectionConfig->description;

        // Inicializa las colecciones como vacías.
        $this->photosForDisplay       = new Collection();
        $this->photosFromAlbumModal   = new Collection();
        $this->likedPhotosForUserModal = new Collection();

        $this->loadPhotosForDisplay(); // Carga las fotos que se van a mostrar en la galería.

        // Si es admin, carga datos adicionales para el modal de gestión.
        if ($this->isAdmin) {
            $this->allAlbumsModalCollection = class_exists(Album::class)
                ? Album::orderBy('name')->withCount('photos')->get() // Obtiene todos los álbumes con contador de fotos.
                : new Collection(); // Colección vacía si el modelo Album no existe.

            // Carga las fotos favoritas del usuario admin.
            if (method_exists(Auth::user(), 'likedPhotos')) {
                $this->likedPhotosForUserModal = Auth::user()
                    ->likedPhotos() // Asume que el modelo User tiene una relación 'likedPhotos'.
                    ->orderBy('photo_user_likes.created_at', 'desc') // Ordena por fecha en que se dio "like".
                    ->limit(50) // Limita la cantidad para no sobrecargar.
                    ->get();
            }
        }
    }

    /**
     * Carga las fotos que se deben mostrar en la galería pública.
     * Obtiene las fotos asociadas a la configuración actual y las ordena.
     */
    public function loadPhotosForDisplay()
    {
        if (! $this->collectionConfig) { // Si la configuración no se cargó correctamente.
            Log::error("CuratedPortraitGallery: configuración '{$this->identifier}' no encontrada");
            $this->photosForDisplay = new Collection();
            $this->galleryTitle     = 'Error';
            $this->galleryDescription = 'Galería no encontrada';
            return;
        }

        $this->collectionConfig->refresh(); // Recarga la configuración desde la BD para tener los datos más recientes.
        // Obtiene las fotos de la relación 'photos' (definida en PortraitCollection) y las ordena por el campo 'order' de la tabla pivote.
        $this->photosForDisplay    = $this->collectionConfig->photos()->orderByPivot('order')->get();
        // Actualiza el título y descripción públicos.
        $this->galleryTitle        = $this->collectionConfig->title;
        $this->galleryDescription  = $this->collectionConfig->description;
    }

    // --- Lightbox ---

    /**
     * Abre el lightbox personalizado mostrando la foto con el ID especificado.
     *
     * @param int $photoId El ID de la foto que se debe mostrar en el lightbox.
     */
    public function openCustomLightbox(int $photoId)
    {
        $this->currentLightboxPhoto = Photo::find($photoId); // Busca la foto por ID.
        if (! $this->currentLightboxPhoto) { // Si no se encuentra la foto.
            session()->flash('cpg_error', 'Foto no encontrada.'); // Muestra un mensaje de error.
            return;
        }

        // Asegura que la colección de fotos para el lightbox ($photosForDisplay) esté cargada.
        if ($this->photosForDisplay->isEmpty()) {
            $this->loadPhotosForDisplay();
        }

        // Busca el índice de la foto actual dentro de la colección.
        $this->currentLightboxPhotoIndex = $this->photosForDisplay
            ->pluck('id') // Obtiene solo los IDs de las fotos.
            ->search($photoId); // Busca el índice del ID coincidente.

        $this->showCustomLightbox = true; // Muestra el modal del lightbox.
    }

    /**
     * Cierra el modal del lightbox y resetea las propiedades relacionadas.
     */
    public function closeCustomLightbox()
    {
        $this->showCustomLightbox        = false;
        $this->currentLightboxPhoto      = null;
        $this->currentLightboxPhotoIndex = 0;
        // Recarga las fotos de la galería para asegurar consistencia visual tras cerrar el lightbox.
        $this->loadPhotosForDisplay();
    }

    /**
     * Navega a la siguiente foto en el lightbox.
     * Si está en la última foto, vuelve a la primera (comportamiento cíclico).
     */
    public function nextPhotoInLightbox()
    {
        if ($this->photosForDisplay->isEmpty()) { // Si no hay fotos, cierra el lightbox.
            return $this->closeCustomLightbox();
        }

        $count = $this->photosForDisplay->count();
        // Calcula el siguiente índice de forma cíclica.
        $this->currentLightboxPhotoIndex = ($this->currentLightboxPhotoIndex + 1) % $count;
        $this->currentLightboxPhoto      = $this->photosForDisplay[$this->currentLightboxPhotoIndex]; // Actualiza la foto actual.
    }

    /**
     * Navega a la foto anterior en el lightbox.
     * Si está en la primera foto, va a la última (comportamiento cíclico).
     */
    public function previousPhotoInLightbox()
    {
        if ($this->photosForDisplay->isEmpty()) { // Si no hay fotos, cierra el lightbox.
            return $this->closeCustomLightbox();
        }

        $count = $this->photosForDisplay->count();
        // Calcula el índice anterior de forma cíclica.
        $this->currentLightboxPhotoIndex = ($this->currentLightboxPhotoIndex - 1 + $count) % $count;
        $this->currentLightboxPhoto      = $this->photosForDisplay[$this->currentLightboxPhotoIndex]; // Actualiza la foto actual.
    }

    // --- Gestión de galería (métodos para el modal de administración) ---

    /**
     * Abre el modal de gestión para el administrador.
     * Carga datos frescos y resetea campos del formulario del modal.
     */
    public function openManagerModal()
    {
        if (! $this->isAdmin) return; // Solo para administradores.
        $this->collectionConfig->refresh(); // Recarga la configuración.
        // Carga el título y descripción actuales en los campos editables del modal.
        $this->editableCollectionTitleModal       = $this->galleryTitle;
        $this->editableCollectionDescriptionModal = $this->galleryDescription;
        $this->resetManagerModalFields(); // Limpia otros campos del modal.
        // Recarga las fotos favoritas del admin para el selector.
        if (method_exists(Auth::user(), 'likedPhotos')) {
            $this->likedPhotosForUserModal = Auth::user()
                ->likedPhotos()
                ->orderBy('photo_user_likes.created_at', 'desc')
                ->limit(50)
                ->get();
        }
        $this->showManagerModal = true; // Muestra el modal.
    }

    /**
     * Cierra el modal de gestión.
     */
    public function closeManagerModal()
    {
        $this->showManagerModal = false;
        $this->resetManagerModalFields(); // Limpia los campos del modal.
    }

    /**
     * Resetea todos los campos y errores del formulario del modal de gestión.
     */
    private function resetManagerModalFields()
    {
        $this->newPhotosToUploadModal            = [];
        $this->selectedAlbumIdModal              = null;
        $this->photosFromAlbumModal              = new Collection();
        $this->selectedPhotosFromAlbumModalArray = [];
        $this->selectedLikedPhotosModalArray     = [];
        $this->searchQueryModal                  = '';
        $this->searchedPhotosModalPaginator      = null;
        $this->selectedExistingPhotosModalArray  = [];
        $this->resetErrorBag(); // Limpia errores de validación de Livewire.
        $this->resetValidation(); // Limpia el estado de validación.
    }

    /**
     * Hook de Livewire: Se ejecuta cuando la propiedad $selectedAlbumIdModal se actualiza.
     * Carga las fotos del álbum recién seleccionado en el modal.
     * @param mixed $albumId El ID del álbum seleccionado.
     */
    public function updatedSelectedAlbumIdModal($albumId)
    {
        $this->photosFromAlbumModal = new Collection(); // Limpia la colección de fotos del álbum.
        $this->selectedPhotosFromAlbumModalArray = []; // Limpia la selección de fotos de ese álbum.
        if ($albumId && class_exists(Album::class)) { // Si se seleccionó un ID y el modelo Album existe.
            $album = Album::find($albumId); // Busca el álbum.
            if ($album) {
                // Carga las fotos del álbum, ordenadas por fecha de creación descendente.
                $this->photosFromAlbumModal = $album->photos()->orderBy('created_at', 'desc')->get();
            }
        }
    }

    /**
     * Realiza una búsqueda de fotos en el modal de gestión.
     * La búsqueda se activa si $searchQueryModal tiene al menos 3 caracteres.
     */
    public function searchPhotosInModal()
    {
        $this->resetPage('managerSearchedPhotosPage'); // Resetea la paginación de los resultados de búsqueda.
        if (strlen($this->searchQueryModal) >= 3) { // Si el término de búsqueda es suficientemente largo.
            // Busca fotos cuyo nombre de archivo ('filename') contenga el término de búsqueda.
            $this->searchedPhotosModalPaginator = Photo::where('filename', 'like', '%' . $this->searchQueryModal . '%')
                ->orderBy('created_at', 'desc') // Ordena por fecha de creación.
                ->paginate(10, ['*'], 'managerSearchedPhotosPage'); // Pagina los resultados.
        } else {
            $this->searchedPhotosModalPaginator = null; // Limpia los resultados si la búsqueda es corta.
        }
    }

    /**
     * Hook de Livewire: Se ejecuta cuando la propiedad $searchQueryModal se actualiza.
     * Si la búsqueda es menor a 3 caracteres, limpia los resultados. Si no, ejecuta la búsqueda.
     */
    public function updatedSearchQueryModal()
    {
        if (strlen($this->searchQueryModal) < 3) {
            $this->searchedPhotosModalPaginator = null;
        } else {
            $this->searchPhotosInModal(); // Llama al método de búsqueda.
        }
    }

    /**
     * Guarda los metadatos (título y descripción) de la galería.
     */
    public function saveGalleryMetadata()
    {
        if (! $this->isAdmin) return; // Solo para administradores.
        // Valida solo los campos de título y descripción del modal.
        $this->validateOnly('editableCollectionTitleModal');
        $this->validateOnly('editableCollectionDescriptionModal');

        // Actualiza la configuración de la galería en la base de datos.
        $this->collectionConfig->update([
            'title'       => $this->editableCollectionTitleModal,
            'description' => $this->editableCollectionDescriptionModal,
        ]);

        // Actualiza las propiedades públicas para reflejar los cambios en la vista.
        $this->galleryTitle       = $this->collectionConfig->title;
        $this->galleryDescription = $this->collectionConfig->description;
        session()->flash('cpg_modal_message', 'Información actualizada.'); // Mensaje de éxito para el modal.
    }

    /**
     * Sube las nuevas fotos seleccionadas en el modal y las asocia a la galería.
     */
    public function uploadAndAttachToCollection()
    {
        if (! $this->isAdmin) return; // Solo para administradores.
        $this->validateOnly('newPhotosToUploadModal.*'); // Valida solo los archivos subidos.
        if (empty($this->newPhotosToUploadModal)) { // Si no hay fotos para subir.
            return session()->flash('cpg_modal_error', 'No hay fotos para subir.');
        }

        // Calcula el siguiente número de orden para las nuevas fotos.
        $orderStart = ($this->collectionConfig->photos()->max('order') ?: 0) + 1;
        $toAttach   = []; // Array para almacenar los IDs de las fotos creadas y su orden.

        foreach ($this->newPhotosToUploadModal as $file) {
            // Genera un nombre de archivo único y sanitizado.
            $slug     = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
            $name     = $slug . '-' . uniqid() . '.' . $file->extension();
            // Almacena el archivo en el disco S3 configurado, bajo una carpeta específica para esta galería.
            $path     = $file->storeAs("curated_portrait_galleries/{$this->identifier}", $name, $this->disk);

            // Crea un nuevo registro de Photo en la base de datos.
            $photo = Photo::create([
                'file_path'      => $path, // Ruta del archivo principal.
                'thumbnail_path' => $path, // Asume que el thumbnail es el mismo archivo (idealmente se generaría uno).
                'filename'       => $file->getClientOriginalName(), // Nombre original del archivo.
                'album_id'       => null, // No pertenece a un álbum específico si se sube directamente aquí.
                'uploaded_by'     => Auth::id(), // ID del usuario que subió la foto.
            ]);

            $toAttach[$photo->id] = ['order' => $orderStart++]; // Prepara los datos para la tabla pivote.
        }

        if ($toAttach) { // Si se crearon fotos.
            // Asocia las nuevas fotos a la galería actual usando la relación 'photos' y el método attach().
            $this->collectionConfig->photos()->attach($toAttach);
        }

        $this->newPhotosToUploadModal = []; // Limpia el array de archivos temporales.
        $this->loadPhotosForDisplay(); // Recarga las fotos de la galería.
        session()->flash('cpg_modal_message', count($toAttach).' fotos subidas y añadidas.'); // Mensaje de éxito.
    }

    /**
     * Asocia las fotos existentes (seleccionadas desde álbumes, favoritas o búsqueda) a la galería.
     */
    public function attachExistingPhotosToCollection()
    {
        if (! $this->isAdmin) return; // Solo para administradores.
        // Combina todos los arrays de IDs seleccionados, elimina duplicados y filtra valores vacíos.
        $ids = array_unique(array_filter(array_merge(
            $this->selectedPhotosFromAlbumModalArray,
            $this->selectedLikedPhotosModalArray,
            $this->selectedExistingPhotosModalArray
        )));

        if (empty($ids)) { // Si no se seleccionó ninguna foto.
            return session()->flash('cpg_modal_error', 'No seleccionaste ninguna foto.');
        }

        // Calcula el siguiente número de orden.
        $orderStart = ($this->collectionConfig->photos()->max('order') ?: 0) + 1;
        $attach     = []; // Array para los datos de la tabla pivote.

        foreach ($ids as $id) {
            // Verifica que la foto no esté ya asociada a esta galería para evitar duplicados.
            if (! $this->collectionConfig->photos()->where('photo_id', $id)->exists()) {
                $attach[$id] = ['order' => $orderStart++];
            }
        }

        if ($attach) { // Si hay fotos nuevas para asociar.
            $this->collectionConfig->photos()->attach($attach); // Asocia las fotos.
            $this->loadPhotosForDisplay(); // Recarga la galería.
            // Limpia los arrays de selección del modal.
            $this->selectedPhotosFromAlbumModalArray = [];
            $this->selectedLikedPhotosModalArray     = [];
            $this->selectedExistingPhotosModalArray  = [];
            session()->flash('cpg_modal_message', count($attach).' fotos añadidas.'); // Mensaje de éxito.
        } else {
            session()->flash('cpg_modal_error', 'Las fotos ya estaban en la colección.'); // Mensaje si todas ya existían.
        }
    }

    /**
     * Elimina una foto de la asociación con esta galería (no borra el archivo Photo).
     * @param int $photoId El ID de la foto a desasociar.
     */
    public function removeFromCollection(int $photoId)
    {
        if (! $this->isAdmin) return; // Solo para administradores.
        // Usa el método detach() de la relación para eliminar el registro de la tabla pivote.
        $this->collectionConfig->photos()->detach($photoId);
        $this->loadPhotosForDisplay(); // Recarga la galería.
        session()->flash('cpg_message', 'Foto eliminada.'); // Mensaje de éxito (para la vista principal).
    }

    /**
     * Actualiza el orden de las fotos en la galería.
     * Este método está pensado para ser llamado por una librería de arrastrar y soltar (ej. SortableJS).
     * @param array $orderedItems Array de objetos, donde cada objeto tiene 'value' (ID) y 'order' (nuevo índice).
     */
    public function updateCollectionPhotoOrder($orderedItems)
    {
        if (! $this->isAdmin) return; // Solo para administradores.
        foreach ($orderedItems as $item) {
            // Actualiza el campo 'order' en la tabla pivote para la foto y galería específicas.
            $this->collectionConfig->photos()->updateExistingPivot($item['value'], ['order' => $item['order']]);
        }
        $this->loadPhotosForDisplay(); // Recarga la galería para reflejar el nuevo orden.
        session()->flash('cpg_message', 'Orden actualizado.'); // Mensaje de éxito.
    }

    /**
     * Renderiza la vista del componente.
     * Pasa los datos necesarios a la plantilla Blade.
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        // Retorna la vista Blade 'livewire.curated-portrait-gallery'.
        // Las propiedades públicas del componente estarán automáticamente disponibles en la vista.
        return view('livewire.curated-portrait-gallery');
    }
}
