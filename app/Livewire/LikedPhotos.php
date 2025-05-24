<?php

namespace App\Livewire;

use App\Models\Photo; // Ensure this points to your Photo model
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // Import Storage facade
use Illuminate\Support\Facades\Log;   // Optional: for logging

class LikedPhotos extends Component
{
    use WithPagination;

    public $user;
    public string $disk = 'albums'; // Your S3 disk name, public to be accessible in view if needed elsewhere (though not directly for alpinePhotos generation now)
    public array $alpinePhotos = []; // Public property to hold data for Alpine

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $this->user = Auth::user();
    }

    public function unlikePhoto(int $photoId)
    {
        if (! $this->user) {
            return;
        }

        $photo = $this->user->likedPhotos()->where('photos.id', $photoId)->first();
        if ($photo) {
            $this->user->likedPhotos()->detach($photoId);
        }
        $this->resetPage();
    }

    public function render()
    {
        if (! $this->user) {
            $this->alpinePhotos = []; // Ensure it's empty if user is not logged in
            return view('livewire.liked-photos', ['likedPhotos' => null]);
        }

        $likedPhotosPaginator = $this->user
            ->likedPhotos()
            ->with([
                'album' => function ($query) {
                    $query->select('id', 'name');
                }
            ])
            ->orderByPivot('created_at', 'desc')
            ->paginate(20);

        // Prepare $alpinePhotos based on the paginator's current collection
        if ($likedPhotosPaginator && $likedPhotosPaginator->count() > 0) {
            $this->alpinePhotos = $likedPhotosPaginator->getCollection()
                ->map(fn($photo) => [
                    // Use file_path for modal, assuming it's the main image.
                    // $this->disk is accessed from the component's property.
                    'url' => ($photo->file_path && $this->disk) ? Storage::disk($this->disk)->url($photo->file_path) : null,
                    'alt' => e($photo->filename ?? 'Foto favorita') . ($photo->album?->name ? ' - ' . e($photo->album->name) : '')
                ])
                ->values() // Reset keys to be 0-indexed for JS array
                ->all();   // Convert Laravel Collection to a plain PHP array
        } else {
            $this->alpinePhotos = []; // Ensure it's an empty array if no liked photos
        }

        // Pass the paginator for the grid and pagination links.
        // $this->alpinePhotos is a public property, so it's automatically available to the view.
        return view('livewire.liked-photos', [
            'likedPhotos' => $likedPhotosPaginator
        ]);
    }
}
