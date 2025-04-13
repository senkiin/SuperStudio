<?php

namespace App\Livewire;

// --- Importaciones de Clases y Facades Necesarios ---
use App\Jobs\ProcessPhotoThumbnail; // Job para procesar miniaturas en segundo plano
use App\Models\Album;               // Modelo Eloquent para Álbumes
use App\Models\Photo;               // Modelo Eloquent para Fotos
use App\Models\User;                // Modelo Eloquent para Usuarios
use Illuminate\Support\Facades\Auth; // Facade para interactuar con el sistema de autenticación
use Illuminate\Support\Facades\Log;  // Facade para escribir en los logs de Laravel (útil para depuración)
use Illuminate\Support\Facades\Storage; // Facade para interactuar con el sistema de archivos (local, s3, etc.)
use Illuminate\Validation\Rule;      // Clase para construir reglas de validación avanzadas
use Livewire\Attributes\Computed;    // Atributo para propiedades computadas (cacheables)
use Livewire\Attributes\On;          // Atributo para escuchar eventos de Livewire/JS
use Livewire\Attributes\Validate;    // Atributo para validación de propiedades (PHP 8+)
use Livewire\Component;             // Clase base para componentes Livewire
use Livewire\WithFileUploads;       // Trait que habilita la subida de archivos en Livewire
use Livewire\WithPagination;        // Trait que habilita la paginación automática de Livewire

// --- Definición del Componente Livewire 'Albums' ---
class Albums extends Component
{
    // --- Uso de Traits ---
    use WithPagination;    // Añade funcionalidad de paginación
    use WithFileUploads;   // Añade funcionalidad para subir archivos

    // ============================================================
    // Propiedades Públicas (El "Estado" del Componente)
    // Estas propiedades están disponibles en la vista Blade y se sincronizan.
    // ============================================================
    // -- Para Modal Editar Álbum --
    public bool $showEditAlbumModal = false; // Controla visibilidad modal edición
    public ?Album $editingAlbum = null; // Guarda el álbum que se está editando
    // Propiedades para los campos del formulario de edición
    #[Validate] // Usaremos el método rulesForUpdate() para las reglas
    public string $editAlbumName = '';
    #[Validate]
    public string $editAlbumDescription = '';
    #[Validate]
    public string $editAlbumType = '';
    #[Validate]
    public ?string $editAlbumClientId = '';
    #[Validate('nullable|image|max:5120')] // Validación para nueva portada
    public $editAlbumNewCover = null; // Para el nuevo archivo de portada
    public ?string $editAlbumCurrentCover = null; // Para mostrar la portada actual


    // -- Para Búsqueda y Ordenación de la lista principal de Álbumes --
    public string $cadena = '';         // Vinculada al input de búsqueda. Guarda el término a buscar.
    public string $campo = 'created_at'; // Columna por la que se ordenan los álbumes por defecto.
    public string $order = 'desc';      // Dirección ('asc' o 'desc') de ordenación por defecto.

    // -- Para el Modal Principal (Galería de Fotos de un Álbum) --
    public bool $showModal = false;       // true si el modal de la galería está visible, false si no.
    public ?Album $selectedAlbum = null; // Guarda el objeto Album completo que se está viendo en el modal. Es Nullable.

    // -- Para Selección Múltiple de Fotos dentro del Modal de Galería --
    public array $selectedPhotos = [];  // Array que guarda los IDs de las fotos que el usuario marca.
    public bool $selectionMode = false; // true si el modo "Seleccionar Fotos" está activo, false si no.

    // -- Para Subida de Fotos dentro del Modal de Galería --
    // Reglas de validación para los archivos subidos (usando atributos PHP 8+)
    #[Validate(['uploadedPhotos.*' => 'image|max:51200'])] // Cada archivo debe ser imagen, max 50MB (¡Ajustar!)
    public $uploadedPhotos = [];       // Array temporal donde Livewire guarda los archivos seleccionados en el input 'file'.

    // -- Para Visor de Foto Grande (segundo modal) --
    public ?Photo $viewingPhoto = null;  // Guarda el objeto Photo que se está mostrando en el visor grande. Nullable.
    public bool $showPhotoViewer = false; // Controla si el visor grande está visible.

    // -- Para Modal de Creación de Nuevo Álbum --
    public bool $showCreateAlbumModal = false; // Controla si el modal de creación es visible.
    // Propiedades vinculadas a los campos del formulario de creación, con validación
    #[Validate('required|string|max:191')]
    public string $newAlbumName = '';
    #[Validate('nullable|string|max:1000')]
    public string $newAlbumDescription = '';
    #[Validate('required|in:public,private,client')] // ¡Asegúrate que estos tipos coincidan con tu BD!
    public string $newAlbumType = 'public';
    #[Validate('nullable|image|max:5120')] // Portada opcional, 5MB max (¡Ajustar!)
    public $newAlbumCover = null;          // Para el archivo de portada subido
    public ?string $newAlbumClientId = '';    // ID del cliente seleccionado (si type='client'). Nullable string.
    public $clients = [];                   // Array para guardar la lista de clientes (para el select).

    // --- Reglas de Validación para Edición ---
    protected function rulesForUpdate()
    {
        // Muy similar a 'rules', pero adaptado para edición
        return [
            'editAlbumName' => 'required|string|max:191',
            'editAlbumDescription' => 'nullable|string|max:1000',
            'editAlbumType' => 'required|in:public,private,client', // Ajusta tipos
            'editAlbumNewCover' => 'nullable|image|max:5120', // 5MB Max para nueva imagen
            'editAlbumClientId' => [
                Rule::requiredIf($this->editAlbumType === 'client'),
                'nullable', 'integer', 'exists:users,id'
            ],
        ];
    }
    // ============================================================
    // Reglas de Validación (Método para validación más compleja)
    // ============================================================
    protected function rules()
    {
        // Define las reglas para el formulario de creación de álbum.
        // Este método permite definir reglas más complejas, como las condicionales.
        return [
            'newAlbumName' => 'required|string|max:191', // Nombre requerido, máx 191 caracteres
            'newAlbumDescription' => 'nullable|string|max:1000', // Descripción opcional, máx 1000
            'newAlbumType' => 'required|in:public,private,client', // Tipo requerido, debe ser uno de estos
            'newAlbumCover' => 'nullable|image|max:5120', // Portada opcional, debe ser imagen, máx 5MB
            // Regla para el ID del cliente
            'newAlbumClientId' => [
                // Es requerido SOLO SI el tipo de álbum es 'client'
                Rule::requiredIf($this->newAlbumType === 'client'),
                'nullable', // Permitir que sea nulo (si el tipo no es client)
                'integer', // Debe ser un número entero
                'exists:users,id' // El ID debe existir en la tabla 'users'
            ],
        ];
    }

    // ============================================================
    // Métodos Públicos (Acciones llamadas desde la Vista Blade)
    // ============================================================

    // --- Lógica de Ordenación (Álbumes) ---
    // Se llama desde la vista para cambiar la columna o dirección de ordenación
    public function sortBy(string $field): void
    {
        $allowedSorts = ['name', 'created_at', 'type']; // Columnas permitidas para ordenar (¡Ajustar!)
        if (!in_array($field, $allowedSorts)) return; // Ignorar si no es válida

        if ($this->campo === $field) { // Si se pulsa en la misma columna, invertir orden
            $this->order = $this->order === 'asc' ? 'desc' : 'asc';
        } else { // Si se pulsa en otra columna, usarla y poner orden ascendente
            $this->campo = $field;
            $this->order = 'asc';
        }
        $this->resetPage('albumsPage'); // Volver a la página 1 de la lista de álbumes
    }

    // --- Lógica de Búsqueda ---
    // Este es un "hook" de Livewire. Se ejecuta JUSTO ANTES de que se actualice la propiedad $cadena.
    public function updatingCadena(): void
    {
        // Resetea la paginación de ambas listas para evitar problemas al filtrar
        $this->resetPage('albumsPage');
        $this->resetPage('photosPage');
    }

    // --- Lógica del Modal Principal (Galería) ---
    // Se llama al hacer clic en una tarjeta de álbum en la rejilla principal
    public function openModal(int $albumId)
    {
        $this->selectedAlbum = Album::with('user')->find($albumId); // Busca el álbum y carga su usuario
        $this->closePhotoViewer(); // Asegura que el visor de fotos grande esté cerrado
        $this->reset(['selectedPhotos', 'uploadedPhotos', 'selectionMode']); // Limpia estados previos del modal
        $this->showModal = true; // Marca el modal como visible (lo mostrará en la vista)
        $this->resetPage('photosPage'); // Resetea la paginación de las fotos DENTRO del modal
    }
    // Se llama al hacer clic en el botón 'X' o fuera del modal de galería
    public function closeModal()
    {
        $this->closePhotoViewer(); // Cierra también el visor de fotos por si acaso
        $this->showModal = false; // Oculta el modal
        // Dispara un evento JS después de un pequeño retraso para permitir la animación de cierre de Alpine
        // y luego llama al método resetModalState() de este componente.
        $this->js('setTimeout(() => { Livewire.dispatch(\'resetModalState\') }, 300)');
    }
    // Escucha el evento 'resetModalState' disparado por JS y resetea propiedades
    #[On('resetModalState')]
    public function resetModalState()
    {
        $this->reset(['selectedAlbum', 'selectedPhotos', 'uploadedPhotos', 'selectionMode']);
    }

    // --- Lógica de Selección Múltiple de Fotos ---
    // Se llama al pulsar el botón "Seleccionar Fotos" / "Cancelar Selección"
    public function toggleSelectionMode()
    {
        $this->selectionMode = !$this->selectionMode; // Invierte el estado del modo selección
        if (!$this->selectionMode) {
            $this->reset('selectedPhotos');
        } // Limpia selección si se desactiva
    }
    // Se llama al hacer clic en una foto SI $selectionMode es true
    public function toggleSelection(int $photoId)
    {
        if (!$this->selectionMode) return; // No hace nada si no está en modo selección
        // Comprueba si el ID ya está en el array
        if (in_array($photoId, $this->selectedPhotos)) {
            // Si está, lo quita del array
            $this->selectedPhotos = array_diff($this->selectedPhotos, [$photoId]);
        } else {
            // Si no está, lo añade
            $this->selectedPhotos[] = $photoId;
        }
        $this->selectedPhotos = array_values($this->selectedPhotos); // Reindexa el array
    }

    // --- Lógica de Subida de Fotos ---
    // Se llama al enviar el formulario de subida de fotos
    public function savePhotos()
    {
        $this->validateOnly('uploadedPhotos'); // Valida solo la propiedad $uploadedPhotos con su regla definida arriba
        $user = Auth::user(); // Obtiene el usuario autenticado

        // Comprobación de Permisos (Usando la columna 'role' directamente)
        // ¡ASEGÚRATE que $user->role existe y que 'admin' es el valor correcto!
        if (!$user || !$this->selectedAlbum || ($user->role != 'admin' && $this->selectedAlbum->user_id !== $user->id)) {
            session()->flash('error', 'No tienes permiso para añadir fotos a este álbum.'); // Mensaje de error
            $this->reset('uploadedPhotos'); // Limpiar los archivos seleccionados
            return; // Detener ejecución
        }

        // Si hay álbum seleccionado y archivos subidos válidos
        if (!empty($this->uploadedPhotos)) {
            $disk = 'public'; // Define el disco de Storage a usar (o 's3')
            $albumPathBase = "albums/{$this->selectedAlbum->id}"; // Define la carpeta base para este álbum

            // Asegura que los directorios de destino existan
            Storage::disk($disk)->makeDirectory("{$albumPathBase}/photos");
            Storage::disk($disk)->makeDirectory("{$albumPathBase}/thumbnails");

            // Itera sobre cada archivo subido
            foreach ($this->uploadedPhotos as $photoFile) {
                // Guarda el archivo original en 'storage/app/public/albums/{id}/photos'
                // El nombre del archivo será generado automáticamente por Laravel para ser único
                $path = $photoFile->store("{$albumPathBase}/photos", $disk);

                // Crea el registro en la tabla 'photos'
                $photo = Photo::create([
                    'album_id' => $this->selectedAlbum->id, // ID del álbum actual
                    'file_path' => $path, // Ruta del archivo original guardado
                    'thumbnail_path' => null, // El Job se encargará de esto
                    'uploaded_by' => $user->id, // ID del usuario que subió
                    'like' => false, // Valor inicial del 'like'
                ]);

                // Despacha el Job para generar el thumbnail en segundo plano
                // Lo envía a la cola llamada 'image-processing' (opcional)
                ProcessPhotoThumbnail::dispatch($photo)->onQueue('image-processing');
            }
            $this->reset('uploadedPhotos'); // Limpia el input de archivos subidos
            session()->flash('message', 'Fotos subidas. Las miniaturas se están procesando.'); // Mensaje de éxito
            $this->resetPage('photosPage'); // Vuelve a la página 1 de la galería del modal
            // $this->selectedAlbum->refresh(); // Opcional: Recargar datos del álbum si es necesario
        }
    }

    // --- Lógica de Like ---
    // Se llama al hacer clic simple en una foto SI $selectionMode es false
    public function toggleLike(int $photoId)
    {
        if ($this->selectionMode) return; // No hacer nada si se está en modo selección

        $photo = Photo::find($photoId); // Busca la foto por ID
        // Verifica que existe, que hay un álbum seleccionado y que la foto pertenece a ese álbum
        if ($photo && $this->selectedAlbum && $photo->album_id === $this->selectedAlbum->id) {
            $photo->like = !$photo->like; // Invierte el valor del campo 'like' (true a false, false a true)
            $photo->save(); // Guarda el cambio en la base de datos
        }
    }

    // --- Lógica de Borrado de Fotos Seleccionadas ---
    // Se llama al pulsar el botón 'Eliminar Selección'
    public function deleteSelectedPhotos()
    {
        if (empty($this->selectedPhotos)) return; // Salir si no hay fotos seleccionadas

        $user = Auth::user();
        // Comprobación de Permisos (¡Ajusta según tu lógica!)
        if (!$user || !$this->selectedAlbum || ($user->role !== 'admin' && $this->selectedAlbum->user_id !== $user->id)) {
            session()->flash('error', 'No tienes permiso para borrar estas fotos.');
            $this->reset(['selectedPhotos', 'selectionMode']);
            return;
        }

        // Busca los registros de las fotos a borrar (verificando que pertenecen al álbum actual)
        $photosToDelete = Photo::whereIn('id', $this->selectedPhotos)
            ->where('album_id', $this->selectedAlbum->id)
            ->get();

        if ($photosToDelete->isEmpty()) {
            $this->reset(['selectedPhotos', 'selectionMode']);
            return;
        }

        // Obtener las rutas de los archivos originales y miniaturas para borrarlos de Storage
        $pathsToDelete = $photosToDelete->pluck('file_path')->filter()->toArray();
        $thumbPathsToDelete = $photosToDelete->pluck('thumbnail_path')->filter()->toArray();
        $disk = 'public'; // O 's3'
        Storage::disk($disk)->delete($pathsToDelete); // Borra archivos originales
        Storage::disk($disk)->delete($thumbPathsToDelete); // Borra miniaturas

        // Borrar los registros de la base de datos usando los IDs
        Photo::destroy($this->selectedPhotos);

        // Limpiar estado y notificar
        $this->reset(['selectedPhotos', 'selectionMode']);
        session()->flash('message', 'Fotos eliminadas correctamente.');
        $this->resetPage('photosPage'); // Resetear paginación del modal
    }

    // --- Lógica Visor de Fotos Grande ---
    // Propiedades Computadas para obtener IDs de foto anterior/siguiente
    #[Computed] // Sin persist:true para asegurar recálculo
    public function previousPhotoId(): ?int
    {
        if (!$this->viewingPhoto || !$this->selectedAlbum) return null;
        // Busca el ID máximo MÁS PEQUEÑO que el actual, dentro del mismo álbum
        return $this->selectedAlbum->photos()->where('id', '<', $this->viewingPhoto->id)->max('id');
    }
    #[Computed] // Sin persist:true
    public function nextPhotoId(): ?int
    {
        if (!$this->viewingPhoto || !$this->selectedAlbum) return null;
        // Busca el ID mínimo MÁS GRANDE que el actual, dentro del mismo álbum
        return $this->selectedAlbum->photos()->where('id', '>', $this->viewingPhoto->id)->min('id');
    }
    // Métodos para navegar llamados por los botones '<' y '>'
    public function viewPreviousPhoto()
    {
        if ($id = $this->previousPhotoId) {
            $this->viewPhoto($id);
        }
    }
    public function viewNextPhoto()
    {
        if ($id = $this->nextPhotoId) {
            $this->viewPhoto($id);
        }
    }

    // Método para abrir el visor (llamado por Doble Clic en una foto de la galería)
    public function viewPhoto(int $photoId)
    {
        $this->viewingPhoto = Photo::find($photoId);
        // Comprobar pertenencia al álbum actual por seguridad
        if ($this->viewingPhoto && $this->selectedAlbum && $this->viewingPhoto->album_id === $this->selectedAlbum->id) {
            $this->showPhotoViewer = true; // Mostrar el visor grande
        } else {
            $this->viewingPhoto = null;
        } // Limpiar si no pertenece
    }
    // Método para cerrar el visor grande
    public function closePhotoViewer()
    {
        $this->showPhotoViewer = false;
        $this->viewingPhoto = null;
    }

    // --- Lógica Modal Crear Álbum ---
    // Abre el modal de creación y carga la lista de clientes
    public function openCreateAlbumModal()
    {
        $this->resetValidation(); // Limpiar errores de validación previos
        $this->reset(['newAlbumName', 'newAlbumDescription', 'newAlbumType', 'newAlbumCover', 'newAlbumClientId']);
        $this->newAlbumType = 'public'; // Resetear tipo a por defecto
        // Cargar usuarios con rol 'client' (¡Ajusta el rol si es diferente!)
        $this->clients = User::where('role', 'client')->orderBy('name')->get(['id', 'name', 'email']); // Añadido email
        $this->showCreateAlbumModal = true; // Mostrar el modal
    }
    // Cierra el modal de creación y limpia los campos y errores
    public function closeCreateAlbumModal()
    {
        $this->showCreateAlbumModal = false;
        $this->resetValidation();
        $this->reset(['newAlbumName', 'newAlbumDescription', 'newAlbumType', 'newAlbumCover', 'newAlbumClientId']);
        $this->clients = []; // Limpiar lista
    }
    // Se llama al enviar el formulario de creación de álbum
    public function createAlbum()
    {
        $user = Auth::user();
        // Comprobación de permiso (¡Ajusta!)
        if (!$user || $user->role !== 'admin') { /* ... */
            return;
        }
        // Validar usando las reglas definidas en el método rules()
        $validatedData = $this->validate($this->rules());
        $coverPath = null;
        // Guardar imagen de portada si se subió
        if ($this->newAlbumCover) {
            try {
                $coverPath = $this->newAlbumCover->store('albums/covers', 'public');
            } catch (\Exception $e) {
                $this->addError('newAlbumCover', 'Error al subir.');
                Log::error(...);
                return;
            }
        }
        // Crear el registro Album en la base de datos
        Album::create([
            'name' => $validatedData['newAlbumName'],
            'description' => $validatedData['newAlbumDescription'],
            'type' => $validatedData['newAlbumType'],
            'user_id' => $user->id, // El admin es el creador/dueño
            'cover_image' => $coverPath,
            // Asigna client_id solo si el tipo es 'client' y se seleccionó un cliente válido
            'client_id' => ($validatedData['newAlbumType'] === 'client' && !empty($validatedData['newAlbumClientId'])) ? $validatedData['newAlbumClientId'] : null,
        ]);
        $this->closeCreateAlbumModal(); // Cerrar el modal
        session()->flash('message', 'Álbum creado.'); // Mensaje de éxito
    }

    // --- Método Render (Se ejecuta en cada actualización del componente) ---
    public function render()
    {
        // Obtener usuario autenticado
        $user = Auth::user();
        // Iniciar consulta Eloquent para Álbumes
        $query = Album::query();

        // Aplicar filtro basado en rol
        if ($user) { // Si hay usuario logueado
            if ($user->role != 'admin') { // Si NO es admin (asume cliente) ¡Ajusta rol!
                // Solo ve sus álbumes (por user_id) O los de tipo 'public'
                $query->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                        // ->orWhere('client_id', $user->id) // Descomentar si un cliente debe ver álbumes donde es client_id
                        ->orWhere('type', 'public'); // ¡Ajusta columna/valor si es necesario!
                });
            }
            // Si es admin, no se aplica filtro de usuario/tipo (ve todo)
        } else { // Si es invitado
            // Solo ve los de tipo 'public'
            $query->where('type', 'public'); // ¡Ajusta columna/valor si es necesario!
        }

        // Aplicar filtro de búsqueda si $cadena no está vacía
        if (!empty($this->cadena)) {
            $query->where(function ($q) { // Agrupar condiciones OR
                $searchTerm = '%' . $this->cadena . '%';
                $q->where('name', 'like', $searchTerm) // Buscar en nombre
                    ->orWhere('description', 'like', $searchTerm); // Buscar en descripción
            });
        }

        // Cargar relaciones necesarias para la vista (Eager Loading)
        $query->with(['user']); // Cargar el usuario dueño de cada álbum

        // Aplicar ordenación
        $sortField = in_array($this->campo, ['name', 'created_at', 'type']) ? $this->campo : 'created_at'; // Validar campo
        $sortDirection = $this->order === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sortField, $sortDirection);

        // Obtener resultados paginados para la lista de álbumes
        $albums = $query->paginate(12, ['*'], 'albumsPage'); // 12 álbumes por página

        // Obtener fotos paginadas para el modal (SOLO si el modal está abierto)
        $photosInModal = null;
        if ($this->showModal && $this->selectedAlbum) {
            $photosInModal = $this->selectedAlbum
                ->photos() // Usa la relación definida en el modelo Album
                ->orderBy('id') // Ordena las fotos por ID (puedes cambiarlo)
                ->paginate(15, ['*'], 'photosPage'); // 15 fotos por página en el modal
        }

        // Obtener lista de usuarios (para modal crear álbum) - MOVIDO a openCreateAlbumModal

        // Devolver la vista Blade y pasarle las variables necesarias
        $usuarios = User::all();
        return view('livewire.albums', [
            'albums' => $albums,                // Colección paginada de álbumes para la rejilla principal
            'photosInModal' => $photosInModal,
            'usuarios' => $usuarios,
        ]);
    }

     //Metodos para editar album
     public function openEditAlbumModal(int $albumId)
     {
         $this->resetValidation(); // Limpiar errores previos
         $album = Album::find($albumId);
         if (!$album) return; // Salir si no se encuentra

          // Verificar permiso (solo admin o dueño - ¡Ajusta rol!)
          if (Auth::user()?->role !== 'admin' && Auth::id() !== $album->user_id) {
              session()->flash('error', 'No tienes permiso para editar este álbum.');
              return;
          }

         $this->editingAlbum = $album; // Guardar el álbum a editar

         // Rellenar las propiedades del formulario con los datos actuales del álbum
         $this->editAlbumName = $album->name;
         $this->editAlbumDescription = $album->description ?? '';
         $this->editAlbumType = $album->type;
         $this->editAlbumClientId = $album->client_id ? (string)$album->client_id : ''; // Convertir a string para select
         $this->editAlbumCurrentCover = $album->cover_image; // Guardar ruta actual
         $this->editAlbumNewCover = null; // Limpiar posible archivo previo

         // Cargar clientes si el tipo actual o potencial es 'client'
         // (Podría optimizarse cargando solo si type es 'client' al abrir)
         $this->clients = User::where('role', 'client')->orderBy('name')->get(['id', 'name', 'email']);

         $this->showEditAlbumModal = true; // Mostrar modal
     }

     public function closeEditAlbumModal()
     {
         $this->showEditAlbumModal = false;
         $this->resetValidation();
         $this->reset(['editingAlbum', 'editAlbumName', 'editAlbumDescription', 'editAlbumType', 'editAlbumClientId', 'editAlbumNewCover', 'editAlbumCurrentCover']);
         $this->clients = [];
     }

     public function updateAlbum()
     {
         if (!$this->editingAlbum) return; // Salir si no hay álbum en edición

         $user = Auth::user();
         // Verificar permiso (¡Ajusta rol!)
         if (!$user || ($user->role !== 'admin' && $this->editingAlbum->user_id !== $user->id)) {
             session()->flash('error', 'No tienes permiso para editar este álbum.');
             $this->closeEditAlbumModal();
             return;
         }

         // Validar usando las reglas específicas para update
         $validatedData = $this->validate($this->rulesForUpdate());

         $coverPath = $this->editingAlbum->cover_image; // Mantener portada actual por defecto

         // Si se subió una nueva portada
         if ($this->editAlbumNewCover) {
             try {
                 // Borrar la portada anterior si existía
                 if ($coverPath) {
                     Storage::disk('public')->delete($coverPath);
                 }
                 // Guardar la nueva portada
                 $coverPath = $this->editAlbumNewCover->store('albums/covers', 'public');
             } catch (\Exception $e) {
                 $this->addError('editAlbumNewCover', 'Error al subir la nueva portada.');
                 Log::error("Error subiendo nueva cover para album {$this->editingAlbum->id}: " . $e->getMessage());
                 return;
             }
         }

         // Actualizar el álbum en la base de datos
         $this->editingAlbum->update([
             'name' => $validatedData['editAlbumName'],
             'description' => $validatedData['editAlbumDescription'],
             'type' => $validatedData['editAlbumType'],
             'cover_image' => $coverPath, // Nueva ruta o la anterior
             'client_id' => ($validatedData['editAlbumType'] === 'client' && !empty($validatedData['editAlbumClientId']))
                            ? $validatedData['editAlbumClientId']
                            : ($validatedData['editAlbumType'] === 'public' ? $user->id : null), // Asigna el usuario si es público
         ]);

         $this->closeEditAlbumModal(); // Cerrar modal
         session()->flash('message', 'Álbum actualizado correctamente.');
         // $this->dispatch('$refresh'); // Refrescar componente actual si es necesario
     }

} // Fin de la clase Albums
