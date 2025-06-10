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

// Intervention Image v3.x
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver; // O usa ImagickDriver si prefieres y lo tienes instalado
use Intervention\Image\Interfaces\ImageInterface;
// Si necesitas especificar formatos de codificación de forma avanzada:
// use Intervention\Image\Encoders\JpegEncoder;
// use Intervention\Image\Encoders\PngEncoder;

class ProcessPhotoThumbnail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var Photo El modelo Photo a procesar */
    public Photo $photo;

    /** @var int Número máximo de intentos */
    public int $tries = 3;

    /**
     * Constructor recibe instancia de Photo
     */
    public function __construct(Photo $photo)
    {
        $this->photo = $photo;
    }

    /**
     * Ejecuta el procesamiento de la miniatura
     */
    public function handle(): void
    {
        // -------------------------------------------------------------------------
        // CONFIGURACIÓN IMPORTANTE DEL DISCO S3
        // -------------------------------------------------------------------------
        // Especifica el nombre del disco configurado en 'config/filesystems.php'
        // que utiliza el driver 's3' y tiene tus credenciales de AWS.
        // Puede ser 's3', 'albums_s3', o 'albums' si 'albums' está configurado como S3.
        $s3DiskName = 'albums'; 
        // -------------------------------------------------------------------------

        Log::info("[Thumbnail] Job starting for Photo ID: {$this->photo->id}. Attempting to use S3 disk: '{$s3DiskName}'.");

        // Verificar que el disco S3 esté configurado y use el driver 's3'
        $diskConfig = config("filesystems.disks.{$s3DiskName}");
        if (!$diskConfig) {
            $errorMessage = "S3 Disk '{$s3DiskName}' is not configured in filesystems.php.";
            Log::error("[Thumbnail] {$errorMessage} Photo ID: {$this->photo->id}");
            $this->fail(new \Exception($errorMessage)); // Fallar el job
            return;
        }
        if ($diskConfig['driver'] !== 's3') {
            $errorMessage = "Disk '{$s3DiskName}' is NOT configured as an S3 driver. Actual driver: '{$diskConfig['driver']}'.";
            Log::error("[Thumbnail] {$errorMessage} Photo ID: {$this->photo->id}");
            $this->fail(new \Exception($errorMessage)); // Fallar el job
            return;
        }

        $originalFilePath = $this->photo->file_path; // Ruta del archivo original en el disco S3

        Log::info("[Thumbnail] Original S3 file path: s3://{$diskConfig['bucket']}/{$originalFilePath}. Photo ID: {$this->photo->id}");

        if (!$originalFilePath || !Storage::disk($s3DiskName)->exists($originalFilePath)) {
            Log::error("[Thumbnail] Original file NOT FOUND on S3 disk '{$s3DiskName}' at path '{$originalFilePath}'. Photo ID: {$this->photo->id}");
            // Considera si fallar el job aquí o simplemente retornar si el original no es estrictamente necesario
            // $this->fail(new \Exception("Original file not found on S3 at '{$originalFilePath}'."));
            return;
        }

        try {
            Log::info("[Thumbnail] Reading original file from S3 disk '{$s3DiskName}', path '{$originalFilePath}'. Photo ID: {$this->photo->id}");
            $binaryImageData = Storage::disk($s3DiskName)->get($originalFilePath);

            if ($binaryImageData === null) { // get() puede devolver null si el archivo no se encuentra o hay error
                $errorMessage = "Failed to read binary data from S3 for '{$originalFilePath}'. File might be missing or permissions issue.";
                Log::error("[Thumbnail] {$errorMessage} Photo ID: {$this->photo->id}");
                $this->fail(new \Exception($errorMessage));
                return;
            }

            // Inicializar ImageManager con driver GD (Intervention Image v3)
            $manager = ImageManager::withDriver(new GdDriver());

            /** @var ImageInterface $image */
            $image = $manager->read($binaryImageData);

            Log::info("[Thumbnail] Resizing image. Photo ID: {$this->photo->id}");
            $image->resize(400, null, function ($constraint) {
                $constraint->aspectRatio();
                // Nota: En Intervention Image v3, resize() no agranda la imagen por defecto si es más pequeña que las dimensiones dadas.
                // Si necesitas la funcionalidad de 'upsize', revisa la documentación de v3.
                // Ejemplo: $constraint->upsize(); // Si está disponible y es necesario.
            });

            // Definir rutas para la miniatura
            $originalDir    = pathinfo($originalFilePath, PATHINFO_DIRNAME); // Directorio del original, ej: 'user1/photos'
            $originalName   = pathinfo($originalFilePath, PATHINFO_FILENAME); // Nombre sin extensión, ej: 'my_image'
            $originalExt    = pathinfo($originalFilePath, PATHINFO_EXTENSION); // Extensión, ej: 'jpg'

            // Lógica para el directorio de miniaturas. Ejemplo: 'user1/photos/img.jpg' -> 'user1/thumbnails/img_thumb.jpg'
            // Si $originalDir es '.', significa que el archivo está en la raíz del bucket.
            // Si $originalDir no contiene 'photos', 'thumbnails' se antepondrá o se colocará en una estructura definida.
            if ($originalDir === '.' || empty($originalDir)) {
                $thumbnailDir = 'thumbnails'; // Miniaturas de archivos en raíz van a 'thumbnails/'
            } else {
                // Reemplaza 'photos' con 'thumbnails'. Si 'photos' no está, considera otra lógica.
                $thumbnailDir  = str_replace('photos', 'thumbnails', $originalDir);
                // Si 'photos' no estaba en $originalDir y no hubo reemplazo, decide una estructura.
                // Por ejemplo, anteponer 'thumbnails/' al directorio original.
                if ($thumbnailDir === $originalDir && strpos($originalDir, 'thumbnails') === false) {
                     $thumbnailDir = 'thumbnails/' . $originalDir;
                }
            }
            // Limpiar slashes duplicados por si acaso
            $thumbnailDir = rtrim(str_replace('//', '/', $thumbnailDir), '/');


            $thumbnailName = "{$originalName}_thumb.{$originalExt}";
            $thumbnailPath = ($thumbnailDir === '.' || empty($thumbnailDir)) ? $thumbnailName : "{$thumbnailDir}/{$thumbnailName}";
            // Asegurar que no haya una barra inicial si $thumbnailPath se forma sin $thumbnailDir
            $thumbnailPath = ltrim($thumbnailPath, '/');


            Log::info("[Thumbnail] Attempting to save thumbnail to S3 disk '{$s3DiskName}' at path '{$thumbnailPath}'. Photo ID: {$this->photo->id}");

            // Codificar imagen. Intervention Image v3 devuelve un objeto EncodedImage.
            // Puedes especificar el formato y calidad si es necesario, ejemplo:
            // $encodedOutput = $image->encode(new JpegEncoder(quality: 80));
            $encodedOutput = $image->encode(); // Usa el formato por defecto o el que infiera

            Storage::disk($s3DiskName)->put($thumbnailPath, (string) $encodedOutput);

            Log::info("[Thumbnail] Thumbnail successfully saved to S3 at 's3://{$diskConfig['bucket']}/{$thumbnailPath}'. Photo ID: {$this->photo->id}");

            // Actualizar modelo en base de datos
            $this->photo->update(['thumbnail_path' => $thumbnailPath]);
            Log::info("[Thumbnail] Photo model updated with thumbnail_path: '{$thumbnailPath}'. Photo ID: {$this->photo->id}");

        } catch (\Throwable $e) {
            Log::error("[Thumbnail] CRITICAL ERROR during thumbnail processing for Photo ID: {$this->photo->id}. Message: " . $e->getMessage() . ". File: " . $e->getFile() . ". Line: " . $e->getLine());
            // Para un log más completo en caso de error (puede ser muy largo):
            // Log::debug("[Thumbnail] Full Trace: " . $e->getTraceAsString());
            $this->fail($e); // Esto llamará al método failed() y reencolará el job si $tries lo permite
        }
    }

    /**
     * Manejar falla del job
     * Este método se llama si el job falla después de todos sus intentos.
     */
    public function failed(\Throwable $exception): void
    {
        Log::critical("[Thumbnail] JOB TOTALLY FAILED after all retries - Photo ID: {$this->photo->id}. Reason: " . $exception->getMessage());
        // Aquí podrías enviar una notificación, limpiar algo, etc.
    }
}
