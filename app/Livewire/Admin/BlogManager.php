<?php

namespace App\Livewire\Admin;

use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Models\BlogPostImage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Illuminate\Validation\Rule;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class BlogManager extends Component
{
    use WithFileUploads;
    use WithPagination;

    public bool $showModal = false;
    public bool $showVideoGalleryModal = false;
    public ?BlogPost $editingPost = null;
    public $categories;

    // Propiedades del formulario
    public $title, $slug, $content, $blog_category_id, $video_url, $status;
    public $new_images = [];

    // CORRECCIÓN: Declaramos la propiedad pero la inicializamos como colección en mount()
    public $existing_images;

    protected $listeners = ['videoSelected'];

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'slug' => ['required', 'string', 'max:255', Rule::unique('blog_posts')->ignore($this->editingPost?->id)],
            'content' => 'required|string|min:20',
            'blog_category_id' => 'required|exists:blog_categories,id',
            'video_url' => 'nullable|string',
            'new_images' => 'nullable|array',
            'new_images.*' => 'image|max:10240',
            'status' => 'required|in:draft,published',
        ];
    }

    public function mount()
    {
        $this->categories = BlogCategory::orderBy('name')->get();
        // CORRECCIÓN: Inicializamos como una colección vacía
        $this->existing_images = collect();
    }

    public function updatedTitle($value)
    {
        $this->slug = Str::slug($value);
    }

    // ... openModalToCreate se mantiene igual ...
    public function openModalToCreate()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openModalToEdit(BlogPost $post)
    {
        $this->resetForm();
        $this->editingPost = $post;
        $this->title = $post->title;
        $this->slug = $post->slug;
        $this->content = $post->content;
        $this->blog_category_id = $post->blog_category_id;
        $this->video_url = $post->video_url;
        $this->status = $post->status;
        $this->existing_images = $post->images; // Esto ya devuelve una colección
        $this->showModal = true;
    }

    public function videoSelected($videoUrl)
    {
        $this->video_url = $videoUrl;
        $this->showVideoGalleryModal = false;
    }

    // ... savePost y deleteImage se mantienen igual ...
    public function savePost()
    {
        $this->validate();

        $postData = [
            'user_id' => Auth::id(),
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'blog_category_id' => $this->blog_category_id,
            'video_url' => $this->video_url,
            'status' => $this->status,
            'published_at' => $this->status === 'published' ? now() : null,
        ];

        $post = BlogPost::updateOrCreate(['id' => $this->editingPost?->id], $postData);

        if (!empty($this->new_images)) {
            foreach ($this->new_images as $image) {
                $path = $image->store('', 'blog-media');
                $post->images()->create(['image_path' => $path]);
            }
        }

        session()->flash('message', 'Post guardado con éxito.');
        $this->closeModal();
    }

    public function deleteImage($imageId)
    {
        $image = BlogPostImage::find($imageId);
        if ($image) {
            Storage::disk('blog-media')->delete($image->image_path);
            $image->delete();
            $this->existing_images = $this->editingPost->fresh()->images;
            session()->flash('message', 'Imagen eliminada.');
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->showVideoGalleryModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->editingPost = null;
        $this->title = '';
        $this->slug = '';
        $this->content = '';
        $this->blog_category_id = null;
        $this->video_url = '';
        $this->status = 'draft';
        $this->new_images = [];
        // CORRECCIÓN: reseteamos a una colección vacía
        $this->existing_images = collect();
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.admin.blog-manager', [
            'posts' => BlogPost::with('category', 'author', 'images')->latest()->paginate(10),
        ]);
    }
}
