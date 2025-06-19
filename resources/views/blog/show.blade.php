<x-app-layout>
    <div class="bg-black text-gray-300">
        {{-- Cabecera Hero con la primera imagen del carrusel como fondo --}}
        <header class="relative h-[60vh] md:h-[70vh] w-full flex items-center justify-center text-center text-white"
            style="background-image: url('{{ $post->first_image_url }}'); background-size: cover; background-position: center;">

            {{-- Overlay oscuro para legibilidad --}}
            <div class="absolute inset-0 bg-black opacity-60"></div>

            {{-- Contenido de la cabecera --}}
            <div class="relative z-10 px-4">
                <div class="mb-4">
                    <a href="{{ route('blog.index', ['category' => $post->category->id]) }}"
                        class="bg-white/10 backdrop-blur-sm text-white text-xs font-semibold uppercase tracking-wider px-4 py-2 rounded-full hover:bg-white/20 transition-colors">
                        {{ $post->category->name }}
                    </a>
                </div>
                <h1 class="text-4xl md:text-6xl font-black tracking-tighter uppercase">
                    {{ $post->title }}
                </h1>
                <p class="mt-4 text-md text-gray-300">
                    {{ $post->published_at?->translatedFormat('F j, Y') }}
                </p>
            </div>
        </header>

        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <article class="max-w-4xl mx-auto py-12 md:py-16">

                {{-- Sección de Autor y Compartir --}}
                <div class="flex flex-col sm:flex-row justify-between items-center mb-10 pb-6 border-b border-gray-800">
                    <div class="flex items-center mb-4 sm:mb-0">
                        <img class="h-12 w-12 rounded-full object-cover mr-4"
                            src="{{ $post->author->profile_photo_url }}" alt="{{ $post->author->name }}">
                        <div>
                            <p class="font-semibold text-white">{{ $post->author->name }}</p>
                            <p class="text-sm text-gray-400">Autor</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm font-semibold text-gray-400">Compartir en----></span>

                        @php
                            $shareUrl = urlencode(url()->current());
                            $shareTitle = urlencode($post->title);
                        @endphp

                        {{-- Enlace para Facebook --}}
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" target="_blank"
                            class="text-gray-400 hover:text-white transition-colors" title="Compartir en Facebook">
                            Facebook
                        </a>

                        {{-- Enlace para Twitter --}}
                        <a href="https://twitter.com/intent/tweet?url={{ $shareUrl }}&text={{ $shareTitle }}"
                            target="_blank" class="text-gray-400 hover:text-white transition-colors"
                            title="Compartir en Twitter">
                            Twitter
                        </a>

                        {{-- Enlace para LinkedIn --}}
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $shareUrl }}"
                            target="_blank" class="text-gray-400 hover:text-white transition-colors"
                            title="Compartir en LinkedIn">
                            Linkedin
                        </a>

                        {{-- Enlace para WhatsApp (muy útil en móviles) --}}
                        <a href="https://api.whatsapp.com/send?text={{ $shareTitle }}%20{{ $shareUrl }}"
                            target="_blank" class="text-gray-400 hover:text-white transition-colors"
                            title="Compartir en WhatsApp">
                            Whatssap
                        </a>
                    </div>
                </div>

                {{-- Carrusel de imágenes (si hay más de una) y vídeo --}}
                @if ($post->images->count() > 1)
                   {{-- INICIO DEL CÓDIGO DEL CARRUSEL --}}
<div x-data="{
    activeSlide: 1,
    slides: {{ $post->images->count() }},
    next() {
        this.activeSlide = this.activeSlide === this.slides ? 1 : this.activeSlide + 1
    },
    prev() {
        this.activeSlide = this.activeSlide === 1 ? this.slides : this.activeSlide - 1
    }
}" class="relative mb-10 shadow-lg rounded-lg overflow-hidden bg-gray-900">

    {{-- Contenedor de las Imágenes --}}
    <div class="relative w-full aspect-video">
        @foreach ($post->images as $image)
            <div x-show="activeSlide === {{ $loop->iteration }}"
                 class="absolute inset-0 transition-opacity duration-500 ease-in-out"
                 x-transition:enter="transition-opacity ease-in-out duration-500"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-in-out duration-500"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
                <img src="{{ Storage::disk('blog-media')->url($image->image_path) }}" alt="{{ $post->title }} - Imagen {{ $loop->iteration }}" class="w-full h-full object-cover">
            </div>
        @endforeach
    </div>

    {{-- Botones de Navegación (solo si hay más de una imagen) --}}
    @if($post->images->count() > 1)
        <div class="absolute inset-0 flex items-center justify-between px-4">
            <button @click="prev()" class="bg-black bg-opacity-40 text-white p-2 rounded-full hover:bg-opacity-60 transition focus:outline-none focus:ring-2 ring-white ring-offset-2 ring-offset-black/50">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </button>
            <button @click="next()" class="bg-black bg-opacity-40 text-white p-2 rounded-full hover:bg-opacity-60 transition focus:outline-none focus:ring-2 ring-white ring-offset-2 ring-offset-black/50">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>
        </div>

        {{-- Indicadores de Puntos --}}
        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex space-x-2">
            @foreach ($post->images as $image)
                <button @click="activeSlide = {{ $loop->iteration }}"
                        :class="{ 'bg-white': activeSlide === {{ $loop->iteration }}, 'bg-white/50': activeSlide !== {{ $loop->iteration }} }"
                        class="w-3 h-3 rounded-full hover:bg-white transition"></button>
            @endforeach
        </div>
    @endif
</div>
{{-- FIN DEL CÓDIGO DEL CARRUSEL --}}  @endif




                {{-- Contenido del Post --}}
                <div
                    class="prose prose-lg prose-invert max-w-none text-gray-300 prose-headings:text-white prose-a:text-indigo-400 hover:prose-a:text-indigo-300 prose-strong:text-white">
                    {!! $post->content !!}
                </div>
 @if($post->video_url)
    <div class="mb-10 rounded-lg overflow-hidden shadow-2xl bg-black">
        @php
            $embedUrl = '';
            // Detecta si es una URL de YouTube
            if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $post->video_url, $youtubeMatch)) {
                $embedUrl = 'https://www.youtube.com/embed/' . $youtubeMatch[1];

            // Detecta si es una URL o un ID de Vimeo
            } elseif (preg_match('/(https?:\/\/)?(www\.)?(player\.)?vimeo\.com\/([a-z]*\/)*([0-9]{6,11})[?]?.*/', $post->video_url, $vimeoMatch)) {
                $vimeoId = $vimeoMatch[5];
                $embedUrl = "https://player.vimeo.com/video/{$vimeoId}";

            // Acepta un ID de Vimeo numérico directamente
            } elseif (is_numeric($post->video_url)) {
                    $embedUrl = "https://player.vimeo.com/video/{$post->video_url}";
            }
        @endphp

        @if($embedUrl)
            {{-- Este es el contenedor que mantiene la proporción 16:9 --}}
            <div class="relative w-full" style="padding-top: 56.25%;"> {{-- 56.25% = 9 / 16 --}}
                <iframe src="{{ $embedUrl }}"
                        class="absolute top-0 left-0 w-full h-full" {{-- El iframe ocupa el 100% del contenedor --}}
                        frameborder="0"
                        allow="autoplay; fullscreen; picture-in-picture"
                        allowfullscreen>
                </iframe>
            </div>
        @endif
    </div>
@endif
                {{-- Componente de acciones: likes y comentarios --}}
                <div class="mt-16 pt-8 border-t border-gray-800">
                    @auth
                        @livewire('blog.post-actions', ['post' => $post])
                    @else
                        <p class="text-center text-gray-500">
                            <a href="{{ route('login') }}" class="text-indigo-400 hover:underline">Inicia sesión</a> o
                            <a href="{{ route('register') }}" class="text-indigo-400 hover:underline">regístrate</a> para
                            dejar un comentario o dar "me gusta".
                        </p>
                    @endauth
                </div>

            </article>
        </div>
    </div>
    <x-self.superPie></x-self.superPie>
</x-app-layout>
