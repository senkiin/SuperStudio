<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\PageHeaderSetting;
use Illuminate\Filesystem\FilesystemAdapter;

class ConfigurablePageHeader extends Component
{
    use WithFileUploads;

    /** @var PageHeaderSetting */
    public ?PageHeaderSetting $settings = null;

    /** Identificador único para esta cabecera */
    public string $identifier;

    /** Campos del formulario */
    public string $heroTitle         = '';
    public string $heroSubtitle      = '';
    public $newBackgroundImage;                          // Uploaded file
    public ?string $existingBackgroundImageUrl = null;    // Path en S3

    /** Control de modal y permisos */
    public bool $showEditModal = false;
    public bool $isAdmin       = false;

    /** Disco configurado en filesystems.php */
    protected string $disk = 'page-headers';

    protected function rules()
    {
        return [
            'heroTitle'          => 'required|string|max:255',
            'heroSubtitle'       => 'required|string|max:1000',
            'newBackgroundImage' => 'nullable|image|max:2048|mimes:jpeg,png,jpg,webp',
        ];
    }

    protected $messages = [
        'heroTitle.required'          => 'El título principal es obligatorio.',
        'heroSubtitle.required'       => 'El subtítulo es obligatorio.',
        'newBackgroundImage.image'    => 'El archivo debe ser una imagen válida.',
        'newBackgroundImage.max'      => 'La imagen no debe superar 2 MB.',
        'newBackgroundImage.mimes'    => 'Formatos permitidos: jpeg, png, jpg, webp.',
    ];

    public function mount(
        string $identifier,
        string $defaultTitle    = 'Título por defecto',
        string $defaultSubtitle = 'Subtítulo por defecto',
        ?string $defaultImage   = null
    ) {
        $this->identifier = $identifier ?: 'unknown_' . uniqid();
        $this->isAdmin    = Auth::check() && Auth::user()->role === 'admin';

        $this->loadSettings($defaultTitle, $defaultSubtitle, $defaultImage);
    }

    protected function loadSettings(
        string $defaultTitle = '',
        string $defaultSubtitle = '',
        ?string $defaultImage = null
    ) {
        Log::info("Load settings for header: {$this->identifier}");

        $this->settings = PageHeaderSetting::firstOrCreate(
            ['identifier' => $this->identifier],
            [
                'hero_title'           => $defaultTitle,
                'hero_subtitle'        => $defaultSubtitle,
                'background_image_url' => $defaultImage,
            ]
        );

        $this->heroTitle                  = $this->settings->hero_title;
        $this->heroSubtitle               = $this->settings->hero_subtitle;
        $this->existingBackgroundImageUrl = $this->settings->background_image_url;
    }

    public function openEditModal()
    {
        if (! $this->isAdmin) {
            return;
        }

        $this->settings = PageHeaderSetting::firstWhere('identifier', $this->identifier);
        if (! $this->settings) {
            session()->flash('error', 'No se encontró la configuración.');
            return;
        }

        $this->heroTitle                  = $this->settings->hero_title;
        $this->heroSubtitle               = $this->settings->hero_subtitle;
        $this->existingBackgroundImageUrl = $this->settings->background_image_url;
        $this->newBackgroundImage         = null;

        $this->resetErrorBag();
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal     = false;
        $this->newBackgroundImage = null;
        $this->resetErrorBag();
    }

    public function saveSettings()
    {
        if (! $this->isAdmin) {
            session()->flash('error', 'Acción no autorizada.');
            return $this->closeEditModal();
        }

        $this->validate();

        if (! $this->settings) {
            session()->flash('error', 'Configuración no encontrada.');
            return $this->closeEditModal();
        }

        $dataToUpdate = [
            'hero_title'    => $this->heroTitle,
            'hero_subtitle' => $this->heroSubtitle,
        ];

        if ($this->newBackgroundImage) {
            // Elimina la anterior si existe
            if ($this->settings->background_image_url) {
                Storage::disk($this->disk)
                    ->delete($this->settings->background_image_url);
            }

            // Guarda en S3 bajo un subdirectorio por identifier
            $path = $this->newBackgroundImage
                ->store("page_headers/{$this->identifier}", $this->disk);

            $dataToUpdate['background_image_url'] = $path;
        }

        $this->settings->update($dataToUpdate);

        session()->flash('message', 'Cabecera actualizada correctamente.');
        $this->closeEditModal();

        // Refresca propiedades
        $this->loadSettings(
            $this->settings->hero_title,
            $this->settings->hero_subtitle,
            $this->settings->background_image_url
        );
    }

    public function render()
    {
        // …
        // @var FilesystemAdapter $diskAdapter
        $diskAdapter = Storage::disk($this->disk);

        // ahora Intelephense sabe que $diskAdapter tiene url()
        if ($this->existingBackgroundImageUrl) {
            $this->existingBackgroundImageUrl = $diskAdapter->url($this->existingBackgroundImageUrl);
        }

        return view('livewire.configurable-page-header');
    }
}
