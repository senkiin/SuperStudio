<?php

namespace App\Livewire;

use App\Models\CarouselImage;
use Livewire\Component;
use Illuminate\Support\Collection; // Import Collection

class HomepageCarousel extends Component
{
    public Collection $images; // Usaremos una Colección de Laravel
    public string $slogan = "Igniting Passion"; // Eslogan por defecto

    public function mount()
    {
        $this->loadImages();
        // Aquí podrías cargar el slogan desde la BD si lo haces editable
        // $setting = Setting::where('key', 'homepage_slogan')->first();
        // $this->slogan = $setting->value ?? "Igniting Passion";
    }

    public function loadImages()
    {
        // Carga las imágenes activas, ordenadas
        $this->images = CarouselImage::where('is_active', true)
                                    ->orderBy('order', 'asc')
                                    ->get();
    }

    public function render()
    {
        // Si no hay imágenes, podríamos mostrar una por defecto
        if ($this->images->isEmpty()) {
             // Crear una imagen placeholder si la colección está vacía
             $placeholder = new CarouselImage([
                'image_path' => 'images/placeholder-hero.jpg', // Ruta en public/images
                'caption' => $this->slogan,
            ]);
            // Usamos asset() para rutas en public/
            $placeholder->imageUrl = asset($placeholder->image_path);
            $this->images = collect([$placeholder]);
        }

        return view('livewire.homepage-carousel');
    }
}
