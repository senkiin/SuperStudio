<?php

namespace App\Livewire\Admin;

use App\Livewire\HomepageCarousel;
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
use Livewire\Attributes\On; // Importar el atributo On para listeners

class ManageHomepageCarousel extends Component
{
    use WithFileUploads;
    use WithPagination;

    // Controla la visibilidad del modal de Jetstream
    public bool $showModal = false;

    // Propiedades para subir nuevas imágenes
    #[Validate('nullable|image|max:5120')] // 5MB Max
    public $newImage;
    #[Validate('nullable|string|max:255')]
    public string $newCaption = '';
    #[Validate('nullable|url|max:255')]
    public string $newLinkUrl = '';

    // Propiedades para mostrar imágenes actuales
    // Inicializar como colección vacía para evitar errores antes de cargar
    public Collection $carouselImages;

    // Propiedades para edición inline
    public ?int $editingImageId = null;
    #[Validate('nullable|string|max:255')]
    public string $editingImageCaption = '';
    #[Validate('nullable|url|max:255')]
    public string $editingImageLinkUrl = '';

    // Atributos para mensajes de validación amigables
    protected $validationAttributes = [
        'editingImageCaption' => 'título (edición)',
        'editingImageLinkUrl' => 'enlace URL (edición)',
        'newCaption' => 'título (nuevo)',
        'newLinkUrl' => 'enlace URL (nuevo)',
        'newImage' => 'archivo de imagen (nuevo)',
    ];

    // Se ejecuta cuando el componente se inicializa
    public function mount()
    {
        // Inicializa la colección vacía, se cargará al abrir el modal
        $this->carouselImages = collect();
        Log::debug('ManageHomepageCarousel MOUNTED, initial $showModal state: ' . ($this->showModal ? 'true' : 'false'));
    }

    // Carga las imágenes ordenadas
    public function loadCarouselImages()
    {
        $this->carouselImages = CarouselImage::orderBy('order', 'asc')->get();
    }

     // Propiedad computada para obtener fotos favoritas del admin (paginadas)
     // Se recalcula cuando cambia una dependencia (como $showModal si se usa)
     // O simplemente cuando Livewire renderiza de nuevo.
    public function getLikedPhotosProperty(): LengthAwarePaginator | Collection
    {
        $admin = Auth::user();
        // Solo carga si el modal está visible para optimizar
        if ($admin && $this->showModal) {
            return Photo::query()
                ->join('photo_user_likes', 'photos.id', '=', 'photo_user_likes.photo_id')
                ->where('photo_user_likes.user_id', $admin->id)
                ->with('album:id,name') // Carga la relación álbum eficientemente
                ->orderBy('photo_user_likes.created_at', 'desc')
                ->paginate(6, ['photos.*'], 'likedPhotosPage'); // Pagina con 6 items
        }
        // Devuelve colección vacía si el modal no está visible o no hay admin
        return collect();
    }

    // Listener: Se ejecuta cuando se recibe el evento 'openCarouselModal'
    #[On('openCarouselModal')]
    public function openModal()
    {
        Log::debug('ManageHomepageCarousel openModal method CALLED');
        $this->resetInputFields(); // Limpia campos antes de mostrar
        $this->loadCarouselImages(); // Carga los datos frescos
        $this->showModal = true;      // Establece la propiedad para mostrar el modal
        Log::debug('ManageHomepageCarousel Modal Opened via event');
    }

    // Se ejecuta al hacer clic en el botón "Cerrar" del modal
    public function closeModal()
    {
        Log::debug('ManageHomepageCarousel closeModal method CALLED');
        $this->showModal = false;     // Establece la propiedad para ocultar el modal
        $this->resetInputFields(); // Limpia campos al cerrar
        Log::debug('ManageHomepageCarousel Modal Closed');
    }

    // Helper para limpiar todos los campos de formulario y estado de edición
    private function resetInputFields()
    {
        $this->reset(['newImage', 'newCaption', 'newLinkUrl']);
        $this->cancelEditing(); // También resetea los campos de edición
        $this->resetErrorBag(); // Limpia errores de validación previos
        $this->resetPage('likedPhotosPage'); // Resetea paginación de favoritos
    }

    // Guarda la nueva imagen subida
    public function uploadImage()
    {
        $this->validate([
            'newImage' => 'required|image|max:5120',
            'newCaption' => 'nullable|string|max:255',
            'newLinkUrl' => 'nullable|url|max:255',
        ]);

        $disk = 'public';
        $directory = 'carousel_images';

        try {
            $path = $this->newImage->store($directory, $disk);
            $thumbnailPath = null; // Lógica de thumbnail omitida por simplicidad aquí

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
            $this->reset(['newImage', 'newCaption', 'newLinkUrl']);
            $this->loadCarouselImages(); // Recargar
            $this->dispatch('carouselUpdated')->to(HomepageCarousel::class);

        } catch (\Exception $e) {
            Log::error("Error al subir imagen carrusel: " . $e->getMessage());
            session()->flash('error', 'Error al subir la imagen. Inténtalo de nuevo.');
        }
    }

    // Elimina una imagen del carrusel y sus archivos
    public function deleteImage(int $id)
    {
        if ($this->editingImageId === $id) {
            $this->cancelEditing();
        }

        $image = CarouselImage::find($id);
        if ($image) {
            try {
                // Intentar borrar archivos asociados del disco
                if ($image->image_path && Storage::disk('public')->exists($image->image_path)) {
                    Storage::disk('public')->delete($image->image_path);
                }
                if ($image->thumbnail_path && Storage::disk('public')->exists($image->thumbnail_path)) {
                    Storage::disk('public')->delete($image->thumbnail_path);
                }

                $image->delete(); // Eliminar registro de la BD
                session()->flash('message', 'Imagen eliminada del carrusel.');
                $this->loadCarouselImages(); // Recargar
                $this->dispatch('carouselUpdated')->to(HomepageCarousel::class);

            } catch (\Exception $e) {
                Log::error("Error al eliminar imagen carrusel ID {$id}: " . $e->getMessage());
                session()->flash('error', 'Error al eliminar la imagen.');
            }
        }
    }

    // Añade una foto favorita al carrusel (copiando los archivos)
    public function addFromFavorite(int $photoId)
    {
        $admin = Auth::user();
        if (!$admin) { return; }

        $photo = Photo::with('album:id,name')->find($photoId);
        if (!$photo) { return; }

        // Opcional: Verificar si el admin realmente le dio like
        // $isAdminLiked = DB::table('photo_user_likes')->where('user_id', $admin->id)->where('photo_id', $photoId)->exists();
        // if (!$isAdminLiked) { return; }

        $disk = 'public';
        $sourceImagePath = $photo->file_path;
        $sourceThumbPath = $photo->thumbnail_path;
        $carouselDirectory = 'carousel_images';

        if (!$sourceImagePath || !Storage::disk($disk)->exists($sourceImagePath)) {
            session()->flash('error', 'No se encontró el archivo de la foto original.');
            Log::error("Archivo original no encontrado para Photo ID {$photoId}: {$sourceImagePath}");
            return;
        }

        $newFilenameBase = Str::random(32);
        $extension = pathinfo($sourceImagePath, PATHINFO_EXTENSION);
        $newImagePath = $carouselDirectory . '/' . $newFilenameBase . '.' . $extension;
        $newThumbPath = null;

        try {
            // 1. Copiar imagen principal
            Storage::disk($disk)->copy($sourceImagePath, $newImagePath);

            // 2. Copiar miniatura si existe
            if ($sourceThumbPath && Storage::disk($disk)->exists($sourceThumbPath)) {
                $thumbExtension = pathinfo($sourceThumbPath, PATHINFO_EXTENSION);
                $newThumbPath = $carouselDirectory . '/' . $newFilenameBase . '_thumb.' . $thumbExtension;
                Storage::disk($disk)->copy($sourceThumbPath, $newThumbPath);
            }

            // 3. Crear registro en BD con las nuevas rutas
            $maxOrder = CarouselImage::max('order') ?? -1;
            CarouselImage::create([
                'photo_id' => $photo->id, // Referencia al original
                'image_path' => $newImagePath, // Ruta de la copia
                'thumbnail_path' => $newThumbPath, // Ruta copia thumbnail
                'caption' => $photo->album->name ?? 'Favorita',
                'link_url' => null,
                'order' => $maxOrder + 1,
                'is_active' => true,
            ]);

            session()->flash('message', 'Imagen favorita añadida (copiada) al carrusel.');
            $this->loadCarouselImages(); // Recargar
            $this->dispatch('carouselUpdated')->to(HomepageCarousel::class);

        } catch (\Exception $e) {
             Log::error("Error copiando o guardando favorita {$photoId} al carrusel: " . $e->getMessage());
             session()->flash('error', 'Error al añadir la imagen favorita al carrusel.');
             // Limpieza de archivos copiados si falla la BD
             if (isset($newImagePath) && Storage::disk($disk)->exists($newImagePath)) { Storage::disk($disk)->delete($newImagePath); }
             if (isset($newThumbPath) && $newThumbPath && Storage::disk($disk)->exists($newThumbPath)) { Storage::disk($disk)->delete($newThumbPath); }
        }
    }

    // Cambia el estado activo/inactivo
    public function toggleActive(int $id)
    {
        $image = CarouselImage::find($id);
        if ($image) {
            $image->update(['is_active' => !$image->is_active]);
            $this->loadCarouselImages();
            $this->dispatch('carouselUpdated')->to(HomepageCarousel::class);

        }
    }

    // *** CORREGIDO: Actualiza el orden procesando el array de objetos ***
    public function updateImageOrder($orderedItems)
    {
        Log::debug('updateImageOrder recibió:', $orderedItems);

        // Verifica que sea un array válido
        if (!is_array($orderedItems)) {
            Log::error('updateImageOrder no recibió un array:', $orderedItems);
            session()->flash('error', 'Error al procesar el nuevo orden (datos inválidos).');
            $this->loadCarouselImages(); // Recarga el orden actual
            return;
        }

        $this->cancelEditing(); // Cancela edición antes de reordenar

        try {
            // Itera sobre el array de objetos [{ order: index, value: id }, ...]
            foreach ($orderedItems as $item) {
                 // Verifica que el item tenga 'value' (ID) y 'order' (nuevo índice)
                 if (isset($item['value']) && isset($item['order'])) {
                     CarouselImage::where('id', $item['value'])->update(['order' => $item['order']]);
                 } else {
                     Log::warning('Item inválido en updateImageOrder:', $item);
                 }
            }

            $this->loadCarouselImages(); // Recargar con el nuevo orden
            session()->flash('message', 'Orden de imágenes actualizado.');
            $this->dispatch('carouselUpdated')->to(HomepageCarousel::class);


        } catch (\Exception $e) {
            Log::error("Error actualizando orden carrusel: " . $e->getMessage());
            session()->flash('error', 'Error al actualizar el orden.');
            $this->loadCarouselImages(); // Recarga orden original si falla
        }
    }

    // --- Métodos para Edición Inline ---
    public function startEditing(int $imageId)
    {
        $this->cancelEditing(); // Cancela cualquier edición previa
        $image = CarouselImage::find($imageId);
        if ($image) {
            $this->editingImageId = $image->id;
            $this->editingImageCaption = $image->caption ?? '';
            $this->editingImageLinkUrl = $image->link_url ?? '';
        }
    }

    public function saveEditing()
    {
        if ($this->editingImageId === null) return;

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
                $this->cancelEditing();
                $this->loadCarouselImages();
            } catch (\Exception $e) {
                Log::error("Error guardando edición imagen carrusel ID {$this->editingImageId}: " . $e->getMessage());
                session()->flash('error', 'Error al guardar los cambios.');
            }
        } else {
             session()->flash('error', 'No se encontró la imagen para guardar.');
             $this->cancelEditing();
        }
    }

    public function cancelEditing()
    {
        $this->reset(['editingImageId', 'editingImageCaption', 'editingImageLinkUrl']);
        $this->resetErrorBag(['editingImageCaption', 'editingImageLinkUrl']); // Limpia solo errores de edición
    }

    // Renderiza la vista del modal
    public function render()
    {
        // La vista ahora es el componente x-dialog-modal que se muestra/oculta
        // basado en $this->showModal a través de wire:model.live
        return view('livewire.admin.manage-homepage-carousel', [
            // Pasamos los datos necesarios. La propiedad computada de favoritos se recalculará.
            'currentImages' => $this->carouselImages,
            'favoritePhotos' => $this->getLikedPhotosProperty(), // Llama a la propiedad computada
        ]);
    }
}
