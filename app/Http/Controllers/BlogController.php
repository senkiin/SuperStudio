<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
        if ($post->status !== 'published' && !(Auth::check() && Auth::user()->role === 'admin')) {
            abort(404);
        }

        // Cargar relaciones para la vista
        $post->load('category', 'author', 'images', 'comments.user', 'likes');

        // Preparar datos SEO
        $seoData = $this->generateSeoData($post);

        return view('blog.show', compact('post', 'seoData'));
    }

    /**
     * Genera los datos SEO para un post del blog.
     */
    private function generateSeoData(BlogPost $post)
    {
        // Generar descripción desde el contenido (primeros 160 caracteres)
        $description = strip_tags($post->content);
        $description = preg_replace('/\s+/', ' ', $description); // Limpiar espacios múltiples
        $description = trim($description);
        $description = strlen($description) > 160 ? substr($description, 0, 157) . '...' : $description;

        // Generar keywords basadas en el título, categoría y contenido
        $keywords = [];
        $keywords[] = $post->category->name;
        $keywords[] = 'fotografía';
        $keywords[] = 'fotógrafo';
        $keywords[] = 'foto valera';

        // Extraer palabras clave del título
        $titleWords = explode(' ', strtolower($post->title));
        foreach ($titleWords as $word) {
            if (strlen($word) > 3) {
                $keywords[] = $word;
            }
        }

        // URL canónica
        $canonicalUrl = route('blog.show', $post->slug);

        // Imagen principal para Open Graph
        $ogImage = $post->first_image_url;

        return [
            'title' => $post->title . ' | Foto Valera - Fotógrafo Profesional',
            'description' => $description,
            'keywords' => implode(', ', array_unique($keywords)),
            'canonical' => $canonicalUrl,
            'og_title' => $post->title,
            'og_description' => $description,
            'og_image' => $ogImage,
            'og_type' => 'article',
            'article_author' => $post->author->name,
            'article_published_time' => $post->published_at?->toISOString(),
            'article_modified_time' => $post->updated_at->toISOString(),
            'article_section' => $post->category->name,
            'article_tag' => $post->category->name,
            'twitter_card' => 'summary_large_image',
            'twitter_site' => '@foto_valera',
            'twitter_creator' => '@foto_valera',
            'robots' => 'index, follow',
            'author' => $post->author->name,
            'publisher' => 'Foto Valera',
            'lang' => 'es',
        ];
    }
}
