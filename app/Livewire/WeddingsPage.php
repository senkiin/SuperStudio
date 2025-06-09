<?php

namespace App\Livewire; // Define el espacio de nombres para este componente de Livewire.

use Livewire\Component; // Importa la clase base 'Component' de Livewire.

class WeddingsPage extends Component // Define la clase 'WeddingsPage' que hereda de 'Component'.
{
    // --- Propiedades Públicas del Componente ---
    // Estas propiedades estarán disponibles automáticamente en la vista Blade asociada.
    // Se utilizan para configurar el contenido de la cabecera, la imagen de cabecera y los metadatos de la página.

    // El comentario original sugiere que estas propiedades podrían hacerse dinámicas
    // obteniéndolas de una tabla de configuración o un modelo dedicado, lo cual es una buena práctica
    // si se necesita que este contenido sea editable por un administrador sin modificar el código.

    /** @var string Título principal que se mostrará en la cabecera de la página de bodas. */
    public string $headerTitle = 'Bodas de Ensueño';

    /** @var string Subtítulo o texto descriptivo que acompaña al título principal en la cabecera. */
    public string $headerSubtitle = 'Capturamos los momentos más especiales de tu gran día.';

    /** @var string URL de la imagen que se usará como fondo o imagen principal en la cabecera. Actualmente es un placeholder. */
    public string $headerImageUrl = 'https://via.placeholder.com/1920x800/cccccc/888888?text=Imagen+Cabecera+Bodas'; // Placeholder

    /** @var string Título que se usará en la etiqueta <title> de la página HTML, importante para SEO. */
    public string $pageTitle = 'Bodas - Fotografía y Videografía Profesional';

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
        // Devuelve la vista 'livewire.weddings-page'.
        // Todas las propiedades públicas ($headerTitle, $headerSubtitle, $headerImageUrl, etc.)
        // estarán disponibles como variables dentro de esa vista Blade.
        return view('livewire.weddings-page');
    }
}
