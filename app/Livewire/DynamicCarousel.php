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
    use WithFileUploads;

    public Collection $slides;
    public int $currentSlideIndex = 0;
    public bool $isAdmin = false;

    // Modal de gestión de slides
    public bool $showManageSlidesModal = false;
    public Collection $allSlidesForManagement;

    // Campos para añadir/editar slide
    public ?int $editingSlideId = null;
    public string $slideTitle = '';
    public string $slideSubtitle = '';
    public $newSlideImage;
    public ?string $existingSlideImagePreview = null;
    public string $slideTextColor = '#FFFFFF';
    public string $slideTextAnimation = 'fade-in-up';
    public string $slideButtonText = '';
    public string $slideButtonLink = '';
    public int $slideOrder = 0;
    public bool $slideIsActive = true;

    protected function rules()
    {
        return [
            'slideTitle'          => 'required|string|max:255',
            'slideSubtitle'       => 'nullable|string|max:1000',
            'newSlideImage'       => ($this->editingSlideId ? 'nullable' : 'required') . '|image|max:2048|mimes:jpeg,png,jpg,webp',
            'slideTextColor'      => 'required|string|max:20',
            'slideTextAnimation'  => 'nullable|string|max:50',
            'slideButtonText'     => 'nullable|string|max:50',
            'slideButtonLink'     => 'nullable|url|max:255',
            'slideOrder'          => 'required|integer|min:0',
            'slideIsActive'       => 'required|boolean',
        ];
    }

    protected $messages = [
        'slideTitle.required'      => 'El título es obligatorio.',
        'newSlideImage.required'   => 'La imagen de fondo es obligatoria al crear un nuevo slide.',
        'newSlideImage.image'      => 'El archivo debe ser una imagen.',
        'newSlideImage.max'        => 'La imagen no debe superar los 2MB.',
        'slideButtonLink.url'      => 'El enlace del botón debe ser una URL válida (ej: https://ejemplo.com).',
    ];

    public function mount()
    {
        $this->isAdmin = Auth::check() && Auth::user()->role === 'admin';
        $this->slides = new Collection();
        $this->allSlidesForManagement = new Collection();
        $this->loadActiveSlides();
        if ($this->isAdmin) {
            $this->loadAllSlidesForManagement();
        }
    }

    public function loadActiveSlides()
    {
        $this->slides = CarouselSlide::where('is_active', true)->orderBy('order')->get();
        $this->currentSlideIndex = $this->slides->isNotEmpty() ? 0 : -1;
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

    // --- Administración ---
    public function openManageSlidesModal()
    {
        if (!$this->isAdmin) return;
        $this->loadAllSlidesForManagement();
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
        $this->slideOrder = CarouselSlide::max('order') + 10;
        $this->slideIsActive = true;
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function editSlide(int $slideId)
    {
        if (!$this->isAdmin) return;
        $slide = CarouselSlide::find($slideId);
        if ($slide) {
            $this->editingSlideId = $slide->id;
            $this->slideTitle = $slide->title;
            $this->slideSubtitle = $slide->subtitle ?? '';
            $this->existingSlideImagePreview = $slide->background_image_path
                ? Storage::disk('s3')->url($slide->background_image_path)
                : null;
            $this->newSlideImage = null;
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
            'title'          => $this->slideTitle,
            'subtitle'       => $this->slideSubtitle,
            'text_color'     => $this->slideTextColor,
            'text_animation' => $this->slideTextAnimation,
            'button_text'    => $this->slideButtonText,
            'button_link'    => $this->slideButtonLink,
            'order'          => $this->slideOrder,
            'is_active'      => $this->slideIsActive,
        ];

        if ($this->newSlideImage) {
            if ($this->editingSlideId) {
                $old = CarouselSlide::find($this->editingSlideId);
                if ($old && $old->background_image_path) {
                    Storage::disk('s3')->delete($old->background_image_path);
                }
            }
            $imageName = Str::slug($this->slideTitle ?: 'slide')
                . '-' . uniqid()
                . '.' . $this->newSlideImage->extension();
            $data['background_image_path'] = $this->newSlideImage
                ->storeAs('carousel_backgrounds', $imageName, 's3');
        }

        if ($this->editingSlideId) {
            CarouselSlide::find($this->editingSlideId)->update($data);
            session()->flash('modal_carousel_message', 'Slide actualizado con éxito.');
        } else {
            CarouselSlide::create($data);
            session()->flash('modal_carousel_message', 'Nuevo slide creado con éxito.');
        }

        $this->loadActiveSlides();
        $this->loadAllSlidesForManagement();
        $this->resetFormFields();
    }

    public function deleteSlide(int $slideId)
    {
        if (!$this->isAdmin) return;
        $slide = CarouselSlide::find($slideId);
        if ($slide) {
            if ($slide->background_image_path) {
                Storage::disk('s3')->delete($slide->background_image_path);
            }
            $slide->delete();
            session()->flash('modal_carousel_message', 'Slide eliminado.');
            $this->loadActiveSlides();
            $this->loadAllSlidesForManagement();
            if ($this->editingSlideId === $slideId) {
                $this->resetFormFields();
            }
        }
    }

    public function updateSlideOrder($orderedSlides)
    {
        if (!$this->isAdmin) return;
        foreach ($orderedSlides as $item) {
            CarouselSlide::find($item['value'])
                ->update(['order' => $item['order']]);
        }
        $this->loadActiveSlides();
        $this->loadAllSlidesForManagement();
        session()->flash('modal_carousel_message', 'Orden de los slides actualizado.');
    }

    public function render()
    {
        if (!$this->slides instanceof Collection) {
            $this->loadActiveSlides();
        }
        if ($this->isAdmin && !$this->allSlidesForManagement instanceof Collection) {
            $this->loadAllSlidesForManagement();
        }

        return view('livewire.dynamic-carousel');
    }
}