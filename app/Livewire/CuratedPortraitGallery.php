<?php

namespace App\Livewire;

use App\Models\GridGallery as PortraitCollection;
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

    // --- Disco S3 donde guardar y leer fotos ----
    public string $disk = 'albums';

    public ?PortraitCollection $collectionConfig = null;
    public string $identifier = 'default_portrait_gallery';
    public ?string $galleryTitle = '';
    public ?string $galleryDescription = '';

    public Collection $photosForDisplay;

    public bool $isAdmin = false;
    public bool $showManagerModal = false;

    // --- Lightbox ---
    public bool $showCustomLightbox = false;
    public ?Photo $currentLightboxPhoto = null;
    public int $currentLightboxPhotoIndex = 0;

    // --- Modal de gestión ---
    public $newPhotosToUploadModal = [];
    public $selectedAlbumIdModal = null;
    public Collection $photosFromAlbumModal;
    public Collection $likedPhotosForUserModal;
    public string $searchQueryModal = '';
    public $searchedPhotosModalPaginator = null;
    public $selectedPhotosFromAlbumModalArray = [];
    public $selectedLikedPhotosModalArray = [];
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
            'selectedLikedPhotosModalArray'    => 'array',
            'selectedExistingPhotosModalArray' => 'array',
            'editableCollectionTitleModal'     => 'nullable|string|max:255',
            'editableCollectionDescriptionModal' => 'nullable|string|max:5000',
        ];
    }

    protected $messages = [
        'newPhotosToUploadModal.*.image' => 'Cada archivo debe ser una imagen.',
        'newPhotosToUploadModal.*.max'   => 'Cada imagen no debe superar los 2MB.',
    ];

    public function mount(string $identifier, ?string $defaultTitle = "Retratos Seleccionados", ?string $defaultDescription = "")
    {
        $this->identifier = $identifier;
        $this->isAdmin   = Auth::check() && Auth::user()->role === 'admin';

        $this->collectionConfig = PortraitCollection::firstOrCreate(
            ['identifier' => $this->identifier],
            ['title' => $defaultTitle, 'description' => $defaultDescription]
        );

        $this->galleryTitle       = $this->collectionConfig->title;
        $this->galleryDescription = $this->collectionConfig->description;

        $this->photosForDisplay       = new Collection();
        $this->photosFromAlbumModal   = new Collection();
        $this->likedPhotosForUserModal = new Collection();

        $this->loadPhotosForDisplay();

        if ($this->isAdmin) {
            $this->allAlbumsModalCollection = class_exists(Album::class)
                ? Album::orderBy('name')->withCount('photos')->get()
                : new Collection();

            if (method_exists(Auth::user(), 'likedPhotos')) {
                $this->likedPhotosForUserModal = Auth::user()
                    ->likedPhotos()
                    ->orderBy('photo_user_likes.created_at', 'desc')
                    ->limit(50)
                    ->get();
            }
        }
    }

    public function loadPhotosForDisplay()
    {
        if (! $this->collectionConfig) {
            Log::error("CuratedPortraitGallery: configuración '{$this->identifier}' no encontrada");
            $this->photosForDisplay = new Collection();
            $this->galleryTitle     = 'Error';
            $this->galleryDescription = 'Galería no encontrada';
            return;
        }

        $this->collectionConfig->refresh();
        $this->photosForDisplay    = $this->collectionConfig->photos()->orderByPivot('order')->get();
        $this->galleryTitle        = $this->collectionConfig->title;
        $this->galleryDescription  = $this->collectionConfig->description;
    }

    // --- Lightbox ---
    public function openCustomLightbox(int $photoId)
    {
        $this->currentLightboxPhoto = Photo::find($photoId);
        if (! $this->currentLightboxPhoto) {
            session()->flash('cpg_error', 'Foto no encontrada.');
            return;
        }

        if ($this->photosForDisplay->isEmpty()) {
            $this->loadPhotosForDisplay();
        }

        $this->currentLightboxPhotoIndex = $this->photosForDisplay
            ->pluck('id')
            ->search($photoId);

        $this->showCustomLightbox = true;
    }

    public function closeCustomLightbox()
    {
        $this->showCustomLightbox        = false;
        $this->currentLightboxPhoto      = null;
        $this->currentLightboxPhotoIndex = 0;
        // recarga para que la galería quede bien tras cerrar
        $this->loadPhotosForDisplay();
    }

    public function nextPhotoInLightbox()
    {
        if ($this->photosForDisplay->isEmpty()) {
            return $this->closeCustomLightbox();
        }

        $count = $this->photosForDisplay->count();
        $this->currentLightboxPhotoIndex = ($this->currentLightboxPhotoIndex + 1) % $count;
        $this->currentLightboxPhoto      = $this->photosForDisplay[$this->currentLightboxPhotoIndex];
    }

    public function previousPhotoInLightbox()
    {
        if ($this->photosForDisplay->isEmpty()) {
            return $this->closeCustomLightbox();
        }

        $count = $this->photosForDisplay->count();
        $this->currentLightboxPhotoIndex = ($this->currentLightboxPhotoIndex - 1 + $count) % $count;
        $this->currentLightboxPhoto      = $this->photosForDisplay[$this->currentLightboxPhotoIndex];
    }

    // --- Gestión de galería ---
    public function openManagerModal()
    {
        if (! $this->isAdmin) return;
        $this->collectionConfig->refresh();
        $this->editableCollectionTitleModal       = $this->galleryTitle;
        $this->editableCollectionDescriptionModal = $this->galleryDescription;
        $this->resetManagerModalFields();
        if (method_exists(Auth::user(), 'likedPhotos')) {
            $this->likedPhotosForUserModal = Auth::user()
                ->likedPhotos()
                ->orderBy('photo_user_likes.created_at', 'desc')
                ->limit(50)
                ->get();
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
        $this->newPhotosToUploadModal            = [];
        $this->selectedAlbumIdModal              = null;
        $this->photosFromAlbumModal              = new Collection();
        $this->selectedPhotosFromAlbumModalArray = [];
        $this->selectedLikedPhotosModalArray     = [];
        $this->searchQueryModal                  = '';
        $this->searchedPhotosModalPaginator      = null;
        $this->selectedExistingPhotosModalArray  = [];
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function updatedSelectedAlbumIdModal($albumId)
    {
        $this->photosFromAlbumModal = new Collection();
        $this->selectedPhotosFromAlbumModalArray = [];
        if ($albumId && class_exists(Album::class)) {
            $album = Album::find($albumId);
            if ($album) {
                $this->photosFromAlbumModal = $album->photos()->orderBy('created_at', 'desc')->get();
            }
        }
    }

    public function searchPhotosInModal()
    {
        $this->resetPage('managerSearchedPhotosPage');
        if (strlen($this->searchQueryModal) >= 3) {
            $this->searchedPhotosModalPaginator = Photo::where('filename', 'like', '%' . $this->searchQueryModal . '%')
                ->orderBy('created_at', 'desc')
                ->paginate(10, ['*'], 'managerSearchedPhotosPage');
        } else {
            $this->searchedPhotosModalPaginator = null;
        }
    }

    public function updatedSearchQueryModal()
    {
        if (strlen($this->searchQueryModal) < 3) {
            $this->searchedPhotosModalPaginator = null;
        } else {
            $this->searchPhotosInModal();
        }
    }

    public function saveGalleryMetadata()
    {
        if (! $this->isAdmin) return;
        $this->validateOnly('editableCollectionTitleModal');
        $this->validateOnly('editableCollectionDescriptionModal');

        $this->collectionConfig->update([
            'title'       => $this->editableCollectionTitleModal,
            'description' => $this->editableCollectionDescriptionModal,
        ]);

        $this->galleryTitle       = $this->collectionConfig->title;
        $this->galleryDescription = $this->collectionConfig->description;
        session()->flash('cpg_modal_message', 'Información actualizada.');
    }

    public function uploadAndAttachToCollection()
    {
        if (! $this->isAdmin) return;
        $this->validateOnly('newPhotosToUploadModal.*');
        if (empty($this->newPhotosToUploadModal)) {
            return session()->flash('cpg_modal_error', 'No hay fotos para subir.');
        }

        $orderStart = ($this->collectionConfig->photos()->max('order') ?: 0) + 1;
        $toAttach   = [];

        foreach ($this->newPhotosToUploadModal as $file) {
            $slug     = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
            $name     = $slug . '-' . uniqid() . '.' . $file->extension();
            // 👉 aquí pasamos el disco S3
            $path     = $file->storeAs("curated_portrait_galleries/{$this->identifier}", $name, $this->disk);

            $photo = Photo::create([
                'file_path'      => $path,
                'thumbnail_path' => $path,
                'filename'       => $file->getClientOriginalName(),
                'album_id'       => null,
                'uploaded_by'     => Auth::id(),
            ]);

            $toAttach[$photo->id] = ['order' => $orderStart++];
        }

        if ($toAttach) {
            $this->collectionConfig->photos()->attach($toAttach);
        }

        $this->newPhotosToUploadModal = [];
        $this->loadPhotosForDisplay();
        session()->flash('cpg_modal_message', count($toAttach).' fotos subidas y añadidas.');
    }

    public function attachExistingPhotosToCollection()
    {
        if (! $this->isAdmin) return;
        $ids = array_unique(array_filter(array_merge(
            $this->selectedPhotosFromAlbumModalArray,
            $this->selectedLikedPhotosModalArray,
            $this->selectedExistingPhotosModalArray
        )));

        if (empty($ids)) {
            return session()->flash('cpg_modal_error', 'No seleccionaste ninguna foto.');
        }

        $orderStart = ($this->collectionConfig->photos()->max('order') ?: 0) + 1;
        $attach     = [];

        foreach ($ids as $id) {
            if (! $this->collectionConfig->photos()->where('photo_id', $id)->exists()) {
                $attach[$id] = ['order' => $orderStart++];
            }
        }

        if ($attach) {
            $this->collectionConfig->photos()->attach($attach);
            $this->loadPhotosForDisplay();
            $this->selectedPhotosFromAlbumModalArray = [];
            $this->selectedLikedPhotosModalArray     = [];
            $this->selectedExistingPhotosModalArray  = [];
            session()->flash('cpg_modal_message', count($attach).' fotos añadidas.');
        } else {
            session()->flash('cpg_modal_error', 'Las fotos ya estaban en la colección.');
        }
    }

    public function removeFromCollection(int $photoId)
    {
        if (! $this->isAdmin) return;
        $this->collectionConfig->photos()->detach($photoId);
        $this->loadPhotosForDisplay();
        session()->flash('cpg_message', 'Foto eliminada.');
    }

    public function updateCollectionPhotoOrder($orderedItems)
    {
        if (! $this->isAdmin) return;
        foreach ($orderedItems as $item) {
            $this->collectionConfig->photos()->updateExistingPivot($item['value'], ['order' => $item['order']]);
        }
        $this->loadPhotosForDisplay();
        session()->flash('cpg_message', 'Orden actualizado.');
    }

    public function render()
    {
        return view('livewire.curated-portrait-gallery');
    }
}
