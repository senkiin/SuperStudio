<?php

namespace App\Livewire;

use App\Models\CarouselImage;
use Livewire\Component;
use Illuminate\Support\Collection; // Import Collection
use Livewire\Attributes\On;       // <-- Importa esto
use Illuminate\Support\Facades\Log; // <-- Importa Log si quieres depurar
use App\Livewire\Admin\ManageHomepageCarousel;
class HomepageCarousel extends Component
{
    public Collection $images;
    public string $slogan = 'IGNITING PASSION'; // O como lo obtengas

    public function mount()
    {
        $this->loadImages();
    }

    public function loadImages()
    {
        // Carga solo imágenes activas y ordenadas para mostrar
        $this->images = CarouselImage::where('is_active', true)
                                    ->orderBy('order', 'asc')
                                    ->get();
        Log::debug('HomepageCarousel loaded ' . $this->images->count() . ' images.');
    }

    // Listener que se activa cuando ManageHomepageCarousel dispara 'carouselUpdated'
    #[On('carouselUpdated')]
    public function refreshCarouselData()
    {
        Log::debug('HomepageCarousel received carouselUpdated event. Refreshing data...');
        $this->loadImages(); // Vuelve a cargar las imágenes

        // Opcional: Forzar actualización de Alpine si es necesario (ver nota abajo)
        // $this->dispatch('alpine-carousel-images-updated', images: $this->images->toArray()); // Ejemplo
    }

    public function render()
    {
        return view('livewire.homepage-carousel');
    }
}
