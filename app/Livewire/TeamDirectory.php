<?php

namespace App\Livewire;

use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
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
    #[Rule('nullable|image|mimes:jpeg,png,jpg,gif,webp|max:20480')]
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


        try {
            $this->validate();
            $photoPath = $this->existingPhoto;

            if ($this->photo) {
                // Eliminar foto anterior si existe
                if ($this->existingPhoto) {
                    try {
                        Storage::disk('empleados')->delete($this->existingPhoto);
                    } catch (\Exception $e) {
                        Log::warning('Could not delete existing photo: ' . $e->getMessage());
                    }
                }

                // Subir nueva foto
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
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed: ' . json_encode($e->errors()));
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error saving employee: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            session()->flash('error', 'Error al guardar: ' . $e->getMessage());
        }
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

    public function removePhoto()
    {
        $this->photo = null;
    }


    public function updatedPhoto()
    {
        if ($this->photo) {
            $this->validate([
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:20480'
            ]);
        }
    }


}
