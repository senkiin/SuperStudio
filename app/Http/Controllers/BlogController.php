<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // ¡Asegúrate de que esta línea esté presente!

class BlogController extends Controller
{
    /**
     * Muestra la página principal del blog con posts, categorías y archivos.
     */
    public function index(Request $request)
    {
        // --- Cargar Categorías ---
        $categoryId = $request->get('category');
        $categories = BlogCategory::whereHas('posts', function ($query) {
                $query->where('status', 'published');
            })
            ->withCount(['posts' => function ($query) {
                $query->where('status', 'published');
            }])
            ->orderBy('name')->get();

        // --- Cargar Posts Paginados ---
        $posts = BlogPost::where('status', 'published')
            ->when($categoryId, function ($query) use ($categoryId) {
                return $query->where('blog_category_id', $categoryId);
            })
            ->with('category', 'author', 'images')
            ->latest('published_at')
            ->paginate(9);

        // --- LÓGICA DE ARCHIVOS COMPATIBLE CON MYSQL y POSTGRESQL ---
        $driver = DB::connection()->getDriverName(); // Detecta el driver: 'mysql', 'pgsql', etc.

        if ($driver === 'mysql') {
            // Sintaxis para MySQL
            $archives = BlogPost::where('status', 'published')
                ->selectRaw("YEAR(published_at) as year, MONTHNAME(published_at) as month_name, MONTH(published_at) as month, COUNT(*) as post_count")
                ->groupBy('year', 'month_name', 'month')
                ->orderByRaw('year DESC, month DESC')
                ->get();
        } else { // Asumimos pgsql u otros
            // Sintaxis para PostgreSQL
            $archives = BlogPost::where('status', 'published')
                ->selectRaw("EXTRACT(YEAR FROM published_at) as year, TO_CHAR(published_at, 'Month') as month_name, EXTRACT(MONTH FROM published_at) as month, COUNT(*) as post_count")
                ->groupBy('year', 'month_name', 'month')
                ->orderByRaw('year DESC, month DESC')
                ->get();
        }

        return view('blog.index', compact('posts', 'categories', 'archives'));
    }

    /**
     * Muestra un post individual del blog.
     */
    public function show(BlogPost $post)
    {
        // Permite al admin ver posts en borrador, pero no a otros usuarios
        if ($post->status !== 'published' && !(auth()->check() && auth()->user()->role === 'admin')) {
            abort(404);
        }

        // Cargar relaciones para la vista
        $post->load('category', 'author', 'images', 'comments.user', 'likes');

        return view('blog.show', compact('post'));
    }
}
