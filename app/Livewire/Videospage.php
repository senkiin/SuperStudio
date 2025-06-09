<?php

namespace App\Livewire; // Define el espacio de nombres para este componente de Livewire.

use Livewire\Component; // Importa la clase base 'Component' de Livewire.

class Videospage extends Component // Define la clase 'Videospage' que hereda de 'Component'.
{
    // --- Propiedades Públicas del Componente ---
    // Estas propiedades estarán disponibles automáticamente en la vista Blade asociada.
    // Se utilizan para configurar el contenido de la cabecera y los metadatos de la página.

    /** @var string Título principal que se mostrará en la cabecera de la página. */
    public string $headerTitle = 'Videografos';

    /** @var string Subtítulo o texto descriptivo que acompaña al título principal en la cabecera. */
    public string $headerSubtitle = 'Capturamos los momentos más especiales de tu gran día.';

    /** @var string Título que se usará en la etiqueta <title> de la página HTML, importante para SEO. */
    public string $pageTitle = 'Videografos - Fotografía y Videografía Profesional';

    /** @var string Descripción que se usará en la etiqueta <meta name="description">, importante para SEO. */
    public string $metaDescription = 'Descubre nuestros álbumes de bodas y cómo podemos inmortalizar tu celebración. Fotografía y videografía profesional para bodas.';

    /**
     * Método render: Se encarga de renderizar la vista Blade asociada a este componente.
     * Livewire llamará a este método para obtener el HTML que debe mostrarse.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function render()
    {
        // Devuelve la vista 'livewire.videospage'.
        // Las propiedades públicas ($headerTitle, $headerSubtitle, etc.)
        // estarán disponibles como variables dentro de esa vista Blade.
        return view('livewire.videospage');
    }
}
