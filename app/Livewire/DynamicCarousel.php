<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\CarouselSlide;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use Illuminate\Support\Collection; // Asegúrate de importar Collection
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


class DynamicCarousel extends Component
{
    use WithFileUploads;

    public Collection $slides;
    public int $currentSlideIndex = 0;
    public bool $isAdmin = false;

    // Para el modal de gestión de slides
    public bool $showManageSlidesModal = false;
    public Collection $allSlidesForManagement; // Para mostrar todos en el modal, activos e inactivos

    // Propiedades para el formulario de añadir/editar slide
    public ?int $editingSlideId = null;
    public string $slideTitle = '';
    public string $slideSubtitle = '';
    public $newSlideImage; // Para la subida de la nueva imagen
    public ?string $existingSlideImagePreview = null;
    public string $slideTextColor = '#FFFFFF';
    public string $slideTextAnimation = 'fade-in-up'; // Animación por defecto
    public string $slideButtonText = '';
    public string $slideButtonLink = '';
    public int $slideOrder = 0;
    public bool $slideIsActive = true;

    protected function rules()
    {
        return [
            'slideTitle' => 'required|string|max:255',
            'slideSubtitle' => 'nullable|string|max:1000',
            'newSlideImage' => ($this->editingSlideId ? 'nullable' : 'required') . '|image|max:2048|mimes:jpeg,png,jpg,webp', // Requerido solo al crear
            'slideTextColor' => 'required|string|max:20', // Ej: #FFFFFF o text-white
            'slideTextAnimation' => 'nullable|string|max:50',
            'slideButtonText' => 'nullable|string|max:50',
            'slideButtonLink' => 'nullable|url|max:255',
            'slideOrder' => 'required|integer|min:0',
            'slideIsActive' => 'required|boolean',
        ];
    }

    protected $messages = [
        'slideTitle.required' => 'El título es obligatorio.',
        'newSlideImage.required' => 'La imagen de fondo es obligatoria al crear un nuevo slide.',
        'newSlideImage.image' => 'El archivo debe ser una imagen.',
        'newSlideImage.max' => 'La imagen no debe superar los 2MB.',
        'slideButtonLink.url' => 'El enlace del botón debe ser una URL válida (ej: https://ejemplo.com).',
    ];

    public function mount()
    {
        $this->isAdmin = Auth::check() && Auth::user()->role === 'admin';
        $this->slides = new Collection(); // Inicializar
        $this->allSlidesForManagement = new Collection(); // Inicializar
        $this->loadActiveSlides();
        if ($this->isAdmin) {
            $this->loadAllSlidesForManagement();
        }
    }

    public function loadActiveSlides()
    {
        $this->slides = CarouselSlide::where('is_active', true)->orderBy('order')->get();
        $this->currentSlideIndex = $this->slides->isNotEmpty() ? 0 : -1; // -1 si no hay slides
    }

    public function loadAllSlidesForManagement()
    {
        $this->allSlidesForManagement = CarouselSlide::orderBy('order')->get();
    }

    public function nextSlide()
    {
        if ($this->slides->isEmpty()) return;
        $this->currentSlideIndex = ($this->currentSlideIndex + 1) % $this->slides->count();
    }

    public function previousSlide()
    {
        if ($this->slides->isEmpty()) return;
        $this->currentSlideIndex = ($this->currentSlideIndex - 1 + $this->slides->count()) % $this->slides->count();
    }

    public function goToSlide($index)
    {
        if ($this->slides->has($index)) {
            $this->currentSlideIndex = $index;
        }
    }

    // --- Métodos de Administración ---
    public function openManageSlidesModal()
    {
        if (!$this->isAdmin) return;
        $this->loadAllSlidesForManagement(); // Recargar por si hubo cambios
        $this->resetFormFields();
        $this->showManageSlidesModal = true;
    }

    public function closeManageSlidesModal()
    {
        $this->showManageSlidesModal = false;
        $this->resetFormFields();
    }

    public function resetFormFields()
    {
        $this->editingSlideId = null;
        $this->slideTitle = '';
        $this->slideSubtitle = '';
        $this->newSlideImage = null;
        $this->existingSlideImagePreview = null;
        $this->slideTextColor = '#FFFFFF';
        $this->slideTextAnimation = 'fade-in-up';
        $this->slideButtonText = '';
        $this->slideButtonLink = '';
        $this->slideOrder = CarouselSlide::max('order') + 10; // Sugerir siguiente orden
        $this->slideIsActive = true;
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function createNewSlide()
    {
        if (!$this->isAdmin) return;
        $this->resetFormFields(); // Prepara el formulario para un nuevo slide
        // No es necesario abrir el modal aquí si el formulario ya está en el modal principal de gestión
        // Si tuvieras un modal separado para "Añadir", aquí lo abrirías.
    }

    public function editSlide(int $slideId)
    {
        if (!$this->isAdmin) return;
        $slide = CarouselSlide::find($slideId);
        if ($slide) {
            $this->editingSlideId = $slide->id;
            $this->slideTitle = $slide->title;
            $this->slideSubtitle = $slide->subtitle ?? '';
            $this->existingSlideImagePreview = $slide->background_image_path;
            $this->newSlideImage = null; // Limpiar por si había una subida pendiente
            $this->slideTextColor = $slide->text_color;
            $this->slideTextAnimation = $slide->text_animation ?? 'fade-in-up';
            $this->slideButtonText = $slide->button_text ?? '';
            $this->slideButtonLink = $slide->button_link ?? '';
            $this->slideOrder = $slide->order;
            $this->slideIsActive = $slide->is_active;
        }
    }

    public function saveSlide()
    {
        if (!$this->isAdmin) return;
        $this->validate();

        $data = [
            'title' => $this->slideTitle,
            'subtitle' => $this->slideSubtitle,
            'text_color' => $this->slideTextColor,
            'text_animation' => $this->slideTextAnimation,
            'button_text' => $this->slideButtonText,
            'button_link' => $this->slideButtonLink,
            'order' => $this->slideOrder,
            'is_active' => $this->slideIsActive,
        ];

        $imagePathChanged = false;
        if ($this->newSlideImage) {
            // Si se está editando y hay una imagen antigua, borrarla
            if ($this->editingSlideId) {
                $slideS = CarouselSlide::find($this->editingSlideId);
                if ($slideS && $slideS->background_image_path) {
                    Storage::disk('public')->delete($slideS->background_image_path);
                }
            }
            $imageName = Str::slug($this->slideTitle ?: 'slide') . '-' . uniqid() . '.' . $this->newSlideImage->extension();
            $data['background_image_path'] = $this->newSlideImage->storeAs('carousel_backgrounds', $imageName, 'public');
            $imagePathChanged = true;
        }

        if ($this->editingSlideId) {
            CarouselSlide::find($this->editingSlideId)->update($data);
            session()->flash('modal_carousel_message', 'Slide actualizado con éxito.');
        } else {
            CarouselSlide::create($data);
            session()->flash('modal_carousel_message', 'Nuevo slide creado con éxito.');
        }

        $this->loadActiveSlides(); // Para el carrusel principal
        $this->loadAllSlidesForManagement(); // Para la lista en el modal
        $this->resetFormFields(); // Limpiar formulario
        // Opcional: no cerrar el modal para permitir añadir/editar más slides
        // $this->showManageSlidesModal = false;
    }

    public function deleteSlide(int $slideId)
    {
        if (!$this->isAdmin) return;
        $slide = CarouselSlide::find($slideId);
        if ($slide) {
            if ($slide->background_image_path) {
                Storage::disk('public')->delete($slide->background_image_path);
            }
            $slide->delete();
            session()->flash('modal_carousel_message', 'Slide eliminado.');
            $this->loadActiveSlides();
            $this->loadAllSlidesForManagement();
            if ($this->editingSlideId == $slideId) { // Si se estaba editando el slide eliminado
                $this->resetFormFields();
            }
        }
    }

    public function updateSlideOrder($orderedSlides) // Para SortableJS
    {
        if (!$this->isAdmin) return;
        foreach ($orderedSlides as $item) {
            CarouselSlide::find($item['value'])->update(['order' => $item['order']]);
        }
        $this->loadActiveSlides();
        $this->loadAllSlidesForManagement();
        session()->flash('modal_carousel_message', 'Orden de los slides actualizado.');
    }


    public function render()
    {
        // Asegurar que $slides siempre sea una colección, incluso si está vacía
        if (!$this->slides instanceof Collection) {
            $this->slides = new Collection();
            // Intentar recargar si es la primera vez o si se perdió el estado
            if (CarouselSlide::count() > 0 && $this->slides->isEmpty()) {
                $this->loadActiveSlides();
            }
        }
        if ($this->isAdmin && !$this->allSlidesForManagement instanceof Collection) {
            $this->allSlidesForManagement = new Collection();
            if (CarouselSlide::count() > 0 && $this->allSlidesForManagement->isEmpty()) {
                $this->loadAllSlidesForManagement();
            }
        }


        return view('livewire.dynamic-carousel');
        // Las propiedades públicas como $slides, $currentSlideIndex, $isAdmin, etc.,
        // están disponibles automáticamente en la vista.
    }
}
