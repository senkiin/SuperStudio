<?php

namespace App\Jobs;

use App\Models\Photo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Drivers\Gd\Driver;
use \Intervention\Image\ImageManager as Image;
use Intervention\Image\ImageManager;

class ProcessPhotoThumbnail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * El modelo Photo a procesar.
     * Eloquent se encarga de serializar/deserializar el modelo.
     * @var \App\Models\Photo
     */
    public Photo $photo;

     /**
     * Número de veces que el job puede ser intentado.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(Photo $photo)
    {
        $this->photo = $photo;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $disk = 'public'; // Cambia a 's3' u otro si usas almacenamiento diferente
        $originalPath = $this->photo->file_path;

        // 1. Verificar si el archivo original existe
        if (!$originalPath || !Storage::disk($disk)->exists($originalPath)) {
            Log::error("Archivo original no encontrado para procesar thumbnail (Photo ID: {$this->photo->id}): " . $originalPath);
            // Opcional: Marcar la foto con un estado de error en la BD
            // $this->fail("Archivo original no encontrado: " . $originalPath); // Marca el job como fallido
            return; // Salir si no hay archivo
        }

        Log::info("Procesando thumbnail para Photo ID: {$this->photo->id}, Path: {$originalPath}");

        try {
            // 2. Leer el contenido del archivo original
            $imageContent = Storage::disk($disk)->get($originalPath);

            // 3. Procesar con Intervention Image
            $manager = new ImageManager(new Driver());

            $image = $manager->read($imageContent);


            // 4. Redimensionar (ejemplo: 400px de ancho, mantiene proporción)
            // Puedes usar otros métodos: fit(), cover(), etc.
            $image->scaleDown(width: 400);

            // 5. Determinar la ruta y nombre para el thumbnail
            $directory = pathinfo($originalPath, PATHINFO_DIRNAME);
            $filename = pathinfo($originalPath, PATHINFO_FILENAME);
            $extension = pathinfo($originalPath, PATHINFO_EXTENSION);
            // Crear una subcarpeta 'thumbnails' dentro de la carpeta de fotos del álbum
            $thumbDirectory = str_replace('/photos', '/thumbnails', $directory); // Ajusta si tu ruta es diferente
            $thumbFilename = $filename . '_thumb.' . $extension;
            $thumbnailPath = $thumbDirectory . '/' . $thumbFilename;

            // 6. Asegurarse de que el directorio del thumbnail exista (para disco local)
             if ($disk === 'public' || $disk === 'local') {
                Storage::disk($disk)->makeDirectory($thumbDirectory);
            }

            // 7. Guardar la imagen procesada (thumbnail) en el disco
            // encode(null) intenta mantener el formato original o usa uno por defecto
            Storage::disk($disk)->put($thumbnailPath, (string) $image->encode());

            // 8. Actualizar el modelo Photo en la base de datos con la ruta del thumbnail
            $this->photo->update(['thumbnail_path' => $thumbnailPath]);

            Log::info("Thumbnail creado y guardado para Photo ID: {$this->photo->id} en: " . $thumbnailPath);

        } catch (\Exception $e) {
            Log::error("Error al crear thumbnail para Photo ID: {$this->photo->id} - Path: {$originalPath} - Error: " . $e->getMessage());
            // Lanzar la excepción para que la cola maneje reintentos / failed_jobs
            $this->fail($e); // Opcional: marcar como fallido inmediatamente
            // throw $e;
        }
    }

     /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        // Opcional: Enviar notificación, loggear de forma especial, etc.
        Log::critical("Job ProcessPhotoThumbnail falló para Photo ID: {$this->photo->id}. Error: " . $exception->getMessage());
        // Podrías intentar marcar la foto en la BD como 'procesamiento_fallido'
    }
}
