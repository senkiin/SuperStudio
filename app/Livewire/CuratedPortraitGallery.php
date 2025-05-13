<?php

namespace App\Livewire;

use App\Models\GridGallery as PortraitCollection; // Modelo para la configuración de la galería
use App\Models\Photo;
use App\Models\Album;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class CuratedPortraitGallery extends Component
{
    use WithFileUploads, WithPagination;

    public ?PortraitCollection $collectionConfig = null;
    public string $identifier = 'default_portrait_gallery';
    public ?string $galleryTitle = '';
    public ?string $galleryDescription = '';

    public Collection $photosForDisplay;

    public bool $isAdmin = false;
    public bool $showManagerModal = false;

    // --- Propiedades para el Lightbox ---
    public bool $showCustomLightbox = false;
    public ?Photo $currentLightboxPhoto = null;
    public int $currentLightboxPhotoIndex = 0;
    // --- Fin Propiedades Lightbox ---

    // Para el modal de gestión (propiedades existentes)
    public $newPhotosToUploadModal = [];
    public $selectedAlbumIdModal = null;
    public Collection $photosFromAlbumModal;
    public $selectedPhotosFromAlbumModalArray = [];
    public Collection $likedPhotosForUserModal;
    public $selectedLikedPhotosModalArray = [];
    public string $searchQueryModal = '';
    public $searchedPhotosModalPaginator = null;
    public $selectedExistingPhotosModalArray = [];
    public ?Collection $allAlbumsModalCollection = null;
    public string $editableCollectionTitleModal = '';
    public string $editableCollectionDescriptionModal = '';

    protected $paginationTheme = 'tailwind';

    protected function rules()
    {
        return [
            'newPhotosToUploadModal.*' => 'nullable|image|max:2048|mimes:jpeg,png,jpg,webp',
            'selectedPhotosFromAlbumModalArray' => 'array',
            'selectedLikedPhotosModalArray' => 'array',
            'selectedExistingPhotosModalArray' => 'array',
            'editableCollectionTitleModal' => 'nullable|string|max:255',
            'editableCollectionDescriptionModal' => 'nullable|string|max:5000',
        ];
    }

    protected $messages = [
        'newPhotosToUploadModal.*.image' => 'Cada archivo debe ser una imagen.',
        'newPhotosToUploadModal.*.max' => 'Cada imagen no debe superar los 2MB.',
    ];

    public function mount(string $identifier, ?string $defaultTitle = "Retratos Seleccionados", ?string $defaultDescription = "")
    {
        $this->identifier = $identifier;
        $this->isAdmin = Auth::check() && Auth::user()->role === 'admin';

        Log::info("CuratedPortraitGallery Mount: Identifier '{$this->identifier}'");

        $this->collectionConfig = PortraitCollection::firstOrCreate(
            ['identifier' => $this->identifier],
            ['title' => $defaultTitle, 'description' => $defaultDescription]
        );
        $this->galleryTitle = $this->collectionConfig->title;
        $this->galleryDescription = $this->collectionConfig->description;

        $this->photosForDisplay = new Collection();
        $this->photosFromAlbumModal = new Collection();
        $this->likedPhotosForUserModal = new Collection();

        $this->loadPhotosForDisplay();

        if ($this->isAdmin) {
            if (class_exists(Album::class)) {
                $this->allAlbumsModalCollection = Album::orderBy('name')->withCount('photos')->get();
            } else {
                $this->allAlbumsModalCollection = new Collection();
            }
            if (Auth::check() && method_exists(Auth::user(), 'likedPhotos')) {
                $this->likedPhotosForUserModal = Auth::user()->likedPhotos()->orderBy('photo_user_likes.created_at', 'desc')->limit(50)->get();
            }
        }
    }

    public function loadPhotosForDisplay()
    {
        if ($this->collectionConfig) {
            $this->collectionConfig->refresh();
            $this->photosForDisplay = $this->collectionConfig->photos;
            $this->galleryTitle = $this->collectionConfig->title;
            $this->galleryDescription = $this->collectionConfig->description;
            Log::info("CuratedPortraitGallery loadPhotosForDisplay: Loaded " . $this->photosForDisplay->count() . " photos for '{$this->identifier}'.");
        } else {
            Log::error("CuratedPortraitGallery loadPhotosForDisplay: collectionConfig is null for '{$this->identifier}'.");
            $this->photosForDisplay = new Collection();
            $this->galleryTitle = 'Error: Galería no encontrada';
            $this->galleryDescription = 'Por favor, verifica la configuración.';
        }
    }

    // --- Métodos para el Lightbox ---
    public function openCustomLightbox(int $photoId)
    {
        $this->currentLightboxPhoto = Photo::find($photoId);
        if (!$this->currentLightboxPhoto) {
            session()->flash('cpg_error', 'Foto no encontrada para previsualizar.');
            return;
        }

        // Asegurarse de que photosForDisplay esté actualizada antes de buscar el índice
        if ($this->photosForDisplay->isEmpty() && $this->collectionConfig && $this->collectionConfig->photos()->exists()) {
            $this->loadPhotosForDisplay();
        }

        $this->currentLightboxPhotoIndex = $this->photosForDisplay->search(function ($photo) {
            return $photo->id === $this->currentLightboxPhoto->id;
        });

        if ($this->currentLightboxPhotoIndex === false) {
            // Si la foto no está en la lista actual (raro, pero posible si la lista se desincronizó)
            // Por ahora, la mostraremos pero la navegación podría no funcionar como se espera para esta foto.
            // Considerar añadirla temporalmente a una copia de photosForDisplay para la navegación del lightbox
            // o simplemente no permitir navegación si no está en la lista principal.
            Log::warning("CuratedPortraitGallery openCustomLightbox: Photo ID {$photoId} not found in current photosForDisplay for identifier '{$this->identifier}'. Lightbox navigation might be limited.");
            $this->currentLightboxPhotoIndex = 0; // Default a 0 si no se encuentra.
            // Para asegurar que al menos la foto actual se pueda ver si la lista está vacía:
            if ($this->photosForDisplay->isEmpty()) {
                $this->photosForDisplay = new Collection([$this->currentLightboxPhoto]);
            }
        }
        $this->showCustomLightbox = true;
    }

    public function closeCustomLightbox()
    {
        $this->showCustomLightbox = false;
        $this->currentLightboxPhoto = null;
        $this->currentLightboxPhotoIndex = 0;

        // CAMBIO: Volver a cargar las fotos para el display principal
        // Esto asegura que la galería se muestre correctamente después de cerrar el lightbox.
        $this->loadPhotosForDisplay();
    }

    public function nextPhotoInLightbox()
    {
        if ($this->photosForDisplay->isEmpty()){
            $this->closeCustomLightbox();
            return;
        }
        if ($this->currentLightboxPhotoIndex < ($this->photosForDisplay->count() - 1)) {
            $this->currentLightboxPhotoIndex++;
        } else {
            $this->currentLightboxPhotoIndex = 0; // Loop al inicio
        }
        $this->currentLightboxPhoto = $this->photosForDisplay->get($this->currentLightboxPhotoIndex);
    }

    public function previousPhotoInLightbox()
    {
        if ($this->photosForDisplay->isEmpty()){
            $this->closeCustomLightbox();
            return;
        }
        if ($this->currentLightboxPhotoIndex > 0) {
            $this->currentLightboxPhotoIndex--;
        } else {
            $this->currentLightboxPhotoIndex = $this->photosForDisplay->count() - 1; // Loop al final
        }
        $this->currentLightboxPhoto = $this->photosForDisplay->get($this->currentLightboxPhotoIndex);
    }
    // --- Fin Métodos Lightbox ---

    // --- Métodos de Gestión de Galería ---
    public function openManagerModal()
    {
        if (!$this->isAdmin || !$this->collectionConfig) return;
        $this->collectionConfig->refresh();
        $this->editableCollectionTitleModal = $this->galleryTitle ?? '';
        $this->editableCollectionDescriptionModal = $this->galleryDescription ?? '';
        $this->resetManagerModalFields();
        if (Auth::check() && method_exists(Auth::user(), 'likedPhotos')) {
            $this->likedPhotosForUserModal = Auth::user()->likedPhotos()->orderBy('photo_user_likes.created_at', 'desc')->limit(50)->get();
        }
        $this->showManagerModal = true;
    }

    public function closeManagerModal()
    {
        $this->showManagerModal = false;
        $this->resetManagerModalFields();
    }

    private function resetManagerModalFields()
    {
        $this->newPhotosToUploadModal = [];
        $this->selectedAlbumIdModal = null;
        $this->photosFromAlbumModal = new Collection();
        $this->selectedPhotosFromAlbumModalArray = [];
        $this->selectedLikedPhotosModalArray = [];
        $this->searchQueryModal = '';
        $this->searchedPhotosModalPaginator = null;
        $this->selectedExistingPhotosModalArray = [];
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function updatedSelectedAlbumIdModal($albumId)
    {
        $this->photosFromAlbumModal = new Collection();
        $this->selectedPhotosFromAlbumModalArray = [];
        if ($albumId && class_exists(Album::class)) {
            $album = Album::find($albumId);
            if ($album && method_exists($album, 'photos')) {
                $this->photosFromAlbumModal = $album->photos()->orderBy('created_at', 'desc')->get();
            }
        }
    }

    public function searchPhotosInModal()
    {
        $this->resetPage('managerSearchedPhotosPage');
        if (strlen($this->searchQueryModal) >= 3 && method_exists(Photo::class, 'where')) {
            $this->searchedPhotosModalPaginator = Photo::where('filename', 'like', '%' . $this->searchQueryModal . '%')
                ->orderBy('created_at', 'desc')
                ->paginate(10, ['*'], 'managerSearchedPhotosPage');
        } else {
            $this->searchedPhotosModalPaginator = null;
        }
    }

    public function updatedSearchQueryModal() {
         if (strlen($this->searchQueryModal) < 3) {
             $this->searchedPhotosModalPaginator = null;
        } else {
            $this->searchPhotosInModal();
        }
    }

    public function saveGalleryMetadata()
    {
        if (!$this->isAdmin || !$this->collectionConfig) return;
        $this->validate([
            'editableCollectionTitleModal' => 'nullable|string|max:255',
            'editableCollectionDescriptionModal' => 'nullable|string|max:5000',
        ]);
        $this->collectionConfig->title = $this->editableCollectionTitleModal;
        $this->collectionConfig->description = $this->editableCollectionDescriptionModal;
        $this->collectionConfig->save();

        $this->galleryTitle = $this->collectionConfig->title;
        $this->galleryDescription = $this->collectionConfig->description;
        session()->flash('cpg_modal_message', 'Información de la galería actualizada.');
    }

    public function uploadAndAttachToCollection()
    {
        if (!$this->isAdmin || !$this->collectionConfig) return;
        $this->validateOnly('newPhotosToUploadModal.*');

        if (empty($this->newPhotosToUploadModal)) {
            session()->flash('cpg_modal_error', 'No hay fotos seleccionadas para subir.');
            return;
        }

        $orderStart = ($this->collectionConfig->photos()->count() > 0 ? $this->collectionConfig->photos()->max('order') : 0) + 1;
        $newPhotoIdsWithOrder = [];

        foreach ($this->newPhotosToUploadModal as $photoFile) {
            $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
            $filename = Str::slug($originalFilename) . '-' . uniqid() . '.' . $photoFile->extension();
            $path = $photoFile->storeAs('curated_portrait_galleries/' . $this->identifier, $filename, 'public');

            $photo = Photo::create([
                'file_path' => $path,
                'thumbnail_path' => $path,
                'filename' => $photoFile->getClientOriginalName(),
                'album_id' => null,
                'usuario_id' => Auth::id(),
            ]);
            $newPhotoIdsWithOrder[$photo->id] = ['order' => $orderStart++];
        }

        if (!empty($newPhotoIdsWithOrder)) {
            $this->collectionConfig->photos()->attach($newPhotoIdsWithOrder);
        }

        $this->newPhotosToUploadModal = [];
        $this->loadPhotosForDisplay();
        session()->flash('cpg_modal_message', count($newPhotoIdsWithOrder) . ' nuevas fotos subidas y añadidas.');
    }

    public function attachExistingPhotosToCollection()
    {
        if (!$this->isAdmin || !$this->collectionConfig) return;

        $allSelectedIds = array_unique(array_filter(array_merge(
            array_map('intval', $this->selectedPhotosFromAlbumModalArray),
            array_map('intval', $this->selectedLikedPhotosModalArray),
            array_map('intval', $this->selectedExistingPhotosModalArray)
        )));

        if (empty($allSelectedIds)) {
            session()->flash('cpg_modal_error', 'No seleccionaste ninguna foto existente para añadir.');
            return;
        }

        $orderStart = ($this->collectionConfig->photos()->count() > 0 ? $this->collectionConfig->photos()->max('order') : 0) + 1;
        $photosToAttach = [];
        foreach ($allSelectedIds as $photoId) {
            if (!$this->collectionConfig->photos()->where('photo_id', $photoId)->exists()) {
                $photosToAttach[$photoId] = ['order' => $orderStart++];
            }
        }

        if (!empty($photosToAttach)) {
            $this->collectionConfig->photos()->attach($photosToAttach);
            $this->loadPhotosForDisplay();
            $this->selectedPhotosFromAlbumModalArray = [];
            $this->selectedLikedPhotosModalArray = [];
            $this->selectedExistingPhotosModalArray = [];
            session()->flash('cpg_modal_message', count($photosToAttach) . ' fotos existentes añadidas.');
        } else {
            session()->flash('cpg_modal_error', 'Las fotos seleccionadas ya están en la colección o no se seleccionó ninguna nueva.');
        }
    }

    public function removeFromCollection(int $photoId)
    {
        if (!$this->isAdmin || !$this->collectionConfig) return;
        $this->collectionConfig->photos()->detach($photoId);
        $this->loadPhotosForDisplay();
        session()->flash('cpg_message', 'Foto eliminada de esta colección.');
    }

    public function updateCollectionPhotoOrder($orderedItems)
    {
        if (!$this->isAdmin || !$this->collectionConfig) return;
        foreach ($orderedItems as $item) {
            $this->collectionConfig->photos()->updateExistingPivot($item['value'], ['order' => $item['order']]);
        }
        $this->loadPhotosForDisplay();
        session()->flash('cpg_message', 'Orden de fotos actualizado.');
    }
    // --- Fin Métodos de Gestión ---

    public function render()
    {
        // Asegurar que photosForDisplay esté siempre inicializada como una Collection
        if (!$this->photosForDisplay instanceof Collection) {
            // Esto podría ocurrir si loadPhotosForDisplay falla o collectionConfig es null
            Log::warning("CuratedPortraitGallery - render: photosForDisplay no era una colección. Re-inicializando. Identifier: '{$this->identifier}'");
            $this->photosForDisplay = new Collection();
            // Intentar recargar si es posible, aunque mount debería haberlo hecho
            if ($this->collectionConfig) {
                 $this->loadPhotosForDisplay();
            }
        }

        $currentModalSearchedPhotosPaginator = null;
        if ($this->isAdmin && $this->showManagerModal) {
             if (strlen($this->searchQueryModal) >= 3 && !$this->searchedPhotosModalPaginator) {
                $this->searchPhotosInModal();
            }
            $currentModalSearchedPhotosPaginator = $this->searchedPhotosModalPaginator;
        }

        return view('livewire.curated-portrait-gallery', [
            // Las propiedades públicas como $galleryTitle, $galleryDescription, $photosForDisplay,
            // $isAdmin, $showCustomLightbox, $currentLightboxPhoto, $currentLightboxPhotoIndex
            // están disponibles automáticamente en la vista. No es estrictamente necesario pasarlas aquí.
            'currentModalSearchedPhotosPaginator' => $currentModalSearchedPhotosPaginator
        ]);
    }
}
