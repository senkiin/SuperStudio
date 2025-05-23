<?php

namespace App\Livewire;

use App\Models\Album;
use App\Models\Photo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str; // Asegúrate de importar Str
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class AlbumDetailPage extends Component
{
    use WithPagination;

    // --- Disco S3 donde están las fotos ----
    protected string $disk = 'albums';

    // --- Datos del álbum y meta ----
    public Album $album;
    public string $pageTitle         = '';
    public string $metaDescription   = '';

    // --- Lightbox personalizado ----
    public bool $showCustomLightbox       = false;
    public ?Photo $currentLightboxPhoto   = null;
    public int $currentLightboxPhotoIndex = 0;
    public Collection $photosForCustomLightbox;

    protected $paginationTheme = 'tailwind';

    public function mount(Album $album)
    {
        $this->album = $album;

        // Carga las fotos si no vienen precargadas
        if (! $this->album->relationLoaded('photos')) {
            $this->album->load('photos');
        }

        $this->pageTitle       = $this->album->name ?? $this->album->title ?? 'Detalle del Álbum';
        $this->metaDescription = Str::limit($this->album->description ?? '', 150);

        // Prepara colección completa para el lightbox
        $this->photosForCustomLightbox = $this->album
            ->photos()
            ->orderBy('id')
            ->get();
    }

    /**
     * Abre el lightbox personalizado en la foto clicada
     */
    public function openCustomLightbox(int $photoId): void
    {
        $index = $this->photosForCustomLightbox
            ->pluck('id')
            ->search(fn($id) => $id === $photoId);

        if ($index !== false) {
            $this->currentLightboxPhotoIndex = $index;
            $this->currentLightboxPhoto      = $this->photosForCustomLightbox->get($index);
            $this->showCustomLightbox        = true;
        }
    }

    public function closeCustomLightbox(): void
    {
        $this->showCustomLightbox        = false;
        $this->currentLightboxPhoto      = null;
        $this->currentLightboxPhotoIndex = 0;
    }

    public function nextPhotoInLightbox(): void
    {
        if ($this->currentLightboxPhotoIndex < $this->photosForCustomLightbox->count() - 1) {
            $this->currentLightboxPhotoIndex++;
            $this->currentLightboxPhoto = $this->photosForCustomLightbox->get($this->currentLightboxPhotoIndex);
        }
    }

    public function previousPhotoInLightbox(): void
    {
        if ($this->currentLightboxPhotoIndex > 0) {
            $this->currentLightboxPhotoIndex--;
            $this->currentLightboxPhoto = $this->photosForCustomLightbox->get($this->currentLightboxPhotoIndex);
        }
    }

    /**
     * (Opcional) Obtiene dimensiones de la imagen usando S3
     */
    private function getImageDimensions(string $path): array
    {
        if ($path && Storage::disk($this->disk)->exists($path)) {
            try {
                $fullPath   = Storage::disk($this->disk)->path($path);
                $dimensions = @getimagesize($fullPath);
                return [
                    $dimensions[0] ?? 1200,
                    $dimensions[1] ?? 800,
                ];
            } catch (\Throwable $e) {
                // Silencia errores
            }
        }

        return [1200, 800];
    }

   #[Computed]
public function photoUrls(): array
{
    /** @var \Illuminate\Filesystem\FilesystemAdapter $diskAdapter */
    $diskAdapter = Storage::disk($this->disk);

    return $this->photosForCustomLightbox
        ->mapWithKeys(function (Photo $photo) use ($diskAdapter) {
            // Elige primero el thumbnail si existe, si no el file_path
            $path = $photo->thumbnail_path ?: $photo->file_path;

            if ($path && $diskAdapter->exists($path)) {
                return [$photo->id => $diskAdapter->url($path)];
            }

            return [$photo->id => null];
        })
        ->toArray();
}


    public function render()
    {
        // Paginamos solo las fotos que se mostrarán en la galería
        $photos = $this->album
            ->photos()
            ->orderBy('id')
            ->paginate(24, ['*'], 'galleryPage');

        return view('livewire.album-detail-page', [
            'photos'    => $photos,
            'disk'      => $this->disk,
            'photoUrls' => $this->photoUrls(),
        ]);
    }
}
