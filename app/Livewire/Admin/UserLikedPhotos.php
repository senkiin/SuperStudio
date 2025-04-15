<?php

namespace App\Livewire\Admin; // Namespace actualizado

use App\Models\User;
use App\Models\Photo;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class UserLikedPhotos extends Component
{
    use WithPagination;

    // Propiedades públicas
    public $clients = [];          // Guarda la lista de clientes para el dropdown
    public ?int $selectedClientId = null; // Guarda el ID del cliente seleccionado

    /**
     * Se ejecuta cuando el componente se monta por primera vez.
     * Carga la lista inicial de clientes.
     */
    public function mount()
    {
        // Cargar solo si el usuario actual es admin (¡Ajusta la lógica de rol!)
        if (Auth::user()?->role === 'admin') {
            $this->loadClients();
        }
    }

    /**
     * Carga la lista de usuarios que son clientes.
     */
    public function loadClients()
    {
         // Obtener usuarios con rol 'client' (¡Ajusta el rol!)
        $this->clients = User::where('role', 'client')
                             ->orderBy('name')
                             ->get(['id', 'name', 'email']);
    }

    /**
     * Hook que se ejecuta cuando se actualiza $selectedClientId.
     * Resetea la paginación de fotos cada vez que se cambia de cliente.
     */
    public function updatedSelectedClientId()
    {
        $this->resetPage(); // Resetea la paginación por defecto
    }

     /**
      * Método para quitar el "Like" (como admin, actuando sobre la selección) - Opcional
      */
     public function adminUnlikePhoto(int $photoId)
     {
         if (Auth::user()?->role !== 'admin' || !$this->selectedClientId) {
            return; // Solo admin y con cliente seleccionado
         }
         $targetClient = User::find($this->selectedClientId);
         if ($targetClient) {
             $targetClient->likedPhotos()->detach($photoId); // Quita el like para el cliente seleccionado
         }
     }

    /**
     * Renderiza la vista.
     */
    public function render()
    {
        // Doble verificación de permiso de administrador (¡Ajusta rol!)
        if (Auth::user()?->role !== 'admin') {
            // Puedes mostrar una vista de error, abortar, o redirigir
             session()->flash('error', 'Acceso no autorizado.');
             return view('livewire.admin.user-liked-photos', ['likedPhotos' => null]); // Vista vacía o de error
             // O: abort(403);
        }

        $likedPhotos = null;
        // Si se ha seleccionado un cliente válido, busca sus fotos favoritas
        if ($this->selectedClientId) {
            $targetClient = User::find($this->selectedClientId);
            if ($targetClient) {
                $likedPhotos = $targetClient->likedPhotos() // Usa la relación del modelo User
                                    ->with('album:id,name') // Carga nombre del álbum
                                    ->orderByPivot('created_at', 'desc') // Ordena por fecha de like
                                    ->paginate(20); // Pagina los resultados
            }
        }

        $users = User::all();

        return view('livewire.admin.user-liked-photos', [
            'likedPhotos' => $likedPhotos,
            'users'=> $users,
            // 'clients' ya es una propiedad pública, disponible en la vista
        ]);
    }
}
