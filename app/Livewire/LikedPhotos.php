<?php

namespace App\Livewire;

use App\Models\Photo;
use Illuminate\Support\Facades\Auth; // Para obtener el usuario logueado
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination; // Para paginar los resultados

class LikedPhotos extends Component
{
    use WithPagination;

    /**
     * Método para quitar el "Like" desde esta página (opcional)
     */
    public function unlikePhoto(int $photoId)
    {
        $user = Auth::user();
        if ($user) {
            // Eliminar el "like" directamente desde la tabla pivot
            DB::table('photo_user_likes')
                ->where('user_id', $user->id)
                ->where('photo_id', $photoId)
                ->delete();
        }
    }

    /**
     * Renderiza la vista
     */
    public function render()
    {
        $user = Auth::user();

        // Si no hay usuario logueado, no podemos mostrar sus likes
        if (!$user) {
            return view('livewire.liked-photos', ['likedPhotos' => null]);
        }

        // Obtener las fotos que el usuario ha marcado como "me gusta"
        $likedPhotos = Photo::query()
            ->join('photo_user_likes', 'photos.id', '=', 'photo_user_likes.photo_id')
            ->where('photo_user_likes.user_id', $user->id)
            ->with('album:id,name') // Cargar el álbum relacionado
            ->orderBy('photo_user_likes.created_at', 'desc') // Ordenar por fecha de "like"
            ->paginate(20, ['photos.*']); // Paginar los resultados

        return view('livewire.liked-photos', [
            'likedPhotos' => $likedPhotos,
        ]);
    }
}
