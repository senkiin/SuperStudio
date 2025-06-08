<?php

namespace App\Livewire\Admin;

// Importaciones de clases necesarias
use App\Livewire\HomepageCarousel; // Componente público del carrusel para despachar eventos
use App\Models\CarouselImage;    // Modelo Eloquent para las imágenes del carrusel
use App\Models\Photo;             // Modelo Eloquent para las fotos (usado para cargar "favoritas")
use Livewire\Component;           // Clase base para componentes Livewire
use Livewire\WithFileUploads;     // Trait para manejar subida de archivos
use Illuminate\Support\Facades\Storage; // Fachada para interactuar con el sistema de archivos (ej. S3)
use Illuminate\Support\Facades\Auth;    // Fachada para obtener el usuario autenticado
use Illuminate\Support\Facades\DB;      // Fachada para consultas directas a la BD (no se usa directamente aquí, pero es común)
use Illuminate\Support\Facades\Log;     // Fachada para registrar mensajes de log
use Livewire\Attributes\Validate;       // Atributo para definir reglas de validación directamente en propiedades
use Livewire\WithPagination;      // Trait para paginación fácil
use Illuminate\Support\Collection;  // Clase Collection de Laravel para manejar arrays de forma fluida
use Illuminate\Pagination\LengthAwarePaginator; // Clase para el paginador
use Illuminate\Support\Str;         // Helper para manipulación de strings (ej. nombres de archivo)
use Livewire\Attributes\On;         // Atributo para definir listeners de eventos Livewire
use Illuminate\Filesystem\FilesystemAdapter; // Para type-hinting del adaptador de S3

class ManageHomepageCarousel extends Component
{
    use WithFileUploads; // Habilita la subida de archivos en el componente
    use WithPagination;  // Habilita la paginación para listas (ej. fotos favoritas)

    // --- Control del Modal ---
    public bool $showModal = false; // Controla la visibilidad del modal de gestión del carrusel

    // --- Propiedades para Subir Nuevas Imágenes ---
    #[Validate('nullable|image|max:5120')] // Regla de validación: opcional, imagen, máx 5MB
    public $newImage; // Almacena el archivo de imagen temporal subido
    #[Validate('nullable|string|max:255')]
    public string $newCaption = ''; // Título/leyenda para la nueva imagen
    #[Validate('nullable|url|max:255')]
    public string $newLinkUrl = ''; // URL de enlace para la nueva imagen

    // --- Propiedades para Mostrar Imágenes Actuales ---
    public Collection $carouselImages; // Colección de objetos CarouselImage cargados de la BD

    // --- Propiedades para Edición Inline de Imágenes Existentes ---
    public ?int $editingImageId = null; // ID de la imagen que se está editando, null si no hay edición
    #[Validate('nullable|string|max:255')]
    public string $editingImageCaption = ''; // Título/leyenda para la imagen en edición
    #[Validate('nullable|url|max:255')]
    public string $editingImageLinkUrl = ''; // URL de enlace para la imagen en edición

    // Atributos para mensajes de validación amigables (personaliza los nombres de campo en los errores)
    protected $validationAttributes = [
        'editingImageCaption' => 'título (edición)',
        'editingImageLinkUrl' => 'enlace URL (edición)',
        'newCaption' => 'título (nuevo)',
        'newLinkUrl' => 'enlace URL (nuevo)',
        'newImage' => 'archivo de imagen (nuevo)',
    ];

    /**
     * Método `mount`: Se ejecuta una vez cuando el componente se inicializa.
     * Aquí se inicializan propiedades básicas.
     */
    public function mount()
    {
        // Inicializa la colección de imágenes como vacía. Se cargará cuando se abra el modal.
        $this->carouselImages = collect();
        Log::debug('ManageHomepageCarousel MOUNTED, initial $showModal state: ' . ($this->showModal ? 'true' : 'false'));
    }

    /**
     * Carga las imágenes del carrusel desde la base de datos, ordenadas por su campo 'order'.
     */
    public function loadCarouselImages()
    {
        $this->carouselImages = CarouselImage::orderBy('order', 'asc')->get();
    }

    /**
     * Propiedad computada: Obtiene las fotos marcadas como "favoritas" por el administrador.
     * Estas fotos se pueden añadir rápidamente al carrusel.
     * La lista está paginada.
     *
     * @return LengthAwarePaginator|Collection
     */
    public function getLikedPhotosProperty(): LengthAwarePaginator | Collection
    {
        $admin = Auth::user(); // Obtiene el usuario administrador autenticado
        // Solo carga las fotos favoritas si el modal está visible (para optimizar rendimiento)
        if ($admin && $this->showModal) {
            return Photo::query()
                // Une con la tabla pivot 'photo_user_likes' para filtrar por usuario
                ->join('photo_user_likes', 'photos.id', '=', 'photo_user_likes.photo_id')
                ->where('photo_user_likes.user_id', $admin->id) // Solo las del admin actual
                ->with('album:id,name') // Carga la relación 'album' para mostrar su nombre (eficiente)
                ->orderBy('photo_user_likes.created_at', 'desc') // Ordena por fecha en que se dio "like"
                ->paginate(6, ['photos.*'], 'likedPhotosPage'); // Pagina los resultados (6 por página, nombre de paginador 'likedPhotosPage')
        }
        // Si el modal no está visible o no hay admin, devuelve una colección vacía.
        return collect();
    }

    /**
     * Listener de Eventos Livewire: Se ejecuta cuando se recibe el evento 'openCarouselModal'.
     * Este método abre el modal de gestión.
     */
    #[On('openCarouselModal')]
    public function openModal()
    {
        Log::debug('ManageHomepageCarousel openModal method CALLED');
        $this->resetInputFields();   // Limpia todos los campos del formulario y errores antes de mostrar.
        $this->loadCarouselImages(); // Carga las imágenes actuales del carrusel desde la BD.
        $this->showModal = true;     // Establece la propiedad para hacer visible el modal en la vista.
        Log::debug('ManageHomepageCarousel Modal Opened via event');
    }

    /**
     * Se ejecuta al hacer clic en el botón "Cerrar" del modal o al interactuar fuera de él.
     * Cierra el modal de gestión.
     */
    public function closeModal()
    {
        Log::debug('ManageHomepageCarousel closeModal method CALLED');
        $this->showModal = false;    // Oculta el modal.
        $this->resetInputFields(); // Limpia los campos del formulario al cerrar.
        Log::debug('ManageHomepageCarousel Modal Closed');
    }

    /**
     * Método helper privado para resetear todos los campos del formulario,
     * el estado de edición y los errores de validación.
     */
    private function resetInputFields()
    {
        $this->reset(['newImage', 'newCaption', 'newLinkUrl']); // Resetea campos de nueva imagen
        $this->cancelEditing(); // Resetea campos y estado de edición inline
        $this->resetErrorBag(); // Limpia todos los errores de validación de Livewire
        $this->resetPage('likedPhotosPage'); // Resetea la paginación de la lista de fotos favoritas
    }

    /**
     * Sube una nueva imagen al carrusel.
     * Valida los datos, almacena el archivo en S3 y guarda la información en la BD.
     */
    public function uploadImage()
    {
        // Valida los campos del formulario para la nueva imagen.
        $this->validate([
            'newImage'      => 'required|image|max:5120', // Imagen obligatoria, máx 5MB
            'newCaption'    => 'nullable|string|max:255',   // Título opcional
            'newLinkUrl'    => 'nullable|url|max:255',      // URL opcional y válida
        ]);

        $disk    = 's3'; // Define el disco de S3 a utilizar (configurado en filesystems.php)
        try {
            /** @var FilesystemAdapter $s3 */
            $s3  = Storage::disk($disk); // Obtiene una instancia del adaptador de S3
            // Almacena el archivo en S3 en la carpeta 'carousel_images' y obtiene la ruta.
            // Laravel genera un nombre de archivo único automáticamente.
            $path = $this->newImage->store('carousel_images', $disk);

            // $url = $s3->url($path); // Obtiene la URL pública del archivo (no se usa directamente aquí pero es útil saberlo)

            // Guarda la información de la imagen en la base de datos.
            $maxOrder = CarouselImage::max('order') ?? -1; // Obtiene el orden máximo actual o -1 si no hay imágenes
            CarouselImage::create([
                'photo_id'       => null, // No se asocia a una foto existente de la galería principal
                'image_path'     => $path, // Ruta del archivo en S3
                'thumbnail_path' => null, // Podría generarse un thumbnail aquí si fuera necesario
                'caption'        => $this->newCaption,
                'link_url'       => $this->newLinkUrl,
                'order'          => $maxOrder + 1, // Asigna el siguiente orden
                'is_active'      => true, // Activa la imagen por defecto
            ]);

            session()->flash('message', 'Imagen añadida al carrusel en S3.'); // Mensaje de éxito
            $this->reset(['newImage', 'newCaption', 'newLinkUrl']); // Limpia los campos del formulario
            $this->loadCarouselImages(); // Recarga la lista de imágenes del carrusel
            // Emite un evento para que el componente público del carrusel (HomepageCarousel) se actualice.
            $this->dispatch('carouselUpdated')->to(HomepageCarousel::class);
        } catch (\Exception $e) {
            Log::error("Error al subir imagen al S3: " . $e->getMessage());
            session()->flash('error', 'Error al subir la imagen a S3. Inténtalo de nuevo.');
        }
    }

    /**
     * Elimina una imagen del carrusel.
     * Borra el archivo de S3 y el registro de la base de datos.
     *
     * @param int $id ID de la CarouselImage a eliminar.
     */
    public function deleteImage(int $id): void
    {
        $image = CarouselImage::findOrFail($id); // Encuentra la imagen o falla

        try {
            /** @var \Illuminate\Filesystem\FilesystemAdapter $s3 */
            $s3 = Storage::disk('s3'); // Disco S3

            // Prepara un array con las rutas de los archivos a eliminar (imagen principal y miniatura si existe)
            $toDelete = [$image->image_path];
            if ($image->thumbnail_path) {
                $toDelete[] = $image->thumbnail_path;
            }

            $s3->delete($toDelete); // Elimina los archivos de S3
            $image->delete();       // Elimina el registro de la BD

            session()->flash('message', 'Imagen eliminada correctamente.');
            $this->loadCarouselImages(); // Recarga la lista
            $this->dispatch('carouselUpdated')->to(HomepageCarousel::class); // Notifica al carrusel público
        } catch (\Exception $e) {
            Log::error("Error al eliminar imagen S3 [ID:{$id}]: {$e->getMessage()}");
            session()->flash('error', 'No se pudo eliminar la imagen. Inténtalo de nuevo.');
        }
    }

    /**
     * Añade una foto marcada como "favorita" por el admin al carrusel.
     * Esto implica COPIAR los archivos de la foto original a la carpeta del carrusel en S3.
     *
     * @param int $photoId ID de la Photo original a añadir.
     */
    public function addFromFavorite(int $photoId)
    {
        $admin = Auth::user();
        if (!$admin) return; // Seguridad básica

        $photo = Photo::with('album:id,name')->find($photoId); // Encuentra la foto con su álbum
        if (!$photo) return;

        // Disco donde están las fotos originales (ej. 'public', 'albums', etc.)
        // ¡¡¡IMPORTANTE!!! Este disco debe ser el correcto para las fotos originales.
        // Si las fotos originales están en S3 en un disco diferente a 'public', cámbialo.
        // Para este ejemplo, se asume que las fotos originales están en el disco 'public'.
        // Si tus fotos de `Photo` están en el disco 'albums' de S3, cambia 'public' por 'albums'.
        $sourceDisk = 'public'; // O 'albums' si las fotos originales están en ese disco S3
        $targetDisk = 's3';     // Disco de destino para el carrusel (S3)

        $sourceImagePath = $photo->file_path;
        $sourceThumbPath = $photo->thumbnail_path;
        $carouselDirectory = 'carousel_images'; // Carpeta de destino en S3 para el carrusel

        // Verifica que el archivo original exista en el disco fuente
        if (!$sourceImagePath || !Storage::disk($sourceDisk)->exists($sourceImagePath)) {
            session()->flash('error', 'No se encontró el archivo de la foto original.');
            Log::error("Archivo original no encontrado para Photo ID {$photoId} en disco '{$sourceDisk}': {$sourceImagePath}");
            return;
        }

        // Genera nombres de archivo únicos para las copias en el carrusel
        $newFilenameBase = Str::random(32); // Nombre base aleatorio
        $extension = pathinfo($sourceImagePath, PATHINFO_EXTENSION);
        $newImagePath = $carouselDirectory . '/' . $newFilenameBase . '.' . $extension; // Nueva ruta para la imagen principal
        $newThumbPath = null; // Nueva ruta para la miniatura (si existe)

        try {
            // 1. Copiar imagen principal del disco fuente al disco destino (S3)
            $fileContent = Storage::disk($sourceDisk)->get($sourceImagePath);
            Storage::disk($targetDisk)->put($newImagePath, $fileContent);

            // 2. Copiar miniatura si existe
            if ($sourceThumbPath && Storage::disk($sourceDisk)->exists($sourceThumbPath)) {
                $thumbExtension = pathinfo($sourceThumbPath, PATHINFO_EXTENSION);
                $newThumbPath = $carouselDirectory . '/' . $newFilenameBase . '_thumb.' . $thumbExtension;
                $thumbContent = Storage::disk($sourceDisk)->get($sourceThumbPath);
                Storage::disk($targetDisk)->put($newThumbPath, $thumbContent);
            }

            // 3. Crear registro en la BD para la imagen del carrusel
            $maxOrder = CarouselImage::max('order') ?? -1;
            CarouselImage::create([
                'photo_id' => $photo->id, // Referencia a la foto original
                'image_path' => $newImagePath, // Ruta de la copia en S3
                'thumbnail_path' => $newThumbPath, // Ruta de la copia de la miniatura en S3
                'caption' => $photo->album->name ?? 'Favorita', // Título por defecto
                'link_url' => null, // Sin enlace por defecto
                'order' => $maxOrder + 1,
                'is_active' => true,
            ]);

            session()->flash('message', 'Imagen favorita añadida (copiada) al carrusel.');
            $this->loadCarouselImages(); // Recargar lista
            $this->dispatch('carouselUpdated')->to(HomepageCarousel::class); // Notificar
        } catch (\Exception $e) {
            Log::error("Error copiando o guardando favorita {$photoId} al carrusel: " . $e->getMessage());
            session()->flash('error', 'Error al añadir la imagen favorita al carrusel.');
            // Intenta limpiar archivos copiados si la operación de BD falla
            if (isset($newImagePath) && Storage::disk($targetDisk)->exists($newImagePath)) {
                Storage::disk($targetDisk)->delete($newImagePath);
            }
            if (isset($newThumbPath) && $newThumbPath && Storage::disk($targetDisk)->exists($newThumbPath)) {
                Storage::disk($targetDisk)->delete($newThumbPath);
            }
        }
    }


    /**
     * Cambia el estado activo/inactivo de una imagen del carrusel.
     *
     * @param int $id ID de la CarouselImage a modificar.
     */
    public function toggleActive(int $id)
    {
        $image = CarouselImage::find($id);
        if ($image) {
            $image->update(['is_active' => !$image->is_active]); // Invierte el estado actual
            $this->loadCarouselImages(); // Recarga la lista
            $this->dispatch('carouselUpdated')->to(HomepageCarousel::class); // Notifica
        }
    }

    /**
     * Actualiza el orden de las imágenes del carrusel.
     * Este método es llamado por la librería SortableJS (o similar) desde el frontend.
     *
     * @param array $orderedItems Array de objetos, donde cada objeto tiene 'value' (ID) y 'order' (nuevo índice).
     */
    public function updateImageOrder($orderedItems)
    {
        Log::debug('updateImageOrder recibió:', $orderedItems);

        if (!is_array($orderedItems)) { // Comprobación básica de tipo
            Log::error('updateImageOrder no recibió un array:', $orderedItems);
            session()->flash('error', 'Error al procesar el nuevo orden (datos inválidos).');
            $this->loadCarouselImages(); // Recarga el orden actual para evitar inconsistencias
            return;
        }

        $this->cancelEditing(); // Cancela cualquier edición inline antes de reordenar

        try {
            // Itera sobre los ítems recibidos (cada uno representa una imagen y su nuevo orden)
            foreach ($orderedItems as $item) {
                // Asegura que el ítem tiene las claves esperadas 'value' (ID) y 'order' (nuevo orden)
                if (isset($item['value']) && isset($item['order'])) {
                    CarouselImage::where('id', $item['value'])->update(['order' => $item['order']]);
                } else {
                    Log::warning('Item inválido en updateImageOrder:', $item); // Registra si un ítem no es válido
                }
            }

            $this->loadCarouselImages(); // Recarga las imágenes con el nuevo orden
            session()->flash('message', 'Orden de imágenes actualizado.');
            $this->dispatch('carouselUpdated')->to(HomepageCarousel::class); // Notifica
        } catch (\Exception $e) {
            Log::error("Error actualizando orden carrusel: " . $e->getMessage());
            session()->flash('error', 'Error al actualizar el orden.');
            $this->loadCarouselImages(); // Recarga el orden original si algo falla
        }
    }

    // --- Métodos para Edición Inline ---

    /**
     * Inicia el modo de edición para una imagen específica.
     * Carga los datos de la imagen en las propiedades de edición.
     *
     * @param int $imageId ID de la CarouselImage a editar.
     */
    public function startEditing(int $imageId)
    {
        $this->cancelEditing(); // Cancela cualquier edición previa para evitar conflictos.
        $image = CarouselImage::find($imageId);
        if ($image) {
            $this->editingImageId = $image->id;
            $this->editingImageCaption = $image->caption ?? ''; // Carga el título actual o string vacío
            $this->editingImageLinkUrl = $image->link_url ?? ''; // Carga la URL actual o string vacío
        }
    }

    /**
     * Guarda los cambios realizados durante la edición inline.
     * Valida los datos y actualiza el registro en la BD.
     */
    public function saveEditing()
    {
        if ($this->editingImageId === null) return; // No hay nada que guardar si no se está editando

        // Valida los campos de edición.
        $validated = $this->validate([
            'editingImageCaption' => 'nullable|string|max:255',
            'editingImageLinkUrl' => 'nullable|url|max:255',
        ]);

        $image = CarouselImage::find($this->editingImageId);
        if ($image) {
            try {
                $image->update([ // Actualiza la imagen con los datos validados
                    'caption' => $validated['editingImageCaption'],
                    'link_url' => $validated['editingImageLinkUrl'],
                ]);
                session()->flash('message', 'Imagen actualizada.');
                $this->cancelEditing(); // Sale del modo edición
                $this->loadCarouselImages(); // Recarga la lista
                // No es necesario disparar 'carouselUpdated' aquí si solo cambian caption/link,
                // a menos que el carrusel público también los muestre y necesite refrescarse.
                // Si el carrusel público SÍ muestra caption/link, entonces sí:
                // $this->dispatch('carouselUpdated')->to(HomepageCarousel::class);
            } catch (\Exception $e) {
                Log::error("Error guardando edición imagen carrusel ID {$this->editingImageId}: " . $e->getMessage());
                session()->flash('error', 'Error al guardar los cambios.');
            }
        } else {
            session()->flash('error', 'No se encontró la imagen para guardar.');
            $this->cancelEditing(); // Sale del modo edición si la imagen ya no existe
        }
    }

    /**
     * Cancela el modo de edición inline.
     * Resetea las propiedades de edición y los errores de validación asociados.
     */
    public function cancelEditing()
    {
        $this->reset(['editingImageId', 'editingImageCaption', 'editingImageLinkUrl']);
        // Limpia solo los errores de validación de los campos de edición.
        $this->resetErrorBag(['editingImageCaption', 'editingImageLinkUrl']);
    }

    /**
     * Renderiza la vista del componente.
     * Pasa los datos necesarios a la plantilla Blade del modal.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        // La vista es el modal que se muestra/oculta con $this->showModal.
        return view('livewire.admin.manage-homepage-carousel', [
            'currentImages' => $this->carouselImages, // Imágenes actuales para mostrar en la lista del modal
            'favoritePhotos' => $this->getLikedPhotosProperty(), // Fotos favoritas paginadas para el selector
        ]);
    }
}
