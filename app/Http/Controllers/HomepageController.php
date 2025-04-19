<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Para URL de storage si es necesario
// Opcional: Importar modelo si guardas contenido en BD (ej. Page o Setting)
// use App\Models\PageContent;

class HomepageController extends Controller
{
    /**
     * Muestra la página de inicio.
     */
    public function __invoke(Request $request) // Usamos __invoke para que el controlador maneje una sola acción
    {
        // --- Obtener Contenido Editable ---
        // Forma 1: Valores por defecto (si aún no tienes BD para esto)
        $heroImagePath = '/media/default/hero-placeholder.jpg'; // Ruta DENTRO de public/storage o public/images
        $isStorageImage = false; // ¿Está en 'storage' o en 'public'?
        $heroTitle = "IGNITING PASSION"; // Título por defecto
        $heroSubtitle = "Let's ignite your passion for photography and travel"; // Subtítulo por defecto

        // Forma 2: Obtener de BD (RECOMENDADO para que sea editable)
        // Esto requiere que tengas una tabla y lógica para guardar/recuperar estos valores.
        // Ejemplo conceptual (necesitarías adaptar esto a tu modelo real):
        /*
        $homepageContent = PageContent::where('slug', 'homepage')->first();
        if ($homepageContent) {
             $heroImagePath = $homepageContent->hero_image_path ?? $heroImagePath; // Usa valor de BD o default
             $isStorageImage = true; // Asume que las imágenes de BD están en Storage
             $heroTitle = $homepageContent->title ?? $heroTitle;
             $heroSubtitle = $homepageContent->subtitle ?? $heroSubtitle;
        }
        */

        // Generar la URL correcta para la imagen
        // $heroImageUrl = $isStorageImage ? Storage::url($heroImagePath) : asset($heroImagePath);
        // -- SIMPLIFICADO POR AHORA: Asumamos que usaremos asset() para una imagen en public/images --
         $heroImageUrl = asset('images/home-hero.jpg'); // Asegúrate que esta imagen exista

        // Pasar los datos a la vista
        return view('home', [ // Usaremos una nueva vista 'home.blade.php'
            'heroImageUrl' => $heroImageUrl,
            'heroTitle' => $heroTitle,
            'heroSubtitle' => $heroSubtitle,
        ]);
    }
}
