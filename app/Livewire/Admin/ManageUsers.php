<?php

namespace App\Livewire\Admin;

use Livewire\Component; // Clase base para todos los componentes Livewire.
use App\Models\User;    // Modelo Eloquent para interactuar con la tabla de usuarios.
use Livewire\WithPagination; // Trait para habilitar la paginación de forma sencilla.
// No se necesita: use Livewire\Attributes\Layout; // Atributo para especificar un layout (no se usa aquí).
use Illuminate\Support\Facades\Auth; // Fachada para acceder a la información del usuario autenticado.

class ManageUsers extends Component
{
    use WithPagination; // Habilita la paginación para la lista de usuarios.

    public string $search = ''; // Propiedad pública para almacenar el término de búsqueda ingresado por el usuario.
    protected $paginationTheme = 'tailwind'; // Especifica que se usarán las vistas de paginación de Tailwind CSS.

    // Configura cómo la propiedad 'search' interactúa con la URL (query string).
    // 'except' => '' significa que si 'search' está vacío, no se incluirá en la URL.
    protected $queryString = [
        'search' => ['except' => ''],
    ];

    /**
     * Método `mount`: Se ejecuta una vez cuando el componente se inicializa.
     * Aquí se verifica si el usuario autenticado tiene permisos de administrador.
     */
    public function mount()
    {
        // Verifica si el usuario autenticado tiene el rol 'admin'.
        // Si no es admin, se aborta la solicitud con un error 403 (Acceso no autorizado).
         if (Auth::user()?->role !== 'admin') {
             abort(403, 'Acceso no autorizado');
         }
    }

    /**
     * Método `render`: Se encarga de obtener los datos necesarios y devolver la vista Blade.
     * Livewire llamará a este método para generar el HTML del componente.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        // Obtiene el usuario administrador actualmente autenticado.
        $adminUser = Auth::user();

        // Construye la consulta para obtener los usuarios.
        $users = User::where('role', '!=', 'admin') // Excluye a otros administradores de la lista.
                     ->where('id', '!=', $adminUser->id) // Excluye al propio administrador de la lista.
                     ->when($this->search, function ($query) { // Aplica el filtro de búsqueda si $this->search no está vacío.
                         $query->where(function ($subQuery) { // Agrupa las condiciones de búsqueda con OR.
                             $subQuery->where('name', 'like', '%'.$this->search.'%') // Busca por nombre.
                                      ->orWhere('email', 'like', '%'.$this->search.'%'); // Busca por email.
                         });
                     })
                     ->orderBy('name', 'asc') // Ordena los resultados por nombre en orden ascendente.
                     ->paginate(15); // Pagina los resultados, mostrando 15 usuarios por página.

        // Retorna la vista Blade 'livewire.admin.manage-users'.
        // El layout por defecto (probablemente 'layouts.app') se usará automáticamente.
        // La variable $users estará disponible en la vista.
        return view('livewire.admin.manage-users', [
            'users' => $users,
        ]);
    }
}
