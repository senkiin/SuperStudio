<?php

namespace App\Livewire;

use Livewire\Component;

class WeddingsPage extends Component
{
    // These properties can be made dynamic by fetching from a settings table or a dedicated model
    public string $headerTitle = 'Bodas de Ensueño';
    public string $headerSubtitle = 'Capturamos los momentos más especiales de tu gran día.';
    public string $headerImageUrl = 'https://via.placeholder.com/1920x800/cccccc/888888?text=Imagen+Cabecera+Bodas'; // Placeholder
    public string $pageTitle = 'Bodas - Fotografía y Videografía Profesional';
    public string $metaDescription = 'Descubre nuestros álbumes de bodas y cómo podemos inmortalizar tu celebración. Fotografía y videografía profesional para bodas.';


    public function render()
    {
        return view('livewire.weddings-page');
    }
}
