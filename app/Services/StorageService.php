<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class StorageService
{
    /**
     * Disco usado (ej. 's3' si FILESYSTEM_DRIVER=s3).
     *
     * @var string
     */
    protected string $disk;

    public function __construct()
    {
        $this->disk = config('filesystems.default');
    }

    /**
     * Guarda contenido arbitrario.
     *
     * @param  string       $path
     * @param  mixed        $contents
     * @param  string       $visibility
     * @return string|bool
     */
    public function put(string $path, $contents, string $visibility = 'public'): string|bool
    {
        return Storage::disk($this->disk)->put($path, $contents, $visibility);
    }

    /**
     * Sube un UploadedFile a un directorio.
     *
     * @param  string         $directory
     * @param  UploadedFile   $file
     * @param  string         $visibility
     * @return string         Ruta generada
     */
    public function putFile(string $directory, UploadedFile $file, string $visibility = 'public'): string
    {
        return Storage::disk($this->disk)->putFile($directory, $file, $visibility);
    }

    /**
     * Obtiene la URL pública del archivo.
     *
     * @param  string  $path
     * @return string
     */
    public function url(string $path): string
    {
        return Storage::disk($this->disk)->path($path);
    }

    /**
     * Elimina un archivo.
     *
     * @param  string  $path
     * @return bool
     */
    public function delete(string $path): bool
    {
        return Storage::disk($this->disk)->delete($path);
    }

    /**
     * Comprueba si existe un archivo.
     *
     * @param  string  $path
     * @return bool
     */
    public function exists(string $path): bool
    {
        return Storage::disk($this->disk)->exists($path);
    }

    /**
     * Copia un archivo dentro del mismo disco.
     *
     * @param  string  $from
     * @param  string  $to
     * @return bool
     */
    public function copy(string $from, string $to): bool
    {
        return Storage::disk($this->disk)->copy($from, $to);
    }
}
