<?php

namespace App\Livewire\Homepage;

use App\Livewire\Forms\InfoBlockForm; // Importa el Form Object
use App\Models\InfoBlock;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Rule; // Para validar $photo

class InfoBlocksManager extends Component
{
    use WithFileUploads;

    public InfoBlockForm $form; // Propiedad para el Form Object
    public Collection $blocks;
    public bool $showModal = false;
    public bool $isEditing = false;

    #[Rule('nullable|image|max:2048', as: 'imagen')]
    public $photo = null; // Para el archivo subido
    public ?string $current_image_path = null;

    public bool $showConfirmDeleteModal = false;
    public ?int $blockIdToDelete = null;

    public function mount()
    {
        $this->loadBlocks();
        // $this->form->resetForm(); // Quitamos esto de mount
    }

    public function loadBlocks()
    {
        $this->blocks = InfoBlock::orderBy('order_column', 'asc')->get();
    }

    public function openCreateModal()
    {
        $this->resetValidation();
        $this->form->resetForm(); // Resetea el Form Object
        $this->isEditing = false;
        $this->photo = null;
        $this->current_image_path = null;
        $this->form->order_column = (InfoBlock::max('order_column') ?? -1) + 1;
        $this->showModal = true;
    }

    public function openEditModal(int $blockId)
    {
        $this->resetValidation();
        $block = InfoBlock::find($blockId);
        if (!$block) { session()->flash('error', 'Bloque no encontrado.'); return; }
        $this->form->setBlock($block); // Llena el Form Object
        $this->isEditing = true;
        $this->photo = null;
        $this->current_image_path = $block->image_path;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->form->resetForm();
        $this->photo = null;
        $this->current_image_path = null;
        $this->resetValidation();
    }

    public function saveBlock()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') { /* ... error ... */ return; }
        $this->validateOnly('photo'); // Valida solo $photo aquí

        $newImagePath = null;
        $oldImagePath = $this->isEditing ? $this->form->image_path : null;

        DB::beginTransaction();
        try {
            if ($this->photo) { // Manejo de imagen
                if ($this->isEditing && $oldImagePath && Storage::disk('public')->exists($oldImagePath)) {
                    Storage::disk('public')->delete($oldImagePath);
                }
                $newImagePath = $this->photo->store('info-block-images', 'public');
                $this->form->image_path = $newImagePath; // Asigna al form object
            }

            // Llama al método del form object (valida y guarda/actualiza)
            $result = $this->isEditing ? $this->form->update() : $this->form->store();

            if ($result) { // Éxito
                 DB::commit();
                 session()->flash('message', 'Bloque ' . ($this->isEditing ? 'actualizado' : 'creado') . ' correctamente.');
                 $this->closeModal();
                 $this->loadBlocks();
            } else { // Fallo en store/update
                 DB::rollBack();
                 if ($newImagePath && Storage::disk('public')->exists($newImagePath)) { Storage::disk('public')->delete($newImagePath); } // Limpia imagen subida
                 session()->flash('error', 'No se pudo guardar el bloque. Revisa los campos.');
            }
        } catch (\Illuminate\Validation\ValidationException $e) { // Captura error validación (de $photo o del form)
            DB::rollBack();
             Log::warning("ValidationException en saveBlock: " . $e->getMessage());
             session()->flash('error', 'Error de validación. Revisa los campos marcados.');
        } catch (\Exception $e) { // Captura otros errores
            DB::rollBack();
             if ($newImagePath && Storage::disk('public')->exists($newImagePath)) { Storage::disk('public')->delete($newImagePath); }
             Log::error("Error general guardando InfoBlock: " . $e->getMessage());
             session()->flash('error', 'Error inesperado al guardar el bloque.');
        }
    }

    public function confirmDelete(int $blockId) { /* ... sin cambios ... */
        if (!Auth::check() || Auth::user()->role !== 'admin') { /* ... error ... */ return; }
        $this->blockIdToDelete = $blockId;
        $this->showConfirmDeleteModal = true;
    }
    public function deleteBlock() { /* ... sin cambios ... */
        if (!Auth::check() || Auth::user()->role !== 'admin') { /* ... error ... */ $this->showConfirmDeleteModal = false; return; }
        $block = InfoBlock::find($this->blockIdToDelete);
        if ($block) {
            DB::beginTransaction(); try { $imagePath = $block->image_path; $block->delete(); if ($imagePath && Storage::disk('public')->exists($imagePath)) { Storage::disk('public')->delete($imagePath); } DB::commit(); session()->flash('message', 'Bloque eliminado.'); } catch (\Exception $e) { DB::rollBack(); Log::error("Error eliminando: " . $e->getMessage()); session()->flash('error', 'Error al eliminar.'); }
        } else { session()->flash('error', 'Bloque no encontrado.'); }
        $this->showConfirmDeleteModal = false; $this->blockIdToDelete = null; $this->loadBlocks();
    }
    public function updateBlockOrder($orderedItems) { /* ... sin cambios ... */
        if (!Auth::check() || Auth::user()->role !== 'admin') { /* ... error ... */ return; }
        if (!is_array($orderedItems)) { /* ... error ... */ $this->loadBlocks(); return; }
        DB::beginTransaction(); try { foreach ($orderedItems as $item) { if (isset($item['value']) && isset($item['order'])) { InfoBlock::where('id', $item['value'])->update(['order_column' => $item['order']]); } else { Log::warning('Item inválido:', $item); } } DB::commit(); $this->loadBlocks(); session()->flash('message', 'Orden actualizado.'); } catch (\Exception $e) { DB::rollBack(); Log::error("Error reordenando: " . $e->getMessage()); session()->flash('error', 'Error al reordenar.'); $this->loadBlocks(); }
    }

    public function render()
    {
        $isAdmin = Auth::check() && Auth::user()->role === 'admin';
        return view('livewire.homepage.info-blocks-manager', [
            'isAdmin' => $isAdmin,
        ]);
    }
}
