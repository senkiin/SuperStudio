<?php

namespace App\Livewire;

use App\Models\Album;
use App\Models\AlbumSectionConfig;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Asegúrate de tener Log importado
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class ConfigurableAlbumSection extends Component
{
    use WithPagination;

    // --- Propiedades de la Configuración de la Sección ---
    public ?AlbumSectionConfig $sectionConfig = null;
    public string $sectionTitle = '';
    public array $selectedAlbumOrder = []; // Array de IDs de álbumes en el orden de visualización

    // --- Para la Vista Pública ---
    public Collection $albumsToDisplay; // Álbumes seleccionados (objetos)

    // --- Para el Modal de Configuración Principal ---
    public bool $showConfigurationModal = false;
    public string $editableSectionTitle = ''; // Para editar el título en el modal

    // --- Para el Modal de Selección de Álbumes ---
    public bool $showAlbumSelectionModal = false;
    public string $albumSearchQuery = '';
    public string $albumSortField = 'created_at';
    public string $albumSortDirection = 'desc';
    public array $tempSelectedAlbumIds = []; // IDs marcados en el modal de selección

    protected $paginationTheme = 'tailwind';

    // protected $listeners = ['albumsConfirmed' => 'handleAlbumsConfirmed']; // Si no usas este listener, puedes quitarlo.

    public function mount(string $identifier = 'default_featured_albums')
    {
        $this->sectionConfig = AlbumSectionConfig::firstOrCreate(
            ['identifier' => $identifier],
            ['section_title' => 'Destacados', 'selected_album_ids_ordered' => []]
        );

        $this->sectionTitle = $this->sectionConfig->section_title ?? 'Destacados';
        $this->selectedAlbumOrder = $this->sectionConfig->selected_album_ids_ordered ?? [];
        $this->editableSectionTitle = $this->sectionTitle;
        $this->loadAlbumsToDisplay();
    }

    protected function loadAlbumsToDisplay()
    {
        if (empty($this->selectedAlbumOrder)) {
            $this->albumsToDisplay = collect();
            return;
        }
        // Asegurarse que los IDs son enteros para la consulta y el ordenamiento
        $integerIds = array_map('intval', $this->selectedAlbumOrder);
        if (empty($integerIds)) {
            $this->albumsToDisplay = collect();
            return;
        }
        $this->albumsToDisplay = Album::whereIn('id', $integerIds)
            ->get()
            ->sortBy(function ($album) use ($integerIds) {
                return array_search($album->id, $integerIds);
            });
    }

    // --- MODAL DE CONFIGURACIÓN PRINCIPAL ---
    public function openConfigurationModal()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') return;
        // Recargar la configuración desde la BD al abrir el modal para asegurar datos frescos
        $this->sectionConfig->refresh();
        $this->selectedAlbumOrder = $this->sectionConfig->selected_album_ids_ordered ?? [];
        $this->editableSectionTitle = $this->sectionConfig->section_title ?? 'Destacados';
        $this->showConfigurationModal = true;
    }

    public function saveConfiguration()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') return;

        $this->validate([
            'editableSectionTitle' => 'nullable|string|max:255',
            'selectedAlbumOrder' => 'array',
        ]);

        if (!$this->sectionConfig) {
            session()->flash('error', 'Error: Configuración de la sección no encontrada.');
            return;
        }

        $this->sectionConfig->section_title = $this->editableSectionTitle;
        $this->sectionConfig->selected_album_ids_ordered = array_map('intval', $this->selectedAlbumOrder); // Asegurar IDs como enteros
        $this->sectionConfig->save();

        $this->sectionTitle = $this->editableSectionTitle;
        $this->loadAlbumsToDisplay();
        $this->showConfigurationModal = false;
        session()->flash('message', 'Configuración guardada con éxito.');
    }

    public function removeAlbumFromSelection(int $albumId)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') return;

        Log::info('[ConfigurableAlbumSection] Intentando eliminar album ID: ' . $albumId . ' de la lista y BD.');
        Log::info('[ConfigurableAlbumSection] selectedAlbumOrder ANTES: ', $this->selectedAlbumOrder);

        // 1. Modificar el array local
        $newSelectedAlbumOrder = array_filter($this->selectedAlbumOrder, fn($id) => intval($id) !== $albumId);
        $this->selectedAlbumOrder = array_values($newSelectedAlbumOrder); // Re-indexar

        Log::info('[ConfigurableAlbumSection] selectedAlbumOrder DESPUÉS (local): ', $this->selectedAlbumOrder);

        // 2. Actualizar el modelo y guardar en la base de datos inmediatamente
        if ($this->sectionConfig) {
            $this->sectionConfig->selected_album_ids_ordered = $this->selectedAlbumOrder; // Ya está reindexado y con IDs como enteros
            try {
                $this->sectionConfig->save();
                Log::info('[ConfigurableAlbumSection] Album ID: ' . $albumId . ' eliminado de la BD. Configuración guardada.');
                // La vista del modal se actualizará porque $selectedAlbumOrder (propiedad pública) cambió.
                // $albumsInConfiguration se recalculará en el render.
                // Opcional: Mensaje flash específico para esta acción si no interfiere con el guardado general.
                // session()->flash('message', 'Álbum quitado de la selección.');
            } catch (\Exception $e) {
                Log::error('[ConfigurableAlbumSection] Error al guardar la eliminación del album ID: ' . $albumId . ' en la BD: ' . $e->getMessage());
                // Considera revertir $this->selectedAlbumOrder si el guardado falla
                session()->flash('error', 'Error al quitar el álbum de la base de datos.');
            }
        } else {
            Log::error('[ConfigurableAlbumSection] sectionConfig no está cargado. No se puede eliminar el álbum de la BD.');
            session()->flash('error', 'Error: Configuración de la sección no encontrada.');
        }
    }

    // Este método se usaba para el drag-and-drop. Si ya no lo usas, puedes comentarlo o eliminarlo.
    // Si lo mantienes para otros usos futuros, está bien.
    public function updateDisplayOrder(array $orderedIds)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') return;
        $this->selectedAlbumOrder = array_map('intval', $orderedIds);
        // Si quieres que el reordenamiento también se guarde inmediatamente en la BD:
        /*
        if ($this->sectionConfig) {
            $this->sectionConfig->selected_album_ids_ordered = $this->selectedAlbumOrder;
            $this->sectionConfig->save();
            session()->flash('message', 'Orden de álbumes actualizado.');
        }
        */
    }


    // --- MODAL DE SELECCIÓN DE ÁLBUMES ---
    public function openAlbumSelectionSubModal()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') return;
        $this->albumSearchQuery = '';
        $this->albumSortField = 'created_at';
        $this->albumSortDirection = 'desc';
        // Al abrir el submodal, $tempSelectedAlbumIds debe reflejar el estado actual de $selectedAlbumOrder
        $this->tempSelectedAlbumIds = array_map('intval', $this->selectedAlbumOrder);
        $this->resetPage('availableAlbumsPage');
        $this->showAlbumSelectionModal = true;
    }

    public function closeAlbumSelectionSubModal()
    {
        $this->showAlbumSelectionModal = false;
    }

    public function sortBy($field)
    {
        if ($this->albumSortField === $field) {
            $this->albumSortDirection = $this->albumSortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->albumSortField = $field;
            $this->albumSortDirection = 'asc';
        }
        $this->resetPage('availableAlbumsPage');
    }

    public function updatedAlbumSearchQuery()
    {
        $this->resetPage('availableAlbumsPage');
    }

    public function confirmAndCloseAlbumSelection()
    {
        // Los IDs ya están en $this->tempSelectedAlbumIds gracias a wire:model
        // Asegurarse que son enteros
        $this->selectedAlbumOrder = array_map('intval', $this->tempSelectedAlbumIds);
        $this->closeAlbumSelectionSubModal();
        // IMPORTANTE: Si quieres que esta confirmación también guarde en BD inmediatamente:
        /*
        if ($this->sectionConfig) {
            $this->sectionConfig->selected_album_ids_ordered = $this->selectedAlbumOrder;
            $this->sectionConfig->save();
            session()->flash('message', 'Selección de álbumes actualizada y guardada.');
        }
        */
        // Si no, los cambios se guardarán al presionar "Guardar Configuración" en el modal principal.
    }


    #[Computed()]
    public function availableAlbums()
    {
        return Album::query()
            ->when(!empty($this->albumSearchQuery), function ($query) {
                $query->where('name', 'like', '%' . $this->albumSearchQuery . '%')
                    ->orWhere('description', 'like', '%' . $this->albumSearchQuery . '%');
            })
            ->orderBy($this->albumSortField, $this->albumSortDirection)
            ->paginate(10, ['*'], 'availableAlbumsPage');
    }

    public function render()
    {
        $isAdmin = Auth::check() && Auth::user()->role === 'admin';

        // Asegurar que $selectedAlbumOrder contiene solo enteros antes de pasarlo a la vista o usarlo en consultas
        $safeSelectedAlbumOrder = array_map('intval', $this->selectedAlbumOrder);

        $albumsInConfigurationView = collect();
        if (!empty($safeSelectedAlbumOrder)) {
            $albumsInConfigurationView = Album::whereIn('id', $safeSelectedAlbumOrder)
                ->get()
                ->sortBy(function ($album) use ($safeSelectedAlbumOrder) {
                    return array_search($album->id, $safeSelectedAlbumOrder);
                });
        }

        return view('livewire.configurable-album-section', [
            'isAdmin' => $isAdmin,
            'displayedAlbums' => $this->albumsToDisplay,
            'albumsInConfiguration' => $albumsInConfigurationView,
        ]);
    }
}
