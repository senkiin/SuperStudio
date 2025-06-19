<?php

namespace App\Livewire\Admin;

use App\Models\BlogCategory;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;

class BlogCategoryManager extends Component
{
    use WithPagination;

    public bool $showModal = false;
    public ?BlogCategory $editingCategory = null;

    // Propiedades del formulario
    public $name, $slug, $description;

    protected function rules()
    {
        return [
        'name' => [
            'required',
            'string',
            'max:255',
            // Así es como se usa Rule::unique para ignorar el ID actual al editar
            Rule::unique('blog_categories')->ignore($this->editingCategory?->id),
        ],
        'slug' => [
            'required',
            'string',
            'max:255',
            // Hacemos lo mismo para el slug
            Rule::unique('blog_categories')->ignore($this->editingCategory?->id),
        ],
        'description' => ['nullable', 'string', 'max:500'],
    ];
    }

    // Generar slug automáticamente al escribir el nombre
    public function updatedName($value)
    {
        $this->slug = Str::slug($value);
    }

    public function openModalToCreate()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openModalToEdit(BlogCategory $category)
    {
        $this->resetForm();
        $this->editingCategory = $category;
        $this->name = $category->name;
        $this->slug = $category->slug;
        $this->description = $category->description;
        $this->showModal = true;
    }

    public function saveCategory()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
        ];

        if ($this->editingCategory) {
            $this->editingCategory->update($data);
            session()->flash('message', 'Categoría actualizada con éxito.');
        } else {
            BlogCategory::create($data);
            session()->flash('message', 'Categoría creada con éxito.');
        }

        $this->closeModal();
    }

    public function deleteCategory(BlogCategory $category)
    {
        // Opcional: Validar si la categoría tiene posts asociados antes de borrar
        if ($category->posts()->count() > 0) {
            session()->flash('error', 'No se puede eliminar la categoría porque tiene posts asociados.');
            return;
        }

        $category->delete();
        session()->flash('message', 'Categoría eliminada con éxito.');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->editingCategory = null;
        $this->name = '';
        $this->slug = '';
        $this->description = '';
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.admin.blog-category-manager', [
            'categories' => BlogCategory::withCount('posts')->latest()->paginate(10),
        ]);
    }
}
