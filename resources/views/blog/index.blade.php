<x-app-layout>
    <meta charset="utf-t8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- ========================================================== --}}
    {{--    INICIO: ETIQUETAS META ESPECÍFICAS PARA LA PÁGINA DEL BLOG --}}
    {{-- ========================================================== --}}

    <title>Blog de Fotografía y Vídeo | Consejos y Reportajes - Fotovalera</title>
    <meta name="description" content="Bienvenido al blog de Fotovalera. Descubre consejos sobre fotografía, nuestros últimos reportajes de boda en Almería y sesiones de estudio. ¡Inspírate con nosotros!">
    <meta name="keywords" content="blog de fotografia, consejos de fotografia, fotografo almeria, blog de bodas, fotografia de paisajes, edicion fotografica, fotovalera blog">

    {{-- Open Graph / Facebook --}}
    <meta property="og:title" content="Blog de Fotografía y Vídeo | Fotovalera">
    <meta property="og:description" content="Descubre consejos, técnicas y nuestros últimos trabajos en fotografía de bodas, estudio y paisajes en Almería.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ route('blog.index') }}">
    <meta property="og:image" content="{{ asset('images/og_blog.jpg') }}"> {{-- RECOMENDACIÓN: Crear y subir la imagen public/images/og_blog.jpg --}}

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Blog de Fotografía y Vídeo | Fotovalera">
    <meta name="twitter:description" content="Descubre consejos, técnicas y nuestros últimos trabajos en fotografía de bodas, estudio y paisajes en Almería.">
    <meta name="twitter:image" content="{{ asset('images/twitter_blog.jpg') }}"> {{-- RECOMENDACIÓN: Crear y subir la imagen public/images/twitter_blog.jpg --}}

    {{-- Etiquetas Adicionales --}}
    <link rel="canonical" href="{{ route('blog.index') }}" />
    <meta name="author" content="Fotovalera">
    <meta name="publisher" content="Fotovalera">
    <meta name="robots" content="index, follow">
    {{-- Para un fondo negro consistente en toda la página del blog --}}
    <div class="bg-black">

        {{-- Cabecera de la página del Blog --}}
        @livewire('configurable-page-header', [
                'identifier' => 'Blog_header',
            ])

        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
            <div class="flex flex-col lg:flex-row gap-12">

                {{-- Columna Principal: Posts (2/3 de ancho en pantallas grandes) --}}
<main class="w-full lg:w-2/3">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-16">
        @forelse($posts as $post)
            <article class="flex flex-col group" x-data>
                {{-- Sección de la Imagen --}}
                {{-- title añadido --}}
                <a href="{{ route('blog.show', $post->slug) }}" title="Leer más sobre {{ $post->title }}" class="block relative rounded-lg overflow-hidden shadow-lg mb-5">
                    <div class="aspect-w-16 aspect-h-10 bg-gray-900">
                        <img src="{{ $post->first_image_url }}" alt="{{ $post->title }}"
                             class="w-full h-full object-cover transition-transform duration-500 ease-in-out group-hover:scale-110">
                    </div>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                    <div class="absolute top-4 left-4">
                        <span class="bg-indigo-600 text-white text-xs font-bold uppercase tracking-wider px-3 py-1.5 rounded-md shadow-md">
                            {{ $post->category->name }}
                        </span>
                    </div>
                </a>

                {{-- Sección del Contenido --}}
                <div class="flex flex-col flex-grow px-2">
                    <p class="text-sm text-gray-500 mb-2">
                        <time datetime="{{ $post->published_at?->toIso8601String() }}">
                            {{ $post->published_at?->translatedFormat('j \d\e F, Y') }}
                        </time>
                    </p>
                    <h2 class="text-2xl font-bold text-gray-100 leading-tight flex-grow">
                        {{-- title añadido --}}
                        <a href="{{ route('blog.show', $post->slug) }}" title="Leer más sobre {{ $post->title }}" class="bg-left-bottom bg-gradient-to-r from-indigo-400 to-indigo-400 bg-[length:0%_2px] bg-no-repeat group-hover:bg-[length:100%_2px] transition-all duration-500">
                            {{ $post->title }}
                        </a>
                    </h2>
                    <p class="text-gray-400 mt-3 text-base leading-relaxed">
                        {{ Str::limit(strip_tags($post->content), 150) }}
                    </p>
                    <div class="mt-6">
                        {{-- title añadido --}}
                        <a href="{{ route('blog.show', $post->slug) }}" title="Continuar leyendo '{{ $post->title }}'" class="inline-flex items-center text-indigo-400 font-semibold hover:text-white transition-colors duration-300">
                            <span>Leer Más</span>
                            <svg class="w-4 h-4 ml-2 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </a>
                    </div>
                </div>
            </article>
        @empty
            <div class="md:col-span-2 text-center py-16 px-6 bg-gray-900/50 rounded-lg">
                <svg class="mx-auto h-12 w-12 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                  <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                </svg>
                <h3 class="mt-2 text-xl font-semibold text-white">Aún no hay posts</h3>
                <p class="mt-1 text-sm text-gray-400">¡Vuelve pronto! Estamos preparando contenido increíble.</p>
            </div>
        @endforelse
    </div>

    {{-- Paginación --}}
    <div class="mt-16">
        {{ $posts->links() }}
    </div>
</main>

{{-- Barra Lateral (1/3 de ancho en pantallas grandes) --}}
<aside class="w-full lg:w-1/3">
    <div class="sticky top-24 space-y-8">
        {{-- Widget de Categorías --}}
        <div>
            <h3 class="text-sm font-semibold text-gray-300 uppercase tracking-widest border-b border-gray-700 pb-2 mb-4">Categories</h3>
            <ul class="space-y-2 text-gray-400">
                <li>
                    {{-- title añadido --}}
                    <a href="{{ route('blog.index') }}" title="Ver todas las categorías" class="flex justify-between items-center hover:text-white transition-colors">
                        <span>Todas</span>
                    </a>
                </li>
                @foreach ($categories as $category)
                    <li>
                        {{-- title añadido --}}
                        <a href="{{ route('blog.index', ['category' => $category->id]) }}" title="Ver posts en la categoría {{ $category->name }}" class="flex justify-between items-center hover:text-white transition-colors">
                            <span>{{ $category->name }}</span>
                            <span class="text-xs bg-gray-700 px-2 py-0.5 rounded-full">{{ $category->posts_count }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- Widget de Archivos --}}
        <div>
            <h3 class="text-sm font-semibold text-gray-300 uppercase tracking-widest border-b border-gray-700 pb-2 mb-4">Archives</h3>
            <ul class="space-y-2 text-gray-400">
                @foreach ($archives as $archive)
                    <li>
                        {{-- title añadido --}}
                        <a href="#" title="Ver posts de {{ ucfirst($archive->month_name) }} {{ $archive->year }}" class="flex justify-between items-center hover:text-white transition-colors">
                            <span>{{ ucfirst($archive->month_name) }} {{ $archive->year }}</span>
                            <span class="text-xs bg-gray-700 px-2 py-0.5 rounded-full">{{ $archive->post_count }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</aside>
            </div>
        </div>
    </div>
     <x-self.superPie></x-self.superPie>
</x-app-layout>
