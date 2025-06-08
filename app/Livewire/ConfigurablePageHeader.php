<?php

namespace App\Livewire;

use Livewire\Component; // Clase base para todos los componentes Livewire.
use Livewire\WithFileUploads; // Trait para habilitar la subida de archivos.
use Illuminate\Support\Facades\Auth; // Fachada para acceder a la información del usuario autenticado.
use Illuminate\Support\Facades\Storage; // Fachada para interactuar con el sistema de archivos (ej. S3).
use Illuminate\Support\Facades\Log; // Fachada para registrar mensajes de log, útil para depuración.
use App\Models\PageHeaderSetting; // Modelo Eloquent para la configuración de la cabecera de página.
use Illuminate\Filesystem\FilesystemAdapter; // Para el type-hinting del adaptador del sistema de archivos.

class ConfigurablePageHeader extends Component
{
    use WithFileUploads; // Habilita la funcionalidad de subida de archivos en este componente.

    /** @var PageHeaderSetting|null El modelo que contiene la configuración de esta cabecera. */
    public ?PageHeaderSetting $settings = null;

    /** @var string Identificador único para esta instancia de cabecera (ej. 'comuniones_header', 'bodas_header'). */
    public string $identifier;

    /** @var string Título principal de la cabecera, editable por el admin. */
    public string $heroTitle         = '';
    /** @var string Subtítulo de la cabecera, editable por el admin. */
    public string $heroSubtitle      = '';
    /** @var mixed Archivo de imagen temporal subido para el fondo. */
    public $newBackgroundImage;
    /** @var string|null Ruta de la imagen de fondo existente almacenada (ej. en S3). */
    public ?string $existingBackgroundImageUrl = null;

    /** @var bool Controla la visibilidad del modal de edición. */
    public bool $showEditModal = false;
    /** @var bool Indica si el usuario actual es administrador. */
    public bool $isAdmin       = false;

    /** @var string Nombre del disco configurado en filesystems.php para guardar las imágenes de cabecera. */
    protected string $disk = 'page-headers'; // Asegúrate de que este disco esté configurado en config/filesystems.php

    /**
     * Define las reglas de validación para el formulario de edición de la cabecera.
     * @return array
     */
    protected function rules()
    {
        return [
            'heroTitle'          => 'required|string|max:255', // Título obligatorio, máximo 255 caracteres.
            'heroSubtitle'       => 'required|string|max:1000', // Subtítulo obligatorio, máximo 1000 caracteres.
            // Nueva imagen de fondo: opcional, debe ser imagen, máximo 2MB, formatos permitidos.
            'newBackgroundImage' => 'nullable|image|max:2048|mimes:jpeg,png,jpg,webp',
        ];
    }

    /**
     * Define mensajes de error personalizados para las reglas de validación.
     * @var array
     */
    protected $messages = [
        'heroTitle.required'          => 'El título principal es obligatorio.',
        'heroSubtitle.required'       => 'El subtítulo es obligatorio.',
        'newBackgroundImage.image'    => 'El archivo debe ser una imagen válida.',
        'newBackgroundImage.max'      => 'La imagen no debe superar 2 MB.',
        'newBackgroundImage.mimes'    => 'Formatos permitidos: jpeg, png, jpg, webp.',
    ];

    /**
     * Método `mount`: Se ejecuta una vez cuando el componente se inicializa.
     *
     * @param string $identifier Identificador único para esta cabecera.
     * @param string $defaultTitle Título por defecto si no hay configuración guardada.
     * @param string $defaultSubtitle Subtítulo por defecto.
     * @param string|null $defaultImage Ruta de la imagen por defecto.
     */
    public function mount(
        string $identifier,
        string $defaultTitle    = 'Título por defecto',
        string $defaultSubtitle = 'Subtítulo por defecto',
        ?string $defaultImage   = null
    ) {
        // Asigna el identificador, asegurando que no esté vacío.
        $this->identifier = $identifier ?: 'unknown_' . uniqid();
        // Verifica si el usuario autenticado tiene el rol de 'admin'.
        $this->isAdmin    = Auth::check() && Auth::user()->role === 'admin';

        // Carga la configuración de la cabecera o crea una nueva con valores por defecto.
        $this->loadSettings($defaultTitle, $defaultSubtitle, $defaultImage);
    }

    /**
     * Carga la configuración de la cabecera desde la base de datos o la crea si no existe.
     *
     * @param string $defaultTitle Título por defecto.
     * @param string $defaultSubtitle Subtítulo por defecto.
     * @param string|null $defaultImage Imagen por defecto.
     */
    protected function loadSettings(
        string $defaultTitle = '',
        string $defaultSubtitle = '',
        ?string $defaultImage = null
    ) {
        Log::info("Cargando configuración para la cabecera: {$this->identifier}");

        // Busca una configuración con el identificador dado. Si no existe, crea una nueva.
        $this->settings = PageHeaderSetting::firstOrCreate(
            ['identifier' => $this->identifier], // Atributos para buscar.
            [ // Valores a usar si se crea un nuevo registro.
                'hero_title'           => $defaultTitle,
                'hero_subtitle'        => $defaultSubtitle,
                'background_image_url' => $defaultImage,
            ]
        );

        // Asigna los valores cargados/creados a las propiedades del componente.
        $this->heroTitle                  = $this->settings->hero_title;
        $this->heroSubtitle               = $this->settings->hero_subtitle;
        $this->existingBackgroundImageUrl = $this->settings->background_image_url;
    }

    /**
     * Abre el modal de edición para el administrador.
     * Carga los datos actuales de la configuración en el formulario del modal.
     */
    public function openEditModal()
    {
        if (! $this->isAdmin) { // Solo los administradores pueden abrir el modal.
            return;
        }

        // Vuelve a cargar la configuración desde la BD para asegurar que los datos estén frescos.
        $this->settings = PageHeaderSetting::firstWhere('identifier', $this->identifier);
        if (! $this->settings) { // Si no se encuentra la configuración (caso improbable con firstOrCreate).
            session()->flash('error', 'No se encontró la configuración para editar.');
            return;
        }

        // Asigna los valores actuales al formulario del modal.
        $this->heroTitle                  = $this->settings->hero_title;
        $this->heroSubtitle               = $this->settings->hero_subtitle;
        $this->existingBackgroundImageUrl = $this->settings->background_image_url;
        $this->newBackgroundImage         = null; // Limpia cualquier archivo temporal previo.

        $this->resetErrorBag(); // Limpia errores de validación previos.
        $this->showEditModal = true; // Muestra el modal.
    }

    /**
     * Cierra el modal de edición.
     * Limpia el archivo de imagen temporal y los errores de validación.
     */
    public function closeEditModal()
    {
        $this->showEditModal     = false;
        $this->newBackgroundImage = null; // Limpia el archivo temporal.
        $this->resetErrorBag(); // Limpia errores de validación.
    }

    /**
     * Guarda los cambios realizados en la configuración de la cabecera.
     * Valida los datos, sube la nueva imagen si se proporcionó, y actualiza la BD.
     */
    public function saveSettings()
    {
        if (! $this->isAdmin) { // Verifica permisos de administrador.
            session()->flash('error', 'Acción no autorizada.');
            return $this->closeEditModal(); // Cierra el modal si no es admin.
        }

        $this->validate(); // Valida los campos del formulario según las reglas definidas.

        if (! $this->settings) { // Verifica que la configuración esté cargada.
            session()->flash('error', 'Configuración no encontrada para guardar.');
            return $this->closeEditModal();
        }

        // Prepara los datos a actualizar.
        $dataToUpdate = [
            'hero_title'    => $this->heroTitle,
            'hero_subtitle' => $this->heroSubtitle,
        ];

        if ($this->newBackgroundImage) { // Si se subió una nueva imagen.
            // Elimina la imagen anterior del almacenamiento si existía.
            if ($this->settings->background_image_url) {
                Storage::disk($this->disk)
                    ->delete($this->settings->background_image_url);
            }

            // Guarda la nueva imagen en el disco S3 (o el configurado en $this->disk)
            // bajo un subdirectorio específico para esta cabecera (usando $this->identifier).
            $path = $this->newBackgroundImage
                ->store("page_headers/{$this->identifier}", $this->disk); // Ej: 'page_headers/comuniones_header/nombre_archivo.jpg'

            $dataToUpdate['background_image_url'] = $path; // Actualiza la ruta de la imagen.
        }

        $this->settings->update($dataToUpdate); // Actualiza el registro en la base de datos.

        session()->flash('message', 'Cabecera actualizada correctamente.'); // Muestra mensaje de éxito.
        $this->closeEditModal(); // Cierra el modal.

        // Recarga las propiedades del componente para reflejar los cambios inmediatamente en la vista.
        $this->loadSettings(
            $this->settings->hero_title,
            $this->settings->hero_subtitle,
            $this->settings->background_image_url
        );
    }

    /**
     * Renderiza la vista del componente.
     * Pasa los datos necesarios a la plantilla Blade.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        // El type-hinting para $diskAdapter ayuda a IDEs como Intelephense
        // a saber qué métodos están disponibles (ej. url()).
        /** @var FilesystemAdapter $diskAdapter */
        $diskAdapter = Storage::disk($this->disk);

        // Si hay una imagen existente, se asegura de que la propiedad $existingBackgroundImageUrl
        // contenga la URL completa y accesible públicamente (si el disco es público).
        // NOTA: Esta lógica parece redundante aquí si la vista ya usa Storage::disk($disk)->url($settings->background_image_url).
        // Se podría simplificar o eliminar si la vista ya maneja la generación de URLs.
        // Si se mantiene, es importante que $this->existingBackgroundImageUrl contenga la RUTA RELATIVA
        // antes de este punto, y aquí se convierta a URL.
        // Sin embargo, loadSettings() ya asigna la ruta relativa a $this->existingBackgroundImageUrl.
        // La vista (configurable-page-header.blade.php) ya usa Storage::disk('page-headers')->url($settings->background_image_url)
        // por lo que esta transformación en render() podría no ser necesaria o incluso causar confusión
        // si $this->existingBackgroundImageUrl ya es una URL completa.
        //
        // Si la intención es que $this->existingBackgroundImageUrl siempre sea una URL para la vista,
        // entonces la asignación en loadSettings() debería ser:
        // $this->existingBackgroundImageUrl = $this->settings->background_image_url ? $diskAdapter->url($this->settings->background_image_url) : null;
        // Y esta sección en render() podría eliminarse.
        //
        // Por ahora, se asume que $this->existingBackgroundImageUrl puede ser una ruta relativa y aquí se convierte.
        // Pero es un punto a revisar para evitar doble conversión o lógica confusa.
        // if ($this->existingBackgroundImageUrl && !filter_var($this->existingBackgroundImageUrl, FILTER_VALIDATE_URL)) {
        //     // Solo convierte si no es ya una URL (simple heurística)
        //     $this->existingBackgroundImageUrl = $diskAdapter->url($this->existingBackgroundImageUrl);
        // }
        // La vista ya usa $settings->background_image_url directamente con Storage::disk()->url(),
        // por lo que no es necesario pasar $existingBackgroundImageUrl transformado aquí.
        // Las propiedades públicas $settings, $isAdmin, $identifier, $heroTitle, $heroSubtitle
        // ya están disponibles en la vista.

        return view('livewire.configurable-page-header');
    }
}
