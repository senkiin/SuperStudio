<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VideoUploadController extends Controller
{
    /**
     * Genera una URL pre-firmada válida para un PUT a S3.
     */
    public function getPresignedUrl(Request $request)
    {
        $request->validate([
            'filename' => 'required|string'
        ]);

        // Definimos la ruta dentro del bucket
        $path = "video_sections/{$request->user()->id}/"
            . now()->timestamp . '_' . $request->filename;

        // Genera la URL pre-firmada (válida 5 minutos)
        /** @var HasTemporaryUrls $disk */
        $disk = Storage::disk('videos');

        $url = $disk->temporaryUrl(
            'ruta/al/video.mp4',
            now()->addMinutes(5),
            ['s3Command' => 'PutObject']
        );

        return response()->json([
            'url'  => $url,
            'path' => $path,
        ]);
    }
}
