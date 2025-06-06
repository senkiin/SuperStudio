<?php

namespace App\Livewire;

use App\Jobs\ProcessPhotoThumbnail;
use App\Models\Album;
use App\Models\Photo;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Albums extends Component
{
    use WithPagination;
    use WithFileUploads;

    // --- Edit Album Modal ---
    public bool $showEditAlbumModal = false;
    public ?Album $editingAlbum = null;
    #[Validate] public string $editAlbumName = '';
    #[Validate] public string $editAlbumDescription = '';
    #[Validate] public string $editAlbumType = '';
    #[Validate] public ?string $editAlbumClientId = '';
    #[Validate('nullable|image|max:5120')] public $editAlbumNewCover = null;
    public ?string $editAlbumCurrentCover = null;

    // --- Search & Sort (Albums) ---
    public string $cadena = ''; // Para búsqueda de álbumes
    public string $campo  = 'created_at';
    public string $order  = 'desc';

    // --- Gallery Modal ---
    public bool $showModal = false;
    public ?Album $selectedAlbum = null;

    // --- Multi-select photos ---
    public array $selectedPhotos = [];
    public bool $selectionMode = false;

    // --- Upload Photos ---
    #[Validate(['uploadedPhotos.*' => 'image|max:51200'])] // Límite de 50MB por foto
    public $uploadedPhotos = [];

    // --- Photo Viewer Modal ---
    public ?Photo $viewingPhoto = null;
    public bool $showPhotoViewer = false;

    // --- Create Album Modal ---
    public bool $showCreateAlbumModal = false;
    #[Validate('required|string|max:191')]  public string $newAlbumName = '';
    #[Validate('nullable|string|max:1000')] public string $newAlbumDescription = '';
    #[Validate('required|in:public,private,client')] public string $newAlbumType = 'public';
    #[Validate('nullable|image|max:5120')]  public $newAlbumCover = null;
    public ?string $newAlbumClientId = '';

    // --- Client Selection ---
    public $clients = []; // Lista de clientes para los modales
    public string $clientSearchEmail = ''; // Término de búsqueda para clientes (nombre o email)

    protected function rulesForUpdate(): array
    {
        return [
            'editAlbumName'        => 'required|string|max:191',
            'editAlbumDescription' => 'nullable|string|max:1000',
            'editAlbumType'        => 'required|in:public,private,client',
            'editAlbumNewCover'    => 'nullable|image|max:5120', // 5MB
            'editAlbumClientId'    => [
                Rule::requiredIf(fn() => $this->editAlbumType === 'client'),
                'nullable','integer','exists:users,id'
            ],
        ];
    }

    protected function rules(): array
    {
        return [
            'newAlbumName'        => 'required|string|max:191',
            'newAlbumDescription' => 'nullable|string|max:1000',
            'newAlbumType'        => 'required|in:public,private,client',
            'newAlbumCover'       => 'nullable|image|max:5120', // 5MB
            'newAlbumClientId'    => [
                Rule::requiredIf(fn() => $this->newAlbumType === 'client'),
                'nullable','integer','exists:users,id'
            ],
        ];
    }

    // --- Client Search Logic ---
    private function refreshClientsList(): void
    {
        $query = User::where('role', 'user');

        if (!empty($this->clientSearchEmail)) {
            $searchTerm = '%' . $this->clientSearchEmail . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('email', 'like', $searchTerm);
            });
        }

        $query->orderBy('name');
        $this->clients = $query->limit(50)->get(['id', 'name', 'email']);
    }

    public function updatedClientSearchEmail(): void
    {
        if (($this->showCreateAlbumModal && $this->newAlbumType === 'client') ||
            ($this->showEditAlbumModal && $this->editAlbumType === 'client')) {
            $this->refreshClientsList();
        }
    }

    public function updatedNewAlbumType(string $value): void
    {
        $this->newAlbumClientId = ''; // Reset selected client ID
        if ($value === 'client') {
            $this->clientSearchEmail = '';
            $this->refreshClientsList();
        } else {
            $this->clients = [];
        }
    }

    public function updatedEditAlbumType(string $value): void
    {
        $this->editAlbumClientId = ''; // Reset selected client ID
        if ($value === 'client') {
            $this->clientSearchEmail = '';
            $this->refreshClientsList();
        } else {
            $this->clients = [];
        }
    }

    // --- Sorting & Searching (Albums) ---
    public function sortBy(string $field): void
    {
        $allowed = ['name','created_at','type'];
        if (! in_array($field, $allowed)) return;
        if ($this->campo === $field) {
            $this->order = $this->order === 'asc' ? 'desc' : 'asc';
        } else {
            $this->campo = $field;
            $this->order = 'asc';
        }
        $this->resetPage('albumsPage');
    }

    public function updatingCadena(): void
    {
        $this->resetPage('albumsPage');
        if ($this->showModal) {
            $this->resetPage('photosPage');
        }
    }

    // --- Open / Close Gallery Modal ---
    public function openModal(int $albumId): void
    {
        $this->selectedAlbum = Album::with('user')->find($albumId); // Carga el creador del álbum
        $this->closePhotoViewer();
        $this->reset(['selectedPhotos','uploadedPhotos','selectionMode']);
        $this->showModal = true;
        $this->resetPage('photosPage');
    }

    public function closeModal(): void
    {
        $this->closePhotoViewer();
        $this->showModal = false;
        $this->js("setTimeout(() => Livewire.dispatch('resetModalState'), 300)");
    }

    #[On('resetModalState')]
    public function resetModalState(): void
    {
        $this->reset(['selectedAlbum','selectedPhotos','uploadedPhotos','selectionMode']);
    }

    // --- Photo Selection ---
    public function toggleSelectionMode(): void
    {
        $this->selectionMode = ! $this->selectionMode;
        if (! $this->selectionMode) {
            $this->reset('selectedPhotos');
        }
    }

    public function toggleSelection(int $photoId): void
    {
        if (! $this->selectionMode) return;
        if (in_array($photoId, $this->selectedPhotos)) {
            $this->selectedPhotos = array_diff($this->selectedPhotos, [$photoId]);
        } else {
            $this->selectedPhotos[] = $photoId;
        }
        $this->selectedPhotos = array_values($this->selectedPhotos);
    }

    // --- Save Uploaded Photos ---
    public function savePhotos(): void
    {
        $this->validateOnly('uploadedPhotos');
        $user = Auth::user();
        if (! $user
            || ! $this->selectedAlbum
            || ($user->role !== 'admin' && $this->selectedAlbum->user_id !== $user->id)
        ) {
            session()->flash('error','No tienes permiso para añadir fotos.');
            $this->reset('uploadedPhotos');
            return;
        }
        if (empty($this->uploadedPhotos)) {
            return;
        }

        $disk = 'albums';
        $basePath = "{$this->selectedAlbum->id}";
        Storage::disk($disk)->makeDirectory("{$basePath}/photos");
        Storage::disk($disk)->makeDirectory("{$basePath}/thumbnails");

        foreach ($this->uploadedPhotos as $file) {
            $path = $file->store("{$basePath}/photos", $disk);
            // Asumiendo que 'like' no es un campo de Photo o se maneja de otra forma.
            // Si existe, puedes inicializarlo: 'like' => 0,
            $photo = Photo::create([
                'album_id'       => $this->selectedAlbum->id,
                'file_path'      => $path,
                'thumbnail_path' => null,
                'uploaded_by'    => $user->id,
            ]);
            ProcessPhotoThumbnail::dispatch($photo)->onQueue('image-processing');
        }

        $this->reset('uploadedPhotos');
        session()->flash('message','Fotos subidas. Miniaturas en proceso.');
        $this->resetPage('photosPage');
    }

    // --- Delete Selected Photos ---
    public function deleteSelectedPhotos(): void
    {
        if (empty($this->selectedPhotos)) return;
        $user = Auth::user();
        if (! $user
            || ! $this->selectedAlbum
            || ($user->role !== 'admin' && $this->selectedAlbum->user_id !== $user->id)
        ) {
            session()->flash('error','No tienes permiso para borrar fotos.');
            $this->reset(['selectedPhotos','selectionMode']);
            return;
        }

        $photos = Photo::whereIn('id',$this->selectedPhotos)
            ->where('album_id',$this->selectedAlbum->id)
            ->get();

        if ($photos->isEmpty()) {
            $this->reset(['selectedPhotos','selectionMode']);
            return;
        }

        $disk = 'albums';
        $filesToDelete = $photos->flatMap(fn($photo) => array_filter([$photo->file_path, $photo->thumbnail_path]))->all();

        if (!empty($filesToDelete)) {
            Storage::disk($disk)->delete($filesToDelete);
        }
        Photo::destroy($this->selectedPhotos);

        $this->reset(['selectedPhotos','selectionMode']);
        session()->flash('message','Fotos eliminadas.');
        $this->resetPage('photosPage');
    }

    // --- Photo Viewer ---
    #[Computed]
    public function previousPhotoId(): ?int
    {
        if (! $this->viewingPhoto || ! $this->selectedAlbum) return null;
        return $this->selectedAlbum
            ->photos()
            ->where('id','<',$this->viewingPhoto->id)
            ->orderBy('id', 'desc')
            ->max('id');
    }

    #[Computed]
    public function nextPhotoId(): ?int
    {
        if (! $this->viewingPhoto || ! $this->selectedAlbum) return null;
        return $this->selectedAlbum
            ->photos()
            ->where('id','>',$this->viewingPhoto->id)
            ->orderBy('id', 'asc')
            ->min('id');
    }

    public function viewPreviousPhoto(): void
    {
        if ($id = $this->previousPhotoId) {
            $this->viewPhoto($id);
        }
    }

    public function viewNextPhoto(): void
    {
        if ($id = $this->nextPhotoId) {
            $this->viewPhoto($id);
        }
    }

    public function viewPhoto(int $photoId): void
    {
        $photo = Photo::find($photoId);
        if ($photo && $this->selectedAlbum && $photo->album_id === $this->selectedAlbum->id) {
            $this->viewingPhoto    = $photo;
            $this->showPhotoViewer = true;
        }
    }

    public function closePhotoViewer(): void
    {
        $this->showPhotoViewer = false;
        $this->viewingPhoto    = null;
    }

    // --- Create Album ---
    public function openCreateAlbumModal(): void
    {
        $this->resetValidation();
        $this->reset([
            'newAlbumName','newAlbumDescription',
            'newAlbumType','newAlbumCover','newAlbumClientId',
            'clientSearchEmail'
        ]);
        $this->newAlbumType = 'public';
        $this->clients = []; // Se poblará con refreshClientsList si el tipo es 'client'
        $this->showCreateAlbumModal = true;
    }

    public function closeCreateAlbumModal(): void
    {
        $this->showCreateAlbumModal = false;
        $this->resetValidation();
        $this->reset([
            'newAlbumName','newAlbumDescription',
            'newAlbumType','newAlbumCover','newAlbumClientId',
            'clientSearchEmail'
        ]);
        $this->clients = [];
    }

    public function createAlbum(): void
    {
        $user = Auth::user();
        if (! $user || $user->role !== 'admin') {
            session()->flash('error', 'Solo los administradores pueden crear álbumes.');
            return;
        }
        $data = $this->validate(); // Valida todas las reglas definidas en rules()

        $coverPath = null;
        if ($this->newAlbumCover) {
            try {
                $coverPath = $this->newAlbumCover->store('covers','albums');
            } catch (\Exception $e) {
                $this->addError('newAlbumCover','Error al subir portada: ' . $e->getMessage());
                Log::error("Error al subir portada para nuevo álbum: {$e->getMessage()}");
                return;
            }
        }

        // Lógica original para client_id
        $finalClientId = ($data['newAlbumType'] === 'client' && !empty($data['newAlbumClientId']))
            ? $data['newAlbumClientId']
            : $user->id;

        Album::create([
            'name'        => $data['newAlbumName'],
            'description' => $data['newAlbumDescription'],
            'type'        => $data['newAlbumType'],
            'user_id'     => $user->id,
            'cover_image' => $coverPath,
            'client_id'   => $finalClientId,
        ]);
        $this->closeCreateAlbumModal();
        session()->flash('message','Álbum creado exitosamente.');
    }

    // --- Edit Album ---
    public function openEditAlbumModal(int $albumId): void
    {
        $this->resetValidation();
        $album = Album::find($albumId);
        if (! $album) {
            session()->flash('error', 'Álbum no encontrado.');
            return;
        }
        $user = Auth::user();
        if (!$user || ($user->role!=='admin' && $album->user_id!==$user->id)) {
            session()->flash('error','No tienes permiso para editar este álbum.');
            return;
        }
        $this->editingAlbum         = $album;
        $this->editAlbumName        = $album->name;
        $this->editAlbumDescription = $album->description ?? '';
        $this->editAlbumType        = $album->type;
        $this->editAlbumClientId    = $album->client_id ? (string)$album->client_id : '';
        $this->editAlbumCurrentCover= $album->cover_image;
        $this->editAlbumNewCover    = null;

        $this->clientSearchEmail = '';
        $this->clients = [];
        if ($this->editAlbumType === 'client') {
            $this->refreshClientsList();
        }
        $this->showEditAlbumModal = true;
    }

    public function closeEditAlbumModal(): void
    {
        $this->showEditAlbumModal = false;
        $this->resetValidation();
        $this->reset([
            'editingAlbum','editAlbumName','editAlbumDescription',
            'editAlbumType','editAlbumClientId','editAlbumNewCover',
            'editAlbumCurrentCover','clientSearchEmail'
        ]);
        $this->clients = [];
    }

    public function updateAlbum(): void
    {
        if (! $this->editingAlbum) return;
        $user = Auth::user();
        if (! $user || ($user->role!=='admin' && $this->editingAlbum->user_id!==$user->id)) {
            session()->flash('error','No tienes permiso para editar este álbum.');
            $this->closeEditAlbumModal();
            return;
        }
        $data = $this->validate($this->rulesForUpdate());

        $coverPath = $this->editingAlbum->cover_image;
        if ($this->editAlbumNewCover) {
            try {
                if ($coverPath) {
                    Storage::disk('albums')->delete($coverPath);
                }
                $coverPath = $this->editAlbumNewCover->store('covers','albums');
            } catch (\Exception $e) {
                $this->addError('editAlbumNewCover','Error al subir nueva portada: ' . $e->getMessage());
                Log::error("Error al actualizar portada álbum [ID:{$this->editingAlbum->id}]: {$e->getMessage()}");
                return;
            }
        }

        // Lógica original para client_id
        $finalClientId = ($data['editAlbumType'] === 'client' && !empty($data['editAlbumClientId']))
            ? $data['editAlbumClientId']
            : (($data['editAlbumType'] === 'public') ? $user->id : null);


        $this->editingAlbum->update([
            'name'        => $data['editAlbumName'],
            'description' => $data['editAlbumDescription'],
            'type'        => $data['editAlbumType'],
            'cover_image' => $coverPath,
            'client_id'   => $finalClientId,
        ]);
        $this->closeEditAlbumModal();
        session()->flash('message','Álbum actualizado exitosamente.');
    }

    // --- Delete Album ---
    public function deleteAlbum(int $albumId): void
    {
        $album = Album::with('photos')->findOrFail($albumId);
        $user  = Auth::user();
        // Solo admin puede borrar, o también el dueño?
        // Para que solo admin borre: if (!$user || $user->role !== 'admin')
        // Para que admin O dueño borre (como está ahora):
        if (! $user || ($user->role!=='admin' && $album->user_id!==$user->id)) {
            session()->flash('error','No tienes permiso para eliminar este álbum.');
            return;
        }
        try {
            $disk = 'albums';
            $filesToDelete = [];
            if ($album->cover_image) {
                $filesToDelete[] = $album->cover_image;
            }
            foreach ($album->photos as $photo) {
                if ($photo->file_path) $filesToDelete[] = $photo->file_path;
                if ($photo->thumbnail_path) $filesToDelete[] = $photo->thumbnail_path;
            }

            if(!empty($filesToDelete)){
                Storage::disk($disk)->delete($filesToDelete);
            }

            $album->photos()->delete();
            $album->delete();

            session()->flash('message','Álbum y todas sus fotos han sido eliminados.');
            $this->resetPage('albumsPage');
        } catch (\Exception $e) {
            Log::error("Error eliminando álbum [ID:{$albumId}]: {$e->getMessage()}");
            session()->flash('error','Ocurrió un error al eliminar el álbum.');
        }
    }

    // --- Toggle Like on Photo ---
    public function toggleLike(int $photoId): void
    {
        $user = Auth::user();
        if (! $user || ! $this->selectedAlbum) {
            return;
        }
        $photo = Photo::where('id',$photoId)
            ->where('album_id',$this->selectedAlbum->id)
            ->first();

        if (! $photo) return;

        $likeEntry = DB::table('photo_user_likes')
            ->where('user_id',$user->id)
            ->where('photo_id',$photoId);

        if ($likeEntry->exists()) {
            $likeEntry->delete();
        } else {
            DB::table('photo_user_likes')->insert([
                'user_id'    => $user->id,
                'photo_id'   => $photoId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        // $this->dispatch('$refresh'); // Es una opción, pero puede ser menos eficiente
        // Considera refrescar solo la sección de fotos si es posible, o confiar en la reactividad de Livewire
    }

    public function render()
    {
        $user  = Auth::user();
        $query = Album::query();

        if ($user && $user->role !== 'admin') {
            $query->where(function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('client_id', $user->id)
                  ->orWhere('type', 'public');
            });
        } elseif (! $user) {
            $query->where('type', 'public');
        }

        if (! empty($this->cadena)) {
            $search = '%'.$this->cadena.'%';
            $query->where(function($q) use ($search) {
                $q->where('name','like',$search)
                  ->orWhere('description','like',$search);
            });
        }

        $query->with(['user', 'clientUser']) // Eager load user (creator) and clientUser
              ->withCount('photos')         // Eager load photos_count attribute
              ->orderBy(
                  in_array($this->campo,['name','created_at','type'])
                      ? $this->campo
                      : 'created_at',
                  $this->order === 'asc' ? 'asc' : 'desc'
              );

        $albums      = $query->paginate(12,['*'],'albumsPage');
        $photosInModal = null;

        if ($this->showModal && $this->selectedAlbum) {
            $photosInModal = $this->selectedAlbum
                ->photos()
                ->withExists(['likedByUsers as liked_by_current_user'=> function($q){ $q->where('user_id',Auth::id()); }])
                ->orderBy('id')
                ->paginate(15,['*'],'photosPage');
        }

        return view('livewire.albums', [
            'albums'        => $albums,
            'photosInModal' => $photosInModal,
            // 'usuarios'   => User::all(), // ELIMINADO: $this->clients maneja la lista para modales
        ]);
    }
}
