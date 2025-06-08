<?php

namespace App\Livewire\Admin;

// Importa los modelos necesarios para obtener datos.
use App\Models\User; // Modelo para los usuarios.
use App\Models\Album; // Modelo para los álbumes.
use App\Models\Superappointment; // Modelo para las "supercitas" o citas mejoradas.
use Carbon\Carbon; // Librería para manejar fechas y horas de forma sencilla.
use Livewire\Component; // Clase base para todos los componentes Livewire.
use Illuminate\Support\Facades\Auth; // Fachada para manejar la autenticación.


class Dashboard extends Component
{
    // Propiedades públicas que estarán disponibles en la vista del componente.
    public $userCount; // Almacenará el número total de usuarios (no administradores).
    public $albumCount; // Almacenará el número total de álbumes.
    // public $orderCount; // Almacenará el número de pedidos pendientes o en proceso (comentado, parece eliminado).
    public $pendingAppointmentCount; // Almacenará el número de citas pendientes o confirmadas para el futuro.

    /**
     * Método que se ejecuta cuando el componente se inicializa (monta).
     * Aquí se realizan las comprobaciones de permisos y se cargan los datos iniciales.
     */
    public function mount()
    {
        // Verifica si el usuario autenticado tiene el rol de 'admin'.
        // Si no es admin, se aborta la solicitud con un error 403 (Acceso no autorizado).
        if (Auth::user()?->role !== 'admin') {
            abort(403, 'Acceso no autorizado');
        }

        // Cuenta el número de usuarios que tienen el rol 'user' (clientes).
        $this->userCount = User::where('role', 'user')->count();
        // Cuenta el número total de álbumes en el sistema.
        $this->albumCount = Album::count();
        // $this->orderCount = Order::whereIn('status', ['pending', 'processing'])->count(); // Línea eliminada para contar pedidos.

        // Calcula el número de citas (Superappointment) que están pendientes o confirmadas
        // y cuya fecha y hora son futuras a la actual.
        $this->pendingAppointmentCount = Superappointment::where('appointment_datetime', '>', Carbon::now()) // Citas futuras.
                                                       ->whereIn('status', ['pending', 'confirmed']) // Con estado 'pending' o 'confirmed'.
                                                       ->count(); // Cuenta el total.
    }

    /**
     * Método que renderiza la vista del componente.
     * Livewire llamará a este método para generar el HTML del componente.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        // Retorna la vista Blade asociada a este componente.
        // Se espera que exista un archivo en 'resources/views/livewire/admin/dashboard.blade.php'.
        // Las propiedades públicas ($userCount, $albumCount, $pendingAppointmentCount)
        // estarán automáticamente disponibles en esta vista.
        return view('livewire.admin.dashboard');
    }
}
