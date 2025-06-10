{{-- ////////////////////////////////////////////////////////////////////// --}}
{{-- ARCHIVO: resources/views/vendor/livewire/tailwind.blade.php --}}
{{-- ESTA ES LA VERSIÓN MODIFICADA CON EL TEMA OSCURO Y LA ALINEACIÓN CORRECTA --}}
{{-- ////////////////////////////////////////////////////////////////////// --}}
@php
if (! isset($scrollTo)) {
    $scrollTo = 'body';
}
$scrollIntoViewJsSnippet = ($scrollTo !== false) ? "(\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView({ behavior: 'smooth' })" : '';
@endphp

<div>
    @if ($paginator->hasPages())
        <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between">

            {{-- Versión móvil --}}
            <div class="flex justify-between flex-1 sm:hidden">
                @if ($paginator->onFirstPage())
                    <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-gray-800 border border-gray-700 cursor-default leading-5 rounded-md">
                        {!! __('pagination.previous') !!}
                    </span>
                @else
                    <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-300 bg-gray-800 border border-gray-700 leading-5 rounded-md hover:bg-gray-700">
                        {!! __('pagination.previous') !!}
                    </button>
                @endif

                @if ($paginator->hasMorePages())
                    <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-300 bg-gray-800 border border-gray-700 leading-5 rounded-md hover:bg-gray-700">
                        {!! __('pagination.next') !!}
                    </button>
                @else
                    <span class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-500 bg-gray-800 border border-gray-700 cursor-default leading-5 rounded-md">
                        {!! __('pagination.next') !!}
                    </span>
                @endif
            </div>

            {{-- Versión de escritorio con la alineación corregida --}}
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-end sm:space-x-8">
                <div>
                    <p class="text-sm text-gray-400 leading-5">
                        Mostrando del
                        <span class="font-semibold text-gray-200">{{ $paginator->firstItem() }}</span>
                        al
                        <span class="font-semibold text-gray-200">{{ $paginator->lastItem() }}</span>
                        de
                        <span class="font-semibold text-gray-200">{{ $paginator->total() }}</span>
                        resultados
                    </p>
                </div>

                <div>
                    <span class="relative z-0 inline-flex shadow-sm rounded-md">
                        {{-- Botón "Anterior" --}}
                        <span>
                            @if ($paginator->onFirstPage())
                                <span class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-600 bg-gray-800 border border-gray-700 cursor-default rounded-l-md">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                                </span>
                            @else
                                <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-400 bg-gray-800 border border-gray-700 rounded-l-md hover:text-gray-200 hover:bg-gray-700 focus:z-10 focus:outline-none focus:ring-2 ring-offset-2 ring-offset-black focus:ring-indigo-500">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                                </button>
                            @endif
                        </span>

                        {{-- Números de Página --}}
                        @foreach ($elements as $element)
                            @if (is_string($element))
                                <span class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-500 bg-gray-800 border border-gray-700 cursor-default">{{ $element }}</span>
                            @endif
                            @if (is_array($element))
                                @foreach ($element as $page => $url)
                                    @if ($page == $paginator->currentPage())
                                        <span class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-semibold text-white bg-indigo-600 border border-indigo-500 cursor-default">{{ $page }}</span>
                                    @else
                                        <button type="button" wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-400 bg-gray-800 border border-gray-700 hover:text-gray-200 hover:bg-gray-700 focus:z-10 focus:outline-none focus:ring-2 ring-offset-2 ring-offset-black focus:ring-indigo-500">
                                            {{ $page }}
                                        </button>
                                    @endif
                                @endforeach
                            @endif
                        @endforeach

                        {{-- Botón "Siguiente" --}}
                        <span>
                            @if ($paginator->hasMorePages())
                                <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-gray-400 bg-gray-800 border border-gray-700 rounded-r-md hover:text-gray-200 hover:bg-gray-700 focus:z-10 focus:outline-none focus:ring-2 ring-offset-2 ring-offset-black focus:ring-indigo-500">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                                </button>
                            @else
                                <span class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-gray-600 bg-gray-800 border border-gray-700 cursor-default rounded-r-md">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                                </span>
                            @endif
                        </span>
                    </span>
                </div>
            </div>
        </nav>
    @endif
</div>
