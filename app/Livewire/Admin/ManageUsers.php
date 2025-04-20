<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use Livewire\WithPagination;
// No se necesita: use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

class ManageUsers extends Component
{
    use WithPagination;

    public string $search = '';
    protected $paginationTheme = 'tailwind'; // Usa la paginación de Tailwind

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function mount()
    {
         if (Auth::user()?->role !== 'admin') {
             abort(403, 'Acceso no autorizado');
         }
    }

    public function render()
    {
        $adminUser = Auth::user();

        $users = User::where('role', '!=', 'admin') // No mostrar otros admins
                     ->where('id', '!=', $adminUser->id) // No mostrarse a sí mismo
                     ->when($this->search, function ($query) {
                         $query->where(function ($subQuery) {
                             $subQuery->where('name', 'like', '%'.$this->search.'%')
                                      ->orWhere('email', 'like', '%'.$this->search.'%');
                         });
                     })
                     ->orderBy('name', 'asc')
                     ->paginate(15); // O el número que prefieras

        // Devuelve la vista SIN especificar layout (usará el default app.blade.php)
        return view('livewire.admin.manage-users', [
            'users' => $users,
        ]);
    }
}
