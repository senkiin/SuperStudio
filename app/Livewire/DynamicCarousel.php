<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\CarouselSlide;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class DynamicCarousel extends Component
{
    use WithFileUploads; // Trait para manejar la subida de archivos en Livewire

    public Collection $slides; // Colección de slides activos para mostrar en el carrusel
    public int $currentSlideIndex = 0; // Índice del slide actualmente visible
    public bool $isAdmin = false; // Indica si el usuario actual es administrador

    // Modal de gestión de slides
    public bool $showManageSlidesModal = false; // Controla la visibilidad del modal de gestión
    public Collection $allSlidesForManagement; // Colección de todos los slides (activos e inactivos) para la gestión

    // Campos para añadir/editar slide (variables que se bindean con el formulario en la vista)
    public ?int $editingSlideId = null; // ID del slide que se está editando (null para crear uno nuevo)
    public string $slideTitle = ''; // Título del slide
    public string $slideSubtitle = ''; // Subtítulo del slide
    public $newSlideImage; // Nueva imagen de fondo subida (o null si no se sube una nueva)
    public ?string $existingSlideImagePreview = null; // URL de previsualización de la imagen existente (si se está editando)
    public string $slideTextColor = '#FFFFFF'; // Color del texto del slide (por defecto blanco)
    public string $slideTextAnimation = 'fade-in-up'; // Animación del texto del slide
    public string $slideButtonText = ''; // Texto del botón del slide
    public string $slideButtonLink = ''; // Enlace del botón del slide
    public int $slideOrder = 0; // Orden del slide en el carrusel
    public bool $slideIsActive = true; // Indica si el slide está activo y debe mostrarse

    // Reglas de validación para los campos del formulario
    protected function rules()
    {
        return [
            'slideTitle'          => 'required|string|max:255',
            'slideSubtitle'       => 'nullable|string|max:1000',
            'newSlideImage'       => ($this->editingSlideId ? 'nullable' : 'required') . '|image|max:200000000048|mimes:jpeg,png,jpg,webp',
            'slideTextColor'      => 'required|string|max:20',
            'slideTextAnimation'  => 'nullable|string|max:50',
            'slideButtonText'     => 'nullable|string|max:50',
            'slideButtonLink'     => 'nullable|url|max:255',
            'slideOrder'          => 'required|integer|min:0',
            'slideIsActive'       => 'required|boolean',
        ];
    }

    // Mensajes de error personalizados para las reglas de validación
    protected $messages = [
        'slideTitle.required'      => 'El título es obligatorio.',
        'newSlideImage.required'   => 'La imagen de fondo es obligatoria al crear un nuevo slide.',
        'newSlideImage.image'      => 'El archivo debe ser una imagen.',
        'newSlideImage.max'        => 'La imagen no debe superar los 2Gb.',
        'slideButtonLink.url'      => 'El enlace del botón debe ser una URL válida (ej: https://ejemplo.com).',
    ];

    // Método que se ejecuta al inicializar el componente
    public function mount()
    {
        $this->isAdmin = Auth::check() && Auth::user()->role === 'admin'; // Verifica si el usuario es administrador
        $this->slides = new Collection(); // Inicializa la colección de slides activos
        $this->allSlidesForManagement = new Collection(); // Inicializa la colección de todos los slides
        $this->loadActiveSlides(); // Carga los slides activos
        if ($this->isAdmin) {
            $this->loadAllSlidesForManagement(); // Si es admin, carga todos los slides para la gestión
        }
    }

    // Carga los slides activos desde la base de datos, ordenados por orden
    public function loadActiveSlides()
    {
        $this->slides = CarouselSlide::where('is_active', true)->orderBy('order')->get();
        $this->currentSlideIndex = $this->slides->isNotEmpty() ? 0 : -1; // Establece el índice del primer slide o -1 si no hay slides
    }

    // Carga todos los slides desde la base de datos, ordenados por orden
    public function loadAllSlidesForManagement()
    {
        $this->allSlidesForManagement = CarouselSlide::orderBy('order')->get();
    }

    // Navega al siguiente slide
    public function nextSlide()
    {
        if ($this->slides->isEmpty()) return;
        $this->currentSlideIndex = ($this->currentSlideIndex + 1) % $this->slides->count();
    }

    // Navega al slide anterior
    public function previousSlide()
    {
        if ($this->slides->isEmpty()) return;
        $this->currentSlideIndex = ($this->currentSlideIndex - 1 + $this->slides->count()) % $this->slides->count();
    }

    // Navega a un slide específico por índice
    public function goToSlide($index)
    {
        if ($this->slides->has($index)) {
            $this->currentSlideIndex = $index;
        }
    }

    // --- Administración ---
    // Abre el modal de gestión de slides
    public function openManageSlidesModal()
    {
        if (!$this->isAdmin) return;
        $this->loadAllSlidesForManagement(); // Carga todos los slides (por si hubo cambios)
        $this->resetFormFields(); // Limpia los campos del formulario
        $this->showManageSlidesModal = true;
    }

    // Cierra el modal de gestión de slides
    public function closeManageSlidesModal()
    {
        $this->showManageSlidesModal = false;
        $this->resetFormFields(); // Limpia los campos del formulario
    }

    // Resetea los campos del formulario a sus valores por defecto
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
        $this->slideOrder = CarouselSlide::max('order') + 10; // Establece un orden por defecto alto para nuevos slides
        $this->slideIsActive = true;
        $this->resetErrorBag(); // Limpia los errores de validación
        $this->resetValidation(); // Resetea el estado de validación
    }

    // Prepara el formulario para editar un slide existente
    public function editSlide(int $slideId)
    {
        if (!$this->isAdmin) return;
        $slide = CarouselSlide::find($slideId);
        if ($slide) {
            $this->editingSlideId = $slide->id;
            $this->slideTitle = $slide->title;
            $this->slideSubtitle = $slide->subtitle ?? '';
            $this->existingSlideImagePreview = $slide->background_image_path
                ? Storage::disk('s3')->url($slide->background_image_path) // Obtiene la URL de la imagen desde S3
                : null;
            $this->newSlideImage = null; // Limpia el campo de nueva imagen (solo se usa para actualizar)
            $this->slideTextColor = $slide->text_color;
            $this->slideTextAnimation = $slide->text_animation ?? 'fade-in-up';
            $this->slideButtonText = $slide->button_text ?? '';
            $this->slideButtonLink = $slide->button_link ?? '';
            $this->slideOrder = $slide->order;
            $this->slideIsActive = $slide->is_active;
        }
    }

    // Guarda un nuevo slide o actualiza uno existente
    public function saveSlide()
    {
        if (!$this->isAdmin) return;
        $this->validate(); // Valida los campos del formulario

        $data = [
            'title'          => $this->slideTitle,
            'subtitle'       => $this->slideSubtitle,
            'text_color'     => $this->slideTextColor,
            'text_animation' => $this->slideTextAnimation,
            'button_text'    => $this->slideButtonText,
            'button_link'    => $this->slideButtonLink,
            'order'          => $this->slideOrder,
            'is_active'      => $this->slideIsActive,
        ];

        // Si se subió una nueva imagen...
        if ($this->newSlideImage) {
            // Si se está editando un slide, elimina la imagen anterior
            if ($this->editingSlideId) {
                $old = CarouselSlide::find($this->editingSlideId);
                if ($old && $old->background_image_path) {
                    Storage::disk('s3')->delete($old->background_image_path);
                }
            }
            // Genera un nombre único para la imagen y la guarda en S3
            $imageName = Str::slug($this->slideTitle ?: 'slide')
                . '-' . uniqid()
                . '.' . $this->newSlideImage->extension();
            $data['background_image_path'] = $this->newSlideImage
                ->storeAs('carousel_backgrounds', $imageName, 's3');
        }

        // Si se está editando, actualiza el slide existente
        if ($this->editingSlideId) {
            CarouselSlide::find($this->editingSlideId)->update($data);
            session()->flash('modal_carousel_message', 'Slide actualizado con éxito.');
        } else { // Si no, crea un nuevo slide
            CarouselSlide::create($data);
            session()->flash('modal_carousel_message', 'Nuevo slide creado con éxito.');
        }

        $this->loadActiveSlides(); // Recarga los slides activos
        $this->loadAllSlidesForManagement(); // Recarga todos los slides
        $this->resetFormFields(); // Limpia el formulario
    }

    // Elimina un slide
    public function deleteSlide(int $slideId)
    {
        if (!$this->isAdmin) return;
        $slide = CarouselSlide::find($slideId);
        if ($slide) {
            // Elimina la imagen del slide de S3 si existe
            if ($slide->background_image_path) {
                Storage::disk('s3')->delete($slide->background_image_path);
            }
            $slide->delete(); // Elimina el slide de la base de datos
            session()->flash('modal_carousel_message', 'Slide eliminado.');
            $this->loadActiveSlides();
            $this->loadAllSlidesForManagement();
            if ($this->editingSlideId === $slideId) {
                $this->resetFormFields(); // Si se estaba editando el slide eliminado, limpia el formulario
            }
        }
    }

    // Actualiza el orden de los slides
    public function updateSlideOrder($orderedSlides)
    {
        if (!$this->isAdmin) return;
        // Recorre la lista de slides ordenados y actualiza el orden en la base de datos
        foreach ($orderedSlides as $item) {
            CarouselSlide::find($item['value'])
                ->update(['order' => $item['order']]);
        }
        $this->loadActiveSlides();
        $this->loadAllSlidesForManagement();
        session()->flash('modal_carousel_message', 'Orden de los slides actualizado.');
    }

    // Renderiza la vista del componente
    public function render()
    {
        // Se asegura de que las colecciones estén cargadas antes de renderizar
        if (!$this->slides instanceof Collection) {
            $this->loadActiveSlides();
        }
        if ($this->isAdmin && !$this->allSlidesForManagement instanceof Collection) {
            $this->loadAllSlidesForManagement();
        }

        return view('livewire.dynamic-carousel');
    }
}
