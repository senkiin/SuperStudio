<?php

namespace App\Livewire\Homepage;

use App\Livewire\Forms\ContentCardForm;
use App\Models\ContentCard;
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

    /** @var ContentCardForm El form object para validación y guardado */
    public ContentCardForm $form;

    /** @var Collection Colección de tarjetas cargadas */
    public Collection $cards;

    /** Control de la visualización del modal */
    public bool $showModal = false;
    public bool $isEditing = false;

    /** Para subir la imagen */
    #[Rule('nullable|image|max:2048', as: 'imagen')]
    public $photo = null;

    /** Ruta de la imagen actual (antes de editar) */
    public ?string $current_image_path = null;

    /** Confirmación de borrado */
    public bool $showConfirmDeleteModal = false;
    public ?int $cardIdToDelete = null;

    /** Nombre del disco configurado en config/filesystems.php */
    protected string $disk = 'content-cards';

    public function mount()
    {
        $this->loadCards();
    }

    /** Carga todas las tarjetas ordenadas */
    public function loadCards()
    {
        $this->cards = ContentCard::orderBy('order_column', 'asc')->get();
    }

    /** Abre el modal para crear una nueva tarjeta */
    public function openCreateModal()
    {
        if (! Auth::check() || Auth::user()->role !== 'admin') {
            return;
        }

        $this->resetValidation();
        $this->form->resetForm();
        $this->isEditing = false;
        $this->photo = null;
        $this->current_image_path = null;
        $this->form->order_column = (ContentCard::max('order_column') ?? -1) + 1;
        $this->showModal = true;
    }

    /** Abre el modal para editar una tarjeta existente */
    public function openEditModal(int $cardId)
    {
        if (! Auth::check() || Auth::user()->role !== 'admin') {
            return;
        }

        $this->resetValidation();

        $card = ContentCard::find($cardId);
        if (! $card) {
            session()->flash('error', 'Tarjeta no encontrada.');
            return;
        }

        $this->form->setCard($card);
        $this->isEditing = true;
        $this->photo = null;
        $this->current_image_path = $card->image_path;
        $this->showModal = true;
    }

    /** Cierra el modal de creación/edición */
    public function closeModal()
    {
        $this->showModal = false;
        $this->form->resetForm();
        $this->photo = null;
        $this->current_image_path = null;
        $this->resetValidation();
    }

    /** Guarda la tarjeta (crea o actualiza) */
    public function saveCard()
    {
        if (! Auth::check() || Auth::user()->role !== 'admin') {
            session()->flash('error', 'Acción no autorizada.');
            return;
        }

        // Foto requerida solo al crear
        $photoRule = $this->isEditing ? 'nullable' : 'required';
        $this->validateOnly('photo', ['photo' => [$photoRule, 'image', 'max:2048']]);

        $newImagePath = null;
        $oldImagePath  = $this->isEditing
            ? ContentCard::find($this->form->id)?->image_path
            : null;

        DB::beginTransaction();
        try {
            // Si se sube foto nueva
            if ($this->photo) {
                // Borrar anterior si existe
                if ($this->isEditing && $oldImagePath && Storage::disk($this->disk)->exists($oldImagePath)) {
                    Storage::disk($this->disk)->delete($oldImagePath);
                    Log::info("Old image deleted: {$oldImagePath}");
                }
                // Guardar nueva
                $newImagePath = $this->photo->store('content-card-images', $this->disk);
                $this->form->image_path = $newImagePath;
                Log::info("New image uploaded: {$newImagePath}");
            }
            elseif (! $this->isEditing) {
                // Creando sin imagen => error
                session()->flash('error', 'La imagen es requerida para crear una tarjeta.');
                DB::rollBack();
                return;
            }
            else {
                // Editando sin foto nueva: conservar la ruta antigua
                $this->form->image_path = $oldImagePath;
                Log::info("Keeping existing image: {$oldImagePath}");
            }

            // Llamada al form object
            $result = $this->isEditing
                ? $this->form->update()
                : $this->form->store();

            if ($result) {
                DB::commit();
                session()->flash('message', 'Tarjeta ' . ($this->isEditing ? 'actualizada' : 'creada') . ' correctamente.');
                $this->closeModal();
                $this->loadCards();
            } else {
                DB::rollBack();
                // Limpiar la imagen recién subida si algo falló
                if ($newImagePath && Storage::disk($this->disk)->exists($newImagePath)) {
                    Storage::disk($this->disk)->delete($newImagePath);
                    Log::warning("Rolled back: new image deleted: {$newImagePath}");
                }
                session()->flash('error', session('form_error', 'No se pudo guardar la tarjeta.'));
                session()->forget('form_error');
            }
        }
        catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            if ($newImagePath && Storage::disk($this->disk)->exists($newImagePath)) {
                Storage::disk($this->disk)->delete($newImagePath);
            }
            Log::warning("ValidationException en saveCard: " . $e->getMessage());
            session()->flash('error', 'Error de validación. Revisa los campos marcados.');
        }
        catch (\Exception $e) {
            DB::rollBack();
            if ($newImagePath && Storage::disk($this->disk)->exists($newImagePath)) {
                Storage::disk($this->disk)->delete($newImagePath);
            }
            Log::error("Error general guardando ContentCard: " . $e->getMessage());
            session()->flash('error', 'Error inesperado al guardar la tarjeta.');
        }
        finally {
            // Siempre limpiar el archivo temporal de Livewire
            if ($this->photo) {
                try { $this->photo->delete(); } catch (\Throwable $__) {}
            }
        }
    }

    /** Abre confirmación y marca tarjeta a borrar */
    public function confirmDelete(int $cardId)
    {
        if (! Auth::check() || Auth::user()->role !== 'admin') {
            return;
        }
        $this->cardIdToDelete = $cardId;
        $this->showConfirmDeleteModal = true;
    }

    /** Borra la tarjeta y su imagen asociada */
    public function deleteCard()
    {
        if (! Auth::check() || Auth::user()->role !== 'admin') {
            session()->flash('error', 'Acción no autorizada.');
            $this->showConfirmDeleteModal = false;
            return;
        }

        $card = ContentCard::find($this->cardIdToDelete);
        if ($card) {
            DB::beginTransaction();
            try {
                $imagePath = $card->image_path;
                $card->delete();

                if ($imagePath && Storage::disk($this->disk)->exists($imagePath)) {
                    Storage::disk($this->disk)->delete($imagePath);
                    Log::info("Image deleted on card delete: {$imagePath}");
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
        $this->loadCards();
    }

    /**
     * Reordena las tarjetas según Drag & Drop
     * @param array $orderedItems Cada item contiene ['value' => id, 'order' => nueva posición]
     */
    public function updateCardOrder($orderedItems)
    {
        if (! Auth::check() || Auth::user()->role !== 'admin') {
            session()->flash('error', 'Acción no autorizada.');
            return;
        }

        if (! is_array($orderedItems)) {
            Log::error('Invalid data in updateCardOrder:', ['data' => $orderedItems]);
            session()->flash('error', 'Error al procesar el reordenamiento.');
            $this->loadCards();
            return;
        }

        DB::beginTransaction();
        try {
            foreach ($orderedItems as $item) {
                if (isset($item['value'], $item['order'])
                    && is_numeric($item['value'])
                    && is_numeric($item['order'])
                ) {
                    ContentCard::where('id', (int)$item['value'])
                               ->update(['order_column' => (int)$item['order']]);
                } else {
                    Log::warning('Item inválido en updateCardOrder:', $item);
                }
            }
            DB::commit();
            $this->loadCards();
            session()->flash('message', 'Orden de las tarjetas actualizado.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error reordenando ContentCards: " . $e->getMessage());
            session()->flash('error', 'Error al reordenar las tarjetas.');
            $this->loadCards();
        }
    }

    public function render()
    {
        $isAdmin = Auth::check() && Auth::user()->role === 'admin';
        return view('livewire.homepage.content-cards', compact('isAdmin'));
    }
}
