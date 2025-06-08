<?php

namespace App\Livewire\Admin;

use App\Models\User; // Modelo para interactuar con la tabla de usuarios.
use App\Models\Photo; // Modelo para interactuar con la tabla de fotos (aunque no se usa directamente para crear instancias aquí, sí para la relación).
use Illuminate\Support\Facades\Auth; // Fachada para acceder a la información del usuario autenticado.
use Illuminate\Support\Facades\Storage; // Fachada para interactuar con el sistema de archivos (ej. para URLs de imágenes).
use Livewire\Component; // Clase base para todos los componentes Livewire.
use Livewire\WithPagination; // Trait para habilitar la paginación de forma sencilla.
use Illuminate\Database\Eloquent\Collection; // Importar la clase Collection para un tipado más estricto si es necesario.

class UserLikedPhotos extends Component
{
    use WithPagination; // Habilita la paginación para la lista de fotos favoritas.

    // --- Propiedades para Búsqueda ---
    public string $searchQuery = '';      // Término de búsqueda para clientes (nombre o email).
    public ?int $selectedClientId = null; // Guarda el ID del cliente cuyas fotos favoritas se están mostrando.

    /**
     * Hook de Livewire: Se ejecuta automáticamente cuando la propiedad $selectedClientId se actualiza.
     * Resetea la paginación de las fotos cada vez que se selecciona un nuevo cliente.
     */
    public function updatedSelectedClientId()
    {
        $this->resetPage(); // Resetea la paginación de la lista de fotos.
    }

    /**
     * Hook de Livewire: Se ejecuta automáticamente cuando la propiedad $searchQuery se actualiza.
     * Limpia la selección del cliente anterior y resetea la paginación de las fotos.
     */
    public function updatedSearchQuery()
    {
        $this->selectedClientId = null; // Deselecciona cualquier cliente previamente elegido.
        $this->resetPage();         // Resetea la paginación de la lista de fotos.
                                    // La paginación de la lista de clientes se maneja implícitamente por Livewire al cambiar la consulta.
    }

    /**
     * Selecciona un cliente de la lista de resultados de búsqueda.
     * Almacena el ID del cliente y limpia el campo de búsqueda.
     *
     * @param int $clientId El ID del usuario (cliente) seleccionado.
     */
    public function selectClient(int $clientId)
    {
        $this->selectedClientId = $clientId;
        $this->searchQuery = ''; // Limpia el campo de búsqueda para ocultar la lista de resultados.
        $this->resetPage();      // Asegura que la paginación de las fotos comience desde la primera página para el nuevo cliente.
    }

    /**
     * Método para que un administrador quite el "Like" de una foto específica
     * para el cliente actualmente seleccionado.
     *
     * @param int $photoId El ID de la foto a la que se le quitará el "Like".
     */
    public function adminUnlikePhoto(int $photoId)
    {
        // Verifica que el usuario autenticado sea un administrador y que haya un cliente seleccionado.
        if (Auth::user()?->role !== 'admin' || !$this->selectedClientId) {
            session()->flash('error', 'Acción no permitida.'); // Muestra un mensaje de error.
            return;
        }

        $targetClient = User::find($this->selectedClientId); // Encuentra al cliente en la base de datos.
        if ($targetClient) {
            // Usa la relación 'likedPhotos' (definida en el modelo User) para quitar el "Like".
            // El método detach() elimina el registro de la tabla pivote 'photo_user_likes'.
            $targetClient->likedPhotos()->detach($photoId);
            session()->flash('message', 'Like eliminado correctamente.'); // Muestra un mensaje de éxito.
            // $this->resetPage(); // Opcional: resetear la paginación si la lista de fotos se actualiza y puede cambiar de página.
                                 // Livewire debería refrescar la vista y la paginación automáticamente.
        } else {
            session()->flash('error', 'No se pudo encontrar al cliente.'); // Muestra un mensaje si el cliente no se encuentra.
        }
    }

    /**
     * Renderiza la vista del componente.
     * Se encarga de obtener y pasar los datos necesarios a la plantilla Blade.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        // Doble verificación de permiso de administrador.
        // (¡Ajusta 'admin' si el nombre del rol es diferente en tu aplicación!)
        if (Auth::user()?->role !== 'admin') {
             session()->flash('error', 'Acceso no autorizado.');
             // Retorna la vista con datos vacíos o nulos si no es admin.
             return view('livewire.admin.user-liked-photos', [
                 'likedPhotos' => null,
                 'filteredClients' => collect(), // Una colección vacía de Laravel.
                 'selectedClient' => null,
             ]);
        }

        // --- Búsqueda de Clientes ---
        $filteredClients = collect(); // Inicializa como una colección vacía.
        if (!empty($this->searchQuery)) { // Si hay algo en el campo de búsqueda...
            $query = User::where('role', 'user') // Busca solo usuarios con el rol 'user' (clientes).
                         ->where(function ($q) { // Agrupa las condiciones de búsqueda con OR.
                             $q->where('name', 'like', '%' . $this->searchQuery . '%') // Busca por nombre.
                               ->orWhere('email', 'like', '%' . $this->searchQuery . '%'); // Busca por email.
                         })
                         ->orderBy('name') // Ordena los resultados por nombre.
                         ->limit(10); // Limita el número de resultados para no sobrecargar la UI.

            $filteredClients = $query->get(['id', 'name', 'email']); // Obtiene solo los campos necesarios.
        }

        // --- Obtener Cliente Seleccionado y sus Fotos Favoritas ---
        $selectedClient = null; // Cliente actualmente seleccionado.
        $likedPhotos = null;    // Paginador de fotos favoritas del cliente seleccionado.

        if ($this->selectedClientId) { // Si se ha seleccionado un ID de cliente...
            $selectedClient = User::find($this->selectedClientId); // Busca el cliente.
            // Verifica que el cliente exista y tenga el rol 'user'.
            if ($selectedClient && $selectedClient->role === 'user') {
                $likedPhotos = $selectedClient->likedPhotos() // Accede a la relación 'likedPhotos'.
                                        ->with('album:id,name') // Carga la relación 'album' de cada foto, seleccionando solo id y name para eficiencia.
                                        ->orderByPivot('created_at', 'desc') // Ordena las fotos por la fecha en que se dio "like" (columna 'created_at' en la tabla pivot).
                                        ->paginate(20); // Pagina los resultados, mostrando 20 fotos por página.
            } else {
                // Si el ID no corresponde a un cliente válido o no se encontró, resetea la selección.
                $this->selectedClientId = null;
                $selectedClient = null;
            }
        }

        // Retorna la vista Blade 'livewire.admin.user-liked-photos' y le pasa los datos.
        return view('livewire.admin.user-liked-photos', [
            'filteredClients' => $filteredClients, // Clientes encontrados en la búsqueda para mostrar en la lista desplegable.
            'selectedClient'  => $selectedClient,  // El objeto User del cliente seleccionado.
            'likedPhotos'     => $likedPhotos,     // Las fotos favoritas (paginadas) del cliente seleccionado.
        ]);
    }
}
