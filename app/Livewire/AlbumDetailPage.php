<?php

namespace App\Livewire;

use App\Models\Album; // Modelo Eloquent para los álbumes.
use App\Models\Photo; // Modelo Eloquent para las fotos.
use Illuminate\Support\Collection; // Clase Collection de Laravel para un manejo de arrays más fluido.
use Illuminate\Support\Facades\Storage; // Fachada para interactuar con el sistema de archivos (ej. S3).
use Illuminate\Support\Str; // Helper de Laravel para manipulación de strings (ej. para descripciones).
use Livewire\Attributes\Computed; // Atributo para definir propiedades computadas (cacheadas).
use Livewire\Component; // Clase base para todos los componentes Livewire.
use Livewire\WithPagination; // Trait para habilitar la paginación de forma sencilla.

class AlbumDetailPage extends Component
{
    use WithPagination; // Habilita la paginación para la lista de fotos.

    // --- Disco S3 donde están las fotos ----
    // Define el disco de S3 que se utilizará para obtener las URLs de las imágenes.
    // Este nombre debe coincidir con una configuración en config/filesystems.php.
    protected string $disk = 'albums';

    // --- Datos del álbum y meta ----
    public Album $album; // El objeto del modelo Album que se está mostrando. Se inyecta en mount().
    public string $pageTitle         = ''; // Título de la página, usado para SEO y la etiqueta <title>.
    public string $metaDescription   = ''; // Meta descripción para SEO.

    // --- Lightbox personalizado ----
    public bool $showCustomLightbox       = false; // Controla la visibilidad del modal del lightbox.
    public ?Photo $currentLightboxPhoto   = null; // Almacena el objeto Photo de la imagen actual en el lightbox.
    public int $currentLightboxPhotoIndex = 0;    // Índice de la foto actual dentro de la colección $photosForCustomLightbox.
    public Collection $photosForCustomLightbox; // Colección de todas las fotos del álbum, usada para la navegación en el lightbox.

    protected $paginationTheme = 'tailwind'; // Especifica que se usarán las vistas de paginación de Tailwind CSS.

    /**
     * Método `mount`: Se ejecuta una vez cuando el componente se inicializa.
     * Recibe el álbum (inyectado por Route Model Binding) y prepara los datos iniciales.
     *
     * @param Album $album El modelo del álbum a mostrar.
     */
    public function mount(Album $album)
    {
        $this->album = $album; // Asigna el álbum recibido a la propiedad pública.

        // Carga la relación 'photos' si no ha sido cargada previamente (eager loading).
        // Esto es útil si el álbum se pasa sin sus fotos ya cargadas.
        if (! $this->album->relationLoaded('photos')) {
            $this->album->load('photos');
        }

        // Establece el título de la página y la meta descripción para SEO.
        // Usa el nombre del álbum o un título genérico si no está disponible.
        $this->pageTitle       = $this->album->name ?? $this->album->title ?? 'Detalle del Álbum';
        // Limita la longitud de la meta descripción.
        $this->metaDescription = Str::limit($this->album->description ?? '', 150);

        // Prepara la colección completa de fotos del álbum para el lightbox.
        // Se obtienen todas las fotos ordenadas por ID para una navegación consistente.
        $this->photosForCustomLightbox = $this->album
            ->photos() // Accede a la relación 'photos' del modelo Album.
            ->orderBy('id') // Ordena las fotos (puedes cambiar el criterio si es necesario).
            ->get(); // Obtiene la colección de fotos.
    }

    /**
     * Abre el lightbox personalizado mostrando la foto con el ID especificado.
     *
     * @param int $photoId El ID de la foto que se debe mostrar en el lightbox.
     */
    public function openCustomLightbox(int $photoId): void
    {
        // Busca el índice de la foto seleccionada dentro de la colección completa de fotos del lightbox.
        $index = $this->photosForCustomLightbox
            ->pluck('id') // Obtiene solo los IDs de las fotos.
            ->search(fn($id) => $id === $photoId); // Busca el índice del ID coincidente.

        if ($index !== false) { // Si se encontró la foto en la colección.
            $this->currentLightboxPhotoIndex = $index; // Establece el índice actual.
            $this->currentLightboxPhoto      = $this->photosForCustomLightbox->get($index); // Obtiene el objeto Photo.
            $this->showCustomLightbox        = true; // Muestra el modal del lightbox.
        }
    }

    /**
     * Cierra el modal del lightbox y resetea las propiedades relacionadas.
     */
    public function closeCustomLightbox(): void
    {
        $this->showCustomLightbox        = false;
        $this->currentLightboxPhoto      = null;
        $this->currentLightboxPhotoIndex = 0;
    }

    /**
     * Navega a la siguiente foto en el lightbox.
     * Si está en la última foto, no hace nada (podría implementarse un bucle).
     */
    public function nextPhotoInLightbox(): void
    {
        if ($this->currentLightboxPhotoIndex < $this->photosForCustomLightbox->count() - 1) {
            $this->currentLightboxPhotoIndex++;
            $this->currentLightboxPhoto = $this->photosForCustomLightbox->get($this->currentLightboxPhotoIndex);
        }
    }

    /**
     * Navega a la foto anterior en el lightbox.
     * Si está en la primera foto, no hace nada (podría implementarse un bucle).
     */
    public function previousPhotoInLightbox(): void
    {
        if ($this->currentLightboxPhotoIndex > 0) {
            $this->currentLightboxPhotoIndex--;
            $this->currentLightboxPhoto = $this->photosForCustomLightbox->get($this->currentLightboxPhotoIndex);
        }
    }

    /**
     * (Opcional y no usado activamente en la vista actual)
     * Obtiene las dimensiones de una imagen almacenada en S3 (o el disco configurado).
     * Puede ser útil si necesitas las dimensiones para el frontend.
     *
     * @param string $path La ruta relativa del archivo en el disco.
     * @return array Un array con [ancho, alto] o dimensiones por defecto si falla.
     */
    private function getImageDimensions(string $path): array
    {
        if ($path && Storage::disk($this->disk)->exists($path)) {
            try {
                // Obtiene la ruta física del archivo (puede no funcionar igual con todos los adaptadores de S3 sin descarga local).
                // Para S3, generalmente se necesitaría descargar el archivo temporalmente o usar metadatos si están disponibles.
                $fullPath   = Storage::disk($this->disk)->path($path);
                $dimensions = @getimagesize($fullPath); // El @ suprime errores si getimagesize falla.
                return [
                    $dimensions[0] ?? 1200, // Ancho por defecto si no se puede obtener.
                    $dimensions[1] ?? 800,  // Alto por defecto.
                ];
            } catch (\Throwable $e) {
                // Silencia errores y devuelve valores por defecto.
                // Podrías registrar el error si es importante: Log::error("No se pudieron obtener dimensiones: {$e->getMessage()}");
            }
        }
        return [1200, 800]; // Dimensiones por defecto.
    }

   /**
    * Propiedad computada para generar las URLs de las fotos.
    * Utiliza el thumbnail_path si existe, de lo contrario usa file_path.
    * Las URLs generadas son para el disco S3 configurado en $this->disk.
    *
    * @return array Un array asociativo [photo_id => url_de_la_imagen].
    */
   #[Computed]
    public function photoUrls(): array
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $diskAdapter */
        // Obtiene una instancia del adaptador del sistema de archivos para el disco especificado.
        $diskAdapter = Storage::disk($this->disk);

        // Mapea la colección de fotos del lightbox para crear un array de URLs.
        return $this->photosForCustomLightbox
            ->mapWithKeys(function (Photo $photo) use ($diskAdapter) {
                // Elige la ruta de la miniatura (thumbnail_path) si está disponible,
                // de lo contrario, usa la ruta del archivo principal (file_path).
                $path = $photo->thumbnail_path ?: $photo->file_path;

                // Si la ruta existe y el archivo está presente en el disco, genera la URL pública.
                if ($path && $diskAdapter->exists($path)) {
                    return [$photo->id => $diskAdapter->url($path)];
                }
                // Si no, devuelve null para ese ID de foto.
                return [$photo->id => null];
            })
            ->toArray(); // Convierte la colección resultante a un array PHP.
    }


    /**
     * Renderiza la vista del componente.
     * Pasa las fotos paginadas y otros datos necesarios a la plantilla Blade.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        // Obtiene las fotos del álbum actual, ordenadas por ID y paginadas.
        // Esto es para la galería principal en la página, no para el lightbox (que usa la colección completa).
        $photos = $this->album
            ->photos() // Accede a la relación.
            ->orderBy('id') // Ordena las fotos.
            ->paginate(24, ['*'], 'galleryPage'); // Pagina los resultados (24 por página, nombre de paginador 'galleryPage').

        // Retorna la vista Blade 'livewire.album-detail-page' y le pasa los datos.
        return view('livewire.album-detail-page', [
            'photos'    => $photos, // Las fotos paginadas para la galería.
            'disk'      => $this->disk, // El nombre del disco S3 (para la vista si necesita generar URLs directamente).
            'photoUrls' => $this->photoUrls(), // Las URLs precalculadas (propiedad computada).
        ]);
    }
}
