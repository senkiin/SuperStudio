<?php

namespace App\Livewire;

use App\Models\Album; // Modelo Eloquent para los álbumes.
use App\Models\AlbumSectionConfig; // Modelo para guardar la configuración de esta sección.
use Illuminate\Support\Collection; // Clase Collection de Laravel para un manejo de arrays más fluido.
use Illuminate\Support\Facades\Auth; // Fachada para acceder a la información del usuario autenticado.
use Illuminate\Support\Facades\Log;  // Fachada para registrar logs, útil para depuración.
use Illuminate\Support\Facades\Storage; // Fachada para interactuar con el sistema de archivos (ej. S3).
use Livewire\Attributes\Computed;    // Atributo para definir propiedades computadas (cacheadas).
use Livewire\Component;            // Clase base para todos los componentes Livewire.
use Livewire\WithPagination;       // Trait para habilitar la paginación de forma sencilla.

class ConfigurableAlbumSection extends Component
{
    use WithPagination; // Habilita la paginación para la lista de álbumes en el modal de selección.

    // --- Disco S3 por defecto para todas las operaciones ----
    // Define el disco de S3 que se utilizará para obtener las URLs de las portadas de los álbumes.
    // Este nombre debe coincidir con una configuración en config/filesystems.php.
    protected string $disk = 'albums';

    // --- Propiedades de configuración de la sección ---
    public ?AlbumSectionConfig $sectionConfig = null; // Almacena la configuración de la sección cargada de la BD.
    public string $sectionTitle = ''; // Título público de la sección (ej. "Destacados 2024").
    public array $selectedAlbumOrder = []; // Array de IDs de álbumes en el orden en que deben mostrarse.

    // --- Para la vista pública ---
    public Collection $albumsToDisplay; // Colección de objetos Album que se mostrarán en la sección.

    // --- Modal de configuración principal (para el admin) ---
    public bool $showConfigurationModal = false; // Controla la visibilidad del modal principal de configuración.
    public string $editableSectionTitle = ''; // Título de la sección, editable en el modal.

    // --- Sub-modal de selección de álbumes (para el admin) ---
    public bool $showAlbumSelectionModal = false; // Controla la visibilidad del sub-modal para seleccionar álbumes.
    public string $albumSearchQuery = ''; // Término de búsqueda para filtrar álbumes en el sub-modal.
    public string $albumSortField = 'created_at'; // Campo por el cual ordenar los álbumes en el sub-modal.
    public string $albumSortDirection = 'desc'; // Dirección de la ordenación ('asc' o 'desc').
    public array $tempSelectedAlbumIds = []; // IDs de álbumes seleccionados temporalmente en el sub-modal.

    protected $paginationTheme = 'tailwind'; // Especifica que se usarán las vistas de paginación de Tailwind CSS.

    /**
     * Método `mount`: Se ejecuta una vez cuando el componente se inicializa.
     * Carga o crea la configuración de la sección y los álbumes a mostrar.
     *
     * @param string $identifier Identificador único para esta instancia de la sección de álbumes.
     */
    public function mount(string $identifier = 'default_featured_albums')
    {
        // Carga la configuración existente o crea una nueva si no existe para el identificador dado.
        $this->sectionConfig = AlbumSectionConfig::firstOrCreate(
            ['identifier' => $identifier], // Busca por este identificador.
            // Valores por defecto si se crea una nueva configuración:
            ['section_title' => 'Destacados', 'selected_album_ids_ordered' => []]
        );

        // Inicializa las propiedades del componente con los valores de la configuración cargada/creada.
        $this->sectionTitle         = $this->sectionConfig->section_title ?? 'Destacados';
        $this->selectedAlbumOrder   = $this->sectionConfig->selected_album_ids_ordered ?? [];
        $this->editableSectionTitle = $this->sectionTitle; // Para el campo de edición en el modal.

        $this->loadAlbumsToDisplay(); // Carga los álbumes que se van a mostrar.
    }

    /**
     * Carga los álbumes que se deben mostrar en la sección, basándose en $selectedAlbumOrder.
     * Los álbumes se ordenan según el orden guardado en $selectedAlbumOrder.
     */
    protected function loadAlbumsToDisplay(): void
    {
        if (empty($this->selectedAlbumOrder)) { // Si no hay álbumes seleccionados, la colección estará vacía.
            $this->albumsToDisplay = collect();
            return;
        }

        // Convierte los IDs a enteros por seguridad y consistencia.
        $ids = array_map('intval', $this->selectedAlbumOrder);

        // Obtiene los álbumes de la BD cuyos IDs están en la lista y los ordena.
        $this->albumsToDisplay = Album::whereIn('id', $ids)
            ->get()
            // Ordena la colección resultante según el orden de los IDs en $this->selectedAlbumOrder.
            ->sortBy(fn($album) => array_search($album->id, $ids));
    }

    // --------- CONFIGURACIÓN PRINCIPAL (Modal Admin) ---------

    /**
     * Abre el modal principal de configuración para el administrador.
     * Solo los administradores pueden abrir este modal.
     */
    public function openConfigurationModal(): void
    {
        // Verifica si el usuario es administrador.
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return; // No hace nada si no es admin.
        }

        $this->sectionConfig->refresh(); // Recarga la configuración desde la BD para tener los datos más recientes.
        $this->selectedAlbumOrder   = $this->sectionConfig->selected_album_ids_ordered ?? [];
        $this->editableSectionTitle = $this->sectionConfig->section_title ?? 'Destacados';
        $this->showConfigurationModal = true; // Muestra el modal.
    }

    /**
     * Guarda los cambios realizados en la configuración principal de la sección.
     * Esto incluye el título de la sección y el orden de los álbumes seleccionados.
     */
    public function saveConfiguration(): void
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return;
        }

        // Valida los datos del formulario del modal.
        $this->validate([
            'editableSectionTitle' => 'nullable|string|max:255', // Título opcional.
            'selectedAlbumOrder'   => 'array', // Debe ser un array (de IDs).
        ]);

        if (!$this->sectionConfig) { // Si por alguna razón la configuración no está cargada.
            session()->flash('error', 'Configuración no encontrada.');
            return;
        }

        // Actualiza la configuración en la base de datos.
        $this->sectionConfig->update([
            'section_title'              => $this->editableSectionTitle,
            'selected_album_ids_ordered' => array_map('intval', $this->selectedAlbumOrder), // Guarda los IDs como enteros.
        ]);

        // Actualiza las propiedades públicas del componente para reflejar los cambios.
        $this->sectionTitle = $this->editableSectionTitle;
        $this->loadAlbumsToDisplay(); // Recarga los álbumes a mostrar con el nuevo orden/selección.
        $this->showConfigurationModal = false; // Cierra el modal.
        session()->flash('message', 'Configuración guardada.'); // Muestra un mensaje de éxito.
    }

    /**
     * Elimina un álbum de la lista de álbumes seleccionados para esta sección.
     * @param int $albumId El ID del álbum a eliminar de la selección.
     */
    public function removeAlbumFromSelection(int $albumId): void
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return;
        }

        // Filtra el array $selectedAlbumOrder para quitar el ID del álbum especificado.
        $this->selectedAlbumOrder = array_values(array_filter(
            $this->selectedAlbumOrder,
            fn($id) => intval($id) !== $albumId // Compara como enteros.
        ));

        // Actualiza la configuración en la BD si está cargada.
        if ($this->sectionConfig) {
            try {
                $this->sectionConfig->update([
                    'selected_album_ids_ordered' => $this->selectedAlbumOrder,
                ]);
                // No es necesario llamar a loadAlbumsToDisplay aquí si el guardado de la configuración principal
                // (saveConfiguration) se encarga de ello, o si la vista reacciona directamente a $selectedAlbumOrder.
                // Sin embargo, para consistencia en la UI del modal, podría ser útil si la lista se muestra allí.
            } catch (\Exception $e) {
                Log::error("Error al quitar álbum de la sección '{$this->sectionConfig->identifier}': {$e->getMessage()}");
                session()->flash('error', 'No se pudo eliminar el álbum de la selección.');
            }
        }
    }

    /**
     * Actualiza el orden de los álbumes mostrados.
     * Este método está pensado para ser llamado por una librería de arrastrar y soltar (ej. SortableJS).
     * @param array $orderedIds Array de IDs de álbumes en el nuevo orden.
     */
    public function updateDisplayOrder(array $orderedIds): void
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return;
        }
        // Actualiza la propiedad $selectedAlbumOrder con los nuevos IDs ordenados.
        $this->selectedAlbumOrder = array_map('intval', $orderedIds);

        // Nota: El guardado en la BD se hace a través de saveConfiguration().
        // Si se quisiera guardar inmediatamente al reordenar, se añadiría aquí:
        // $this->sectionConfig->update(['selected_album_ids_ordered' => $this->selectedAlbumOrder]);
        // $this->loadAlbumsToDisplay(); // Y se recargarían los álbumes.
    }

    // --------- SUB-MODAL DE SELECCIÓN DE ÁLBUMES (Admin) ---------

    /**
     * Abre el sub-modal para que el administrador seleccione qué álbumes incluir en la sección.
     */
    public function openAlbumSelectionSubModal(): void
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return;
        }

        // Resetea los filtros y la selección temporal del sub-modal.
        $this->albumSearchQuery     = '';
        $this->albumSortField       = 'created_at';
        $this->albumSortDirection   = 'desc';
        // Carga los IDs de los álbumes ya seleccionados en la selección temporal.
        $this->tempSelectedAlbumIds = array_map('intval', $this->selectedAlbumOrder);
        $this->resetPage('availableAlbumsPage'); // Resetea la paginación de la lista de álbumes disponibles.
        $this->showAlbumSelectionModal = true; // Muestra el sub-modal.
    }

    /**
     * Cierra el sub-modal de selección de álbumes.
     */
    public function closeAlbumSelectionSubModal(): void
    {
        $this->showAlbumSelectionModal = false;
        // No resetea tempSelectedAlbumIds aquí, para que si se cancela, la próxima vez
        // que se abra el modal principal, $selectedAlbumOrder siga siendo el correcto.
    }

    /**
     * Cambia el campo y/o la dirección de ordenación para la lista de álbumes disponibles en el sub-modal.
     * @param string $field El campo por el cual ordenar ('name', 'created_at', 'type').
     */
    public function sortBy(string $field): void
    {
        // Valida que el campo sea uno de los permitidos (implícito, ya que se usa directamente).
        if ($this->albumSortField === $field) { // Si se hace clic en el mismo campo, invierte el orden.
            $this->albumSortDirection = $this->albumSortDirection === 'asc' ? 'desc' : 'asc';
        } else { // Si es un campo nuevo, ordena ascendentemente por defecto.
            $this->albumSortField     = $field;
            $this->albumSortDirection = 'asc';
        }
        $this->resetPage('availableAlbumsPage'); // Resetea la paginación.
    }

    /**
     * Hook de Livewire: Se ejecuta cuando la propiedad $albumSearchQuery se actualiza.
     * Resetea la paginación de la lista de álbumes disponibles.
     */
    public function updatedAlbumSearchQuery(): void
    {
        $this->resetPage('availableAlbumsPage');
    }

    /**
     * Confirma la selección de álbumes hecha en el sub-modal y la aplica
     * a la propiedad $selectedAlbumOrder del modal principal.
     */
    public function confirmAndCloseAlbumSelection(): void
    {
        // Asigna los IDs temporalmente seleccionados a la propiedad principal.
        $this->selectedAlbumOrder = array_map('intval', $this->tempSelectedAlbumIds);
        $this->closeAlbumSelectionSubModal(); // Cierra el sub-modal.
        // No se guarda en la BD aquí; se hará al guardar la configuración principal.
    }

    /**
     * Propiedad computada: Obtiene la lista paginada de álbumes disponibles para seleccionar.
     * Filtra por $albumSearchQuery y ordena por $albumSortField y $albumSortDirection.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    #[Computed]
    public function availableAlbums()
    {
        return Album::query()
            // Aplica filtro de búsqueda si $albumSearchQuery no está vacío.
            ->when($this->albumSearchQuery, fn($q) =>
                $q->where('name', 'like', "%{$this->albumSearchQuery}%")
                  ->orWhere('description', 'like', "%{$this->albumSearchQuery}%")
            )
            // Ordena los resultados.
            ->orderBy($this->albumSortField, $this->albumSortDirection)
            // Pagina los resultados (10 por página, nombre de paginador 'availableAlbumsPage').
            ->paginate(10, ['*'], 'availableAlbumsPage');
    }

    /**
     * Propiedad computada: Genera las URLs públicas de las portadas (cover_image)
     * de cada álbum que se va a mostrar en la sección.
     * Utiliza el disco S3 configurado en $this->disk.
     *
     * @return array Un array asociativo [album_id => url_de_la_portada].
     */
    #[Computed]
    public function coverUrls(): array
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $s3 */
        // Obtiene una instancia del adaptador del sistema de archivos para el disco S3.
        $s3 = Storage::disk($this->disk);

        // Mapea la colección de álbumes a mostrar para crear un array de URLs de portadas.
        return $this->albumsToDisplay->mapWithKeys(fn($album) => [
            $album->id => ($album->cover_image && $s3->exists($album->cover_image)) // Si tiene portada y existe en S3.
                ? $s3->url($album->cover_image) // Genera la URL pública.
                : null, // Si no, devuelve null.
        ])->toArray(); // Convierte la colección resultante a un array PHP.
    }

    /**
     * Renderiza la vista del componente.
     * Pasa los datos necesarios a la plantilla Blade.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        // Determina si el usuario actual es administrador.
        $isAdmin = Auth::check() && Auth::user()->role === 'admin';

        // Retorna la vista Blade 'livewire.configurable-album-section' y le pasa los datos.
        return view('livewire.configurable-album-section', [
            'isAdmin'               => $isAdmin, // Para mostrar/ocultar controles de admin en la vista.
            'displayedAlbums'       => $this->albumsToDisplay, // Álbumes que se mostrarán públicamente.
            // Álbumes para mostrar en la lista de "seleccionados" dentro del modal de configuración.
            // Se obtienen y ordenan de nuevo para asegurar consistencia en el modal.
            'albumsInConfiguration' => Album::whereIn('id', array_map('intval', $this->selectedAlbumOrder))
                                             ->get()
                                             ->sortBy(fn($a) => array_search($a->id, $this->selectedAlbumOrder)),
            'disk'                  => $this->disk, // El nombre del disco S3 (para la vista si necesita generar URLs).
            'coverUrls'             => $this->coverUrls(), // Las URLs de las portadas (propiedad computada).
        ]);
    }
}
