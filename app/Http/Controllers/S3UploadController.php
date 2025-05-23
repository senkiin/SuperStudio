<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Aws\S3\S3Client;
use Aws\CommandInterface;

class S3UploadController extends Controller
{
    public function presign(Request $request)
    {
        $request->validate([
            'filename' => 'required|string',
            'filetype' => 'required|string',
        ]);

        // 1) Genera una clave única en tu bucket S3
        $extension = pathinfo($request->filename, PATHINFO_EXTENSION);
        $key = 'carousel_images/' . Str::random(32) . '.' . $extension;

        // 2) Crea el cliente S3 usando la configuración de filesystems.php
        $config = config('filesystems.disks.s3');
        $client = new S3Client([
            'version'     => 'latest',
            'region'      => $config['region'],
            'credentials' => [
                'key'    => $config['key'],
                'secret' => $config['secret'],
            ],
            // Si usas endpoint personalizado o aceleración:
            // 'endpoint' => $config['url'] ?? null,
            // 'use_accelerate_endpoint' => $config['use_accelerate_endpoint'] ?? false,
        ]);

        // 3) Prepara el comando PutObject
        /** @var CommandInterface $cmd */
        $cmd = $client->getCommand('PutObject', [
            'Bucket'      => $config['bucket'],
            'Key'         => $key,
            'ACL'         => 'public-read',
            'ContentType' => $request->filetype,
        ]);

        // 4) Genera la URL pre-firmada (válida 20 minutos)
        $url = (string) $client
            ->createPresignedRequest($cmd, '+20 minutes')
            ->getUri();

        return response()->json([
            'uploadUrl' => $url,
            'key'       => $key,
        ]);
    }
}
