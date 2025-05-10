<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\HeaderSetting;
use Livewire\WithFileUploads; // Importante para la subida de archivos
use Illuminate\Support\Facades\Storage; // Para manejar archivos

class HeaderComponent extends Component
{
    use WithFileUploads; // Usar el trait

    public ?HeaderSetting $settings;

    // Propiedades para los campos del modal
    public string $heroTitle = '';
    public string $heroSubtitle = '';
    public $newBackgroundImage; // Para el nuevo archivo de imagen
    public ?string $existingBackgroundImageUrl = null; // Para mostrar la imagen actual en el modal

    public bool $showEditModal = false; // Controla la visibilidad del modal

    // Reglas de validación actualizadas
    protected function rules()
    {
        return [
            'heroTitle' => 'required|string|max:255',
            'heroSubtitle' => 'required|string|max:1000',
            'newBackgroundImage' => 'nullable|image|max:2048', // Max 2MB, tipo imagen
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
        $this->settings = HeaderSetting::firstOrCreate([], [
            'hero_title' => 'PHOTOGRAPHY TOURS (Default)',
            'hero_subtitle' => "Explore some of the world's most extraordinary photography workshops (Default)",
            'background_image_url' => null, // O una URL por defecto
        ]);

        $this->heroTitle = $this->settings->hero_title;
        $this->heroSubtitle = $this->settings->hero_subtitle;
        $this->existingBackgroundImageUrl = $this->settings->background_image_url;
    }

    public function openEditModal()
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            // Asegurarse de que los datos del formulario están frescos al abrir el modal
            $this->heroTitle = $this->settings->hero_title;
            $this->heroSubtitle = $this->settings->hero_subtitle;
            $this->existingBackgroundImageUrl = $this->settings->background_image_url;
            $this->newBackgroundImage = null; // Limpiar cualquier subida previa no guardada
            $this->resetErrorBag(); // Limpiar errores de validación previos
            $this->showEditModal = true;
        }
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->newBackgroundImage = null; // Limpiar el archivo temporal
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
            // Borrar la imagen anterior si existe
            if ($this->settings->background_image_url) {
                Storage::disk('public')->delete($this->settings->background_image_url);
            }
            // Guardar la nueva imagen y obtener su ruta
            $imagePath = $this->newBackgroundImage->store('header-backgrounds', 'public');
            $dataToUpdate['background_image_url'] = $imagePath;
        }

        $this->settings->update($dataToUpdate);

        session()->flash('message', 'Cabecera actualizada con éxito.');
        $this->closeEditModal();
        $this->loadSettings(); // Recargar para reflejar los cambios inmediatamente
    }

    public function render()
    {
        $isAdmin = Auth::check() && Auth::user()->role === 'admin';

        return view('livewire.header-component', [
            'isAdmin' => $isAdmin,
        ]);
    }
}
