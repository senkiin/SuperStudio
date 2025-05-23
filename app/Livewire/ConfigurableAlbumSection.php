<?php

namespace App\Livewire;

use App\Models\Album;
use App\Models\AlbumSectionConfig;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class ConfigurableAlbumSection extends Component
{
    use WithPagination;

    // --- Disco S3 por defecto para todas las operaciones ----
    protected string $disk = 'albums';

    // --- Propiedades de configuración ---
    public ?AlbumSectionConfig $sectionConfig = null;
    public string $sectionTitle = '';
    public array $selectedAlbumOrder = [];

    // --- Para la vista pública ---
    public Collection $albumsToDisplay;

    // --- Modal de configuración principal ---
    public bool $showConfigurationModal = false;
    public string $editableSectionTitle = '';

    // --- Modal de selección de álbumes ---
    public bool $showAlbumSelectionModal = false;
    public string $albumSearchQuery = '';
    public string $albumSortField = 'created_at';
    public string $albumSortDirection = 'desc';
    public array $tempSelectedAlbumIds = [];

    protected $paginationTheme = 'tailwind';

    public function mount(string $identifier = 'default_featured_albums')
    {
        $this->sectionConfig = AlbumSectionConfig::firstOrCreate(
            ['identifier' => $identifier],
            ['section_title' => 'Destacados', 'selected_album_ids_ordered' => []]
        );

        $this->sectionTitle         = $this->sectionConfig->section_title ?? 'Destacados';
        $this->selectedAlbumOrder   = $this->sectionConfig->selected_album_ids_ordered ?? [];
        $this->editableSectionTitle = $this->sectionTitle;

        $this->loadAlbumsToDisplay();
    }

    protected function loadAlbumsToDisplay(): void
    {
        if (empty($this->selectedAlbumOrder)) {
            $this->albumsToDisplay = collect();
            return;
        }

        $ids = array_map('intval', $this->selectedAlbumOrder);

        $this->albumsToDisplay = Album::whereIn('id', $ids)
            ->get()
            ->sortBy(fn($album) => array_search($album->id, $ids));
    }

    // --------- CONFIGURACIÓN PRINCIPAL ---------

    public function openConfigurationModal(): void
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return;
        }

        $this->sectionConfig->refresh();
        $this->selectedAlbumOrder   = $this->sectionConfig->selected_album_ids_ordered ?? [];
        $this->editableSectionTitle = $this->sectionConfig->section_title ?? 'Destacados';
        $this->showConfigurationModal = true;
    }

    public function saveConfiguration(): void
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return;
        }

        $this->validate([
            'editableSectionTitle' => 'nullable|string|max:255',
            'selectedAlbumOrder'   => 'array',
        ]);

        if (!$this->sectionConfig) {
            session()->flash('error', 'Configuración no encontrada.');
            return;
        }

        $this->sectionConfig->update([
            'section_title'              => $this->editableSectionTitle,
            'selected_album_ids_ordered' => array_map('intval', $this->selectedAlbumOrder),
        ]);

        $this->sectionTitle = $this->editableSectionTitle;
        $this->loadAlbumsToDisplay();
        $this->showConfigurationModal = false;
        session()->flash('message', 'Configuración guardada.');
    }

    public function removeAlbumFromSelection(int $albumId): void
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return;
        }

        $this->selectedAlbumOrder = array_values(array_filter(
            $this->selectedAlbumOrder,
            fn($id) => intval($id) !== $albumId
        ));

        if ($this->sectionConfig) {
            try {
                $this->sectionConfig->update([
                    'selected_album_ids_ordered' => $this->selectedAlbumOrder,
                ]);
            } catch (\Exception $e) {
                Log::error("Error al quitar álbum: {$e->getMessage()}");
                session()->flash('error', 'No se pudo eliminar el álbum.');
            }
        }
    }

    public function updateDisplayOrder(array $orderedIds): void
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return;
        }

        $this->selectedAlbumOrder = array_map('intval', $orderedIds);
        // Si quieres guardar inmediatamente:
        // $this->sectionConfig->update(['selected_album_ids_ordered' => $this->selectedAlbumOrder]);
    }

    // --------- MODAL DE SELECCIÓN DE ÁLBUMES ---------

    public function openAlbumSelectionSubModal(): void
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return;
        }

        $this->albumSearchQuery     = '';
        $this->albumSortField       = 'created_at';
        $this->albumSortDirection   = 'desc';
        $this->tempSelectedAlbumIds = array_map('intval', $this->selectedAlbumOrder);
        $this->resetPage('availableAlbumsPage');
        $this->showAlbumSelectionModal = true;
    }

    public function closeAlbumSelectionSubModal(): void
    {
        $this->showAlbumSelectionModal = false;
    }

    public function sortBy(string $field): void
    {
        if ($this->albumSortField === $field) {
            $this->albumSortDirection = $this->albumSortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->albumSortField     = $field;
            $this->albumSortDirection = 'asc';
        }
        $this->resetPage('availableAlbumsPage');
    }

    public function updatedAlbumSearchQuery(): void
    {
        $this->resetPage('availableAlbumsPage');
    }

    public function confirmAndCloseAlbumSelection(): void
    {
        $this->selectedAlbumOrder = array_map('intval', $this->tempSelectedAlbumIds);
        $this->closeAlbumSelectionSubModal();
    }

    #[Computed]
    public function availableAlbums()
    {
        return Album::query()
            ->when($this->albumSearchQuery, fn($q) =>
                $q->where('name', 'like', "%{$this->albumSearchQuery}%")
                  ->orWhere('description', 'like', "%{$this->albumSearchQuery}%")
            )
            ->orderBy($this->albumSortField, $this->albumSortDirection)
            ->paginate(10, ['*'], 'availableAlbumsPage');
    }

    // Computed: URLs públicas de las portadas (cover_image) de cada álbum
    #[Computed]
    public function coverUrls(): array
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $s3 */
        $s3 = Storage::disk($this->disk);

        return $this->albumsToDisplay->mapWithKeys(fn($album) => [
            $album->id => ($album->cover_image && $s3->exists($album->cover_image))
                ? $s3->url($album->cover_image)
                : null,
        ])->toArray();
    }

    public function render()
    {
        $isAdmin = Auth::check() && Auth::user()->role === 'admin';

        return view('livewire.configurable-album-section', [
            'isAdmin'               => $isAdmin,
            'displayedAlbums'       => $this->albumsToDisplay,
            'albumsInConfiguration' => Album::whereIn('id', array_map('intval', $this->selectedAlbumOrder))
                                             ->get()
                                             ->sortBy(fn($a) => array_search($a->id, $this->selectedAlbumOrder)),
            'disk'                  => $this->disk,
            'coverUrls'             => $this->coverUrls(),
        ]);
    }
}
