<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Album;
// Quita Order si ya no lo usas aquí directamente: use App\Models\Order;
use App\Models\Superappointment; // <<< AÑADIR ESTO
use Carbon\Carbon; // <<< AÑADIR ESTO
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
// Quita View y Layout si no los usas directamente aquí
// use Illuminate\View\View;
// use Livewire\Attributes\Layout;


class Dashboard extends Component
{
    public $userCount;
    public $albumCount;
    // public $orderCount; // <<< ELIMINAR ESTO
    public $pendingAppointmentCount; // <<< AÑADIR ESTO

    public function mount()
    {
        if (Auth::user()?->role !== 'admin') {
            abort(403, 'Acceso no autorizado');
        }

        $this->userCount = User::where('role', 'user')->count();
        $this->albumCount = Album::count();
        // $this->orderCount = Order::whereIn('status', ['pending', 'processing'])->count(); // <<< ELIMINAR ESTO

        // Calcular citas pendientes (fecha futura y estado 'pending' o 'confirmed')
        $this->pendingAppointmentCount = Superappointment::where('appointment_datetime', '>', Carbon::now())
                                                       ->whereIn('status', ['pending', 'confirmed'])
                                                       ->count(); // <<< AÑADIR ESTO
    }

    public function render()
    {
        return view('livewire.admin.dashboard');
    }
}
