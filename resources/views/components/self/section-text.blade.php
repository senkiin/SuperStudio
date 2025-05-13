{{-- resources/views/components/section-hero.blade.php --}}
@props([
    'title',         // Texto del título
    'subtitle' => '',// Texto del subtítulo (opcional)
    'bg'       => 'bg-black',    // Clase de fondo
    'text'     => 'text-white',  // Clase de color de texto
    'py'       => 'py-12'        // Padding vertical
])

<section {{ $attributes->merge(['class' => "$py $bg $text"]) }}>
  <div class="container mx-auto px-6">
    @if($title)
      <h2
        x-data
        x-intersect="$el.classList.add('aos-animate')"
        class="text-3xl font-semibold mb-4 text-center fade-up"
      >
        {{ $title }}
      </h2>
    @endif

    @if($subtitle)
      <p
        x-data
        x-intersect="$el.classList.add('aos-animate')"
        class="text-gray-400 text-center max-w-3xl mx-auto fade-up"
      >
        {{ $subtitle }}
      </p>
    @endif

    {{-- Slot adicional si quieres meter más contenido --}}
    {{ $slot }}
  </div>
</section>
