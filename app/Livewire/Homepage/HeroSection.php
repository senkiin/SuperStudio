<?php

namespace App\Livewire\Homepage;

use App\Livewire\Forms\HeroBlockForm; // Importar Form Object
use App\Models\HeroBlock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithFileUploads; // Añadir Trait

class HeroSection extends Component
{
    use WithFileUploads; // Usar Trait

    public ?HeroBlock $heroBlock = null;
    public HeroBlockForm $form; // Inyectar Form Object

    // Estado del Modal
    public bool $showModal = false;
    public bool $isEditing = false;

    // Para subida de imagen
    #[Rule('nullable|image|max:4096', as: 'imagen')] // Aumentado a 4MB, nullable para editar sin cambiar imagen
    public $photo = null;
    public ?string $current_image_path = null;

    public function mount(): void
    {
        $this->loadHeroBlock();
    }

    public function loadHeroBlock(): void
    {
        $this->heroBlock = HeroBlock::where('is_active', true)
                                    ->latest()
                                    ->first();
        // Establecer datos en el form si estamos editando el bloque actual
        // Esto es útil si queremos que al abrir editar ya tenga los datos
        // Pero lo haremos explícitamente en openEditModal
    }

    // --- Operaciones del Modal ---

    public function openCreateModal()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') return;
        $this->resetValidation();
        $this->form->resetForm();
        $this->isEditing = false;
        $this->photo = null;
        $this->current_image_path = null;
        $this->showModal = true;
    }

    public function openEditModal()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin' || !$this->heroBlock) return;
        $this->resetValidation();
        $this->form->setHeroBlock($this->heroBlock); // Cargar datos del bloque actual en el form
        $this->isEditing = true;
        $this->photo = null; // Resetear campo de subida
        $this->current_image_path = $this->heroBlock->image_path; // Mostrar imagen actual
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

    public function saveHeroBlock()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            session()->flash('error', 'Acción no autorizada.');
            return;
        }

        // Regla de validación para la foto: requerida si se crea, opcional si se edita
        $photoRule = $this->isEditing ? 'nullable' : 'required';
        $this->validateOnly('photo', ['photo' => [$photoRule, 'image', 'max:4096']]); // Validar foto aquí

        $newImagePath = null;
        // Obtener path antiguo ANTES de intentar guardar/actualizar el form
        $oldImagePath = $this->isEditing ? HeroBlock::find($this->form->id)?->image_path : null;

        DB::beginTransaction();
        try {
            // 1. Manejar subida de imagen (si aplica)
            if ($this->photo) {
                // Borrar imagen antigua si se está editando y existe
                if ($this->isEditing && $oldImagePath && Storage::disk('public')->exists($oldImagePath)) {
                    Storage::disk('public')->delete($oldImagePath);
                }
                $newImagePath = $this->photo->store('hero-block-images', 'public');
                $this->form->image_path = $newImagePath; // Asignar nuevo path al form object
            } elseif (!$this->isEditing) {
                 // Creando y no hay foto (la validación inicial debería haber fallado)
                 session()->flash('error', 'La imagen es requerida para crear el bloque.');
                 DB::rollBack();
                 return;
            } else {
                // Editando sin foto nueva: asegurarse que el form object mantiene el path antiguo
                $this->form->image_path = $oldImagePath;
            }

            // 2. Guardar/Actualizar usando el Form Object
            $result = $this->isEditing ? $this->form->update() : $this->form->store();

            if ($result) {
                DB::commit();
                session()->flash('message', 'Bloque Hero ' . ($this->isEditing ? 'actualizado' : 'creado') . ' correctamente.');
                $this->closeModal();
                $this->loadHeroBlock(); // Recargar para mostrar el bloque actualizado/nuevo
            } else {
                DB::rollBack();
                // Limpiar imagen nueva si la transacción falló
                if ($newImagePath && Storage::disk('public')->exists($newImagePath)) {
                    Storage::disk('public')->delete($newImagePath);
                }
                session()->flash('error', session('form_error', 'No se pudo guardar el bloque.'));
                session()->forget('form_error');
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            if ($newImagePath && Storage::disk('public')->exists($newImagePath)) { Storage::disk('public')->delete($newImagePath); }
            Log::warning("ValidationException en saveHeroBlock: " . $e->getMessage());
            session()->flash('error', 'Error de validación. Revisa los campos.');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($newImagePath && Storage::disk('public')->exists($newImagePath)) { Storage::disk('public')->delete($newImagePath); }
            Log::error("Error general guardando HeroBlock: " . $e->getMessage());
            session()->flash('error', 'Error inesperado al guardar.');
        } finally {
             if ($this->photo && method_exists($this->photo, 'delete')) { try { $this->photo->delete(); } catch (\Exception $e) {} }
        }
    }

    public function render()
    {
        $isAdmin = Auth::check() && Auth::user()->role === 'admin';
        // Pasamos isAdmin a la vista para mostrar/ocultar botones
        return view('livewire.homepage.hero-section', [
            'isAdmin' => $isAdmin
        ]);
        // $this->heroBlock ya está disponible en la vista porque es una propiedad pública
    }
}
