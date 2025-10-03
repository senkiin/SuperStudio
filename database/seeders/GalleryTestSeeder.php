<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Album;
use App\Models\Photo;
use Illuminate\Support\Facades\Storage;

class GalleryTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear un usuario admin si no existe
        $admin = User::firstOrCreate(
            ['email' => 'admin@fotovalera.com'],
            [
                'name' => 'Administrador',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Crear algunos álbumes de prueba para la galería
        $albums = [
            [
                'name' => 'Bodas 2024',
                'description' => 'Colección de las mejores bodas del año 2024',
                'type' => 'public',
                'password' => null,
                'is_public_gallery' => true,
            ],
            [
                'name' => 'Comuniones Especiales',
                'description' => 'Fotografías de primeras comuniones con acceso restringido',
                'type' => 'public',
                'password' => 'comuniones2024',
                'is_public_gallery' => true,
            ],
            [
                'name' => 'Sesiones Newborn',
                'description' => 'Fotografías de recién nacidos - acceso privado',
                'type' => 'private',
                'password' => 'newborn123',
                'is_public_gallery' => true,
            ],
            [
                'name' => 'Eventos Corporativos',
                'description' => 'Fotografías de eventos empresariales',
                'type' => 'public',
                'password' => null,
                'is_public_gallery' => true,
            ],
            [
                'name' => 'Álbum Privado',
                'description' => 'Este álbum no aparece en la galería pública',
                'type' => 'private',
                'password' => null,
                'is_public_gallery' => false,
            ],
        ];

        foreach ($albums as $albumData) {
            $album = Album::create([
                'name' => $albumData['name'],
                'description' => $albumData['description'],
                'type' => $albumData['type'],
                'user_id' => $admin->id,
                'client_id' => $admin->id,
                'password' => $albumData['password'],
                'is_public_gallery' => $albumData['is_public_gallery'],
            ]);

            // Crear algunas fotos de prueba para cada álbum
            for ($i = 1; $i <= 5; $i++) {
                Photo::create([
                    'album_id' => $album->id,
                    'file_path' => "test/album_{$album->id}/photo_{$i}.jpg",
                    'thumbnail_path' => "test/album_{$album->id}/thumb_photo_{$i}.jpg",
                    'uploaded_by' => $admin->id,
                ]);
            }
        }

        $this->command->info('Álbumes de prueba para la galería creados exitosamente.');
        $this->command->info('Admin creado: admin@fotovalera.com / password');
    }
}
