<?php

namespace App\Livewire\Homepage;

use App\Livewire\Forms\InfoBlockForm;
use App\Models\InfoBlock;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Rule;

class InfoBlocksManager extends Component
{
    use WithFileUploads;

    /** Form object para validación y guardado */
    public InfoBlockForm $form;

    /** Colección de bloques cargada desde la BD */
    public Collection $blocks;

    /** Control del modal */
    public bool $showModal = false;
    public bool $isEditing = false;

    /** Validación de la imagen con Livewire Attributes */
    #[Rule('nullable|image|max:2048', as: 'imagen')]
    public $photo = null;

    /** Ruta actual de la imagen para previsualizar/borrar */
    public ?string $current_image_path = null;

    /** Para confirmar borrado */
    public bool $showConfirmDeleteModal = false;
    public ?int $blockIdToDelete = null;

    protected string $disk = 'info-blocks';

    public function mount()
    {
        $this->loadBlocks();
    }

    /** Recarga la colección de bloques */
    public function loadBlocks(): void
    {
        $this->blocks = InfoBlock::orderBy('order_column', 'asc')->get();
    }

    /** Abre modal para crear */
    public function openCreateModal(): void
    {
        $this->resetValidation();
        $this->form->resetForm();
        $this->isEditing = false;
        $this->photo = null;
        $this->current_image_path = null;
        $this->form->order_column = (InfoBlock::max('order_column') ?? -1) + 1;
        $this->showModal = true;
    }

    /** Abre modal para editar */
    public function openEditModal(int $blockId): void
    {
        $this->resetValidation();

        $block = InfoBlock::find($blockId);
        if (! $block) {
            session()->flash('error', 'Bloque no encontrado.');
            return;
        }

        $this->form->setBlock($block);
        $this->isEditing = true;
        $this->photo = null;
        $this->current_image_path = $block->image_path;
        $this->showModal = true;
    }

    /** Cierra el modal */
    public function closeModal(): void
    {
        $this->showModal = false;
        $this->form->resetForm();
        $this->photo = null;
        $this->current_image_path = null;
        $this->resetValidation();
    }

    /** Guarda o actualiza el bloque, y sube/borra imagen en S3 */
    public function saveBlock(): void
    {
        if (! Auth::check() || Auth::user()->role !== 'admin') {
            session()->flash('error', 'Sin permiso.');
            return;
        }

        // Valida solo el campo $photo
        $this->validateOnly('photo');

        $newImagePath = null;
        $oldImagePath = $this->isEditing ? $this->form->image_path : null;

        DB::beginTransaction();
        try {
            if ($this->photo) {
                // Si editando y existe imagen previa, la borramos de S3
                if ($this->isEditing && $oldImagePath && Storage::disk($this->disk)->exists($oldImagePath)) {
                    Storage::disk($this->disk)->delete($oldImagePath);
                }

                // Subimos la nueva imagen a S3
                $newImagePath = $this->photo->store('info-block-images', $this->disk);
                $this->form->image_path = $newImagePath;
            }

            // Lógica de guardado propio del Form Object
            $success = $this->isEditing
                ? $this->form->update()
                : $this->form->store();

            if ($success) {
                DB::commit();
                session()->flash(
                    'message',
                    'Bloque ' . ($this->isEditing ? 'actualizado' : 'creado') . ' correctamente.'
                );
                $this->closeModal();
                $this->loadBlocks();
            } else {
                DB::rollBack();
                // Si subimos una imagen y luego falló el guardado, la removemos
                if ($newImagePath && Storage::disk($this->disk)->exists($newImagePath)) {
                    Storage::disk($this->disk)->delete($newImagePath);
                }
                session()->flash('error', 'Error al guardar. Revisa los datos.');
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::warning("ValidationException en saveBlock: {$e->getMessage()}");
            session()->flash('error', 'Error de validación. Revisa los campos.');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($newImagePath && Storage::disk($this->disk)->exists($newImagePath)) {
                Storage::disk($this->disk)->delete($newImagePath);
            }
            Log::error("Error general guardando InfoBlock: {$e->getMessage()}");
            session()->flash('error', 'Error inesperado al guardar el bloque.');
        }
    }

    /** Preparar confirmación de borrado */
    public function confirmDelete(int $blockId): void
    {
        if (! Auth::check() || Auth::user()->role !== 'admin') {
            session()->flash('error', 'Sin permiso.');
            return;
        }
        $this->blockIdToDelete = $blockId;
        $this->showConfirmDeleteModal = true;
    }

    /** Borra el bloque y su imagen en S3 */
    public function deleteBlock(): void
    {
        if (! Auth::check() || Auth::user()->role !== 'admin') {
            session()->flash('error', 'Sin permiso.');
            $this->showConfirmDeleteModal = false;
            return;
        }

        $block = InfoBlock::find($this->blockIdToDelete);
        if (! $block) {
            session()->flash('error', 'Bloque no encontrado.');
            $this->showConfirmDeleteModal = false;
            return;
        }

        DB::beginTransaction();
        try {
            $img = $block->image_path;
            $block->delete();

            if ($img && Storage::disk($this->disk)->exists($img)) {
                Storage::disk($this->disk)->delete($img);
            }

            DB::commit();
            session()->flash('message', 'Bloque eliminado.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error eliminando InfoBlock: {$e->getMessage()}");
            session()->flash('error', 'Error al eliminar.');
        }

        $this->showConfirmDeleteModal = false;
        $this->blockIdToDelete = null;
        $this->loadBlocks();
    }

    /** Reordena en la base de datos */
    public function updateBlockOrder($orderedItems): void
    {
        if (! Auth::check() || Auth::user()->role !== 'admin') {
            session()->flash('error', 'Sin permiso.');
            return;
        }

        if (! is_array($orderedItems)) {
            session()->flash('error', 'Datos de orden inválidos.');
            $this->loadBlocks();
            return;
        }

        DB::beginTransaction();
        try {
            foreach ($orderedItems as $item) {
                if (isset($item['value'], $item['order'])) {
                    InfoBlock::where('id', $item['value'])
                        ->update(['order_column' => $item['order']]);
                } else {
                    Log::warning('Item inválido al reordenar InfoBlocks:', $item);
                }
            }
            DB::commit();
            $this->loadBlocks();
            session()->flash('message', 'Orden actualizado.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error reordenando InfoBlocks: {$e->getMessage()}");
            session()->flash('error', 'Error al reordenar.');
            $this->loadBlocks();
        }
    }

    public function render()
    {
        $isAdmin = Auth::check() && Auth::user()->role === 'admin';
        return view('livewire.homepage.info-blocks-manager', compact('isAdmin'));
    }
}
