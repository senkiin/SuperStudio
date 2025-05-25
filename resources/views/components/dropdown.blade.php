@props([
    'align'          => 'right',
    'width'          => '48',
    'contentClasses' => 'py-1 bg-white',
    'dropdownClasses'=> '',
])

@php
    $alignmentClasses = match ($align) {
        'left'  => 'ltr:origin-top-left rtl:origin-top-right start-0',
        'top'   => 'origin-top',
        default => 'ltr:origin-top-right rtl:origin-top-left end-0',
    };

    $widthClass = match ($width) {
        '48' => 'w-48',
        '60' => 'w-60',
        default => 'w-48',
    };
@endphp

<div
  class="relative inline-block text-left"
  x-data="{ open: false }"
  @mouseenter="open = true"
  @mouseleave="open = false"
>
  {{-- TRIGGER: le metemos normal-case aquí --}}
  <div
    @click.prevent
    class="inline-flex justify-center items-center text-sm font-bold normal-case px-1.5 py-0.5 bg-white text-black rounded-full cursor-pointer transition-colors duration-150 ease-in-out"
  >
    {{ $trigger }}
    <svg class="ml-1 h-3 w-3 fill-current" viewBox="0 0 20 20">
      <path fill-rule="evenodd"
            d="M5.293 7.293a1 1 0 011.414 0L10
               10.586l3.293-3.293a1 1 0 111.414
               1.414l-4 4a1 1 0 01-1.414
               0l-4-4a1 1 0 010-1.414z"
            clip-rule="evenodd" />
    </svg>
  </div>

  {{-- DROPDOWN --}}
  <div
    x-show="open"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="transform opacity-0 scale-95"
    x-transition:enter-end="transform opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-75"
    x-transition:leave-start="transform opacity-100 scale-100"
    x-transition:leave-end="transform opacity-0 scale-95"
    class="absolute z-50 mt-2 {{ $widthClass }} rounded-md shadow-lg {{ $alignmentClasses }} {{ $dropdownClasses }}"
    style="display: none;"
    @click="open = false"
  >
    <div class="rounded-md ring-1 ring-black ring-opacity-5 {{ $contentClasses }}">
      {{ $content }}
    </div>
  </div>
</div>
