<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str; // Importar Str

class HomeVideoManager extends Component
{
    use WithFileUploads;

    public $video; // Para la subida temporal
    public ?string $videoPath = null; // Ruta del video guardado (relativa a storage/app/public)

    // Nombre del archivo donde se guardará la ruta (simple ejemplo)
    private string $configFilePath;

    public function mount()
    {
        // Definir la ruta del archivo de configuración simple
        $this->configFilePath = storage_path('app/home_video_path.txt');

        // Cargar la ruta del video guardada si existe
        if (file_exists($this->configFilePath)) {
            $this->videoPath = trim(file_get_contents($this->configFilePath));
            if (empty($this->videoPath) || !Storage::disk('public')->exists($this->videoPath)) {
                $this->videoPath = null; // Resetear si el archivo no existe en storage
            }
        }
    }

    public function save()
    {
        $this->validate([
            // Ajusta las reglas según necesites (e.g., max size en KB)
            'video' => 'required|file|mimetypes:video/mp4,video/mpeg,video/quicktime|max:5120000000',
        ]);

        // Borrar video anterior si existe
        if ($this->videoPath && Storage::disk('public')->exists($this->videoPath)) {
            Storage::disk('public')->delete($this->videoPath);
        }

        // Generar nombre único y guardar el nuevo video
        $originalFilename = $this->video->getClientOriginalName();
        $extension = $this->video->getClientOriginalExtension();
        // Usar parte del nombre original y añadir hash para unicidad
        $filename = Str::slug(pathinfo($originalFilename, PATHINFO_FILENAME)) . '-' . Str::random(8) . '.' . $extension;

        // Guarda en 'storage/app/public/videos'
        $this->videoPath = $this->video->storeAs('videos', $filename, 'public');

        // Guardar la nueva ruta en el archivo de configuración
        file_put_contents($this->configFilePath, $this->videoPath);

        // Limpiar la propiedad del archivo temporal
        $this->video = null;

        // Opcional: mostrar mensaje de éxito
        session()->flash('message', 'Video subido correctamente.');
    }

    public function deleteVideo()
    {
        if ($this->videoPath && Storage::disk('public')->exists($this->videoPath)) {
            Storage::disk('public')->delete($this->videoPath);
        }
        $this->videoPath = null;

        // Limpiar el archivo de configuración
        if (file_exists($this->configFilePath)) {
            unlink($this->configFilePath);
        }

        // Opcional: mostrar mensaje
        session()->flash('message', 'Video eliminado.');
    }

    public function render()
    {
        return view('livewire.home-video-manager');
    }
}
