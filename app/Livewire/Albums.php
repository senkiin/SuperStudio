<?php

namespace App\Livewire;

// Importaciones de Modelos y Fachadas de Laravel/Livewire
use App\Jobs\ProcessPhotoThumbnail; // Job para procesar miniaturas de fotos en segundo plano.
use App\Models\Album;              // Modelo Eloquent para los álbumes.
use App\Models\Photo;              // Modelo Eloquent para las fotos.
use App\Models\User;               // Modelo Eloquent para los usuarios (usado para clientes y creadores).
use Illuminate\Support\Facades\Auth; // Fachada para acceder a la información del usuario autenticado.
use Illuminate\Support\Facades\Log;  // Fachada para registrar logs, útil para depuración.
use Illuminate\Support\Facades\Storage; // Fachada para interactuar con el sistema de archivos (ej. S3).
use Illuminate\Support\Facades\DB;   // Fachada para realizar consultas directas a la base de datos (usado para 'likes').
use Illuminate\Validation\Rule;      // Clase Rule para validaciones más complejas (ej. 'requiredIf').
use Livewire\Attributes\Computed;    // Atributo para definir propiedades computadas (cacheadas).
use Livewire\Attributes\On;          // Atributo para definir listeners de eventos Livewire.
use Livewire\Attributes\Validate;    // Atributo para definir reglas de validación directamente en propiedades.
use Livewire\Component;            // Clase base para todos los componentes Livewire.
use Livewire\WithFileUploads;      // Trait para manejar subida de archivos.
use Livewire\WithPagination;       // Trait para habilitar la paginación de forma sencilla.
use Intervention\Image\Laravel\Facades\Image;

class Albums extends Component
{
    use WithPagination; // Habilita la paginación para la lista de álbumes y fotos.
    use WithFileUploads; // Habilita la subida de archivos en el componente.

    // --- Propiedades para el Modal de Edición de Álbum ---
    public bool $showEditAlbumModal = false; // Controla la visibilidad del modal para editar un álbum.
    public ?Album $editingAlbum = null;    // Almacena la instancia del álbum que se está editando.
    #[Validate] public string $editAlbumName = ''; // Nombre del álbum en edición (con validación automática).
    #[Validate] public string $editAlbumDescription = ''; // Descripción del álbum en edición.
    #[Validate] public string $editAlbumType = ''; // Tipo de álbum en edición (public, private, client).
    #[Validate] public ?string $editAlbumClientId = ''; // ID del cliente asociado al álbum (si es de tipo 'client').
    #[Validate('nullable|image|max:5120')] public $editAlbumNewCover = null; // Nuevo archivo de portada para el álbum en edición.
    public ?string $editAlbumCurrentCover = null; // Ruta de la portada actual del álbum en edición.
    public string $editAlbumPassword = ''; // Contraseña del álbum en edición.
    public bool $editAlbumIsPublicGallery = false; // Si el álbum aparece en la galería pública.

    // --- Propiedades para Búsqueda y Ordenación de Álbumes ---
    public string $cadena = ''; // Término de búsqueda para filtrar álbumes por nombre o descripción.
    public string $campo  = 'created_at'; // Campo por el cual se ordenan los álbumes (por defecto, fecha de creación).
    public string $order  = 'desc'; // Dirección de la ordenación ('asc' o 'desc').

    // --- Propiedades para el Modal de Galería (Visualización de Fotos de un Álbum) ---
    public bool $showModal = false; // Controla la visibilidad del modal que muestra las fotos de un álbum seleccionado.
    public ?Album $selectedAlbum = null; // Almacena la instancia del álbum cuyas fotos se están mostrando en el modal.

    // --- Propiedades para Selección Múltiple de Fotos ---
    public array $selectedPhotos = []; // Array para almacenar los IDs de las fotos seleccionadas.
    public bool $selectionMode = false; // Indica si el modo de selección múltiple de fotos está activo.

    // --- Propiedades para Subida de Fotos ---
    #[Validate(['uploadedPhotos.*' => 'image|max:51200'])] // Regla de validación para cada foto subida: imagen, máx 50MB.
    public $uploadedPhotos = []; // Array para almacenar los archivos de fotos subidos temporalmente.

    // --- Propiedades para el Visor de Fotos (Lightbox) ---
    public ?Photo $viewingPhoto = null; // Almacena la instancia de la foto que se está viendo en el lightbox.
    public bool $showPhotoViewer = false; // Controla la visibilidad del modal del visor de fotos.

    // --- Propiedades para el Modal de Creación de Álbum ---
    public bool $showCreateAlbumModal = false; // Controla la visibilidad del modal para crear un nuevo álbum.
    #[Validate('required|string|max:191')]  public string $newAlbumName = ''; // Nombre para el nuevo álbum.
    #[Validate('nullable|string|max:1000')] public string $newAlbumDescription = ''; // Descripción para el nuevo álbum.
    #[Validate('required|in:public,private,client')] public string $newAlbumType = 'public'; // Tipo para el nuevo álbum.
    #[Validate('nullable|image|max:5120')]  public $newAlbumCover = null; // Archivo de portada para el nuevo álbum.
    public ?string $newAlbumClientId = ''; // ID del cliente para el nuevo álbum (si es tipo 'client').
    public string $newAlbumPassword = ''; // Contraseña para el nuevo álbum (opcional).
    public bool $newAlbumIsPublicGallery = false; // Si el álbum debe aparecer en la galería pública.

    // --- Propiedades para Selección de Cliente ---
    public $clients = []; // Colección de usuarios (clientes) para seleccionar en los modales.
    public string $clientSearchEmail = ''; // Término de búsqueda para filtrar la lista de clientes.

    /**
     * Define las reglas de validación para la actualización de un álbum.
     * Se usa explícitamente al llamar a $this->validate($this->rulesForUpdate()).
     * @return array
     */
    protected function rulesForUpdate(): array
    {
        return [
            'editAlbumName'        => 'required|string|max:191',
            'editAlbumDescription' => 'nullable|string|max:1000',
            'editAlbumType'        => 'required|in:public,private,client',
            'editAlbumNewCover'    => 'nullable|image|max:5120', // 5MB
            'editAlbumClientId'    => [ // Regla condicional: obligatorio si el tipo es 'client'.
                Rule::requiredIf(fn() => $this->editAlbumType === 'client'),
                'nullable','integer','exists:users,id' // Debe ser un ID de usuario existente.
            ],
            'editAlbumPassword'    => 'nullable|string|max:255',
            'editAlbumIsPublicGallery' => 'boolean',
        ];
    }

    /**
     * Define las reglas de validación para la creación de un nuevo álbum.
     * Se usa cuando se llama a $this->validate() sin argumentos o con $this->rules().
     * @return array
     */
    protected function rules(): array
    {
        return [
            'newAlbumName'        => 'required|string|max:191',
            'newAlbumDescription' => 'nullable|string|max:1000',
            'newAlbumType'        => 'required|in:public,private,client',
            'newAlbumCover'       => 'nullable|image|max:5120', // 5MB
            'newAlbumClientId'    => [ // Regla condicional.
                Rule::requiredIf(fn() => $this->newAlbumType === 'client'),
                'nullable','integer','exists:users,id'
            ],
            'newAlbumPassword'    => 'nullable|string|max:255',
            'newAlbumIsPublicGallery' => 'boolean',
        ];
    }

    // --- Lógica de Búsqueda de Clientes ---

    /**
     * Refresca la lista de clientes disponibles para seleccionar.
     * Filtra por nombre o email si $clientSearchEmail tiene valor.
     */
    private function refreshClientsList(): void
    {
        $query = User::where('role', 'user'); // Solo usuarios con rol 'user'.

        if (!empty($this->clientSearchEmail)) {
            $searchTerm = '%' . $this->clientSearchEmail . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('email', 'like', $searchTerm);
            });
        }

        $query->orderBy('name');
        $this->clients = $query->limit(50)->get(['id', 'name', 'email']); // Limita a 50 resultados.
    }

    /**
     * Hook de Livewire: Se ejecuta cuando la propiedad $clientSearchEmail se actualiza.
     * Refresca la lista de clientes si el modal correspondiente está abierto y el tipo de álbum es 'client'.
     */
    public function updatedClientSearchEmail(): void
    {
        if (($this->showCreateAlbumModal && $this->newAlbumType === 'client') ||
            ($this->showEditAlbumModal && $this->editAlbumType === 'client')) {
            $this->refreshClientsList();
        }
    }

    /**
     * Hook de Livewire: Se ejecuta cuando la propiedad $newAlbumType (tipo de álbum en creación) se actualiza.
     * Si el nuevo tipo es 'client', refresca la lista de clientes.
     * @param string $value El nuevo valor de $newAlbumType.
     */
    public function updatedNewAlbumType(string $value): void
    {
        $this->newAlbumClientId = ''; // Resetea el cliente seleccionado.
        if ($value === 'client') {
            $this->clientSearchEmail = ''; // Limpia la búsqueda.
            $this->refreshClientsList();
        } else {
            $this->clients = []; // Vacía la lista si no es tipo 'client'.
        }
    }

    /**
     * Hook de Livewire: Se ejecuta cuando la propiedad $editAlbumType (tipo de álbum en edición) se actualiza.
     * Similar a updatedNewAlbumType, pero para el formulario de edición.
     * @param string $value El nuevo valor de $editAlbumType.
     */
    public function updatedEditAlbumType(string $value): void
    {
        $this->editAlbumClientId = '';
        if ($value === 'client') {
            $this->clientSearchEmail = '';
            $this->refreshClientsList();
        } else {
            $this->clients = [];
        }
    }

    // --- Lógica de Ordenación y Búsqueda de Álbumes ---

    /**
     * Cambia el campo y/o la dirección de ordenación para la lista de álbumes.
     * @param string $field El campo por el cual ordenar ('name', 'created_at', 'type').
     */
    public function sortBy(string $field): void
    {
        $allowed = ['name','created_at','type']; // Campos permitidos para ordenar.
        if (! in_array($field, $allowed)) return; // Ignora si el campo no es válido.

        if ($this->campo === $field) { // Si se hace clic en el mismo campo, invierte el orden.
            $this->order = $this->order === 'asc' ? 'desc' : 'asc';
        } else { // Si es un campo nuevo, ordena ascendentemente por defecto.
            $this->campo = $field;
            $this->order = 'asc';
        }
        $this->resetPage('albumsPage'); // Resetea la paginación de álbumes.
    }

    /**
     * Hook de Livewire: Se ejecuta cuando la propiedad $cadena (búsqueda de álbumes) se está actualizando.
     * Resetea la paginación de álbumes y, si el modal de galería está abierto, también la de fotos.
     */
    public function updatingCadena(): void
    {
        $this->resetPage('albumsPage');
        if ($this->showModal) { // Si el modal de fotos está abierto.
            $this->resetPage('photosPage');
        }
    }

    // --- Abrir / Cerrar Modal de Galería (Visualización de Fotos) ---

    /**
     * Abre el modal para mostrar las fotos del álbum con el ID especificado.
     * @param int $albumId El ID del álbum a mostrar.
     */
    public function openModal(int $albumId): void
    {
        $this->selectedAlbum = Album::with('user')->find($albumId); // Carga el álbum con su creador.
        $this->closePhotoViewer(); // Cierra el visor de fotos si estaba abierto.
        $this->reset(['selectedPhotos','uploadedPhotos','selectionMode']); // Resetea propiedades relacionadas con la selección y subida.
        $this->showModal = true; // Muestra el modal.
        $this->resetPage('photosPage'); // Resetea la paginación de las fotos dentro del modal.
    }

    /**
     * Cierra el modal de galería.
     * Despacha un evento JS para resetear el estado del modal después de la transición de cierre.
     */
    public function closeModal(): void
    {
        $this->closePhotoViewer();
        $this->showModal = false;
        // Despacha un evento JS para que se ejecute después de que el modal se oculte visualmente.
        $this->js("setTimeout(() => Livewire.dispatch('resetModalState'), 300)");
    }

    /**
     * Listener de Eventos Livewire: Se ejecuta cuando se recibe el evento 'resetModalState'.
     * Resetea las propiedades del modal de galería.
     */
    #[On('resetModalState')]
    public function resetModalState(): void
    {
        $this->reset(['selectedAlbum','selectedPhotos','uploadedPhotos','selectionMode']);
    }

    // --- Selección Múltiple de Fotos ---

    /**
     * Activa o desactiva el modo de selección múltiple de fotos.
     * Si se desactiva, limpia las fotos seleccionadas.
     */
    public function toggleSelectionMode(): void
    {
        $this->selectionMode = ! $this->selectionMode;
        if (! $this->selectionMode) {
            $this->reset('selectedPhotos');
        }
    }

    /**
     * Añade o quita una foto de la lista de selección múltiple.
     * Solo funciona si $selectionMode está activo.
     * @param int $photoId El ID de la foto a seleccionar/deseleccionar.
     */
    public function toggleSelection(int $photoId): void
    {
        if (! $this->selectionMode) return;

        if (in_array($photoId, $this->selectedPhotos)) { // Si ya está seleccionada, la quita.
            $this->selectedPhotos = array_diff($this->selectedPhotos, [$photoId]);
        } else { // Si no, la añade.
            $this->selectedPhotos[] = $photoId;
        }
        $this->selectedPhotos = array_values($this->selectedPhotos); // Reindexa el array.
    }

    // --- Guardar Fotos Subidas ---

    /**
     * Procesa y guarda las fotos subidas al álbum actualmente seleccionado.
     * Crea registros en la BD y encola un job para generar miniaturas.
     */
    public function savePhotos(): void
    {
        $this->validateOnly('uploadedPhotos'); // Valida solo los archivos subidos.
        $user = Auth::user();

        // Verifica permisos: el usuario debe estar autenticado, debe haber un álbum seleccionado,
        // y el usuario debe ser admin o el propietario del álbum.
        if (! $user
            || ! $this->selectedAlbum
            || ($user->role !== 'admin' && $this->selectedAlbum->user_id !== $user->id)
        ) {
            session()->flash('error','No tienes permiso para añadir fotos.');
            $this->reset('uploadedPhotos');
            return;
        }
        if (empty($this->uploadedPhotos)) { // No hace nada si no hay fotos para subir.
            return;
        }

        $disk = 'albums'; // Disco S3 configurado para álbumes.
        $basePath = "{$this->selectedAlbum->id}"; // Ruta base dentro del disco (ID del álbum).
        Storage::disk($disk)->makeDirectory("{$basePath}/photos"); // Crea directorio para fotos originales.
        Storage::disk($disk)->makeDirectory("{$basePath}/thumbnails"); // Crea directorio para miniaturas.

        foreach ($this->uploadedPhotos as $file) {
            $path = $file->store("{$basePath}/photos", $disk); // Guarda el archivo y obtiene su ruta.
            $photo = Photo::create([ // Crea el registro en la BD.
                'album_id'       => $this->selectedAlbum->id,
                'file_path'      => $path,
                'thumbnail_path' => null, // La miniatura se generará después.
                'uploaded_by'    => $user->id,
            ]);
            // Encola un job para generar la miniatura en segundo plano.
            ProcessPhotoThumbnail::dispatch($photo)->onQueue('image-processing');
        }

        $this->reset('uploadedPhotos'); // Limpia el array de archivos subidos.
        session()->flash('message','Fotos subidas. Miniaturas en proceso.');
        $this->resetPage('photosPage'); // Resetea la paginación de fotos en el modal.
    }




    // --- Eliminar Fotos Seleccionadas ---

    /**
     * Elimina las fotos seleccionadas del álbum actual y de S3.
     */
    public function deleteSelectedPhotos(): void
    {
        if (empty($this->selectedPhotos)) return; // No hace nada si no hay fotos seleccionadas.
        $user = Auth::user();

        // Verifica permisos.
        if (! $user
            || ! $this->selectedAlbum
            || ($user->role !== 'admin' && $this->selectedAlbum->user_id !== $user->id)
        ) {
            session()->flash('error','No tienes permiso para borrar fotos.');
            $this->reset(['selectedPhotos','selectionMode']);
            return;
        }

        // Obtiene las fotos de la BD que coinciden con las seleccionadas y pertenecen al álbum actual.
        $photos = Photo::whereIn('id',$this->selectedPhotos)
            ->where('album_id',$this->selectedAlbum->id)
            ->get();

        if ($photos->isEmpty()) { // Si no se encontraron fotos válidas.
            $this->reset(['selectedPhotos','selectionMode']);
            return;
        }

        $disk = 'albums';
        // Recopila todas las rutas de archivos (originales y miniaturas) para eliminar de S3.
        $filesToDelete = $photos->flatMap(fn($photo) => array_filter([$photo->file_path, $photo->thumbnail_path]))->all();

        if (!empty($filesToDelete)) {
            Storage::disk($disk)->delete($filesToDelete); // Elimina los archivos de S3.
        }
        Photo::destroy($this->selectedPhotos); // Elimina los registros de la BD.

        $this->reset(['selectedPhotos','selectionMode']); // Resetea la selección.
        session()->flash('message','Fotos eliminadas.');
        $this->resetPage('photosPage');
    }

    // --- Visor de Fotos (Lightbox) ---

    /**
     * Propiedad computada: Obtiene el ID de la foto anterior a la actual en el visor.
     * @return int|null
     */
    #[Computed]
    public function previousPhotoId(): ?int
    {
        if (! $this->viewingPhoto || ! $this->selectedAlbum) return null;
        // Busca la foto con el ID máximo que sea menor al ID de la foto actual.
        return $this->selectedAlbum
            ->photos()
            ->where('id','<',$this->viewingPhoto->id)
            ->orderBy('id', 'desc')
            ->max('id');
    }

    /**
     * Propiedad computada: Obtiene el ID de la foto siguiente a la actual en el visor.
     * @return int|null
     */
    #[Computed]
    public function nextPhotoId(): ?int
    {
        if (! $this->viewingPhoto || ! $this->selectedAlbum) return null;
        // Busca la foto con el ID mínimo que sea mayor al ID de la foto actual.
        return $this->selectedAlbum
            ->photos()
            ->where('id','>',$this->viewingPhoto->id)
            ->orderBy('id', 'asc')
            ->min('id');
    }

    /**
     * Navega a la foto anterior en el visor.
     */
    public function viewPreviousPhoto(): void
    {
        if ($id = $this->previousPhotoId) { // Usa la propiedad computada.
            $this->viewPhoto($id);
        }
    }

    /**
     * Navega a la foto siguiente en el visor.
     */
    public function viewNextPhoto(): void
    {
        if ($id = $this->nextPhotoId) { // Usa la propiedad computada.
            $this->viewPhoto($id);
        }
    }

    /**
     * Muestra una foto específica en el visor (lightbox).
     * @param int $photoId El ID de la foto a mostrar.
     */
    public function viewPhoto(int $photoId): void
    {
        $photo = Photo::find($photoId);
        // Verifica que la foto exista y pertenezca al álbum seleccionado.
        if ($photo && $this->selectedAlbum && $photo->album_id === $this->selectedAlbum->id) {
            $this->viewingPhoto    = $photo;
            $this->showPhotoViewer = true; // Muestra el modal del visor.
        }
    }

    /**
     * Cierra el visor de fotos.
     */
    public function closePhotoViewer(): void
    {
        $this->showPhotoViewer = false;
        $this->viewingPhoto    = null;
    }

    // --- Creación de Álbum ---

    /**
     * Abre el modal para crear un nuevo álbum.
     * Resetea los campos del formulario y la validación.
     */
    public function openCreateAlbumModal(): void
    {
        $this->resetValidation();
        $this->reset([
            'newAlbumName','newAlbumDescription',
            'newAlbumType','newAlbumCover','newAlbumClientId',
            'newAlbumPassword','newAlbumIsPublicGallery',
            'clientSearchEmail'
        ]);
        $this->newAlbumType = 'public'; // Tipo por defecto.
        $this->clients = []; // La lista de clientes se poblará si se elige tipo 'client'.
        $this->showCreateAlbumModal = true;
    }

    /**
     * Cierra el modal de creación de álbum.
     */
    public function closeCreateAlbumModal(): void
    {
        $this->showCreateAlbumModal = false;
        $this->resetValidation();
        $this->reset([
            'newAlbumName','newAlbumDescription',
            'newAlbumType','newAlbumCover','newAlbumClientId',
            'newAlbumPassword','newAlbumIsPublicGallery',
            'clientSearchEmail'
        ]);
        $this->clients = [];
    }

    /**
     * Valida los datos y crea un nuevo álbum en la base de datos.
     * Solo los administradores pueden crear álbumes.
     */
    public function createAlbum(): void
    {
        $user = Auth::user();
        if (! $user || $user->role !== 'admin') { // Verifica permiso de administrador.
            session()->flash('error', 'Solo los administradores pueden crear álbumes.');
            return;
        }
        $data = $this->validate(); // Valida los campos del formulario de creación.

        $coverPath = null;
        if ($this->newAlbumCover) { // Si se subió una portada.
            try {
                $coverPath = $this->newAlbumCover->store('covers','albums'); // Guarda la portada.
            } catch (\Exception $e) {
                $this->addError('newAlbumCover','Error al subir portada: ' . $e->getMessage());
                Log::error("Error al subir portada para nuevo álbum: {$e->getMessage()}");
                return;
            }
        }

        // Determina el client_id: si es tipo 'client' y se seleccionó un cliente, usa ese ID.
        // Si es 'public', el client_id es el ID del admin que lo crea.
        // Si es 'private', el client_id también es el del admin (o podría ser null si se prefiere).
        $finalClientId = ($data['newAlbumType'] === 'client' && !empty($data['newAlbumClientId']))
            ? $data['newAlbumClientId']
            : $user->id; // Para 'public' o 'private' sin cliente específico, se asocia al admin.

        Album::create([
            'name'        => $data['newAlbumName'],
            'description' => $data['newAlbumDescription'],
            'type'        => $data['newAlbumType'],
            'user_id'     => $user->id, // El creador siempre es el admin.
            'cover_image' => $coverPath,
            'client_id'   => $finalClientId,
            'password'    => $data['newAlbumPassword'] ?: null,
            'is_public_gallery' => $data['newAlbumIsPublicGallery'],
        ]);
        $this->closeCreateAlbumModal();
        session()->flash('message','Álbum creado exitosamente.');
    }

    // --- Edición de Álbum ---

    /**
     * Abre el modal para editar un álbum existente.
     * Carga los datos del álbum en el formulario.
     * @param int $albumId El ID del álbum a editar.
     */
    public function openEditAlbumModal(int $albumId): void
    {
        $this->resetValidation();
        $album = Album::find($albumId);
        if (! $album) {
            session()->flash('error', 'Álbum no encontrado.');
            return;
        }
        $user = Auth::user();
        // Verifica permisos: admin o propietario del álbum.
        if (!$user || ($user->role!=='admin' && $album->user_id!==$user->id)) {
            session()->flash('error','No tienes permiso para editar este álbum.');
            return;
        }
        // Carga los datos del álbum en las propiedades del formulario de edición.
        $this->editingAlbum         = $album;
        $this->editAlbumName        = $album->name;
        $this->editAlbumDescription = $album->description ?? '';
        $this->editAlbumType        = $album->type;
        $this->editAlbumClientId    = $album->client_id ? (string)$album->client_id : ''; // Convierte a string para el select.
        $this->editAlbumCurrentCover= $album->cover_image;
        $this->editAlbumNewCover    = null; // Limpia el campo de nueva portada.
        $this->editAlbumPassword    = $album->password ?? '';
        $this->editAlbumIsPublicGallery = $album->is_public_gallery ?? false;

        $this->clientSearchEmail = '';
        $this->clients = [];
        if ($this->editAlbumType === 'client') { // Si es tipo 'client', carga la lista de clientes.
            $this->refreshClientsList();
        }
        $this->showEditAlbumModal = true;
    }

    /**
     * Cierra el modal de edición de álbum.
     */
    public function closeEditAlbumModal(): void
    {
        $this->showEditAlbumModal = false;
        $this->resetValidation();
        $this->reset([
            'editingAlbum','editAlbumName','editAlbumDescription',
            'editAlbumType','editAlbumClientId','editAlbumNewCover',
            'editAlbumCurrentCover','editAlbumPassword','editAlbumIsPublicGallery',
            'clientSearchEmail'
        ]);
        $this->clients = [];
    }

    /**
     * Valida los datos y actualiza el álbum en la base de datos.
     */
    public function updateAlbum(): void
    {
        if (! $this->editingAlbum) return; // No hace nada si no hay un álbum en edición.
        $user = Auth::user();
        // Verifica permisos.
        if (! $user || ($user->role!=='admin' && $this->editingAlbum->user_id!==$user->id)) {
            session()->flash('error','No tienes permiso para editar este álbum.');
            $this->closeEditAlbumModal();
            return;
        }
        $data = $this->validate($this->rulesForUpdate()); // Valida usando las reglas de actualización.

        $coverPath = $this->editingAlbum->cover_image; // Mantiene la portada actual por defecto.
        if ($this->editAlbumNewCover) { // Si se subió una nueva portada.
            try {
                if ($coverPath) { // Si había una portada anterior, la elimina de S3.
                    Storage::disk('albums')->delete($coverPath);
                }
                $coverPath = $this->editAlbumNewCover->store('covers','albums'); // Guarda la nueva portada.
            } catch (\Exception $e) {
                $this->addError('editAlbumNewCover','Error al subir nueva portada: ' . $e->getMessage());
                Log::error("Error al actualizar portada álbum [ID:{$this->editingAlbum->id}]: {$e->getMessage()}");
                return;
            }
        }

        // Lógica para client_id al editar:
        // Si el tipo es 'client' y se ha seleccionado un cliente, se usa ese ID.
        // Si el tipo es 'public', el client_id se establece al ID del usuario admin (el creador).
        // Si el tipo es 'private' (y no 'client'), el client_id se pone a null (o podría ser el admin ID si se prefiere).
        $finalClientId = ($data['editAlbumType'] === 'client' && !empty($data['editAlbumClientId']))
            ? $data['editAlbumClientId']
            : (($data['editAlbumType'] === 'public' || $data['editAlbumType'] === 'private') ? $this->editingAlbum->user_id : null);
            // Corrección: Si es público o privado, el client_id debería ser el user_id del creador (admin) o null si es privado y no asociado.
            // Si el álbum era 'client' y se cambia a 'public' o 'private', el client_id anterior se borra (se pone a user_id o null).

        $this->editingAlbum->update([
            'name'        => $data['editAlbumName'],
            'description' => $data['editAlbumDescription'],
            'type'        => $data['editAlbumType'],
            'cover_image' => $coverPath,
            'client_id'   => $finalClientId,
            'password'    => $data['editAlbumPassword'] ?: null,
            'is_public_gallery' => $data['editAlbumIsPublicGallery'],
        ]);
        $this->closeEditAlbumModal();
        session()->flash('message','Álbum actualizado exitosamente.');
    }

    // --- Eliminación de Álbum ---

    /**
     * Elimina un álbum, sus fotos asociadas y los archivos de S3.
     * @param int $albumId El ID del álbum a eliminar.
     */
    public function deleteAlbum(int $albumId): void
    {
        $album = Album::with('photos')->findOrFail($albumId); // Carga el álbum con sus fotos.
        $user  = Auth::user();
        // Verifica permisos (admin o propietario).
        if (! $user || ($user->role!=='admin' && $album->user_id!==$user->id)) {
            session()->flash('error','No tienes permiso para eliminar este álbum.');
            return;
        }
        try {
            $disk = 'albums';
            $filesToDelete = [];
            if ($album->cover_image) { // Añade la portada a la lista de archivos a borrar.
                $filesToDelete[] = $album->cover_image;
            }
            foreach ($album->photos as $photo) { // Añade todas las fotos y sus miniaturas.
                if ($photo->file_path) $filesToDelete[] = $photo->file_path;
                if ($photo->thumbnail_path) $filesToDelete[] = $photo->thumbnail_path;
            }

            if(!empty($filesToDelete)){
                Storage::disk($disk)->delete($filesToDelete); // Borra los archivos de S3.
            }

            $album->photos()->delete(); // Elimina los registros de fotos de la BD.
            $album->delete(); // Elimina el registro del álbum de la BD.

            session()->flash('message','Álbum y todas sus fotos han sido eliminados.');
            $this->resetPage('albumsPage'); // Resetea la paginación de álbumes.
        } catch (\Exception $e) {
            Log::error("Error eliminando álbum [ID:{$albumId}]: {$e->getMessage()}");
            session()->flash('error','Ocurrió un error al eliminar el álbum.');
        }
    }

    // --- "Me Gusta" en Fotos ---

    /**
     * Añade o quita el "Me Gusta" de un usuario a una foto específica dentro del álbum seleccionado.
     * @param int $photoId El ID de la foto.
     */
    public function toggleLike(int $photoId): void
    {
        $user = Auth::user();
        if (! $user || ! $this->selectedAlbum) { // Requiere usuario autenticado y álbum seleccionado.
            return;
        }
        // Busca la foto para asegurarse de que pertenece al álbum actual.
        $photo = Photo::where('id',$photoId)
            ->where('album_id',$this->selectedAlbum->id)
            ->first();

        if (! $photo) return; // Si la foto no es válida, no hace nada.

        // Busca si ya existe un "like" del usuario para esta foto.
        $likeEntry = DB::table('photo_user_likes')
            ->where('user_id',$user->id)
            ->where('photo_id',$photoId);

        if ($likeEntry->exists()) { // Si ya existe, lo elimina (unlike).
            $likeEntry->delete();
        } else { // Si no existe, lo crea (like).
            DB::table('photo_user_likes')->insert([
                'user_id'    => $user->id,
                'photo_id'   => $photoId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        // Livewire debería refrescar la sección de fotos automáticamente debido al cambio de datos.
        // $this->dispatch('$refresh'); // Opcional, para forzar un refresco completo si es necesario.
    }

    /**
     * Renderiza la vista del componente.
     * Obtiene los álbumes y las fotos (si un álbum está seleccionado) para pasarlos a la vista.
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        $user  = Auth::user();
        $query = Album::query(); // Inicia la consulta de álbumes.

        // Lógica de permisos para ver álbumes:
        if ($user && $user->role !== 'admin') { // Si es un usuario normal (no admin).
            $query->where(function($q) use ($user) {
                $q->where('user_id', $user->id) // Puede ver sus propios álbumes.
                  ->orWhere('client_id', $user->id) // Puede ver álbumes donde es cliente.
                  ->orWhere('type', 'public'); // Puede ver álbumes públicos.
            });
        } elseif (! $user) { // Si no está autenticado (invitado).
            $query->where('type', 'public'); // Solo puede ver álbumes públicos.
        }
        // Si es admin, no se aplican filtros de permiso aquí (ve todos).

        if (! empty($this->cadena)) { // Si hay un término de búsqueda.
            $search = '%'.$this->cadena.'%';
            $query->where(function($q) use ($search) {
                $q->where('name','like',$search) // Busca por nombre.
                  ->orWhere('description','like',$search); // Busca por descripción.
            });
        }

        // Aplica la ordenación y carga relaciones necesarias.
        $query->with(['user', 'clientUser']) // Carga el creador (user) y el cliente asociado (clientUser).
              ->withCount('photos')         // Carga la cuenta de fotos para cada álbum.
              ->orderBy(
                  in_array($this->campo,['name','created_at','type']) // Valida el campo de ordenación.
                      ? $this->campo
                      : 'created_at', // Campo por defecto si no es válido.
                  $this->order === 'asc' ? 'asc' : 'desc' // Dirección de ordenación.
              );

        $albums      = $query->paginate(12,['*'],'albumsPage'); // Pagina los álbumes.
        $photosInModal = null; // Inicializa las fotos del modal como nulas.

        if ($this->showModal && $this->selectedAlbum) { // Si el modal de galería está abierto y hay un álbum seleccionado.
            // Obtiene las fotos del álbum seleccionado, paginadas.
            // También carga si el usuario actual le ha dado "like" a cada foto.
            $photosInModal = $this->selectedAlbum
                ->photos()
                ->withExists(['likedByUsers as liked_by_current_user'=> function($q){ $q->where('user_id',Auth::id()); }])
                ->orderBy('id') // Ordena las fotos por ID.
                ->paginate(15,['*'],'photosPage'); // Pagina las fotos.
        }

        // Retorna la vista Blade y le pasa los datos.
        return view('livewire.albums', [
            'albums'        => $albums,
            'photosInModal' => $photosInModal,
        ]);
    }
}
