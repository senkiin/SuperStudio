<?php

namespace App\Livewire;

use App\Models\VideoEntry;
use App\Models\VideoSection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On; // Importa el atributo On
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Log;

class VideoGalleryManager extends Component
{
    use WithFileUploads;

    // Propiedades de la Sección
    public string $identifier;
    public ?VideoSection $videoSection = null;
    public string $sectionTitle = '';
    public string $sectionDescription = '';
    public Collection $videos;

    // Modal de Gestión para Administradores
    public bool $showManageSectionModal = false;

    // Modal y Propiedades del Formulario para Añadir/Editar Entradas de Video
    public bool $showAddEditVideoModal = false;
    public ?int $editingVideoEntryId = null;
    public string $entry_title = '';
    public string $entry_description = '';
    public string $video_source_type = 'vimeo';
    public string $source_identifier = '';
    public $newVideoFile = null;
    public ?string $currentVideoPath = null;

    // Propiedades para la subida directa
    public bool $isUploadingLargeFile = false;
    public ?string $presignedUploadPath = null;

    // Disco para subidas a S3
    protected string $s3Disk = 'videos';

    /**
     * Reglas de validación.
     */
    protected function rules(): array
    {
        return [
            'sectionTitle'       => 'nullable|string|max:255',
            'sectionDescription' => 'nullable|string|max:5000',
            'entry_title'        => 'required|string|max:255',
            'entry_description'  => 'nullable|string|max:2000',
            'video_source_type'  => ['required', Rule::in(['vimeo', 's3', 'youtube'])],
            // La regla se ajusta: el identificador es necesario si no es una subida S3,
            // o si es S3 pero no hay un archivo nuevo (ni pequeño ni grande)
            'source_identifier'  => Rule::requiredIf(
                fn() => $this->video_source_type !== 's3' || ($this->video_source_type === 's3' && !$this->newVideoFile && !$this->presignedUploadPath)
            ),
            // El archivo es obligatorio solo al crear una nueva entrada S3. Se valida el tamaño para subidas pequeñas.
            'newVideoFile'       => Rule::requiredIf(
                fn() => $this->video_source_type === 's3' && !$this->editingVideoEntryId && !$this->source_identifier
            ) . '|nullable|file|mimes:mp4,mov,ogg,qt,m4v,avi,wmv,flv', // Límite de 100MB para subidas vía Livewire
        ];
    }

    /**
     * Mensajes y atributos personalizados.
     */
    protected array $validationAttributes = [
        'sectionTitle'      => 'Título de la Sección',
        'sectionDescription' => 'Descripción de la Sección',
        'entry_title'       => 'Título del Video',
        'entry_description' => 'Descripción del Video',
        'video_source_type' => 'Tipo de Video',
        'source_identifier' => 'Identificador de Video (ID/Ruta)',
        'newVideoFile'      => 'Archivo de Video',
    ];

    /**
     * mount: carga o crea la sección y sus vídeos.
     */
    public function mount(string $identifier, string $defaultTitle = 'Videos Destacados', string $defaultDescription = '')
    {
        $this->identifier = $identifier;
        $this->videoSection = VideoSection::firstOrCreate(
            ['identifier'  => $this->identifier],
            ['title'       => $defaultTitle, 'description' => $defaultDescription]
        );
        $this->loadSectionDetails();
        $this->loadVideos();
    }

    /**
     * loadSectionDetails: asigna título y descripción.
     */
    private function loadSectionDetails(): void
    {
        $this->sectionTitle       = $this->videoSection->title ?? '';
        $this->sectionDescription = $this->videoSection->description ?? '';
    }

    /**
     * loadVideos: carga los VideoEntry ordenados.
     */
    private function loadVideos(): void
    {
        $this->videos = $this->videoSection
            ? $this->videoSection->videoEntries()->orderBy('order_column')->get()
            : collect();
    }

    /**
     * openManageSectionModal: abre modal de sección.
     */
    public function openManageSectionModal(): void
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') return;
        $this->loadSectionDetails();
        $this->showManageSectionModal = true;
    }

    /**
     * saveSectionDetails: guarda título y descripción.
     */
    public function saveSectionDetails(): void
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') return;
        $this->validate([
            'sectionTitle'       => 'nullable|string|max:255',
            'sectionDescription' => 'nullable|string|max:5000',
        ]);
        $this->videoSection->update([
            'title'       => $this->sectionTitle,
            'description' => $this->sectionDescription,
        ]);
        $this->loadSectionDetails();
        session()->flash('message', 'Detalles de la sección actualizados.');
    }

    /**
     * openAddEditVideoModal: abre modal de video (nueva o edición).
     */
    public function openAddEditVideoModal(?int $videoEntryId = null): void
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') return;
        $this->resetVideoForm();
        if ($videoEntryId) {
            $entry = VideoEntry::find($videoEntryId);
            if ($entry && $entry->video_section_id === $this->videoSection->id) {
                $this->editingVideoEntryId = $entry->id;
                $this->entry_title         = $entry->entry_title;
                $this->entry_description   = $entry->entry_description ?? '';
                $this->video_source_type   = $entry->video_source_type;
                $this->source_identifier   = $entry->source_identifier;
                if ($entry->video_source_type === 's3') {
                    $this->currentVideoPath = $entry->source_identifier;
                }
            }
        }
        $this->showAddEditVideoModal = true;
    }

    /**
     * resetVideoForm: limpia formulario y errores.
     */
    public function resetVideoForm(): void
    {
        $this->resetErrorBag();
        $this->editingVideoEntryId = null;
        $this->entry_title         = '';
        $this->entry_description   = '';
        $this->video_source_type   = 'vimeo';
        $this->source_identifier   = '';
        $this->newVideoFile        = null;
        $this->currentVideoPath    = null;
        $this->isUploadingLargeFile = false;
        $this->presignedUploadPath = null;
    }

    /**
     * Paso 1 (Invocado desde JS): Genera la URL pre-firmada para una subida directa.
     */
   /**
     * Paso 1 (Invocado desde JS): Genera la URL pre-firmada para una subida directa.
     *
     * ESTA ES LA VERSIÓN FINAL Y CORRECTA QUE MANEJA PREFIJOS.
     */
    public function getPresignedUploadUrl(string $filename)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') return;

        $this->isUploadingLargeFile = true;

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk($this->s3Disk);

        // 1. Definimos la ruta RELATIVA, tal como lo hace storeAs().
        // Esta es la ruta que guardaremos en la base de datos.
        $relativePath = "video_sections/{$this->identifier}/" . now()->timestamp . "_" . $filename;

        // 2. Obtenemos el prefijo configurado para este disco desde config/filesystems.php.
        $prefix = config("filesystems.disks.{$this->s3Disk}.root");

        // 3. Creamos la ruta COMPLETA (Key) para S3, uniendo el prefijo y la ruta relativa.
        // Esta es la ubicación física real dentro del bucket.
        $fullS3Key = $prefix ? rtrim($prefix, '/') . '/' . ltrim($relativePath, '/') : $relativePath;

        // 4. Guardamos la ruta RELATIVA en la propiedad del componente para usarla después.
        $this->presignedUploadPath = $relativePath;

        /** @var \Aws\S3\S3Client $client */
        $client = $disk->getClient();

        $command = $client->getCommand('PutObject', [
            'Bucket' => config("filesystems.disks.{$this->s3Disk}.bucket"),
            'Key'    => $fullS3Key, // Usamos la ruta completa para la URL pre-firmada
        ]);

        $presignedRequest = $client->createPresignedRequest(
            $command,
            now()->addMinutes(30) // Aumentamos a 30 minutos
        );

        $presignedUrl = (string) $presignedRequest->getUri();

        $this->dispatch('presigned-url-generated', url: $presignedUrl);
    }

    /**
     * Paso 3 (Invocado desde JS): El frontend notifica que la subida directa ha finalizado.
     */
    #[On('direct-upload-finished')]
    public function handleDirectUploadFinished()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') return;

        $this->isUploadingLargeFile = false;
        $this->source_identifier = $this->presignedUploadPath;

        // Ya tenemos todos los datos, procedemos a guardar.
        $this->saveVideoEntry();
    }

    /**
     * Guarda o actualiza la entrada de video.
     */
    public function saveVideoEntry(): void
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') return;

        // Si hay un archivo PEQUEÑO, se sube de la forma tradicional.
        if ($this->video_source_type === 's3' && $this->newVideoFile && !$this->isUploadingLargeFile) {
            if ($this->editingVideoEntryId && $this->currentVideoPath) {
                Storage::disk($this->s3Disk)->delete($this->currentVideoPath);
            }
            $this->source_identifier = $this->newVideoFile->storeAs(
                "video_sections/{$this->identifier}",
                now()->timestamp . '_' . $this->newVideoFile->getClientOriginalName(),
                $this->s3Disk
            );
        }

        // Validamos los datos del formulario (la ruta S3 ya está en 'source_identifier' si fue subida directa)
        $this->validate();

        VideoEntry::updateOrCreate(
            ['id' => $this->editingVideoEntryId],
            [
                'video_section_id'  => $this->videoSection->id,
                'entry_title'       => $this->entry_title,
                'entry_description' => $this->entry_description,
                'video_source_type' => $this->video_source_type,
                'source_identifier' => $this->source_identifier, // Aquí usamos la ruta de S3
                'order_column'      => $this->editingVideoEntryId
                    ? VideoEntry::find($this->editingVideoEntryId)->order_column
                    : ($this->videoSection->videoEntries()->max('order_column') ?? -1) + 1,
            ]
        );

        session()->flash('message', 'Video ' . ($this->editingVideoEntryId ? 'actualizado' : 'añadido') . ' correctamente.');
        $this->resetVideoForm();
        $this->showAddEditVideoModal = false;
        $this->loadVideos();
    }

    /**
     * deleteVideoEntry: elimina la entrada y el archivo si es S3.
     */
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

    /**
     * updateVideoOrder: reordena las entradas según el array recibido.
     */
    public function updateVideoOrder($orderedIds): void
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') return;
        foreach ($orderedIds as $item) {
            VideoEntry::where('id', $item['value'])
                ->where('video_section_id', $this->videoSection->id)
                ->update(['order_column' => $item['order']]);
        }
        $this->loadVideos();
        session()->flash('message', 'Orden de los videos actualizado.');
    }

    /**
     * render: muestra la vista del componente.
     */
    public function render()
    {
        $isAdmin = Auth::check() && Auth::user()->role === 'admin';
        return view('livewire.video-gallery-manager', compact('isAdmin'));
    }
}
