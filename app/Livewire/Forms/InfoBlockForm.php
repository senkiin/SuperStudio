<?php

namespace App\Livewire\Forms;

use App\Models\InfoBlock;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Rule;
use Livewire\Form;
use Illuminate\Support\Facades\Log;

class InfoBlockForm extends Form
{
    #[Locked]
    public ?int $id = null;

    #[Rule('required|string|max:255', as: 'título')]
    public string $title = '';

    #[Rule('nullable|string', as: 'descripción')]
    public string $description = '';

    #[Rule('nullable|url|max:2048', as: 'URL del enlace')]
    public ?string $link_url = null;

    #[Rule('nullable|string|max:50', as: 'texto del enlace')]
    public string $link_text = 'Saber Más';

    #[Rule('required|in:left,right', as: 'posición de la imagen')]
    public string $image_position = 'left';

    #[Rule('required|integer|min:0', as: 'orden')]
    public int $order_column = 0;

    public ?string $image_path = null; // Se asigna antes de guardar/actualizar

    public function setBlock(InfoBlock $block): void
    {
        $this->id = $block->id;
        $this->title = $block->title;
        $this->description = $block->description ?? '';
        $this->link_url = $block->link_url;
        $this->link_text = $block->link_text ?? 'Saber Más';
        $this->image_position = $block->image_position;
        $this->order_column = $block->order_column;
        $this->image_path = $block->image_path;
    }

    public function store(): ?InfoBlock
    {
        $this->validate();
        if ($this->order_column <= 0) {
             $this->order_column = (InfoBlock::max('order_column') ?? -1) + 1;
        }
        try {
            // Asegúrate que $fillable en InfoBlock incluye 'image_path'
            return InfoBlock::create(
                $this->only(['title', 'description', 'link_url', 'link_text', 'image_position', 'order_column', 'image_path'])
            );
        } catch (\Exception $e) {
            Log::error("Error creando InfoBlock: " . $e->getMessage());
            return null;
        }
    }

    public function update(): ?InfoBlock
    {
        if (!$this->id) { return null; }
        $this->validate();
        $block = InfoBlock::find($this->id);
        if (!$block) { return null; }
        try {
            // Asegúrate que $fillable en InfoBlock incluye 'image_path'
            $block->update(
                 $this->only(['title', 'description', 'link_url', 'link_text', 'image_position', 'order_column', 'image_path'])
             );
             return $block;
        } catch (\Exception $e) {
             Log::error("Error actualizando InfoBlock ID {$this->id}: " . $e->getMessage());
            return null;
        }
    }

    public function resetForm(): void
    {
        $this->reset();
        $this->link_text = 'Saber Más';
        $this->image_position = 'left';
        $this->order_column = 0;
    }
}
