<?php

namespace App\Livewire;

use Livewire\Component;

class Videospage extends Component
{
    public string $headerTitle = 'Videografos';
    public string $headerSubtitle = 'Capturamos los momentos más especiales de tu gran día.';
    public string $pageTitle = 'Videografos - Fotografía y Videografía Profesional';
    public string $metaDescription = 'Descubre nuestros álbumes de bodas y cómo podemos inmortalizar tu celebración. Fotografía y videografía profesional para bodas.';

    public function render()
    {
        return view('livewire.videospage');
    }
}
