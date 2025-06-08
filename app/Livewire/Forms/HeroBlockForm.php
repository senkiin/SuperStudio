<?php

namespace App\Livewire\Forms;

use App\Models\HeroBlock; // Importa el modelo HeroBlock para interactuar con la base de datos.
use Illuminate\Support\Facades\Log; // Fachada para registrar mensajes de log, útil para depuración.
use Livewire\Attributes\Locked; // Atributo para marcar propiedades que no deben ser actualizadas desde el frontend.
use Livewire\Attributes\Rule;   // Atributo para definir reglas de validación directamente en las propiedades.
use Livewire\Form;              // Clase base para los objetos Form de Livewire.

class HeroBlockForm extends Form
{
    #[Locked] // Indica que el ID no se puede modificar desde el frontend.
    public ?int $id = null; // ID del bloque hero. Nulo si es un nuevo bloque.

    #[Rule('required|string|max:255', as: 'título')] // Regla: obligatorio, texto, máximo 255 caracteres.
                                                    // 'as' define el nombre del campo en mensajes de error.
    public string $title = ''; // Título del bloque hero.

    #[Rule('nullable|string|max:2000', as: 'descripción')] // Regla: opcional, texto, máximo 2000 caracteres.
    public string $description = ''; // Descripción del bloque hero.

    #[Rule('nullable|url|max:2048', as: 'URL del enlace')] // Regla: opcional, debe ser una URL válida, máximo 2048 caracteres.
    public ?string $link_url = null; // URL a la que enlazará el botón del bloque hero.

    #[Rule('nullable|string|max:50', as: 'texto del enlace')] // Regla: opcional, texto, máximo 50 caracteres.
    public string $link_text = 'Ver Más'; // Texto del botón del bloque hero. Valor por defecto 'Ver Más'.

    #[Rule('boolean', as: 'activo')] // Regla: debe ser un valor booleano (true/false).
    public bool $is_active = true; // Indica si el bloque hero está activo. Por defecto true.

    // La ruta de la imagen (image_path) se maneja por separado.
    // El componente Livewire que use este Form Object se encargará de la subida
    // y de asignar la ruta a esta propiedad antes de llamar a store() o update().
    public ?string $image_path = null; // Ruta de la imagen del bloque hero.

    /**
     * Establece los datos del formulario a partir de un modelo HeroBlock existente o resetea el formulario.
     *
     * @param HeroBlock|null $block El modelo HeroBlock para cargar datos, o null para resetear.
     * @return void
     */
    public function setHeroBlock(?HeroBlock $block): void
    {
        if ($block) { // Si se proporciona un bloque, carga sus datos.
            $this->id = $block->id;
            $this->title = $block->title;
            $this->description = $block->description ?? ''; // Usa string vacío si la descripción es nula.
            $this->link_url = $block->link_url;
            $this->link_text = $block->link_text ?? 'Ver Más'; // Usa 'Ver Más' si es nulo.
            $this->is_active = $block->is_active;
            $this->image_path = $block->image_path; // Guarda la ruta de la imagen existente.
        } else {
             $this->resetForm(); // Si no se pasa un bloque, resetea el formulario a sus valores iniciales.
        }
    }

    /**
     * Guarda un nuevo bloque hero en la base de datos.
     *
     * @return HeroBlock|null El modelo HeroBlock creado o null si ocurre un error.
     */
    public function store(): ?HeroBlock
    {
        $this->validate(); // Valida los campos del formulario según las reglas definidas.

        // Verifica si se ha proporcionado una ruta de imagen, ya que es requerida para crear.
        if (empty($this->image_path)) {
            session()->flash('form_error', 'La imagen es requerida para crear.'); // Mensaje de error para el usuario.
            return null; // Retorna null indicando que la operación falló.
        }

        // Lógica opcional: Si solo un bloque puede estar activo a la vez.
        // Si este nuevo bloque se marca como activo, desactiva cualquier otro bloque que esté activo.
        // if ($this->is_active) {
        //     HeroBlock::where('is_active', true)->update(['is_active' => false]);
        // }

        try {
            // Crea el bloque hero usando solo las propiedades especificadas en $this->only().
            // Asegúrate de que 'image_path' esté en el $fillable del modelo HeroBlock.
            return HeroBlock::create(
                $this->only(['title', 'description', 'link_url', 'link_text', 'is_active', 'image_path'])
            );
        } catch (\Exception $e) {
            Log::error("Error creando HeroBlock: " . $e->getMessage()); // Registra el error.
            session()->flash('form_error', 'Error al crear el bloque hero.'); // Informa al usuario.
            return null;
        }
    }

    /**
     * Actualiza un bloque hero existente en la base de datos.
     *
     * @return HeroBlock|null El modelo HeroBlock actualizado o null si falla.
     */
    public function update(): ?HeroBlock
    {
        if (!$this->id) { // No se puede actualizar si no hay un ID.
            return null;
        }
        $this->validate(); // Valida los campos.

        $block = HeroBlock::find($this->id); // Busca el bloque existente.
        if (!$block) { // Si no se encuentra el bloque.
            session()->flash('form_error', 'Bloque hero no encontrado para actualizar.');
            return null;
        }

        // Lógica opcional: Si se activa este bloque y solo uno puede estar activo.
        // if ($this->is_active && !$block->is_active) { // Verifica si se está activando este bloque.
        //     HeroBlock::where('is_active', true)->where('id', '!=', $this->id)->update(['is_active' => false]);
        // }

        try {
             // Prepara los datos para actualizar, excluyendo image_path inicialmente.
             $dataToUpdate = $this->only(['title', 'description', 'link_url', 'link_text', 'is_active']);

             // Solo incluye image_path en la actualización si se proporcionó una nueva ruta
             // y esta es diferente de la que ya tenía el bloque.
             if ($this->image_path && $this->image_path !== $block->image_path) {
                 $dataToUpdate['image_path'] = $this->image_path;
             }
             // Si $this->image_path es null o igual al existente, no se actualiza la imagen.
             // El componente Livewire que usa este form se encarga de borrar el archivo antiguo de S3 si se sube uno nuevo.

             $block->update($dataToUpdate); // Actualiza el bloque.
             return $block->fresh(); // Devuelve el modelo actualizado desde la BD.
        } catch (\Exception $e) {
             Log::error("Error actualizando HeroBlock ID {$this->id}: " . $e->getMessage());
             session()->flash('form_error', 'Error al actualizar el bloque hero.');
            return null;
        }
    }

    /**
     * Reinicia todos los campos del formulario a sus valores por defecto.
     *
     * @return void
     */
    public function resetForm(): void
    {
        $this->reset(); // Método base de Livewire Form para resetear propiedades.
        // Restablece valores por defecto específicos.
        $this->link_text = 'Ver Más';
        $this->is_active = true;
        $this->image_path = null; // Limpia la ruta de la imagen.
    }
}
