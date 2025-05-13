<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\PageHeaderSetting; // Usar el nuevo modelo
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log; // Para depuración

class ConfigurablePageHeader extends Component
{
    use WithFileUploads;

    public ?PageHeaderSetting $settings = null;
    public string $identifier; // Se pasará al montar el componente

    // Propiedades para los campos del formulario/modal
    public string $heroTitle = '';
    public string $heroSubtitle = '';
    public $newBackgroundImage; // Para el archivo de imagen temporal
    public ?string $existingBackgroundImageUrl = null; // Para mostrar la URL de la imagen actual

    public bool $showEditModal = false;
    public bool $isAdmin = false;

    protected function rules()
    {
        return [
            'heroTitle' => 'required|string|max:255',
            'heroSubtitle' => 'required|string|max:1000',
            'newBackgroundImage' => 'nullable|image|max:2048|mimes:jpeg,png,jpg,webp', // Max 2MB
        ];
    }

    protected $messages = [
        'heroTitle.required' => 'El título principal es obligatorio.',
        'heroSubtitle.required' => 'El subtítulo es obligatorio.',
        'newBackgroundImage.image' => 'El archivo debe ser una imagen (jpeg, png, jpg, webp).',
        'newBackgroundImage.max' => 'La imagen no debe superar los 2MB.',
    ];

    public function mount(string $identifier, string $defaultTitle = 'Título de Cabecera por Defecto', string $defaultSubtitle = 'Este es un subtítulo de ejemplo para la cabecera.', ?string $defaultImage = null)
    {
        if (empty($identifier)) {
            Log::error("ConfigurablePageHeader: Se requiere un 'identifier' único. Montaje cancelado o con identifier de error.");
            // Opcional: throw new \InvalidArgumentException("Identifier es requerido para ConfigurablePageHeader.");
            $this->identifier = 'error_identifier_not_set_on_mount_' . uniqid(); // Para evitar error SQL inmediato
        } else {
            $this->identifier = $identifier;
        }

        $this->isAdmin = Auth::check() && Auth::user()->role === 'admin';
        $this->loadSettings($defaultTitle, $defaultSubtitle, $defaultImage);
    }

    public function loadSettings(string $defaultTitle = '', string $defaultSubtitle = '', ?string $defaultImage = null)
    {
        // Log para verificar el identifier ANTES de la consulta
        Log::info("ConfigurablePageHeader - loadSettings - Identifier: '{$this->identifier}'");

        $this->settings = PageHeaderSetting::firstOrCreate(
            ['identifier' => $this->identifier], // Criterio para buscar
            [   // Valores para usar si se CREA un nuevo registro
                'identifier'           => $this->identifier, // ¡CRUCIAL!
                'hero_title'           => $defaultTitle,
                'hero_subtitle'        => $defaultSubtitle,
                'background_image_url' => $defaultImage, // Puede ser null o una ruta a /public/images/default.jpg
            ]
        );

        $this->heroTitle = $this->settings->hero_title;
        $this->heroSubtitle = $this->settings->hero_subtitle;
        $this->existingBackgroundImageUrl = $this->settings->background_image_url;
    }

    public function openEditModal()
    {
        if (!$this->isAdmin) return;

        // Recargar datos desde la BD para asegurar frescura
        $this->settings = PageHeaderSetting::firstWhere('identifier', $this->identifier);
        if (!$this->settings) {
            // Si por alguna razón no existe (mount debería haberlo creado), intentar crearlo de nuevo.
            // Esto es una salvaguarda. Necesitaríamos los defaults originales que se pasaron a mount.
            // Es más simple asumir que mount() funcionó.
            Log::error("ConfigurablePageHeader - openEditModal: Settings no encontrado para identifier '{$this->identifier}'. Intentando recargar con defaults genéricos.");
            $this->loadSettings('Error: Título no cargado', 'Error: Subtítulo no cargado'); // Defaults muy genéricos
            if (!$this->settings) { // Si aún así falla
                session()->flash('error', 'Error crítico: No se pudo cargar la configuración de la cabecera.');
                return;
            }
        } else {
            $this->settings->refresh(); // Si existía, refrescarlo
        }

        // Poblar el formulario del modal
        $this->heroTitle = $this->settings->hero_title;
        $this->heroSubtitle = $this->settings->hero_subtitle;
        $this->existingBackgroundImageUrl = $this->settings->background_image_url;
        $this->newBackgroundImage = null; // Limpiar subida previa
        $this->resetErrorBag(); // Limpiar errores de validación antiguos
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->newBackgroundImage = null;
        $this->resetErrorBag();
    }

    public function saveSettings()
    {
        if (!$this->isAdmin) {
            session()->flash('error', 'No tienes permiso para realizar esta acción.');
            $this->closeEditModal();
            return;
        }

        $this->validate(); // Usa las rules() definidas

        if (!$this->settings) {
             session()->flash('error', 'Error: No se pudo encontrar la configuración para guardar (identifier: ' . $this->identifier . ').');
             $this->closeEditModal();
             return;
        }

        $dataToUpdate = [
            'hero_title' => $this->heroTitle,
            'hero_subtitle' => $this->heroSubtitle,
        ];

        if ($this->newBackgroundImage) {
            // Borrar la imagen anterior si existe
            if ($this->settings->background_image_url) {
                Storage::disk('public')->delete($this->settings->background_image_url);
            }
            // Guardar la nueva imagen y obtener su ruta
            // Guardar en un subdirectorio con el identifier para mejor organización
            $imagePath = $this->newBackgroundImage->store('page_headers/' . $this->identifier, 'public');
            $dataToUpdate['background_image_url'] = $imagePath;
        }

        $this->settings->update($dataToUpdate);

        session()->flash('message', 'Cabecera "' . $this->identifier . '" actualizada con éxito.');
        $this->closeEditModal();
        // Recargar las propiedades del componente desde la BD
        $this->loadSettings($this->settings->hero_title, $this->settings->hero_subtitle, $this->settings->background_image_url);
    }

    public function render()
    {
        // $this->settings ya es una propiedad pública, y loadSettings la inicializa en mount.
        // Si fuera null aquí, es un problema en la lógica de mount o identifier.
        if (!$this->settings && isset($this->identifier)) {
             Log::warning("ConfigurablePageHeader - render: \$settings es null. Identifier: '{$this->identifier}'. Intentando recarga con defaults.");
             $this->loadSettings(); // Intento de recuperación, usará defaults genéricos del método.
        } else if (!isset($this->identifier)) {
             Log::error("ConfigurablePageHeader - render: \$identifier NO ESTÁ SETEADO. Imposible cargar settings.");
             // Podrías querer un estado de error en la vista aquí.
        }

        return view('livewire.configurable-page-header');
        // No es necesario pasar $isAdmin o $settings si son propiedades públicas.
        // Livewire las hace disponibles automáticamente.
    }
}
