<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // <-- ¡Añade esta línea!

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $categoryId = $request->get('category');
        $categories = BlogCategory::whereHas('posts', function ($query) {
                $query->where('status', 'published');
            })
            ->withCount(['posts' => function ($query) {
                $query->where('status', 'published');
            }])
            ->orderBy('name')->get();

        $posts = BlogPost::where('status', 'published')
            ->when($categoryId, function ($query) use ($categoryId) {
                return $query->where('blog_category_id', $categoryId);
            })
            ->with('category', 'author', 'images') // Eager load para optimizar
            ->latest('published_at')
            ->paginate(9);

        // NUEVA LÓGICA PARA LOS ARCHIVOS
        $archives = BlogPost::where('status', 'published')
            ->select(
                DB::raw('YEAR(published_at) as year'),
                DB::raw('MONTHNAME(published_at) as month_name'),
                DB::raw('MONTH(published_at) as month'),
                DB::raw('COUNT(*) as post_count')
            )
            ->groupBy('year', 'month_name', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        return view('blog.index', compact('posts', 'categories', 'archives'));
    }

    public function show(BlogPost $post)
    {
        if ($post->status !== 'published' && !(auth()->check() && auth()->user()->role === 'admin')) {
            abort(404);
        }

        // Cargar relaciones para la vista
        $post->load('category', 'author', 'images', 'comments.user', 'likes');

        return view('blog.show', compact('post'));
    }
}
