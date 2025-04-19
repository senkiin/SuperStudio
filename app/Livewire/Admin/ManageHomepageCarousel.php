<?php

namespace App\Livewire\Admin;

use App\Models\CarouselImage;
use App\Models\Photo; // Para cargar favoritos
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // Para logs
use Livewire\Attributes\Validate;
use Livewire\WithPagination; // Para paginar favoritos
use Illuminate\Support\Collection; // Importar Collection para $carouselImages
use Illuminate\Pagination\LengthAwarePaginator; // Importar Paginator
use Illuminate\Support\Str;


class ManageHomepageCarousel extends Component
{
    use WithFileUploads;
    use WithPagination;

    // Propiedades para subir nuevas imágenes
    #[Validate('nullable|image|max:5120')]
    public $newImage;
    #[Validate('nullable|string|max:255')]
    public string $newCaption = '';
    #[Validate('nullable|url|max:255')]
    public string $newLinkUrl = '';

    // Propiedades para mostrar
    public Collection $carouselImages;

    // *** NUEVAS PROPIEDADES PARA EDICIÓN ***
    public ?int $editingImageId = null; // ID de la imagen que se está editando
    #[Validate('nullable|string|max:255')]
    public string $editingImageCaption = '';
    #[Validate('nullable|url|max:255')]
    public string $editingImageLinkUrl = '';
    // Nota: No necesitamos editar 'order' o 'is_active' inline aquí,
    // ya que se manejan con drag&drop y el botón de toggle.

    protected $validationAttributes = [
        'editingImageCaption' => 'título',
        'editingImageLinkUrl' => 'enlace URL',
        'newCaption' => 'título',
        'newLinkUrl' => 'enlace URL',
        'newImage' => 'archivo de imagen',
    ];


    public function mount()
    {
        $this->loadCarouselImages();
    }

    public function loadCarouselImages()
    {
        $this->carouselImages = CarouselImage::orderBy('order', 'asc')->get();
    }

    public function getLikedPhotosProperty(): LengthAwarePaginator | Collection
    {
        $admin = Auth::user();
        if ($admin) {
            return Photo::query()
                ->join('photo_user_likes', 'photos.id', '=', 'photo_user_likes.photo_id')
                ->where('photo_user_likes.user_id', $admin->id)
                ->with('album:id,name')
                ->orderBy('photo_user_likes.created_at', 'desc')
                ->paginate(10, ['photos.*'], 'likedPhotosPage');
        }
        return collect();
    }

    // --- Métodos existentes (uploadImage, deleteImage, addFromFavorite, toggleActive, updateImageOrder) ---
    // ... (Mantenemos los métodos anteriores sin cambios)...
     // Guarda la imagen subida
    public function uploadImage()
    {
        $this->validate([
            'newImage' => 'required|image|max:5120', // 'required' al subir
            'newCaption' => 'nullable|string|max:255',
            'newLinkUrl' => 'nullable|url|max:255',
        ]);

        $disk = 'public'; // Asegúrate que sea el disco correcto
        $directory = 'carousel_images'; // Carpeta específica

        try {
            // Guardar imagen principal
            $path = $this->newImage->store($directory, $disk);

            // Crear miniatura (Opcional pero recomendado - Usando Intervention Image si está instalado)
             $thumbnailPath = null;
             /* // Descomentar si tienes Intervention Image y el Job configurado
             if (class_exists(\Intervention\Image\ImageManager::class)) {
                 // Lógica para crear thumbnail aquí o despachar un Job
                 // Ejemplo simple (SIN JOB):
                 $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                 $img = $manager->read(Storage::disk($disk)->get($path));
                 $img->scaleDown(width: 300); // Ajusta tamaño thumbnail
                 $thumbFilename = pathinfo($path, PATHINFO_FILENAME) . '_thumb.' . pathinfo($path, PATHINFO_EXTENSION);
                 $thumbnailPath = $directory . '/' . $thumbFilename;
                 Storage::disk($disk)->put($thumbnailPath, (string) $img->encode());
             }
             */

            // Obtener el orden máximo actual para poner la nueva al final
            $maxOrder = CarouselImage::max('order') ?? -1;

            CarouselImage::create([
                'photo_id' => null,
                'image_path' => $path,
                'thumbnail_path' => $thumbnailPath,
                'caption' => $this->newCaption,
                'link_url' => $this->newLinkUrl,
                'order' => $maxOrder + 1,
                'is_active' => true,
            ]);

            session()->flash('message', 'Imagen añadida al carrusel.');
            $this->reset(['newImage', 'newCaption', 'newLinkUrl']); // Limpiar formulario
            $this->loadCarouselImages(); // Recargar lista

        } catch (\Exception $e) {
            Log::error("Error al subir imagen carrusel: " . $e->getMessage());
            session()->flash('error', 'Error al subir la imagen. Inténtalo de nuevo.');
        }
    }

    // Elimina una imagen del carrusel
    public function deleteImage(int $id)
    {
        // Si estamos editando la imagen que se va a borrar, cancelamos edición
        if ($this->editingImageId === $id) {
            $this->cancelEditing();
        }

        $image = CarouselImage::find($id);
        if ($image) {
            try {
                $image->delete();
                session()->flash('message', 'Imagen eliminada del carrusel.');
                $this->loadCarouselImages(); // Recargar
            } catch (\Exception $e) {
                Log::error("Error al eliminar imagen carrusel ID {$id}: " . $e->getMessage());
                session()->flash('error', 'Error al eliminar la imagen.');
            }
        }
    }

     // Añade una foto favorita al carrusel
     public function addFromFavorite(int $photoId)
     {
         $admin = Auth::user();
         if (!$admin) { /* ... error ... */ return; }

         $photo = Photo::with('album:id,name')->find($photoId); // Carga album para caption
         if (!$photo) { /* ... error ... */ return; }

         // Verificar si el admin le dio like (opcional pero bueno mantenerlo)
         $isAdminLiked = DB::table('photo_user_likes')->where('user_id', $admin->id)->where('photo_id', $photoId)->exists();
         if (!$isAdminLiked) { /* ... error ... */ return; }

         // *** LÓGICA DE COPIA DE ARCHIVOS ***
         $disk = 'public';
         $sourceImagePath = $photo->file_path;
         $sourceThumbPath = $photo->thumbnail_path;
         $carouselDirectory = 'carousel_images'; // Directorio destino para las copias

         // Verificar que el archivo original existe
         if (!$sourceImagePath || !Storage::disk($disk)->exists($sourceImagePath)) {
             session()->flash('error', 'No se encontró el archivo de la foto original.');
             Log::error("Archivo original no encontrado para Photo ID {$photoId}: {$sourceImagePath}");
             return;
         }

         // Generar un nuevo nombre único para la copia
         $newFilenameBase = Str::random(32); // Nombre aleatorio
         $extension = pathinfo($sourceImagePath, PATHINFO_EXTENSION);
         $newImagePath = $carouselDirectory . '/' . $newFilenameBase . '.' . $extension;
         $newThumbPath = null;

         try {
             // 1. Copiar la imagen principal
             Storage::disk($disk)->copy($sourceImagePath, $newImagePath);
             Log::debug("Imagen copiada a: {$newImagePath}");

             // 2. Copiar la miniatura si existe
             if ($sourceThumbPath && Storage::disk($disk)->exists($sourceThumbPath)) {
                 $thumbExtension = pathinfo($sourceThumbPath, PATHINFO_EXTENSION);
                 // Usar el mismo nombre base pero con sufijo _thumb (o como prefieras)
                 $newThumbPath = $carouselDirectory . '/' . $newFilenameBase . '_thumb.' . $thumbExtension;
                 Storage::disk($disk)->copy($sourceThumbPath, $newThumbPath);
                  Log::debug("Thumbnail copiado a: {$newThumbPath}");
             }

             // 3. Crear el registro en carousel_images con las NUEVAS RUTAS
             $maxOrder = CarouselImage::max('order') ?? -1;
             CarouselImage::create([
                 'photo_id' => $photo->id, // Guardamos la referencia al original (opcional, pero útil)
                 'image_path' => $newImagePath, // <-- Ruta de la COPIA
                 'thumbnail_path' => $newThumbPath, // <-- Ruta de la COPIA del thumb (puede ser null)
                 'caption' => $photo->album->name ?? 'Favorita', // Usar nombre del álbum o default
                 'link_url' => null,
                 'order' => $maxOrder + 1,
                 'is_active' => true,
             ]);

             session()->flash('message', 'Imagen favorita añadida (copiada) al carrusel.');
             $this->loadCarouselImages(); // Recargar lista

         } catch (\Exception $e) {
              Log::error("Error copiando o guardando favorita {$photoId} al carrusel: " . $e->getMessage());
              session()->flash('error', 'Error al añadir la imagen favorita al carrusel.');

              // Intenta borrar los archivos copiados si la creación del registro falló
              if (isset($newImagePath) && Storage::disk($disk)->exists($newImagePath)) {
                   Storage::disk($disk)->delete($newImagePath);
              }
              if (isset($newThumbPath) && $newThumbPath && Storage::disk($disk)->exists($newThumbPath)) {
                   Storage::disk($disk)->delete($newThumbPath);
              }
         }
     }
    // Cambia el estado activo/inactivo de una imagen
    public function toggleActive(int $id)
    {
        $image = CarouselImage::find($id);
        if ($image) {
            $image->update(['is_active' => !$image->is_active]);
            $this->loadCarouselImages();
        }
    }

    // Actualiza el orden de las imágenes (requiere JS para drag & drop)
    public function updateImageOrder($orderedIds)
    {
        // Si estamos editando alguna imagen, cancelamos para evitar conflictos
        Log::debug('updateImageOrder recibió:', $orderedIds);

        $this->cancelEditing();
        Log::debug('updateImageOrder recibió:', $orderedIds);


         try {
            foreach ($orderedIds as $index => $imageId) {
                // El ID viene como 'id' dentro del array $idInfo de SortableJS
                CarouselImage::where('id', $imageId)->update(['order' => $index]);
            }
             $this->loadCarouselImages(); // Recargar con el nuevo orden
             session()->flash('message', 'Orden de imágenes actualizado.');
         } catch (\Exception $e) {
             Log::error("Error actualizando orden carrusel: " . $e->getMessage());
             session()->flash('error', 'Error al actualizar el orden.');
             // Opcional: Volver a cargar el orden original si falla
             $this->loadCarouselImages();
         }
    }


    // *** NUEVOS MÉTODOS PARA EDICIÓN ***

    /**
     * Pone una imagen en modo de edición.
     */
    public function startEditing(int $imageId)
    {
        // Cancelar cualquier edición previa
        $this->cancelEditing();

        $image = CarouselImage::find($imageId);
        if ($image) {
            $this->editingImageId = $image->id;
            $this->editingImageCaption = $image->caption ?? '';
            $this->editingImageLinkUrl = $image->link_url ?? '';
        }
    }

    /**
     * Guarda los cambios de la imagen en edición.
     */
    public function saveEditing()
    {
        if ($this->editingImageId === null) {
            return; // No hay nada que guardar
        }

        // Validar solo las propiedades de edición
        $validated = $this->validate([
            'editingImageCaption' => 'nullable|string|max:255',
            'editingImageLinkUrl' => 'nullable|url|max:255',
        ]);

        $image = CarouselImage::find($this->editingImageId);
        if ($image) {
            try {
                $image->update([
                    'caption' => $validated['editingImageCaption'],
                    'link_url' => $validated['editingImageLinkUrl'],
                ]);
                session()->flash('message', 'Imagen actualizada.');
                $this->cancelEditing(); // Salir del modo edición
                $this->loadCarouselImages(); // Recargar datos
            } catch (\Exception $e) {
                Log::error("Error guardando edición imagen carrusel ID {$this->editingImageId}: " . $e->getMessage());
                session()->flash('error', 'Error al guardar los cambios.');
            }
        } else {
             session()->flash('error', 'No se encontró la imagen para guardar.');
             $this->cancelEditing(); // Resetea por si acaso
        }
    }

    /**
     * Cancela el modo de edición.
     */
    public function cancelEditing()
    {
        $this->reset(['editingImageId', 'editingImageCaption', 'editingImageLinkUrl']);
        // Limpiar errores de validación específicos de la edición si los hubiera
         $this->resetErrorBag(['editingImageCaption', 'editingImageLinkUrl']);
    }


    // Renderiza la vista de administración
    public function render()
    {
        return view('livewire.admin.manage-homepage-carousel', [
            'currentImages' => $this->carouselImages,
            'favoritePhotos' => $this->getLikedPhotosProperty(),
        ]);
        // ->layout('layouts.admin');
    }
}
