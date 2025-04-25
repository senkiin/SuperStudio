<?php
// app/Livewire/Homepage/ContentCards.php

namespace App\Livewire\Homepage;

use App\Livewire\Forms\ContentCardForm; // Import the new Form Object
use App\Models\ContentCard; // Import the new Model
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class ContentCards extends Component
{
    use WithFileUploads;

    public ContentCardForm $form; // Use the ContentCardForm
    public Collection $cards;
    public bool $showModal = false;
    public bool $isEditing = false;

    // Separate validation for the uploaded photo
    #[Rule('nullable|image|max:2048', as: 'imagen')] // Nullable for updates
    public $photo = null;
    public ?string $current_image_path = null; // To show current image in modal

    public bool $showConfirmDeleteModal = false;
    public ?int $cardIdToDelete = null;

    public function mount()
    {
        $this->loadCards();
    }

    public function loadCards()
    {
        $this->cards = ContentCard::orderBy('order_column', 'asc')->get();
    }

    // --- Modal and CRUD Operations ---

    public function openCreateModal()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') return;
        $this->resetValidation();
        $this->form->resetForm();
        $this->isEditing = false;
        $this->photo = null;
        $this->current_image_path = null;
        $this->form->order_column = (ContentCard::max('order_column') ?? -1) + 1;
        $this->showModal = true;
    }

    public function openEditModal(int $cardId)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') return;
        $this->resetValidation();
        $card = ContentCard::find($cardId);
        if (!$card) {
            session()->flash('error', 'Tarjeta no encontrada.');
            return;
        }
        $this->form->setCard($card);
        $this->isEditing = true;
        $this->photo = null; // Reset photo upload field
        $this->current_image_path = $card->image_path; // Set current image path
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->form->resetForm();
        $this->photo = null;
        $this->current_image_path = null;
        $this->resetValidation(); // Reset component-level validation too
    }

    public function saveCard()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            session()->flash('error', 'Acción no autorizada.');
            return;
        }

        // Validate the photo upload first (if present)
        // Add 'required' rule dynamically for creation if image is mandatory
        $photoRule = $this->isEditing ? 'nullable' : 'required';
        $this->validateOnly('photo', ['photo' => [$photoRule, 'image', 'max:2048']]);

        $newImagePath = null;
        $oldImagePath = $this->isEditing ? ContentCard::find($this->form->id)?->image_path : null; // Get path before update

        DB::beginTransaction();
        try {
             // Handle image upload if a new photo was provided
            if ($this->photo) {
                // Delete old image if updating and old image exists
                if ($this->isEditing && $oldImagePath && Storage::disk('public')->exists($oldImagePath)) {
                    Storage::disk('public')->delete($oldImagePath);
                    Log::info("Old image deleted: " . $oldImagePath);
                }
                // Store the new image
                $newImagePath = $this->photo->store('content-card-images', 'public');
                $this->form->image_path = $newImagePath; // Set the path in the form object
                 Log::info("New image uploaded: " . $newImagePath);
            } elseif (!$this->isEditing) {
                // If creating and no photo provided (but required), validation should catch it
                // Or add an extra check here
                 session()->flash('error', 'La imagen es requerida para crear una tarjeta.');
                 DB::rollBack();
                 return;
            } else {
                // If editing and no new photo, keep the existing path (don't set form->image_path)
                $this->form->image_path = $oldImagePath; // Ensure form has path if not changing
                 Log::info("Keeping existing image: " . $oldImagePath);
            }

            // Call the form object's store or update method
            $result = $this->isEditing ? $this->form->update() : $this->form->store();

            if ($result) {
                DB::commit();
                session()->flash('message', 'Tarjeta ' . ($this->isEditing ? 'actualizada' : 'creada') . ' correctamente.');
                $this->closeModal();
                $this->loadCards(); // Refresh the card list
            } else {
                // Rollback if store/update failed (e.g., form validation error within FormObject)
                DB::rollBack();
                // Clean up newly uploaded image if the transaction failed
                if ($newImagePath && Storage::disk('public')->exists($newImagePath)) {
                    Storage::disk('public')->delete($newImagePath);
                     Log::warning("Rolled back: New image deleted: " . $newImagePath);
                }
                 // Use potential error from form object or set a generic one
                 session()->flash('error', session('form_error', 'No se pudo guardar la tarjeta. Revisa los campos.'));
                 session()->forget('form_error'); // Clear specific form error flash
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
             if ($newImagePath && Storage::disk('public')->exists($newImagePath)) { Storage::disk('public')->delete($newImagePath); }
             Log::warning("ValidationException en saveCard: " . $e->getMessage());
             session()->flash('error', 'Error de validación. Revisa los campos marcados.');
             // Errors should automatically display via $errors bag
        } catch (\Exception $e) {
            DB::rollBack();
             if ($newImagePath && Storage::disk('public')->exists($newImagePath)) { Storage::disk('public')->delete($newImagePath); }
             Log::error("Error general guardando ContentCard: " . $e->getMessage());
             session()->flash('error', 'Error inesperado al guardar la tarjeta.');
        } finally {
            // Clean up the temporary uploaded file regardless of success/failure
             if ($this->photo) { $this->photo->delete(); }
        }
    }

    public function confirmDelete(int $cardId)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') return;
        $this->cardIdToDelete = $cardId;
        $this->showConfirmDeleteModal = true;
    }

    public function deleteCard()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
             session()->flash('error', 'Acción no autorizada.');
             $this->showConfirmDeleteModal = false; return;
        }
        $card = ContentCard::find($this->cardIdToDelete);
        if ($card) {
            DB::beginTransaction();
            try {
                $imagePath = $card->image_path;
                $card->delete();
                // Delete the associated image from storage
                if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                    Log::info("Image deleted on card delete: " . $imagePath);
                }
                DB::commit();
                session()->flash('message', 'Tarjeta eliminada.');
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Error eliminando ContentCard ID {$this->cardIdToDelete}: " . $e->getMessage());
                session()->flash('error', 'Error al eliminar la tarjeta.');
            }
        } else {
            session()->flash('error', 'Tarjeta no encontrada.');
        }
        $this->showConfirmDeleteModal = false;
        $this->cardIdToDelete = null;
        $this->loadCards(); // Refresh list
    }

    // --- Drag and Drop ---
    public function updateCardOrder($orderedItems)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
             session()->flash('error', 'Acción no autorizada.'); return;
         }
        if (!is_array($orderedItems)) {
             Log::error('Invalid data received for updateCardOrder:', ['data' => $orderedItems]);
             session()->flash('error', 'Error al procesar el reordenamiento.');
             $this->loadCards(); // Reload to reset view potentially
             return;
        }

        DB::beginTransaction();
        try {
            foreach ($orderedItems as $item) {
                 if (isset($item['value']) && isset($item['order']) && is_numeric($item['value']) && is_numeric($item['order'])) {
                     ContentCard::where('id', (int)$item['value'])->update(['order_column' => (int)$item['order']]);
                 } else {
                      Log::warning('Item inválido en updateCardOrder:', $item);
                 }
            }
            DB::commit();
            $this->loadCards(); // Refresh order in the collection
            session()->flash('message', 'Orden de las tarjetas actualizado.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error reordenando ContentCards: " . $e->getMessage());
            session()->flash('error', 'Error al reordenar las tarjetas.');
            $this->loadCards(); // Reload original order on failure
        }
    }

    public function render()
    {
        $isAdmin = Auth::check() && Auth::user()->role === 'admin';
        return view('livewire.homepage.content-cards', [
            'isAdmin' => $isAdmin,
        ]);
    }
}
