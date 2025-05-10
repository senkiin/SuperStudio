<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\HeroSectionSetting; // CAMBIO: Usar el nuevo modelo
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class HeroSectionComuniones extends Component // CAMBIO: Nuevo nombre de clase
{
    use WithFileUploads;

    public ?HeroSectionSetting $settings; // CAMBIO: Tipo de la propiedad

    // Propiedades para los campos del modal
    public string $heroTitle = '';
    public string $heroSubtitle = '';
    public $newBackgroundImage;
    public ?string $existingBackgroundImageUrl = null;

    public bool $showEditModal = false;

    protected function rules()
    {
        return [
            'heroTitle' => 'required|string|max:255',
            'heroSubtitle' => 'required|string|max:1000',
            'newBackgroundImage' => 'nullable|image|max:2048', // Max 2MB
        ];
    }

    protected $messages = [
        'heroTitle.required' => 'El título principal es obligatorio.',
        'heroSubtitle.required' => 'El subtítulo es obligatorio.',
        'newBackgroundImage.image' => 'El archivo debe ser una imagen.',
        'newBackgroundImage.max' => 'La imagen no debe superar los 2MB.',
    ];

    public function mount()
    {
        $this->loadSettings();
    }

    public function loadSettings()
    {
        // CAMBIO: Usar el nuevo modelo HeroSectionSetting
        $this->settings = HeroSectionSetting::firstOrCreate([], [
            'hero_title' => 'NUEVO HERO SECTION (Default)',
            'hero_subtitle' => "Explora este nuevo contenido personalizable (Default)",
            'background_image_url' => null,
        ]);

        $this->heroTitle = $this->settings->hero_title;
        $this->heroSubtitle = $this->settings->hero_subtitle;
        $this->existingBackgroundImageUrl = $this->settings->background_image_url;
    }

    public function openEditModal()
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            $this->settings->refresh();
            $this->heroTitle = $this->settings->hero_title;
            $this->heroSubtitle = $this->settings->hero_subtitle;
            $this->existingBackgroundImageUrl = $this->settings->background_image_url;
            $this->newBackgroundImage = null;
            $this->resetErrorBag();
            $this->showEditModal = true;
        }
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->newBackgroundImage = null;
        $this->resetErrorBag();
    }

    public function save()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            session()->flash('error', 'No tienes permiso para realizar esta acción.');
            $this->closeEditModal();
            return;
        }

        $this->validate();

        $dataToUpdate = [
            'hero_title' => $this->heroTitle,
            'hero_subtitle' => $this->heroSubtitle,
        ];

        if ($this->newBackgroundImage) {
            if ($this->settings->background_image_url) {
                Storage::disk('public')->delete($this->settings->background_image_url);
            }
            // Considera cambiar el nombre del directorio de almacenamiento también
            $imagePath = $this->newBackgroundImage->store('hero-section-backgrounds', 'public');
            $dataToUpdate['background_image_url'] = $imagePath;
        }

        $this->settings->update($dataToUpdate);

        session()->flash('message', 'Sección Hero actualizada con éxito.');
        $this->closeEditModal();
        $this->loadSettings();
    }

    public function render()
    {
        $isAdmin = Auth::check() && Auth::user()->role === 'admin';

        if (!$this->settings) {
            $this->loadSettings();
        }
        // CAMBIO: Usar el nuevo nombre de la vista
        return view('livewire.hero-section-comuniones', [
            'isAdmin' => $isAdmin,
        ]);
    }
}
