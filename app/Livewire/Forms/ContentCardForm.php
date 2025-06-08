<?php
// app/Livewire/Forms/ContentCardForm.php

namespace App\Livewire\Forms;

use App\Models\ContentCard; // Importa el modelo ContentCard para interactuar con la base de datos.
use Livewire\Attributes\Locked; // Atributo para marcar propiedades que no deben ser actualizadas desde el frontend.
use Livewire\Attributes\Rule;   // Atributo para definir reglas de validación directamente en las propiedades.
use Livewire\Form;              // Clase base para los objetos Form de Livewire, que encapsulan la lógica de formularios.
use Illuminate\Support\Facades\Log; // Fachada para registrar mensajes de log, útil para depuración.

class ContentCardForm extends Form
{
    #[Locked] // Indica que esta propiedad no se puede modificar desde el frontend directamente.
              // Es útil para IDs o datos sensibles que solo deben cambiar por lógica del backend.
    public ?int $id = null; // ID de la tarjeta de contenido. Es nulo si se está creando una nueva tarjeta.

    #[Rule('required|string|max:255', as: 'título')] // Regla de validación para el título:
                                                    // - 'required': El campo es obligatorio.
                                                    // - 'string': Debe ser una cadena de texto.
                                                    // - 'max:255': Longitud máxima de 255 caracteres.
                                                    // - 'as: 'título'': Alias para mostrar en mensajes de error.
    public string $title = ''; // Título de la tarjeta. Inicializado como cadena vacía.

    #[Rule('nullable|string|max:1000', as: 'descripción')] // Regla de validación para la descripción:
                                                         // - 'nullable': El campo puede ser nulo (opcional).
                                                         // - 'string': Debe ser una cadena de texto.
                                                         // - 'max:1000': Longitud máxima de 1000 caracteres.
    public string $description = ''; // Descripción de la tarjeta. Inicializada como cadena vacía.

    #[Rule('nullable|url|max:2048', as: 'URL del enlace')] // Regla de validación para la URL del enlace:
                                                          // - 'nullable': El campo puede ser nulo.
                                                          // - 'url': Debe ser una URL válida (ej. http://...).
                                                          // - 'max:2048': Longitud máxima de 2048 caracteres (estándar para URLs).
    public ?string $link_url = null; // URL a la que enlazará la tarjeta. Puede ser nulo.

    #[Rule('nullable|string|max:50', as: 'texto del enlace')] // Regla de validación para el texto del enlace:
                                                             // - 'nullable': El campo puede ser nulo.
                                                             // - 'string': Debe ser una cadena de texto.
                                                             // - 'max:50': Longitud máxima de 50 caracteres.
    public string $link_text = 'Saber Más'; // Texto que se mostrará en el botón/enlace.
                                           // Valor por defecto: 'Saber Más'.

    #[Rule('required|integer|min:0', as: 'orden')] // Regla de validación para el orden:
                                                  // - 'required': El campo es obligatorio.
                                                  // - 'integer': Debe ser un número entero.
                                                  // - 'min:0': El valor mínimo permitido es 0.
    public int $order_column = 0; // Columna para ordenar las tarjetas. Valor por defecto 0.

    // La ruta de la imagen se maneja por separado durante el guardado/actualización
    // en el componente Livewire principal, no directamente a través de una regla aquí
    // para el archivo en sí, sino para la ruta almacenada.
    public ?string $image_path = null; // Ruta donde se almacena la imagen de la tarjeta en el sistema de archivos (ej. S3).

    // Comentario sobre la validación de la imagen:
    // La regla de validación para el archivo de imagen en sí (ej. tipo, tamaño)
    // se aplica generalmente en el componente Livewire que utiliza este Form Object,
    // ya que la subida de archivos se maneja allí.
    // Ejemplo de regla en el componente: #[Rule('required|image|max:2048')]
    // (sin 'required' para actualizaciones si la imagen es opcional al editar).

    /**
     * Establece los datos del formulario a partir de un modelo ContentCard existente.
     * Útil para rellenar el formulario cuando se va a editar una tarjeta.
     *
     * @param ContentCard $card El modelo Eloquent de la tarjeta de contenido.
     * @return void
     */
    public function setCard(ContentCard $card): void
    {
        $this->id = $card->id;
        $this->title = $card->title;
        $this->description = $card->description ?? ''; // Usa un string vacío si la descripción es nula en la BD.
        $this->link_url = $card->link_url;
        $this->link_text = $card->link_text ?? 'Saber Más'; // Usa 'Saber Más' si el texto del enlace es nulo.
        $this->order_column = $card->order_column;
        $this->image_path = $card->image_path; // Almacena la ruta de la imagen existente para mostrarla o mantenerla si no se sube una nueva.
    }

    /**
     * Guarda una nueva tarjeta de contenido en la base de datos.
     * La validación de la imagen (el archivo en sí) se espera que se haya realizado
     * en el componente Livewire antes de llamar a este método y que $this->image_path
     * ya contenga la ruta del archivo guardado.
     *
     * @return ContentCard|null El modelo ContentCard creado o null si ocurre un error.
     */
    public function store(): ?ContentCard
    {
        $this->validate(); // Valida los campos del formulario según las reglas definidas.

        // Verifica si se ha proporcionado una ruta de imagen.
        // Esta verificación es para la ruta, no para el archivo en sí.
        if (empty($this->image_path)) {
            // Podrías querer añadir un error de validación específico aquí si la imagen es estrictamente requerida
            // o manejarlo de otra forma.
             Log::warning("Se intentó guardar ContentCard sin una image_path."); // Registra una advertencia.
             session()->flash('form_error', 'La imagen es requerida.'); // Muestra un mensaje flash de error al usuario.
            return null; // Retorna null indicando que la operación falló.
        }

        // Si order_column no es positivo (o es 0, el valor por defecto),
        // calcula el siguiente valor basándose en el máximo actual en la tabla,
        // para que las nuevas tarjetas se añadan al final.
        if ($this->order_column <= 0) {
             $this->order_column = (ContentCard::max('order_column') ?? -1) + 1;
        }

        try {
            // Crea la tarjeta en la base de datos usando solo las propiedades
            // que están definidas en el array de $this->only(...).
            // Esto es una buena práctica para evitar asignación masiva de campos no deseados.
            return ContentCard::create(
                $this->only(['title', 'description', 'link_url', 'link_text', 'order_column', 'image_path'])
            );
        } catch (\Exception $e) {
            // Si ocurre cualquier excepción durante la creación, la registra y retorna null.
            Log::error("Error creando ContentCard: " . $e->getMessage());
             session()->flash('form_error', 'Error al crear la tarjeta.'); // Informa al usuario.
            return null;
        }
    }

    /**
     * Actualiza una tarjeta de contenido existente en la base de datos.
     * Similar a store(), la validación de una *nueva* imagen (si se sube)
     * se espera que se haga en el componente Livewire.
     *
     * @return ContentCard|null El modelo ContentCard actualizado o null si falla.
     */
    public function update(): ?ContentCard
    {
        if (!$this->id) { // Verifica que haya un ID para actualizar.
            Log::warning("Intento de actualizar ContentCard sin ID.");
            session()->flash('form_error', 'No se puede actualizar la tarjeta: ID no encontrado.');
            return null;
        }

        $this->validate(); // Valida los campos del formulario.

        $card = ContentCard::find($this->id); // Busca la tarjeta existente.
        if (!$card) { // Si no se encuentra la tarjeta.
            Log::warning("ContentCard no encontrada para actualizar. ID: {$this->id}");
            session()->flash('form_error', 'Tarjeta no encontrada para actualizar.');
            return null;
        }

         // Lógica sobre image_path:
         // Si $this->image_path es nulo o igual al existente, significa que no se subió una *nueva* imagen.
         // El componente Livewire que usa este Form Object es responsable de:
         // 1. Si se sube una nueva imagen, procesarla, guardarla y asignar la nueva ruta a $this->image_path.
         // 2. Si NO se sube una nueva imagen, $this->image_path debería mantener el valor original
         //    (cargado por setCard()) o ser nulo si se quiere eliminar la imagen (lógica no implementada aquí).

        try {
             // Obtiene solo los datos que se pueden actualizar directamente desde las propiedades del formulario.
             $dataToUpdate = $this->only(['title', 'description', 'link_url', 'link_text', 'order_column']);

             // Incluye image_path en la actualización solo si se ha establecido una nueva ruta de imagen.
             // Es decir, si $this->image_path tiene un valor y es diferente al que ya tenía la tarjeta.
             // O, si $this->image_path tiene un valor y la tarjeta no tenía imagen antes.
             if ($this->image_path && $this->image_path !== $card->image_path) {
                 $dataToUpdate['image_path'] = $this->image_path;
             } elseif (empty($this->image_path) && !empty($card->image_path)) {
                 // Si se quiere permitir eliminar la imagen estableciendo image_path a null o vacío.
                 // $dataToUpdate['image_path'] = null; // Descomentar si se quiere esta funcionalidad.
                 // Considerar borrar el archivo físico del storage también en el componente Livewire.
             }


             $card->update($dataToUpdate); // Actualiza la tarjeta en la BD.
             return $card->fresh(); // Devuelve el modelo actualizado con los datos frescos de la BD.
        } catch (\Exception $e) {
             Log::error("Error actualizando ContentCard ID {$this->id}: " . $e->getMessage());
             session()->flash('form_error', 'Error al actualizar la tarjeta.');
            return null;
        }
    }

    /**
     * Reinicia todos los campos del formulario a sus valores por defecto o iniciales.
     *
     * @return void
     */
    public function resetForm(): void
    {
        $this->reset(); // Método base de Livewire Form para resetear todas las propiedades públicas a su estado inicial.
                        // Esto incluye $id, $title, $description, $link_url, etc.

        // Restablece explícitamente los valores por defecto para propiedades que tienen uno específico
        // y que el $this->reset() podría no manejar como se espera si fueron modificados.
        $this->link_text = 'Saber Más';
        $this->order_column = 0;
        $this->image_path = null; // Asegura que la ruta de la imagen se limpie.
    }
}
