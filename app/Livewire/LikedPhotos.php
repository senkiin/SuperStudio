<?php

namespace App\Livewire; // Define el espacio de nombres para este componente de Livewire.

use App\Models\Photo; // Asegúrate de que esto apunta a tu modelo Photo.
use Livewire\Component; // Importa la clase base 'Component' de Livewire.
use Livewire\WithPagination; // Importa el trait para manejar la paginación.
use Illuminate\Support\Facades\Auth; // Importa la fachada de Autenticación para obtener el usuario actual.
use Illuminate\Support\Facades\Storage; // Importa la fachada de Storage para interactuar con el sistema de archivos (ej. S3).
use Illuminate\Support\Facades\Log;   // Opcional: para registrar información o errores.

class LikedPhotos extends Component
{
    use WithPagination; // Habilita la funcionalidad de paginación en este componente.

    public $user; // Almacenará la instancia del usuario autenticado.
    public string $disk = 'albums'; // Nombre de tu disco S3. Es público para ser accesible en la vista si fuera necesario en otros contextos, aunque no directamente para la generación de alpinePhotos ahora.
    public array $alpinePhotos = []; // Propiedad pública para almacenar los datos de las fotos que usará Alpine.js en la vista (ej. para un lightbox).

    protected $paginationTheme = 'tailwind'; // Especifica que se usarán las vistas de paginación de Tailwind CSS.

    /**
     * Método `mount`: Se ejecuta una vez cuando el componente se inicializa.
     * Aquí, obtenemos el usuario autenticado actualmente.
     */
    public function mount()
    {
        $this->user = Auth::user(); // Asigna el usuario autenticado a la propiedad $user.
    }

    /**
     * Método `unlikePhoto`: Permite al usuario quitar el "me gusta" de una foto.
     *
     * @param int $photoId El ID de la foto a la que se le quitará el "me gusta".
     */
    public function unlikePhoto(int $photoId)
    {
        // Si no hay un usuario autenticado, no hace nada.
        if (! $this->user) {
            return;
        }

        // Busca la foto entre las fotos que le gustan al usuario.
        $photo = $this->user->likedPhotos()->where('photos.id', $photoId)->first();
        if ($photo) {
            // Si la encuentra, elimina la relación (quita el "me gusta").
            $this->user->likedPhotos()->detach($photoId);
        }
        // Resetea la paginación para que la vista se actualice correctamente
        // y no muestre una página que podría quedar vacía.
        $this->resetPage();
    }

    /**
     * Método `render`: Se encarga de renderizar la vista Blade asociada a este componente.
     * Prepara los datos de las fotos favoritas paginadas y la información para Alpine.js.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function render()
    {
        // Si no hay un usuario autenticado, se asegura de que $alpinePhotos esté vacío
        // y pasa null como 'likedPhotos' a la vista.
        if (! $this->user) {
            $this->alpinePhotos = [];
            return view('livewire.liked-photos', ['likedPhotos' => null]);
        }

        // Obtiene las fotos que le gustan al usuario, paginadas.
        $likedPhotosPaginator = $this->user
            ->likedPhotos() // Accede a la relación 'likedPhotos' del modelo User.
            ->with([ // Carga anticipada (eager loading) de la relación 'album' de cada foto.
                'album' => function ($query) {
                    $query->select('id', 'name'); // Selecciona solo los campos 'id' y 'name' del álbum para optimizar.
                }
            ])
            ->orderByPivot('created_at', 'desc') // Ordena las fotos por la fecha en que se les dio "me gusta" (columna 'created_at' en la tabla pivote).
            ->paginate(20); // Pagina los resultados, mostrando 20 fotos por página.

        // Prepara el array $alpinePhotos basándose en la colección de fotos de la página actual del paginador.
        if ($likedPhotosPaginator && $likedPhotosPaginator->count() > 0) {
            $this->alpinePhotos = $likedPhotosPaginator->getCollection() // Obtiene la colección de fotos de la página actual.
                ->map(fn($photo) => [ // Mapea cada foto a un nuevo formato para Alpine.js.
                    // Usa file_path para el modal, asumiendo que es la imagen principal.
                    // $this->disk se accede desde la propiedad del componente.
                    'url' => ($photo->file_path && $this->disk) ? Storage::disk($this->disk)->url($photo->file_path) : null, // Genera la URL pública de la imagen.
                    // Genera un texto alternativo (alt) para la imagen, escapando caracteres especiales.
                    'alt' => e($photo->filename ?? 'Foto favorita') . ($photo->album?->name ? ' - ' . e($photo->album->name) : '')
                ])
                ->values() // Reestablece las claves del array para que sean numéricas consecutivas (0, 1, 2...).
                ->all();   // Convierte la colección de Laravel a un array PHP simple.
        } else {
            $this->alpinePhotos = []; // Asegura que sea un array vacío si no hay fotos favoritas.
        }

        // Pasa el paginador a la vista para mostrar la cuadrícula de fotos y los enlaces de paginación.
        // $this->alpinePhotos es una propiedad pública, por lo que está automáticamente disponible en la vista.
        return view('livewire.liked-photos', [
            'likedPhotos' => $likedPhotosPaginator
        ]);
    }
}
