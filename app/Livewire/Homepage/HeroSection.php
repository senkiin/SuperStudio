<?php

namespace App\Livewire\Homepage;

use App\Livewire\Forms\HeroBlockForm;
use App\Models\HeroBlock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class HeroSection extends Component
{
    use WithFileUploads;

    /** Bloque activo (o null) */
    public ?HeroBlock $heroBlock = null;

    /** Form object para validación y guardado */
    public HeroBlockForm $form;

    /** Control de modal */
    public bool $showModal = false;
    public bool $isEditing = false;

    /** Imagen subida con Livewire */
    #[Rule('nullable|image|max:4096', as: 'imagen')]
    public $photo = null;

    /** Ruta de la imagen previa */
    public ?string $current_image_path = null;

    /** <-- Disco configurable para S3 -->
     *  Asegúrate de que "s3" esté definido en config/filesystems.php
     */
    protected string $disk = 'hero-home';

    public function mount(): void
    {
 $this->loadHeroBlock();
    // Si existe un bloque activo, guarda su ruta en current_image_path
    if ($this->heroBlock?->image_path) {
        $this->current_image_path = $this->heroBlock->image_path;
    }    }

    /** Carga el bloque hero activo */
    public function loadHeroBlock(): void
    {
        $this->heroBlock = HeroBlock::where('is_active', true)
                                    ->latest()
                                    ->first();
    }

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
        $this->showModal = true;
    }

    public function openEditModal()
    {
        if (! Auth::check() || Auth::user()->role !== 'admin' || ! $this->heroBlock) {
            return;
        }

        $this->resetValidation();
        $this->form->setHeroBlock($this->heroBlock);
        $this->isEditing = true;
        $this->photo = null;
        $this->current_image_path = $this->heroBlock->image_path;
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
        if (! Auth::check() || Auth::user()->role !== 'admin') {
            session()->flash('error', 'Acción no autorizada.');
            return;
        }

        // Foto requerida solo al crear
        $photoRule = $this->isEditing ? 'nullable' : 'required';
        $this->validateOnly('photo', ['photo' => [$photoRule, 'image', 'max:4096']]);

        $newImagePath = null;
        $oldImagePath = $this->isEditing
            ? HeroBlock::find($this->form->id)?->image_path
            : null;

        DB::beginTransaction();
        try {
            // Subida/borrado en S3
            if ($this->photo) {
                if ($this->isEditing && $oldImagePath && Storage::disk($this->disk)->exists($oldImagePath)) {
                    Storage::disk($this->disk)->delete($oldImagePath);
                }
                $newImagePath = $this->photo->store('hero-block-images', $this->disk);
                $this->form->image_path = $newImagePath;
            } elseif (! $this->isEditing) {
                session()->flash('error', 'La imagen es requerida.');
                DB::rollBack();
                return;
            } else {
                // Mantener la ruta antigua
                $this->form->image_path = $oldImagePath;
            }

            $result = $this->isEditing
                ? $this->form->update()
                : $this->form->store();

            if ($result) {
                DB::commit();
                session()->flash('message', 'Bloque Hero ' . ($this->isEditing ? 'actualizado' : 'creado') . ' correctamente.');
                $this->closeModal();
                $this->loadHeroBlock();
            } else {
                DB::rollBack();
                if ($newImagePath && Storage::disk($this->disk)->exists($newImagePath)) {
                    Storage::disk($this->disk)->delete($newImagePath);
                }
                session()->flash('error', 'No se pudo guardar el bloque.');
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            if ($newImagePath && Storage::disk($this->disk)->exists($newImagePath)) {
                Storage::disk($this->disk)->delete($newImagePath);
            }
            Log::warning("ValidationException en HeroSection: {$e->getMessage()}");
            session()->flash('error', 'Error de validación.');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($newImagePath && Storage::disk($this->disk)->exists($newImagePath)) {
                Storage::disk($this->disk)->delete($newImagePath);
            }
            Log::error("Error general guardando HeroBlock: {$e->getMessage()}");
            session()->flash('error', 'Error inesperado al guardar.');
        }
    }

    public function render()
    {
        $isAdmin = Auth::check() && Auth::user()->role === 'admin';
        return view('livewire.homepage.hero-section', compact('isAdmin'));
    }
}
