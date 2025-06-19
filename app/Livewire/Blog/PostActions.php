<?php

namespace App\Livewire\Blog;

use App\Models\BlogPost;
use Google\Service\Blogger\BlogPosts;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PostActions extends Component
{
    public BlogPost $post;
    public int $likesCount;
    public bool $isLikedByUser;
    public string $comment = '';

    public function mount()
    {
        $this->updateLikeStatus();
    }

    public function toggleLike()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $like = $this->post->likes()->where('user_id', Auth::id())->first();

        if ($like) {
            $like->delete();
        } else {
            $this->post->likes()->create(['user_id' => Auth::id()]);
        }

        $this->updateLikeStatus();
    }

    public function addComment()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $this->validate(['comment' => 'required|string|min:3']);

        $this->post->comments()->create([
            'user_id' => Auth::id(),
            'content' => $this->comment,
        ]);

        $this->comment = '';
        $this->post->refresh(); // Para recargar la relación de comentarios
    }

    private function updateLikeStatus()
    {
        $this->likesCount = $this->post->likes()->count();
        $this->isLikedByUser = Auth::check() ? $this->post->likes()->where('user_id', Auth::id())->exists() : false;
    }

    public function render()
    {
        return view('livewire.blog.post-actions');
    }
}
