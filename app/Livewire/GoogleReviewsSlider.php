<?php

namespace App\Livewire;

use App\Models\GoogleReview;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Component;

class GoogleReviewsSlider extends Component
{
    public string $sortBy = 'review_time_desc'; // Default sort
    public bool $showModal = false;
    public bool $isEditing = false; // O quizás solo necesitas editar, no crear aquí
    public ?GoogleReview $editingReview = null; // Para guardar la reseña que se está editando

    public array $sortOptions = [
        'review_time_desc' => 'Más Recientes',
        'rating_desc' => 'Mejor Valoradas',
        'rating_asc' => 'Peor Valoradas',
    ];

    public function openEditModal(int $reviewId) {
        $this->editingReview = GoogleReview::find($reviewId);
        if ($this->editingReview) {
            // Aquí podrías llenar un Form Object si lo usas
            $this->isEditing = true; // Necesaria si usas la variable en el título del modal
            $this->showModal = true;
        }
    }

    public function closeModal() {
        $this->showModal = false;
        $this->editingReview = null;
        $this->resetValidation();
    }

    public function saveReview() {
       if ($this->editingReview) {
           // Lógica para guardar cambios (ej. cambiar is_visible)
           // $this->editingReview->is_visible = !$this->editingReview->is_visible; // Ejemplo toggle
           // $this->editingReview->save();
           $this->closeModal();
           unset($this->reviews); // Refrescar la lista
           session()->flash('message', 'Reseña actualizada.');
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

        return $query->limit(20)->get(); // Limitar a 20 reseñas por ejemplo
    }

    public function setSort(string $criteria): void
    {
        if (array_key_exists($criteria, $this->sortOptions)) {
            $this->sortBy = $criteria;
            // Borrar caché de la propiedad computada para forzar recarga
            unset($this->reviews);
            Log::info('Sort changed to: ' . $criteria); // Log para debug
        }
    }

    // Método para refrescar manualmente (ej. con un botón si fuera necesario)
    public function refreshReviews()
    {
         unset($this->reviews);
         $this->dispatch('reviews-refreshed'); // Evento opcional para JS
    }


    public function render()
    {
        $isAdmin = Auth::check() && Auth::user()->role === 'admin';

        return view('livewire.google-reviews-slider', [
            'isAdmin' => $isAdmin
        ]);
    }
}
