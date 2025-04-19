<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage; // Importante

class CarouselImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_photo_id',
        'image_path',
        'thumbnail_path',
        'order',
        'caption',
        'link_url',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Accesor para obtener la URL completa de la imagen principal.
     * Asume que usas el disco 'public'. Cambia si usas S3 u otro.
     */
    public function getImageUrlAttribute(): ?string
    {
        // Accede al atributo raw aquí también por consistencia
        $imagePath = $this->attributes['image_path'] ?? null;
        return $imagePath ? Storage::disk('public')->url($imagePath) : null;
    }

    /**
     * Accesor para obtener la URL completa de la miniatura.
     * CORREGIDO: Usa $this->attributes['...'] para evitar recursión.
     * CORREGIDO: Devuelve URL, no PATH.
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        // *** CORRECCIÓN CLAVE AQUÍ: Acceder a los atributos RAW ***
        $thumbnailPath = $this->attributes['thumbnail_path'] ?? null;
        $imagePath = $this->attributes['image_path'] ?? null;
        $disk = 'public'; // O 's3', etc.

        // Comprueba si el thumbnail existe y devuelve su URL
        if ($thumbnailPath && Storage::disk($disk)->exists($thumbnailPath)) {
             // *** USA Storage::url() para obtener la URL pública ***
             return Storage::disk($disk)->url($thumbnailPath);
        }
        // Fallback: si no hay thumbnail, comprueba si la imagen principal existe y devuelve su URL
        elseif ($imagePath && Storage::disk($disk)->exists($imagePath)) {
            // *** USA Storage::url() para obtener la URL pública ***
            return Storage::disk($disk)->url($imagePath); // Fallback a imagen principal
        }

        // Si ninguno existe, devuelve null (o una URL placeholder si prefieres)
        return null;
        // return asset('images/placeholder-thumb.png'); // Ejemplo de placeholder
    }

    public function sourcePhoto()
    {
        // Asume que tu modelo se llama Photo y la FK es source_photo_id
        return $this->belongsTo(Photo::class, 'photo_id');
    }

    public function delete()
    {
        $disk = 'public'; // O tu disco
        $imagePath = $this->attributes['image_path'] ?? null;
        $thumbnailPath = $this->attributes['thumbnail_path'] ?? null;

        // Log::debug("CarouselImage::delete() - Intentando borrar archivos para ID: {$this->id}. Path: {$imagePath}, Thumb: {$thumbnailPath}");

        if ($imagePath && Storage::disk($disk)->exists($imagePath)) {
            Storage::disk($disk)->delete($imagePath);
             // Log::debug("Archivo de carrusel eliminado: {$imagePath}");
        }
        if ($thumbnailPath && Storage::disk($disk)->exists($thumbnailPath)) {
            Storage::disk($disk)->delete($thumbnailPath);
             // Log::debug("Thumbnail de carrusel eliminado: {$thumbnailPath}");
        }

        // Elimina el registro de la tabla carousel_images
        return parent::delete();
    }
}
