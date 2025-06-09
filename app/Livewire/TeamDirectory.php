<?php

namespace App\Livewire;

use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Rule;

class TeamDirectory extends Component
{
    use WithFileUploads;

    // Propiedades para controlar el estado y el modal
    public $employees;
    public bool $isModalOpen = false;
    public $selectedEmployeeId;

    // Propiedades del formulario
    #[Rule('required|string|max:255')]
    public $name = '';
    #[Rule('required|string|max:255')]
    public $position = '';
    #[Rule('required|string')]
    public $description = '';
    #[Rule('nullable|image|max:20480000')]
    public $photo;
    public $existingPhoto;

    public function mount()
    {
        $this->loadEmployees();
    }

    public function loadEmployees()
    {
        $this->employees = Employee::orderBy('name')->get();
    }

    // El método render simplemente devuelve la vista
    public function render()
    {
        return view('livewire.team-directory');
    }

    //==============================================
    // FUNCIONALIDAD DE CREAR
    //==============================================
    public function create()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403);
        }
        $this->resetForm();
        $this->isModalOpen = true; // <-- Esto abre el modal
    }

    //==============================================
    // FUNCIONALIDAD DE EDITAR
    //==============================================
    public function edit($id)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403);
        }
        $employee = Employee::findOrFail($id);
        $this->selectedEmployeeId = $id;
        $this->name = $employee->name;
        $this->position = $employee->position;
        $this->description = $employee->description;
        $this->existingPhoto = $employee->image_path;
        $this->photo = null;
        $this->isModalOpen = true; // <-- Esto también abre el modal (con datos cargados)
    }

    //==============================================
    // FUNCIONALIDAD DE GUARDAR (CREAR O ACTUALIZAR)
    //==============================================
    public function store()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403);
        }
        $this->validate();

        $photoPath = $this->existingPhoto;

        if ($this->photo) {
            if ($this->existingPhoto) {
                Storage::disk('empleados')->delete($this->existingPhoto);
            }
            $photoPath = $this->photo->store('/', 'empleados');
        }

        Employee::updateOrCreate(['id' => $this->selectedEmployeeId], [
            'name' => $this->name,
            'position' => $this->position,
            'description' => $this->description,
            'image_path' => $photoPath,
        ]);

        session()->flash('message', $this->selectedEmployeeId ? 'Empleado actualizado.' : 'Empleado añadido.');
        $this->closeModal();
        $this->loadEmployees();
    }

    //==============================================
    // FUNCIONALIDAD DE BORRAR
    //==============================================
    public function confirmDelete($id)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403);
        }
        $this->dispatch('show-delete-confirmation', $id);
    }

    public function delete($id)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403);
        }
        $employee = Employee::findOrFail($id);

        if ($employee->image_path) {
            Storage::disk('empleados')->delete($employee->image_path);
        }

        $employee->delete();
        session()->flash('message', 'Empleado eliminado con éxito.');
        $this->loadEmployees();
    }

    //==============================================
    // MÉTODOS AUXILIARES
    //==============================================
    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset(['name', 'position', 'description', 'photo', 'selectedEmployeeId', 'existingPhoto']);
    }
}
