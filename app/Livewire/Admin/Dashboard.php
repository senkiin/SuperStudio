<?php

namespace App\Livewire\Admin; // Namespace correcto

use App\Models\User;
use App\Models\Album;
use App\Models\Order;
use Livewire\Component;
use Illuminate\Support\Facades\Auth; // Importar Auth

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
        // Puedes usar un layout específico de admin si lo creas: ->layout('layouts.admin')
        return view('livewire.admin.dashboard');
           // ->layout('layouts.app'); // O seguir usando el layout de app por ahora
    }
}
