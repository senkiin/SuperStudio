<?php
// app/Livewire/Forms/ContentCardForm.php

namespace App\Livewire\Forms;

use App\Models\ContentCard;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Rule;
use Livewire\Form;
use Illuminate\Support\Facades\Log;

class ContentCardForm extends Form
{
    #[Locked]
    public ?int $id = null;

    #[Rule('required|string|max:255', as: 'título')]
    public string $title = '';

    #[Rule('nullable|string|max:1000', as: 'descripción')] // Max length adjusted
    public string $description = '';

    #[Rule('nullable|url|max:2048', as: 'URL del enlace')]
    public ?string $link_url = null;

    #[Rule('nullable|string|max:50', as: 'texto del enlace')]
    public string $link_text = 'Saber Más';

    #[Rule('required|integer|min:0', as: 'orden')]
    public int $order_column = 0;

    // Path is handled separately during save/update
    public ?string $image_path = null;

    // Validation rule for the image file itself (applied in the component)
    // Example: #[Rule('required|image|max:2048')] without 'required' for updates

    public function setCard(ContentCard $card): void
    {
        $this->id = $card->id;
        $this->title = $card->title;
        $this->description = $card->description ?? '';
        $this->link_url = $card->link_url;
        $this->link_text = $card->link_text ?? 'Saber Más';
        $this->order_column = $card->order_column;
        $this->image_path = $card->image_path; // Store existing path
    }

    public function store(): ?ContentCard
    {
        $this->validate(); // Validate text fields etc. Image validated in component.
        if (empty($this->image_path)) {
            // You might want to add a specific validation error here if image is strictly required
             Log::warning("Attempted to store ContentCard without an image_path.");
             session()->flash('form_error', 'La imagen es requerida.'); // Example flash message
            return null;
        }
        if ($this->order_column <= 0) {
             $this->order_column = (ContentCard::max('order_column') ?? -1) + 1;
        }

        try {
            return ContentCard::create(
                $this->only(['title', 'description', 'link_url', 'link_text', 'order_column', 'image_path'])
            );
        } catch (\Exception $e) {
            Log::error("Error creando ContentCard: " . $e->getMessage());
             session()->flash('form_error', 'Error al crear la tarjeta.');
            return null;
        }
    }

    public function update(): ?ContentCard
    {
        if (!$this->id) { return null; }
        $this->validate(); // Validate text fields etc. Image validated in component.
        $card = ContentCard::find($this->id);
        if (!$card) { return null; }

         // If image_path wasn't updated (no new photo uploaded), keep the existing one
         // The component should set $this->image_path only if a new image is processed
         // If $this->image_path is null here means no *new* image was provided, but update might proceed

        try {
             $dataToUpdate = $this->only(['title', 'description', 'link_url', 'link_text', 'order_column']);
             // Only include image_path if it was explicitly set (meaning a new image was uploaded)
             if ($this->image_path) {
                 $dataToUpdate['image_path'] = $this->image_path;
             }

             $card->update($dataToUpdate);
             return $card->fresh(); // Return updated model
        } catch (\Exception $e) {
             Log::error("Error actualizando ContentCard ID {$this->id}: " . $e->getMessage());
             session()->flash('form_error', 'Error al actualizar la tarjeta.');
            return null;
        }
    }

    public function resetForm(): void
    {
        $this->reset();
        $this->link_text = 'Saber Más';
        $this->order_column = 0;
        $this->image_path = null;
    }
}
