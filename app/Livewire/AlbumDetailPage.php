<?php

namespace App\Livewire;

use App\Models\Album;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Photo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Collection; // Asegúrate de importar Collection

class AlbumDetailPage extends Component
{
    use WithPagination;

    public Album $album;
    public string $pageTitle = '';
    public string $metaDescription = '';

    // Propiedades para el modal personalizado
    public bool $showCustomLightbox = false;
    public ?Photo $currentLightboxPhoto = null;
    public int $currentLightboxPhotoIndex = 0;
    public Collection $photosForCustomLightbox; // Colección de todas las fotos del álbum para el lightbox

    protected $paginationTheme = 'tailwind';

    public function mount(Album $album)
    {
        $this->album = $album;

        if (!$this->album->relationLoaded('photos')) {
            $this->album->load('photos');
        }

        $this->pageTitle = $this->album->name ?? $this->album->title ?? 'Detalle del Álbum';
        $this->metaDescription = Str::limit($this->album->description, 150) ?? 'Viendo fotos de ' . $this->pageTitle;

        // Preparar todas las fotos para el lightbox personalizado
        $this->photosForCustomLightbox = $this->album->photos()->orderBy('id')->get(); // O el orden que prefieras
    }

    // Método para abrir el lightbox personalizado
    public function openCustomLightbox(int $photoId, int $photoIndexFromPaginatedView = 0)
    {
        $clickedPhoto = $this->photosForCustomLightbox->firstWhere('id', $photoId);

        if ($clickedPhoto) {
            $this->currentLightboxPhoto = $clickedPhoto;
            // Encontrar el índice real dentro de la colección completa (photosForCustomLightbox)
            $this->currentLightboxPhotoIndex = $this->photosForCustomLightbox->search(function ($photo) use ($photoId) {
                return $photo->id === $photoId;
            });
            $this->showCustomLightbox = true;
        }
    }

    public function closeCustomLightbox()
    {
        $this->showCustomLightbox = false;
        $this->currentLightboxPhoto = null;
        $this->currentLightboxPhotoIndex = 0;
    }

    public function nextPhotoInLightbox()
    {
        if ($this->currentLightboxPhotoIndex < ($this->photosForCustomLightbox->count() - 1)) {
            $this->currentLightboxPhotoIndex++;
            $this->currentLightboxPhoto = $this->photosForCustomLightbox->get($this->currentLightboxPhotoIndex);
        }
    }

    public function previousPhotoInLightbox()
    {
        if ($this->currentLightboxPhotoIndex > 0) {
            $this->currentLightboxPhotoIndex--;
            $this->currentLightboxPhoto = $this->photosForCustomLightbox->get($this->currentLightboxPhotoIndex);
        }
    }


    // El método getImageDimensions se puede mantener si lo necesitas para algo más,
    // pero para este modal simple no es estrictamente necesario para la imagen principal.
    private function getImageDimensions($path)
    {
        if ($path && Storage::disk('public')->exists($path)) {
            try {
                $fullPath = Storage::disk('public')->path($path);
                $dimensions = @getimagesize($fullPath);
                return [$dimensions[0] ?? 1200, $dimensions[1] ?? 800];
            } catch (\Exception $e) {
                // Log::error(...);
            }
        }
        return [1200, 800];
    }


    public function render()
    {
        $photosQuery = $this->album->photos();
        $photos = $photosQuery->orderBy('id')->paginate(24, ['*'], 'galleryPage');

        return view('livewire.album-detail-page',[
            'photos' => $photos,
        ]);
    }
}
