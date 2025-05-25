<?php

namespace App\Livewire;

use App\Models\VideoEntry;
use App\Models\VideoSection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads; // If you plan direct S3 uploads
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

class VideoGalleryManager extends Component
{
    use WithFileUploads; // For S3 uploads

    // Section Properties
    public string $identifier;
    public ?VideoSection $videoSection = null;
    public string $sectionTitle = '';
    public string $sectionDescription = '';
    public Collection $videos;

    // Admin Management Modal
    public bool $showManageSectionModal = false;

    // Add/Edit Video Entry Modal & Form Properties
    public bool $showAddEditVideoModal = false;
    public ?int $editingVideoEntryId = null;
    public string $entry_title = '';
    public string $entry_description = '';
    public string $video_source_type = 'vimeo'; // Default to Vimeo as per screenshot
    public string $source_identifier = ''; // Vimeo ID or S3 Path
    public $newVideoFile = null; // For S3 file upload
    public ?string $currentVideoPath = null; // For S3 file display/delete

    // Disk for S3 uploads
    protected string $s3Disk = 'videos';

    protected function rules(): array
    {
        return [
            'sectionTitle' => 'nullable|string|max:255',
            'sectionDescription' => 'nullable|string|max:5000',
            'entry_title' => 'required|string|max:255',
            'entry_description' => 'nullable|string|max:2000',
            'video_source_type' => ['required', Rule::in(['vimeo', 's3', 'youtube'])],
            'source_identifier' => Rule::requiredIf(fn () => $this->video_source_type !== 's3' || ($this->video_source_type === 's3' && !$this->newVideoFile && !$this->editingVideoEntryId)),
            'newVideoFile' => Rule::requiredIf($this->video_source_type === 's3' && !$this->editingVideoEntryId && !$this->source_identifier)
                .'|nullable|file|mimes:mp4,mov,ogg,qt,m4v,avi,wmv,flv|max:102400', // 100MB max, adjust as needed
        ];
    }

    protected array $validationAttributes = [
        'sectionTitle' => 'Título de la Sección',
        'sectionDescription' => 'Descripción de la Sección',
        'entry_title' => 'Título del Video',
        'entry_description' => 'Descripción del Video',
        'video_source_type' => 'Tipo de Video',
        'source_identifier' => 'Identificador de Video (ID/Ruta)',
        'newVideoFile' => 'Archivo de Video',
    ];

    public function mount(string $identifier, string $defaultTitle = 'Videos Destacados', string $defaultDescription = '')
    {
        $this->identifier = $identifier;
        $this->videoSection = VideoSection::firstOrCreate(
            ['identifier' => $this->identifier],
            ['title' => $defaultTitle, 'description' => $defaultDescription]
        );
        $this->loadSectionDetails();
        $this->loadVideos();
    }

    private function loadSectionDetails(): void
    {
        $this->sectionTitle = $this->videoSection->title ?? '';
        $this->sectionDescription = $this->videoSection->description ?? '';
    }

    private function loadVideos(): void
    {
        $this->videos = $this->videoSection ? $this->videoSection->videoEntries()->orderBy('order_column')->get() : collect();
    }

    public function openManageSectionModal(): void
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') return;
        $this->loadSectionDetails(); // Ensure form fields are fresh
        $this->showManageSectionModal = true;
    }

    public function saveSectionDetails(): void
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') return;
        $this->validate([
            'sectionTitle' => 'nullable|string|max:255',
            'sectionDescription' => 'nullable|string|max:5000',
        ]);

        $this->videoSection->update([
            'title' => $this->sectionTitle,
            'description' => $this->sectionDescription,
        ]);
        $this->loadSectionDetails(); // Refresh public properties
        session()->flash('message', 'Detalles de la sección actualizados.');
        // $this->showManageSectionModal = false; // Optionally close modal
    }

    public function openAddEditVideoModal(?int $videoEntryId = null): void
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') return;
        $this->resetVideoForm();
        if ($videoEntryId) {
            $entry = VideoEntry::find($videoEntryId);
            if ($entry && $entry->video_section_id === $this->videoSection->id) {
                $this->editingVideoEntryId = $entry->id;
                $this->entry_title = $entry->entry_title;
                $this->entry_description = $entry->entry_description ?? '';
                $this->video_source_type = $entry->video_source_type;
                $this->source_identifier = $entry->source_identifier;
                if ($entry->video_source_type === 's3') {
                    $this->currentVideoPath = $entry->source_identifier;
                }
            }
        }
        $this->showAddEditVideoModal = true;
    }

    public function resetVideoForm(): void
    {
        $this->resetErrorBag();
        $this->editingVideoEntryId = null;
        $this->entry_title = '';
        $this->entry_description = '';
        $this->video_source_type = 'vimeo';
        $this->source_identifier = '';
        $this->newVideoFile = null;
        $this->currentVideoPath = null;
    }

    public function saveVideoEntry(): void
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') return;
        $this->validate($this->rules()); // Use all rules

        $s3Path = $this->currentVideoPath; // Keep existing S3 path if editing and not changing file

        if ($this->video_source_type === 's3' && $this->newVideoFile) {
            // Delete old file if editing and new file uploaded
            if ($this->editingVideoEntryId && $this->currentVideoPath) {
                Storage::disk($this->s3Disk)->delete($this->currentVideoPath);
            }
            // Store new file in S3
            // Create a unique path, e.g., video_sections/{identifier}/{timestamp}_{filename}
            $originalFilename = $this->newVideoFile->getClientOriginalName();
            $s3Path = $this->newVideoFile->storeAs(
                "video_sections/{$this->identifier}",
                now()->timestamp . '_' . $originalFilename,
                $this->s3Disk
            );
            $this->source_identifier = $s3Path; // Update source_identifier for S3 type
        } elseif ($this->video_source_type === 's3' && !$this->newVideoFile && $this->editingVideoEntryId) {
             // Editing S3 type, but no new file uploaded, keep existing path (already in $this->source_identifier)
        }


        VideoEntry::updateOrCreate(
            ['id' => $this->editingVideoEntryId],
            [
                'video_section_id' => $this->videoSection->id,
                'entry_title' => $this->entry_title,
                'entry_description' => $this->entry_description,
                'video_source_type' => $this->video_source_type,
                'source_identifier' => $this->source_identifier, // This will be Vimeo ID or S3 path
                // 'thumbnail_url' => ... // Add logic if you capture thumbnails, e.g., from Vimeo API
                'order_column' => $this->editingVideoEntryId ? VideoEntry::find($this->editingVideoEntryId)->order_column : ($this->videoSection->videoEntries()->max('order_column') ?? -1) + 1,
            ]
        );

        session()->flash('message', 'Video ' . ($this->editingVideoEntryId ? 'actualizado' : 'añadido') . ' correctamente.');
        $this->resetVideoForm();
        $this->showAddEditVideoModal = false;
        $this->loadVideos();
    }

    public function deleteVideoEntry(int $videoEntryId): void
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') return;
        $entry = VideoEntry::find($videoEntryId);
        if ($entry && $entry->video_section_id === $this->videoSection->id) {
            if ($entry->video_source_type === 's3' && $entry->source_identifier) {
                Storage::disk($this->s3Disk)->delete($entry->source_identifier);
            }
            $entry->delete();
            session()->flash('message', 'Video eliminado.');
            $this->loadVideos();
        }
    }

    public function updateVideoOrder($orderedIds): void
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') return;
        foreach ($orderedIds as $item) {
            VideoEntry::where('id', $item['value'])
                ->where('video_section_id', $this->videoSection->id) // Ensure it's within the current section
                ->update(['order_column' => $item['order']]);
        }
        $this->loadVideos();
        session()->flash('message', 'Orden de los videos actualizado.');
    }

    public function render()
    {
        $isAdmin = Auth::check() && Auth::user()->role === 'admin';
        return view('livewire.video-gallery-manager', compact('isAdmin'));
    }
}
