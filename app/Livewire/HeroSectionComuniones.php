<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\HeroSectionSetting;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class HeroSectionComuniones extends Component
{
    use WithFileUploads;
    public ?HeroSectionSetting $settings;
    public string $identifier;

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
            'newBackgroundImage' => 'nullable|image|max:2048',
        ];
    }

    protected $messages = [
        'heroTitle.required' => 'El título principal es obligatorio.',
        'heroSubtitle.required' => 'El subtítulo es obligatorio.',
        'newBackgroundImage.image' => 'El archivo debe ser una imagen.',
        'newBackgroundImage.max' => 'La imagen no debe superar los 2MB.',
    ];

    public function mount(string $identifier, string $defaultTitle = 'Título por Defecto (Comuniones)', string $defaultSubtitle = 'Subtítulo por defecto (Comuniones).', ?string $defaultImage = null)
    {
        $this->identifier = $identifier;
        $this->loadSettings($defaultTitle, $defaultSubtitle, $defaultImage);
    }

    public function loadSettings(string $defaultTitle = 'Título por Defecto', string $defaultSubtitle = 'Subtítulo por defecto.', ?string $defaultImage = null)
    {
        $this->settings = HeroSectionSetting::firstOrCreate(
            ['identifier' => $this->identifier], // Atributos para buscar el registro
            [   // Atributos para usar si se crea un nuevo registro
                'identifier' => $this->identifier, // <--- AÑADIR ESTA LÍNEA AQUÍ
                'hero_title' => $defaultTitle,
                'hero_subtitle' => $defaultSubtitle,
                'background_image_url' => $defaultImage,
            ]
        );

        $this->heroTitle = $this->settings->hero_title;
        $this->heroSubtitle = $this->settings->hero_subtitle;
        $this->existingBackgroundImageUrl = $this->settings->background_image_url;
    }

    public function openEditModal()
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            // Cargar (o crear si no existe) el registro correcto para el identifier actual
            // Es importante que loadSettings maneje los defaults si el registro no existe aún
            $this->loadSettings($this->heroTitle, $this->heroSubtitle, $this->existingBackgroundImageUrl); // Esto asegura que $this->settings esté actualizado o creado para el identifier actual

            // Si después de loadSettings, $this->settings sigue siendo null (no debería pasar con firstOrCreate), hay un problema.
            if(!$this->settings) {
                // Manejar este caso improbable, quizás con valores por defecto o un error.
                // Por ahora, se asume que loadSettings siempre instancia $this->settings.
                session()->flash('error', 'No se pudo cargar la configuración de la sección.');
                return;
            }

            // $this->settings->refresh(); // No es necesario refrescar inmediatamente después de firstOrCreate si es nuevo, pero sí si ya existía.
                                       // loadSettings ya lo carga, así que el refresh es para asegurar que es el más reciente si otro proceso lo modificó.
                                       // Pero como lo acabamos de cargar/crear, debería estar fresco.

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

        if (!$this->settings) {
             session()->flash('error', 'Error: Configuración no encontrada para guardar.');
             $this->closeEditModal();
             return;
        }

        $dataToUpdate = [
            'hero_title' => $this->heroTitle,
            'hero_subtitle' => $this->heroSubtitle,
        ];

        if ($this->newBackgroundImage) {
            if ($this->settings->background_image_url) {
                Storage::disk('public')->delete($this->settings->background_image_url);
            }
            $imagePath = $this->newBackgroundImage->store('hero_sections/' . $this->identifier, 'public');
            $dataToUpdate['background_image_url'] = $imagePath;
        }

        $this->settings->update($dataToUpdate);

        session()->flash('message', 'Sección Hero "' . $this->identifier . '" actualizada con éxito.');
        $this->closeEditModal();
        $this->loadSettings($this->settings->hero_title, $this->settings->hero_subtitle, $this->settings->background_image_url);
    }

    public function render()
    {
        $isAdmin = Auth::check() && Auth::user()->role === 'admin';

        if (!$this->settings && isset($this->identifier)) {
             $this->loadSettings();
        }

        return view('livewire.hero-section-comuniones', [
            'isAdmin' => $isAdmin,
        ]);
    }
}
