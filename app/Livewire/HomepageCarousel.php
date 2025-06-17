<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\CarouselImage;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\FilesystemAdapter;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Log;

class HomepageCarousel extends Component
{
    /**
     * Colección de imágenes transformadas para el carrusel.
     *
     * @var SupportCollection
     */
    public SupportCollection $images;

    /** @var string El slogan o texto que quieras mostrar */
    public string $slogan = '';

    /**
     * Al montar el componente cargamos las imágenes.
     */
    public function mount(): void
    {
        $this->loadImages();
    }

    /**
     * Carga las imágenes activas desde la base de datos,
     * construye los objetos con sus URLs en S3 y las almacena en $this->images.
     */
    public function loadImages(): void
    {
        /** @var FilesystemAdapter $s3 */
        $s3 = Storage::disk('s3');

        $records = CarouselImage::where('is_active', true)
            ->orderBy('order')
            ->get();

        $this->images = $records->map(fn($img) => (object) [
            'caption'       => $img->caption,
            'link_url'      => $img->link_url,
            'image_url'     => $s3->url($img->image_path),
            'thumbnail_url' => $img->thumbnail_path
                ? $s3->url($img->thumbnail_path)
                : null,
        ]);

        Log::debug("HomepageCarousel loaded {$this->images->count()} images from S3.");
    }

    /**
     * Listener de Livewire: cuando el admin emite 'carouselUpdated',
     * recargamos el listado de imágenes.
     */
    #[On('carouselUpdated')]
    public function refreshCarouselData(): void
    {
        Log::debug('HomepageCarousel received carouselUpdated event. Refreshing data…');
        $this->loadImages();
    }

    /**
     * Renderiza la vista sin necesidad de pasar explícitamente $images,
     * Livewire ya expondrá la propiedad pública.
     */
    public function render()
    {
        return view('livewire.homepage-carousel');
    }
}
