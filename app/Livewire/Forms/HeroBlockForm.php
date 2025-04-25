<?php

namespace App\Livewire\Forms;

use App\Models\HeroBlock;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Rule;
use Livewire\Form;

class HeroBlockForm extends Form
{
    #[Locked]
    public ?int $id = null;

    #[Rule('required|string|max:255', as: 'título')]
    public string $title = '';

    #[Rule('nullable|string|max:2000', as: 'descripción')]
    public string $description = '';

    #[Rule('nullable|url|max:2048', as: 'URL del enlace')]
    public ?string $link_url = null;

    #[Rule('nullable|string|max:50', as: 'texto del enlace')]
    public string $link_text = 'Ver Más';

    #[Rule('boolean', as: 'activo')]
    public bool $is_active = true;

    // Path se maneja por separado
    public ?string $image_path = null;

    public function setHeroBlock(?HeroBlock $block): void
    {
        if ($block) {
            $this->id = $block->id;
            $this->title = $block->title;
            $this->description = $block->description ?? '';
            $this->link_url = $block->link_url;
            $this->link_text = $block->link_text ?? 'Ver Más';
            $this->is_active = $block->is_active;
            $this->image_path = $block->image_path; // Guardar path existente
        } else {
             $this->resetForm(); // Reset if null block passed
        }
    }

    public function store(): ?HeroBlock
    {
        $this->validate();
        if (empty($this->image_path)) {
            session()->flash('form_error', 'La imagen es requerida para crear.');
            return null;
        }

        // Opcional: Desactivar otros bloques si solo uno puede estar activo
        // if ($this->is_active) {
        //     HeroBlock::where('is_active', true)->update(['is_active' => false]);
        // }

        try {
            return HeroBlock::create(
                $this->only(['title', 'description', 'link_url', 'link_text', 'is_active', 'image_path'])
            );
        } catch (\Exception $e) {
            Log::error("Error creando HeroBlock: " . $e->getMessage());
            session()->flash('form_error', 'Error al crear el bloque hero.');
            return null;
        }
    }

    public function update(): ?HeroBlock
    {
        if (!$this->id) { return null; }
        $this->validate();
        $block = HeroBlock::find($this->id);
        if (!$block) { return null; }

        // Opcional: Desactivar otros si este se activa y solo uno puede estar activo
        // if ($this->is_active && !$block->is_active) { // Check if activating this one
        //     HeroBlock::where('is_active', true)->where('id', '!=', $this->id)->update(['is_active' => false]);
        // }

        try {
             $dataToUpdate = $this->only(['title', 'description', 'link_url', 'link_text', 'is_active']);
             // Solo incluir image_path si se subió una nueva imagen
             if ($this->image_path && $this->image_path !== $block->image_path) { // Check if path changed
                 $dataToUpdate['image_path'] = $this->image_path;
             }

             $block->update($dataToUpdate);
             return $block->fresh();
        } catch (\Exception $e) {
             Log::error("Error actualizando HeroBlock ID {$this->id}: " . $e->getMessage());
             session()->flash('form_error', 'Error al actualizar el bloque hero.');
            return null;
        }
    }

    public function resetForm(): void
    {
        $this->reset();
        $this->link_text = 'Ver Más';
        $this->is_active = true; // Default a activo
        $this->image_path = null;
    }
}
