<?php

namespace App\Livewire\Admin; // Namespace correcto

use App\Models\User;
use App\Models\Album;
use App\Models\Order;
use Livewire\Component;
use Illuminate\Support\Facades\Auth; // Importar Auth
use Illuminate\View\View;
use Livewire\Attributes\Layout; // <<< Importar el atributo


class Dashboard extends Component
{
    // Propiedades para mostrar datos (ejemplo)
    public $userCount;
    public $albumCount;
    public $orderCount;

    // El método mount se ejecuta al cargar el componente
    public function mount()
    {
        // Otra capa de seguridad (aunque el middleware ya protege la ruta)
        if (Auth::user()?->role !== 'admin') { // ¡Ajusta comprobación de rol!
            abort(403, 'Acceso no autorizado');
        }

        // Cargar datos iniciales para el dashboard (ejemplo)
        $this->userCount = User::where('role', 'user')->count(); // Contar solo clientes
        $this->albumCount = Album::count();
        $this->orderCount = Order::whereIn('status', ['pending', 'processing'])->count(); // Contar pedidos pendientes/procesando
    }

    // El método render simplemente devuelve la vista
    public function render()
    {
        return view('livewire.admin.dashboard'); // Ya no necesita ->layout()

    }
}
