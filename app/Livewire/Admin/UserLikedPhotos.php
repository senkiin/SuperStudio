<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Photo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // Asegúrate de importar Storage
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Collection; // Importar Collection

class UserLikedPhotos extends Component
{
    use WithPagination;

    // --- Propiedades para Búsqueda ---
    public string $searchQuery = '';      // Término de búsqueda para clientes
    public ?int $selectedClientId = null; // Guarda el ID del cliente seleccionado

    /**
     * Hook que se ejecuta cuando se actualiza $selectedClientId.
     * Resetea la paginación de fotos cada vez que se cambia de cliente.
     */
    public function updatedSelectedClientId()
    {
        $this->resetPage(); // Resetea la paginación de fotos
    }

    /**
     * Hook que se ejecuta cuando se actualiza $searchQuery.
     * Limpia la selección anterior y resetea la paginación.
     */
    public function updatedSearchQuery()
    {
        $this->selectedClientId = null; // Deselecciona el cliente al buscar de nuevo
        $this->resetPage();         // Resetea la paginación de fotos
    }

    /**
     * Selecciona un cliente desde los resultados de búsqueda.
     */
    public function selectClient(int $clientId)
    {
        $this->selectedClientId = $clientId;
        $this->searchQuery = ''; // Limpia el campo de búsqueda después de seleccionar
        $this->resetPage();      // Asegura resetear paginación de fotos
    }

    /**
     * Método para quitar el "Like" (como admin, actuando sobre la selección)
     */
    public function adminUnlikePhoto(int $photoId)
    {
        if (Auth::user()?->role !== 'admin' || !$this->selectedClientId) {
            session()->flash('error', 'Acción no permitida.');
            return; // Solo admin y con cliente seleccionado
        }

        $targetClient = User::find($this->selectedClientId);
        if ($targetClient) {
            $targetClient->likedPhotos()->detach($photoId); // Quita el like para el cliente seleccionado
            // Opcional: Refrescar la lista o mostrar mensaje de éxito
             session()->flash('message', 'Like eliminado correctamente.');
             // $this->resetPage(); // Puede ser necesario si no se actualiza automáticamente
        } else {
            session()->flash('error', 'No se pudo encontrar al cliente.');
        }
    }

    /**
     * Renderiza la vista.
     */
    public function render()
    {
        // Doble verificación de permiso de administrador (¡Ajusta rol!)
        if (Auth::user()?->role !== 'admin') {
             session()->flash('error', 'Acceso no autorizado.');
             return view('livewire.admin.user-liked-photos', [
                 'likedPhotos' => null,
                 'filteredClients' => collect(), // Colección vacía
                 'selectedClient' => null,
             ]);
        }

        // --- Búsqueda de Clientes ---
        $filteredClients = collect(); // Inicializa como colección vacía
        if (!empty($this->searchQuery)) {
            $query = User::where('role', 'user') // Busca solo clientes (¡Ajusta rol!)
                         ->where(function ($q) {
                             $q->where('name', 'like', '%' . $this->searchQuery . '%')
                               ->orWhere('email', 'like', '%' . $this->searchQuery . '%');
                         })
                         ->orderBy('name')
                         ->limit(10); // Limita el número de resultados mostrados

            $filteredClients = $query->get(['id', 'name', 'email']);
        }

        // --- Obtener Cliente y Fotos Favoritas ---
        $selectedClient = null;
        $likedPhotos = null;

        if ($this->selectedClientId) {
            $selectedClient = User::find($this->selectedClientId); // Busca el cliente seleccionado
            if ($selectedClient && $selectedClient->role === 'user') { // Verifica que exista y sea cliente
                $likedPhotos = $selectedClient->likedPhotos() // Usa la relación del modelo User
                                        ->with('album:id,name') // Carga nombre del álbum
                                        ->orderByPivot('created_at', 'desc') // Ordena por fecha de like
                                        ->paginate(20); // Pagina los resultados
            } else {
                // Si el ID existe pero no es un cliente válido o no se encontró, resetea
                $this->selectedClientId = null;
                $selectedClient = null;
            }
        }

        return view('livewire.admin.user-liked-photos', [
            'filteredClients' => $filteredClients, // Clientes encontrados en la búsqueda
            'selectedClient'  => $selectedClient,  // El cliente que ha sido seleccionado
            'likedPhotos'     => $likedPhotos,     // Fotos favoritas del cliente seleccionado
        ]);
    }
}
