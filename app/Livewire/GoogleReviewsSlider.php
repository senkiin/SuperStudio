<?php // Archivo: app/Livewire/GoogleReviewsSlider.php

namespace App\Livewire;

use Illuminate\Support\Facades\Artisan;
use App\Models\GoogleReview;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Illuminate\Support\Facades\Gate; // Si quieres protegerlo mejor

class GoogleReviewsSlider extends Component
{
    public string $sortBy = 'review_time_desc'; // Default sort

    public array $sortOptions = [
        'review_time_desc' => 'Más Recientes',
        'rating_desc' => 'Mejor Valoradas',
        'rating_asc' => 'Peor Valoradas',
    ];

    public function fetchReviewsFromAdmin()
{
    // Opcional: proteger que solo el admin lo ejecute
    if (!Auth::check() || Auth::user()->role !== 'admin') {
        session()->flash('error', 'No tienes permisos.');
        return;
    }

    try {
        Artisan::call('app:fetch-google-reviews', ['--verbose' => true]);
        session()->flash('message', 'Reseñas actualizadas correctamente.');
    } catch (\Exception $e) {
        session()->flash('error', 'Error actualizando reseñas: ' . $e->getMessage());
    }
}
    // Usar propiedad computada para que se actualice automáticamente
    #[Computed(persist: true, seconds: 300)] // Cachear resultado por 5 mins opcionalmente
    public function reviews(): Collection
    {
        Log::info('Loading reviews with sort: ' . $this->sortBy); // Log para debug

        $query = GoogleReview::where('is_visible', true);

        switch ($this->sortBy) {
            case 'rating_desc':
                $query->orderBy('rating', 'desc')->orderBy('review_time', 'desc');
                break;
            case 'rating_asc':
                $query->orderBy('rating', 'asc')->orderBy('review_time', 'desc');
                break;
            case 'review_time_desc':
            default:
                $query->orderBy('review_time', 'desc');
                break;
        }

        // Asegúrate de que no haya elementos nulos inesperados
        return $query->limit(20)->get()->filter(); // ->filter() elimina nulls si los hubiera
    }

    public function setSort(string $criteria): void
    {
        if (array_key_exists($criteria, $this->sortOptions)) {
            $this->sortBy = $criteria;
            // Borrar caché de la propiedad computada para forzar recarga
            unset($this->reviews);
            Log::info('Sort changed to: ' . $criteria); // Log para debug
            // Forzar un re-renderizado si es necesario (aunque cambiar sortBy debería ser suficiente)
            // $this->dispatch('$refresh');
        }
    }

    // Método para refrescar manualmente (ej. con un botón si fuera necesario)
    public function refreshReviews()
    {
        unset($this->reviews);
        $this->dispatch('reviews-refreshed'); // Evento opcional para JS
    }


    // Add these inside the GoogleReviewsSlider class

    #[Computed]
    public function averageRating(): float|null
    {
        $reviews = $this->reviews(); // Use the existing computed property
        if ($reviews->isEmpty() || $reviews->avg('rating') === null) {
            return null; // Handle cases with no reviews or no ratings
        }
        return round($reviews->avg('rating'), 1); // Calculate average, round to 1 decimal
    }

    #[Computed]
    public function totalVisibleReviewsCount(): int
    {
        // Counts only the reviews fetched based on current filters/limit
        return $this->reviews()->count();
    }

    // Optional: If you want the *absolute* total count from the DB
    #[Computed]
    public function absoluteTotalReviewsCount(): int
    {
        return GoogleReview::where('is_visible', true)->count();
    }

    // You'll also need your business's Google Review URL.
    // Store it in your .env file for best practice: GOOGLE_REVIEW_LINK=your_link_here
    // Then read it, maybe in the mount() method or directly in render().
    public string $googleReviewLink = '';

    public function mount()
    {
        $this->googleReviewLink = config('services.google.review_link', '#'); // Example reading from config, fallback '#'
        // Or: $this->googleReviewLink = env('GOOGLE_REVIEW_LINK', '#');
    }


    // Modify the render method to pass the new data and the link
    public function render()
    {
        $isAdmin = Auth::check() && Auth::user()->role === 'admin';

        // Ensure computed properties are calculated before passing
        $avgRating = $this->averageRating;
        $totalCount = $this->absoluteTotalReviewsCount; // Or use totalVisibleReviewsCount

        return view('livewire.google-reviews-slider', [
            'isAdmin' => $isAdmin,
            'sortOptions' => $this->sortOptions,
            'averageRating' => $avgRating, // Pass average rating
            'totalReviewsCount' => $totalCount, // Pass total count
            'googleReviewLink' => $this->googleReviewLink // Pass the review link
        ]);
    }
}
